<?php

namespace AppBundle;

use Doctrine\ODM\MongoDB\DocumentRepository;
use Doctrine\ODM\MongoDB\DocumentManager;
use PHPGit\Git;
use Symfony\Component\Process\Process;
use AppBundle\Document\DocumentationFile;
use Psr\Log\LoggerInterface;
use Naneau\SemVer\Sort;

class Worker
{
    public function __construct(DocumentRepository $repository, DocumentManager $dm, Git $git, LoggerInterface $logger, $stageDir, $targetDir, $cacheDir, $samiCmd)
    {
        $this->repository = $repository;
        $this->dm = $dm;
        $this->git = $git;
        $this->logger = $logger;
        $this->stageDir = $stageDir;
        $this->targetDir = $targetDir;
        $this->cacheDir = $cacheDir;
        $this->samiCmd = $samiCmd;
    }
    public function run()
    {
        if (!is_dir($this->stageDir)) {
            mkdir($this->stageDir);
        }
        if (!is_dir($this->targetDir)) {
            mkdir($this->targetDir);
        }
        $run = true;
        $runs = 0;
        while($run) {
            foreach ($this->repository->getPendingProjects() as $project) {
                printf("Processing %s/%s\n", $project->getOwner()->getUsername(), $project->getName());
                $this->process($project);
                printf("Finished %s/%s\n", $project->getOwner()->getUsername(), $project->getName());
            }

            if ($runs++ > 100) {
                $run = false;
            } else {
                echo "waiting...\n";
                sleep(60);
            }
        }
    }

    protected function process($project)
    {
        $userDir = implode('/', array($this->stageDir, $project->getOwner()->getUsername()));
        $stageDir = implode('/', array($userDir, $project->getName()));

        $userTargetDir = implode('/', array($this->targetDir, $project->getOwner()->getUsername()));
        $targetDir = implode('/', array($userTargetDir, $project->getName()));

        $userCacheDir = implode('/', array($this->cacheDir, $project->getOwner()->getUsername()));
        $cacheDir = implode('/', array($userCacheDir, $project->getName()));

        if (!is_dir($userDir)) {
            mkdir($userDir);
        }
        if (!is_dir($userCacheDir)) {
            mkdir($userCacheDir);
        }
        if (!is_dir($stageDir)) {
            $this->git->clone(sprintf('https://github.com/%s.git', $project->getGithubName()), $stageDir);
        }
        $this->git->setRepository($stageDir);
        $this->git->checkout('master');
        $this->git->fetch->all();

        $project->setMasterVersion($this->git->describe->tags('master'));

        if (!is_dir($userTargetDir)) {
            mkdir($userTargetDir);
        }
        if (!is_dir($cacheDir)) {
            mkdir($cacheDir);
        }
        if (!is_dir($targetDir)) {
            mkdir($targetDir);
        }
        $project->setLastLogs(array());
        $process = new Process($this->samiCmd, $stageDir, array('STAGE_DIR' => $stageDir, 'TARGET_DIR' => $targetDir, 'CACHE_DIR' => $cacheDir));
        $process->run(function ($type, $buffer) use ($project) {
            if (Process::ERR === $type) {
                $log = 'ERR > '.$buffer;
            } else {
                $log = 'OUT > '.$buffer;
            }
            $project->addLastLog($log);
            $this->dm->flush();
        });

        $directory = new \RecursiveDirectoryIterator($targetDir);
        $iterator = new \RecursiveIteratorIterator($directory);
        $project->clearDocFiles();
        $versions = array();
        foreach ($iterator AS $file) {
            if ($file->isFile()) {
                $this->logger->info(sprintf('processing file %s', $file->getPathname()));
                $parts = explode('/', str_replace($this->targetDir.'/', '', $file->getPathname()));

                if (array_key_exists(2, $parts)) {
                    $version = $parts[2];
                    $prefix = $project->getTagPrefix();
                    if (substr($version, 0, strlen($prefix)) == $prefix) {
                        $version = substr($version, strlen($prefix));
                    }
                    $versions[$version] = true;
                }

                $file = new \Symfony\Component\HttpFoundation\File\File($file);
                $name = str_replace($this->targetDir.'/', '', $file->getPathname());

                $docFile = $this->dm
                    ->createQueryBuilder('AppBundle\Document\DocumentationFile')
                    ->findAndRemove()
                    ->field('name')->equals($name)
                    ->getQuery()
                    ->execute();
                if (!$docFile) {
                    $docFile = new DocumentationFile;
                }

                $docFile->setName($name);

                $mimeType = $file->getMimeType();
                if ($file->getExtension() == 'js') {
                    $mimeType = 'application/javascript';
                } elseif ($file->getExtension() == 'css') {
                    $mimeType = 'text/css';
                }
                $docFile->setMimeType($mimeType);
                $docFile->setFile($file->getRealPath());

                $this->dm->persist($docFile);
                $project->addDocFile($docFile);
                $this->dm->flush();
            }
        }
        $this->logger->info(sprintf('done with %s', $project->getGithubName()));
        unset($versions['develop']);
        $versions = array_keys($versions);
        $realVersions = array();
        foreach (Sort::sort($versions) as $version) {
            $realVersions[] = $project->getTagPrefix().$version;
        }
        $realVersions[] = 'develop';
        $project->setVersions($realVersions);
        $project->setNeedsUpdate(false);
        $this->dm->flush();
    }
}

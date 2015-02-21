<?php

namespace AppBundle;

use Doctrine\ODM\MongoDB\DocumentRepository;
use Doctrine\ODM\MongoDB\DocumentManager;
use PHPGit\Git;
use Symfony\Component\Process\Process;
use AppBundle\Document\DocumentationFile;

class Worker
{
    public function __construct(DocumentRepository $repository, DocumentManager $dm, Git $git, $stageDir, $targetDir, $samiCmd)
    {
            $this->repository = $repository;
            $this->dm = $dm;
            $this->git = $git;
            $this->stageDir = $stageDir;
            $this->targetDir = $targetDir;
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
        while($run) {
            $project = $this->repository->findOneBy(array(),array('updatedAt', 'DESC'));

            $this->process($project);

            $run = false;
        }
    }

    protected function process($project)
    {
        $userDir = implode('/', array($this->stageDir, $project->getOwner()->getUsername()));
        $stageDir = implode('/', array($userDir, $project->getName()));

        $userTargetDir = implode('/', array($this->targetDir, $project->getOwner()->getUsername()));
        $targetDir = implode('/', array($userTargetDir, $project->getName()));

        if (!is_dir($userDir)) {
            mkdir($userDir);
        }
        if (!is_dir($stageDir)) {
            $this->git->clone(sprintf('https://github.com/%s.git', $project->getGithubName()), $stageDir);
        } else {
            $this->git->fetch->all();
        }
        $this->git->setRepository($stageDir);

        $project->setMasterVersion($this->git->describe->tags('master'));

        if (!is_dir($userTargetDir)) {
            mkdir($userTargetDir);
        }
        if (!is_dir($targetDir)) {
            mkdir($targetDir);
        }
        $process = new Process($this->samiCmd, $stageDir, array('STAGE_DIR' => $stageDir, 'TARGET_DIR' => $targetDir));
        $process->run(function ($type, $buffer) {
            if (Process::ERR === $type) {
                echo 'ERR > '.$buffer;
            } else {
                echo 'OUT > '.$buffer;
            }
        });

        $directory = new \RecursiveDirectoryIterator($targetDir);
        $iterator = new \RecursiveIteratorIterator($directory);
        $project->clearDocFiles();
        foreach ($iterator AS $file) {

            if ($file->isFile() && $file->getFilename() !== '.html') {
                $file = new \Symfony\Component\HttpFoundation\File\File($file);

                $docFile = new DocumentationFile;
                $docFile->setName(str_replace($this->targetDir.'/', '', $file->getPathname()));

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
            }
        }
        $this->dm->flush();
    }
}

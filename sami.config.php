<?php

use Sami\Sami;
use Sami\Version\GitVersionCollection;

$stageDir  = isset($_ENV['STAGE_DIR'])  ? $_ENV['STAGE_DIR']  : getcwd();
$cacheDir = isset($_ENV['CACHE_DIR']) ? $_ENV['CACHE_DIR'] : getcwd();
$targetDir = isset($_ENV['TARGET_DIR']) ? $_ENV['TARGET_DIR'] : getcwd();
echo $targetDir."\n";

use Symfony\Component\Finder\Finder;

$iterator = Finder::create()
    ->files()
    ->name('*.php')
    ->exclude('Resources')
    ->exclude('Tests')
    ->exclude('Test')
    ->in($stageDir)
;

$versions = GitVersionCollection::create($stageDir)
    ->addFromTags('*')
    ->add('develop', 'develop branch')
;

return new Sami($iterator, array(
    'theme'     => 'app',
    'versions'  => $versions,
    'build_dir' => $targetDir.'/%version%',
    'cache_dir' => $cacheDir.'/%version%',
    'template_dirs' => array(__DIR__.'/app/Resources/Sami/themes'),
));

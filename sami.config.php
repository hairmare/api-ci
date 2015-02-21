<?php

use Sami\Sami;
use Sami\Version\GitVersionCollection;

$stageDir  = isset($_ENV['STAGE_DIR'])  ? $_ENV['STAGE_DIR']  : getcwd();
$targetDir = isset($_ENV['TARGET_DIR']) ? $_ENV['TARGET_DIR'] : getcwd();
echo $targetDir."\n";

$versions = GitVersionCollection::create($stageDir)
    ->addFromTags('v*')
    ->add('develop', 'develop branch')
;

return new Sami($stageDir, array(
    'versions'  => $versions,
    'build_dir' => $targetDir.'/%version%',
    'cache_dir' => $stageDir.'/app/cache/%version%',
));

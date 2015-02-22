<?php

$vcap = getenv('VCAP_SERVICES');
if (empty($vcap)) {
    $vcap = getenv('SYMFONY__VCAP__SERVICES');
}

use Graviton\Vcap\Loader;

if (!empty($vcap)) {
    // this next line is clearly a hack
    $container->setParameter('sami_cmd', '/home/vcap/app/php/bin/php /home/vcap/app/php/bin/sami.php update /home/vcap/app/sami.config.php -vvv --force');

    $loader = new Loader;
    $loader->setInput($vcap);

    $type = 'mongodb-2.2';
    $name = 'api-ci-mongodb';
    $container->setParameter('mongodb_host', $loader->getHost($type, $name));
    $container->setParameter('mongodb_port', $loader->getPort($type, $name));
    $container->setParameter('mongodb_user', $loader->getUsername($type, $name));
    $container->setParameter('mongodb_password', $loader->getPassword($type, $name));
    $container->setParameter('mongodb_database', $loader->getDb($type, $name));
}

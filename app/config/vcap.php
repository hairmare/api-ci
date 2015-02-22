<?php

$vcap = getenv('SYMFONY__VCAP__SERVICES');

use Graviton\Vcap\Loader;

if (!empty($vcap)) {
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

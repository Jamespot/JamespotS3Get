<?php

use Jamespot\Misc\OpenstackAccess;

require_once 'config.php';
require_once 'OpenstackAccess.php';

if (isset($config)) {
    $osAccess = new OpenstackAccess($config);
    $osAccess->initClient();
    $object = $osAccess->getFileObject($argv[1])->getMetadata();
    print_r($object);
}

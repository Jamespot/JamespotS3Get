<?php

use Jamespot\Misc\OpenstackAccess;

require_once 'config.php';
require_once 'OpenstackAccess.php';

$osAccess = new OpenstackAccess($config);

$osAccess->initClient();

echo($osAccess->getFileData($argv[1]));

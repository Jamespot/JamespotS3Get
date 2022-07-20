<?php

use Jamespot\Misc\OpenstackAccess;

require_once 'config.php';
require_once 'OpenstackAccess.php';

$osAccess = new OpenstackAccess($config);

$osAccess->initClient();

echo($osAccess->list());

#for ($i = 1; $i <= 10; $i++) {
#    echo $i;
#}

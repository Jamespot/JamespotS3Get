<?php 
##Aller dans la db
##trouver le user et le mot de passe
#select * from properties where file="openstack"; 
##
$config = (object)[
    "url" => "https://auth.cloud.ovh.net/v3/",
    "region" => "GRA",
    "username" => "user-xxxxxxx",
    "password" => "xxxxxxxxxxxxxxx",
    "projectId" => "xxxxxxxxxxxxxxxxxxxxxxx",
    "projectName" => "xxxxxxxxxxxxx",
    "containerName" => "jproXX_instance_XXXX"
]; 
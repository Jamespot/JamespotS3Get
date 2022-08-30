<?php

use Jamespot\Misc\OpenstackAccess;

require_once 'config.php';
require_once 'OpenstackAccess.php';

if (isset($config) and is_object($config)) {
    $objects = [];
    $osAccess = new OpenstackAccess($config);
    $osAccess->initClient();
    $path = $config->containerPath . $config->containerName;
    if (!is_dir($path)) {
        echo 'container path is missing.';
        exit();
    }
    $directories = new DirectoryIterator($path);
    $containerDirectoryPath = 'data/' . $config->containerName;
    foreach ($directories as $directory) {
        if ($directory->isDir() && !$directory->isDot()) {
            if (is_dir($directory->getPathname())) {
                $files = new DirectoryIterator($directory->getPathname());
                foreach ($files as $file) {
                    if ($file->isFile()) {
                        $objects[$directory->getFilename()] = $file->getFilename();
                    }
                }
            }
        }
    }
    if (array_key_exists(1, $argv) && 'stopBeforeFilePutContent' === $argv[1]) {
        echo print_r($objects, 1);
        exit();
    }
    if (!is_dir($containerDirectoryPath)) {
        mkdir($containerDirectoryPath);
    }

    try {
        if (class_exists('ZipArchive')) {
            $zip = new ZipArchive();
            $zip->open('./data.zip', ZipArchive::CREATE | ZipArchive::OVERWRITE);
            foreach ($objects as $objectName => $fileName) {
                $fileData = $osAccess->getFileData($objectName);
                $filePath = $containerDirectoryPath . '/' . $objectName;
                if (!is_dir($filePath)) {
                    mkdir($filePath);
                }
                $fullPath = $filePath . '/' . $fileName;
                file_put_contents($fullPath, $fileData);
                $zip->addFile($fullPath);
            }
            $zip->close();
            $zip = new ZipArchive();
            return;
        }
        echo 'class ZipArchive not found try install php-zip lib';
    } catch (Exception $e) {
        echo $e->getMessage();
    }
}

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
            //var_dump($directory);
            if (is_dir($directory->getPathname())) {
                //echo "dans if isdir
               // ";
                //var_dump($directory->getPathname());
                $files = new DirectoryIterator($directory->getPathname());

                foreach ($files as $file) {
                    //var_dump($file);
                    if ($file->isFile()) {
                       // echo "dans is file
                        //";
                        //var_dump($file->getFilename());
                        $objects[$directory->getFilename()] = $file->getFilename();
                    } else {
                       // echo "dans else
                       // ";
                        //var_dump($file->getFilename());
                        if ($file->isDir()) {
                           // echo "is DIR
                           // ";
                            $filesNv1 = new DirectoryIterator($file->getPathName());
                            foreach ($filesNv1 as $fileNv1) {
                                if ($fileNv1->isFile()) {
                                    //echo "dans is file
                        //";
                                    //var_dump($fileNv1->getFilename());
                                    $objects[$file->getFilename()] = $fileNv1->getFilename();
                                } else {
                                   // echo "dans else
                                     
                                   // ";
                                }
                            }
                        }

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

    //echo($osAccess->getFileData($argv[1]));
    try {
        if (class_exists('ZipArchive')) {
            $zip = new ZipArchive();
            $zip->open('./data.zip', ZipArchive::CREATE | ZipArchive::OVERWRITE);
            foreach ($objects as $objectName => $fileName) {
                //var_dump($objectName);
                //var_dump($fileName);
                echo "Trying $objectName ...";
                try {
                    $fileData = $osAccess->getFileData($objectName);
                    echo "Ok";
                    echo "
                    ";
                    $filePath = $containerDirectoryPath . '/' . $objectName;
                    if (!is_dir($filePath)) {
                        mkdir($filePath);
                    }
                    $fullPath = $filePath . '/' . $fileName;

                    file_put_contents($fullPath, $fileData);
                    $zip->addFile($fullPath);
                } catch (Exception $e) {
                    echo "Error $objectName";
                    echo "
                    ";
                }

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

<?php

require __DIR__ . '/classes/Backup.php';
require __DIR__ . '/vendor/autoload.php';

use Classes\Backup;
use Aws\Common\Exception\MultipartUploadException;
use Aws\S3\MultipartUploader;
use Aws\S3\S3Client;

$backup = new Backup();

$commands = array(
    'setdirectory',
    'getdirectories',
    'searchdirectory',
    'removedirectory',
    'resetdirectories',
    'start',
    '--help'
);

array_shift($argv);

foreach ($argv as $arg) {
    if (in_array($arg, $commands)) {
        if ($arg === $commands[5]) {
            $files = $backup->start();

            if (count($files) > 0) {
                upload($files);
                echo 'Done';
            } else {
                echo 'No backups are created';
            }
        } else {
            $key = array_search($arg, $argv);
            $value = $argv[$key + 1];

            switch ($arg) {
                case $commands[0]:
                    $backup->setDirectory($value);
                    break;
                case $commands[1]:
                    echo join(",\n", $backup->getDirectories());
                    break;
                case $commands[2]:
                    echo $backup->searchDirectory($value);
                    break;
                case $commands[3]:
                    if ($backup->deleteDirectory($value)) {
                        echo 'Directory removed';
                    } else {
                        echo 'Could not find the directory';
                    }

                    break;
                case $commands[4]:
                    $backup->resetDirectories();
                    echo 'Directory list reset';
                    break;
                default:
                    show_available_commands($commands);
            }
        }
    } else {
        show_available_commands($commands);
    }

    echo "\n";
}

function show_available_commands($commands) {
    echo 'Available commands are:', "\n";
    echo join(",\n", $commands);
}

function upload($files) {
    $config = json_decode(file_get_contents(__DIR__ . '/config.json'), true);
    $bucket = $config['aws']['bucket'];

    foreach ($files as $file) {
        $s3 = new S3Client([
            'version' => 'latest',
            'region'  => $config['aws']['region'],
            'credentials' => array(
                'key' => $config['aws']['credentials']['key'],
                'secret'  => $config['aws']['credentials']['secret']
            )
        ]);

        // Prepare the upload parameters.
        $uploader = new MultipartUploader($s3, $file, [
            'bucket' => $bucket,
            'key'    => $file
        ]);

        // Perform the upload.
        try {
            $result = $uploader->upload();
            unlink($file);
            echo "Upload complete: {$result['ObjectURL']}" . PHP_EOL;
        } catch (MultipartUploadException $e) {
            echo $e->getMessage() . PHP_EOL;
        }
    }
}

<?php

require __DIR__ . '/classes/Backup.php';

use Classes\Backup;

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
            $backup->start();
            echo 'Done';
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

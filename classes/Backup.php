<?php

namespace Classes;

use ZipArchive;
use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;

class Backup
{
    private $file = __DIR__ . '/../server_file_backup_directory_list';

    /**
     * Set backup directory.
     *
     * @param $directory
     */
    public function setDirectory($directory)
    {
        if (file_exists($this->file) && filesize($this->file) > 0) {
            $size = filesize($this->file);

            $file = fopen($this->file, 'r');
            $contents = json_decode(fread($file, $size), true);
            $filtered = array_filter($contents, function ($content) use ($directory) {
                if ($content == $directory) {
                    return $content;
                }
            });

            if (count($filtered) > 0) {
                die('Directory is already in list');
            }
        } else {
            $contents = array();
        }

        $file = fopen($this->file, 'w');

        array_push($contents, $directory);
        fwrite($file, json_encode($contents));
    }

    /**
     * Get all the directories for backup.
     *
     * @return array
     */
    public function getDirectories()
    {
        if (file_exists($this->file) && filesize($this->file) > 0) {
            $size = filesize($this->file);
            $file = fopen($this->file, 'r');

            return json_decode(fread($file, $size), true);
        }

        return array();
    }

    /**
     * Search for the specified directory.
     *
     * @param $directory
     * @return string
     */
    public function searchDirectory($directory)
    {
        $directories = $this->getDirectories();

        if (count($directories) > 0) {
            $result = array_filter($directories, function ($item) use ($directory) {
                return $item == $directory;
            });

            if (count($result) > 0) {
                $result = array_values($result);

                return $result[0];
            }
        }

        return 'Empty directory';
    }

    /**
     * Delete the specified directory.
     *
     * @param $directory
     * @return bool
     */
    public function deleteDirectory($directory)
    {
        $directories = $this->getDirectories();

        if (count($directories) > 0) {
            $results = array_filter($directories, function ($item) use ($directory) {
                return $item != $directory;
            });

            if (count($results) > 0) {
                $file = fopen($this->file, 'w');

                fwrite($file, json_encode($results));

                return true;
            }
        }

        return false;
    }

    /**
     * Reset all the listed directories.
     *
     * @return bool
     */
    public function resetDirectories()
    {
        $file = fopen($this->file, 'w');
        fclose($file);

        return true;
    }

    /**
     * Make a zip archive.
     *
     * @return void
     */
    public function start()
    {
        $zip = new ZipArchive;
        $directories = $this->getDirectories();

        if (count($directories) > 0) {
            foreach ($directories as $directory) {
                $filename = explode('/', $directory);
                $filename = $filename[count($filename) - 1];

                $zip->open($filename . '.zip', ZipArchive::CREATE|ZipArchive::OVERWRITE);

                $files = new RecursiveIteratorIterator(
                    new RecursiveDirectoryIterator($directory),
                    RecursiveIteratorIterator::LEAVES_ONLY
                );

                foreach ($files as $name => $file) {
                    if (! $file->isDir())
                    {
                        $filePath = $file->getRealPath();
                        $relativePath = substr($filePath, strlen($directory) + 1);

                        $zip->addFile($filePath, $relativePath);
                    }
                }
            }

            $zip->close();
        }
    }
}

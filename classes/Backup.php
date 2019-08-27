<?php

namespace Classes;

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
}

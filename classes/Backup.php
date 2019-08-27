<?php

namespace Classes;

class Backup
{
    private $directory;

    public function setDirectory($directory)
    {
        $this->directory = $directory;
    }

    public function getDirectory()
    {
        return $this->directory;
    }
}

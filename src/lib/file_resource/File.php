<?php

declare (strict_types=1);

namespace lgdz\lib\file_resource;

class File
{
    private $tmp_path;
    private $fielname;
    private $filepath;
    private $filesize;
    private $mimetype;
    private $extension;

    /**
     * @return mixed
     */
    public function getFielname()
    {
        return $this->fielname;
    }

    /**
     * @param mixed $fielname
     */
    public function setFielname($fielname): void
    {
        $this->fielname = $fielname;
    }

    /**
     * @return mixed
     */
    public function getFilepath()
    {
        return $this->filepath;
    }

    /**
     * @param mixed $filepath
     */
    public function setFilepath($filepath): void
    {
        $this->filepath = $filepath;
    }

    /**
     * @return mixed
     */
    public function getFilesize()
    {
        return $this->filesize;
    }

    /**
     * @param mixed $filesize
     */
    public function setFilesize($filesize): void
    {
        $this->filesize = $filesize;
    }

    /**
     * @return mixed
     */
    public function getMimetype()
    {
        return $this->mimetype;
    }

    /**
     * @param mixed $mimetype
     */
    public function setMimetype($mimetype): void
    {
        $this->mimetype = $mimetype;
    }

    /**
     * @return mixed
     */
    public function getExtension()
    {
        return $this->extension;
    }

    /**
     * @param mixed $extension
     */
    public function setExtension($extension): void
    {
        $this->extension = $extension;
    }

    /**
     * @return mixed
     */
    public function getTmpPath()
    {
        return $this->tmp_path;
    }

    /**
     * @param mixed $tmp_path
     */
    public function setTmpPath($tmp_path): void
    {
        $this->tmp_path = $tmp_path;
    }


}
<?php

declare (strict_types=1);

namespace lgdz\lib\file_resource\drive;

use lgdz\lib\file_resource\{Drive, File, UploadResult};

class Local implements Drive
{
    public function upload(File $file): UploadResult
    {
        $result       = new UploadResult();
        $result->test = '本地存储';
        return $result;
        // TODO: Implement upload() method.
    }

    public function delete($input)
    {
        // TODO: Implement delete() method.
    }
}
<?php

declare (strict_types=1);

namespace lgdz\lib\file_resource\drive;

use lgdz\lib\file_resource\{Drive, File, UploadResult};

class Local implements Drive
{
    public function upload(File $file): UploadResult
    {
        $folder = sprintf('/file/%s/', date('Ymd'));
        $base_path = dirname(__DIR__);
        file_exists($base_path . $folder) || mkdir($base_path . $folder, 0777, true);
        $filepath = sprintf('%s%s.%s', $folder, date('YmdHis') . rand(1000, 9999), $file->getExtension());

        $result       = new UploadResult();
        $result->file = $file;
        $result->url  = $filepath;
        $result->path = $filepath;
        return $result;
    }

    public function delete($input)
    {
        // TODO: Implement delete() method.
    }
}
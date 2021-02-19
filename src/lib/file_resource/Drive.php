<?php

declare (strict_types=1);

namespace lgdz\lib\file_resource;

interface Drive
{
    public function upload(File $file): UploadResult;

    public function delete($input);
}
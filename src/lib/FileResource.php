<?php

declare (strict_types=1);

namespace lgdz\lib;

use lgdz\exception\UploadException;
use lgdz\lib\file_resource\Drive;
use lgdz\lib\file_resource\File;

/**
 * Class FileResource
 * @method Drive upload(File $file);
 * @method Drive delete($input);
 * @package lgdz\lib
 */
class FileResource extends InstanceClass implements InstanceInterface
{
    /**
     * @var Drive
     */
    private $drive;

    public function __construct(array $config)
    {
        $drive_method = $config['drive'] ?? 'Local';
        $class        = sprintf('\lgdz\lib\file_resource\drive\%s', $drive_method);
        try {
            $drive = new $class($config);
        } catch (\Throwable $e) {
            throw new UploadException($e->getMessage());
        }
        if ($drive instanceof Drive) {
            $this->drive = $drive;
        } else {
            throw new UploadException(sprintf('驱动[%s]未扩展', $this->drive));
        }
    }

    public function __call($name, $arguments)
    {
        return $this->drive->$name(...$arguments);
    }
}
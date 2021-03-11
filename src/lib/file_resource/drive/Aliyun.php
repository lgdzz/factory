<?php

declare (strict_types=1);

namespace lgdz\lib\file_resource\drive;

use lgdz\lib\file_resource\{Drive, File, UploadResult};
use OSS\Core\OssException;
use OSS\OssClient;
use Exception;

class Aliyun implements Drive
{
    /**
     * @var string
     */
    private $access_key_id;

    /**
     * @var string
     */
    private $access_key_secret;

    /**
     * @var string
     */
    private $endpoint;

    /**
     * @var string
     */
    private $bucket;

    /**
     * @var string
     */
    private $host;

    /**
     * Aliyun constructor.
     * @param array $config
     * @throws OssException
     */
    public function __construct(array $config)
    {
        isset($config['access_key']) && $this->access_key_id = $config['access_key'];
        isset($config['secret_key']) && $this->access_key_secret = $config['secret_key'];
        isset($config['bucket']) && $this->bucket = $config['bucket'];
        isset($config['endpoint']) && $this->endpoint = $config['endpoint'];
        isset($config['host']) && $this->host = $config['host'];
    }

    protected function OssClient()
    {
        return new OssClient($this->access_key_id, $this->access_key_secret, $this->endpoint);
    }

    public function upload(File $file): UploadResult
    {
        // 文件名称
        $filename = date('YmdHis') . rand(1000, 9999);
        // 本地图片路径
        $local_path = $file->getTmpPath();
        // 阿里云存储路径
        $remote_path = sprintf('%s/%s.%s', $file->getFilepath(), $filename, $file->getExtension());
        try {
            $ossClient = $this->OssClient();
            $ossClient->uploadFile($this->bucket, $remote_path, $local_path);
        } catch (OssException $e) {
            throw new Exception($e->getMessage());
        }

        $result       = new UploadResult();
        $result->file = $file;
        $result->url  = sprintf('%s%s', $this->host, $remote_path);
        $result->path = sprintf($remote_path);
        return $result;
    }

    public function delete($object): void
    {
        try {
            $ossClient = $this->OssClient();
            if (is_string($object)) {
                $ossClient->deleteObject($this->bucket, $object);
            } else {
                $ossClient->deleteObjects($this->bucket, $object);
            }
        } catch (OssException $e) {
            throw new Exception($e->getMessage());
        }
    }

}
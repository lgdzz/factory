<?php

declare (strict_types=1);

namespace lgdz\lib\file_resource\drive;

use Qiniu\Auth;
use Qiniu\Http\Error;
use Qiniu\Storage\BucketManager;
use Qiniu\Storage\UploadManager;
use lgdz\lib\file_resource\{Drive, File, UploadResult};

class Qiniu implements Drive
{
    /**
     * @var string
     */
    private $access_key;

    /**
     * @var string
     */
    private $secret_key;

    /**
     * @var string
     */
    private $bucket;

    /**
     * @var Auth
     */
    private $auth;

    private $token_expires = 3600;
    private $host = '';

    public function __construct(array $config)
    {
        isset($config['access_key']) && $this->access_key = $config['access_key'];
        isset($config['secret_key']) && $this->secret_key = $config['secret_key'];
        isset($config['bucket']) && $this->bucket = $config['bucket'];
        isset($config['host']) && $this->host = $config['host'];
        $this->auth = new Auth($this->access_key, $this->secret_key);
    }

    /**
     * @param File $file
     * @return UploadResult
     * @throws \Exception
     */
    public function upload(File $file): UploadResult
    {
        // 文件名称
        $filename = date('YmdHis') . rand(1000, 9999);
        // 本地图片路径
        $local_path = $file->getTmpPath();
        // 七牛存储路径
        $remote_path = sprintf('%s/%s', $file->getFilepath(), $filename);
        // 上传凭证
        $token     = $this->auth->uploadToken($this->bucket, null, $this->token_expires);
        $uploadMgr = new UploadManager();
        list($ret, $err) = $uploadMgr->putFile($token, $remote_path, $local_path);
        if ($err !== null) {
            if ($err instanceof Error) {
                throw new \Exception($err->message());
            } else {
                throw new \Exception('七牛云上传失败');
            }
        } else {
            $result       = new UploadResult();
            $result->file = $file;
            $result->url  = sprintf('%s%s', $this->host, $ret['key']);
            $result->path = sprintf($remote_path);
            return $result;
        }
    }

    public function delete($key): void
    {
        $bucket = new BucketManager($this->auth);
        $result = $bucket->delete($this->bucket, $key);
        $err    = $result[1];
        if ($err instanceof Error) {
            throw new \Exception($err->message());
        }
    }

}
<?php

declare (strict_types=1);

namespace lgdz;

use lgdz\lib\InstanceInterface;

/**
 * Class Factory
 * @method \lgdz\lib\HttpRequest HttpRequest();
 * @method \lgdz\lib\HttpResponse HttpResponse();
 * @method \lgdz\lib\DataAuth DataAuth(string $secret);
 * @method \lgdz\lib\Helper Helper();
 * @method \lgdz\lib\WorkermanServerToClient WorkermanServerToClient(string $appid, string $secret, string $gateway);
 * @method \lgdz\lib\WorkermanClientToServer WorkermanClientToServer(string $secret);
 * @method \lgdz\lib\JwtAuth JwtAuth(array $config);
 * @method \lgdz\lib\Components Components();
 * @method \lgdz\lib\Tree Tree();
 * @method \lgdz\lib\Time Time();
 * @method \lgdz\lib\Captcha Captcha();
 * @method \lgdz\lib\Pinyin Pinyin();
 * @method \lgdz\lib\Queue Queue(array $config);
 * @method \lgdz\lib\QueueService QueueService(array $config);
 * @method \lgdz\lib\CronExpression CronExpression();
 * @method \lgdz\lib\Password Password();
 * @method \lgdz\lib\FileResource FileResource(array $config);
 * @method \lgdz\lib\NumberFormat NumberFormat();
 * @method \lgdz\lib\NumberCompute NumberCompute();
 * @package lgdz
 */
class Factory
{
    // 单例
    private $entity = [];

    public function __call($name, $arguments)
    {
        $key = empty($arguments) ? $name : sprintf('%s_%s', $name, md5(serialize($arguments)));
        if (isset($this->entity[$key])) {
            return $this->entity[$key];
        } else {
            $class  = sprintf('\lgdz\lib\%s', $name);
            $entity = new $class(...$arguments);
            if (($entity instanceof InstanceInterface) && $entity->isUnique()) {
                $entity->setFactory($this);
                $this->entity[$key] = $entity;
            }
            return $entity;
        }
    }
}
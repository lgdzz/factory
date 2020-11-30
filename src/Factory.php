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
 * @package lgdz
 */
class Factory
{
    // 单一实体缓存
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
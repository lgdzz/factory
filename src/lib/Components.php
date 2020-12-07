<?php

namespace lgdz\lib;

/**
 * Class Components 组件接口方法
 * @package lgdz\lib
 */
class Components extends InstanceClass implements InstanceInterface
{
    /**
     * @var array 注册方法
     */
    protected $registers = ['*'];

    protected $entity = null;

    // 初始化实体类
    public function entity($entity)
    {
        $this->entity = $entity;
    }

    /**
     * 注册方法
     * @param array $ops
     */
    public function registers(array $ops)
    {
        $this->registers = $ops;
    }

    /**
     * 接口调用
     * @param array $methods
     * @return array
     */
    public function api(array $methods)
    {
        $components = [];
        if (empty($methods)) {
            return ['components' => []];
        }
        foreach ($methods as $op => $args) {
            $args = is_null($args) ? [] : (is_array($args) ? $args : [$args]);
            if (!$op) {
                continue;
            } else {
                try {
                    print_r($this->registers);
                    if (empty($this->registers) || ($this->registers[0] !== '*' && !in_array($op, $this->registers)))
                        throw new \Exception('未在Registers中注册');
                    elseif (is_null($this->entity))
                        throw new \Exception('未初始化Entity');
                    else
                        $components[$op] = $this->entity->$op(...$args);
                } catch (\Throwable $e) {
                    $components[$op] = $e->getMessage();
                }
            }
        }
        return ['components' => $components];
    }
}
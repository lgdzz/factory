<?php

namespace lgdz\object;

class Query
{
    /**
     * @var bool
     */
    public $is_page;

    /**
     * @var int
     */
    public $page;

    /**
     * @var int
     */
    public $size;

    /**
     * @var array
     */
    public $where = [];

    public function __construct(array $input = [])
    {
        if (isset($input['page'])) {
            $this->page = (int)$input['page'];
            $this->is_page = true;
            unset($input['page']);
        } else {
            $this->is_page = false;
        }

        if (isset($input['size'])) {
            $this->size = (int)$input['size'];
            unset($input['size']);
        }

        foreach ($input as $name => $value) {
            $this->where[$name] = $value;
        }
    }

    public function __set($name, $value)
    {
        $this->where[$name] = $value;
    }

    public function __get($name)
    {
        return $this->where[$name] ?? null;
    }

    /**
     * 合并数组到where
     * @param array $values
     */
    public function append(array $values = [])
    {
        $this->where = array_merge($this->where, $values);
    }
}
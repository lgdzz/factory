<?php

namespace lgdz\lib;

class Tree extends InstanceClass implements InstanceInterface
{
    public function tree(array $list, $id, string $pid = 'pid')
    {
        $tmp = [];
        foreach ($list as $row) {
            if ($row[$pid] !== $id) continue;
            $children = $this->tree($list, $row['id']);
            if ($children) {
                $row['has_child'] = true;
                $row['children']  = $children;
            } else {
                $row['has_child'] = false;
            }
            $tmp[] = $row;
        }
        return $tmp;
    }
}
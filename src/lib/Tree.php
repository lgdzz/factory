<?php

namespace lgdz\lib;

class Tree
{
    public function build(array $list, $id, string $pid = 'pid')
    {
        $tmp = [];
        foreach ($list as $row) {
            if ($row[$pid] !== $id) continue;
            $children = $this->build($list, $row['id'], $pid);
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
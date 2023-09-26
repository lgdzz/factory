<?php

namespace lgdz\lib;

class Tree
{
    public function build(array $list, $id, string $pid = 'pid', &$already = [])
    {
        $tmp = [];
        foreach ($list as $row) {
            if ($row[$pid] !== $id) continue;

            $already[] = $id;

            $children = $this->build($list, $row['id'], $pid, $already);
            if ($children) {
                $row['has_child'] = true;
                $row['children'] = $children;
            } else {
                $row['has_child'] = false;
            }
            $tmp[] = $row;
        }
        return $tmp;
    }

    public function builds(array $list, $ids, string $pid = 'pid')
    {
        $already = [];
        $result = [];
        foreach ($ids as $id) {
            if (in_array($id, $already)) {
                continue;
            }
            $result[] = $this->build($list, $id, $pid, $already);
        }
        return array_merge(...$result);
    }
}
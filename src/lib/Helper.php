<?php

declare (strict_types=1);

namespace lgdz\lib;

class Helper extends InstanceClass implements InstanceInterface
{
    // 随机字符串
    public function randomString(int $length = 5): string
    {
        $array = array_merge(range('a', 'z'), range('A', 'Z'), range(0, 9));
        shuffle($array);
        $value = '';
        for ($i = 0; $i < $length; $i++) {
            $value .= $array[array_rand($array, 1)];
        }
        return $value;
    }

    // 随机数字
    public function randomNumber(int $length = 5): string
    {
        $array = range(0, 9);
        shuffle($array);
        $value = '';
        for ($i = 0; $i < $length; $i++) {
            $value .= $array[array_rand($array, 1)];
        }
        return $value;
    }

    // 唯一单号（长度最小15，推荐最大19）
    public function orderNo(int $len = 15): string
    {
        return date('ymdHis') . $this->randomNumber($len - 12);
    }

    public function tree(array $list, $id, string $parent = 'pid')
    {
        $tmp = [];
        foreach ($list as $row) {
            if ($row[$parent] !== $id) continue;
            $child = $this->tree($list, $row['id']);
            if ($child) {
                $row['child']     = $child;
                $row['has_child'] = true;
            } else {
                $row['has_child'] = false;
            }
            $tmp[] = $row;
        }
        return $tmp;
    }

    // select表单数据
    public function select(string $label, string $value): array
    {
        return [
            'label' => $label,
            'value' => $value
        ];
    }
}
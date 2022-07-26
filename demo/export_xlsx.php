<?php

require_once "./vendor/autoload.php";

use lgdz\Factory;

// header合并单元格
$group = [
    [
        ['label' => '区域', 'range' => 'A1:A3'],
        ['label' => '2021', 'range' => 'B1:D1'],
        ['label' => '2022', 'range' => 'E1:G1'],
        ['label' => '同比', 'range' => 'H1:J2'],
        ['label' => '比较年度', 'range' => 'B2:D2'],
        ['label' => '被比较年度', 'range' => 'E2:G2']
    ],
    [
        ['label' => '比较年度', 'range' => 'B2:D2'],
        ['label' => '被比较年度', 'range' => 'E2:G2']
    ]
];

$column = [
    new \lgdz\object\excel\HeadItem(['key' => 'username', 'label' => '账号', 'width' => 10, 'bold' => true, 'align' => 'center']),
    new \lgdz\object\excel\HeadItem(['key' => 'password', 'label' => '密码', 'width' => 10, 'bold' => true, 'align' => 'left', 'callback' => function (\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet, $cellIndex, $value, $row) {
        // 单独设置单元格背景色
        $sheet->getStyle($cellIndex)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('cccccc');
        // 单独设置单元格文本颜色
        $sheet->getStyle($cellIndex)->getFont()->getColor()->setRGB('6b62e1');
        // 添加批注
        $sheet->getComment($cellIndex)->getText()->createText('这个密码不安全，请重新修改一下。');
    }]),
];
// 数据
$data = [
    ['username' => 'test1', 'password' => '111111'],
    ['username' => 'test2', 'password' => '111111']
];
$result = Factory::container()->export_excel->build($column, $data, ['debug' => true, 'group' => $group, 'border' => true]);
var_dump($result);
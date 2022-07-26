<?php

namespace lgdz\object\excel;

use Closure;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class HeadItem
{
    // 字段key
    private $key = '';
    // 字段中文名称
    private $label = '';
    // 列宽
    private $width = 10;
    // 标题是否加粗
    private $bold = true;
    // 列对齐方式
    private $align = 'left';
    // 单元格是否自动换行
    private $autoWrap = true;
    // 自定义处理
    private $callback = null;

    public function __construct(array $option)
    {
        isset($option['key']) && $this->key = $option['key'];
        isset($option['label']) && $this->label = $option['label'];
        isset($option['width']) && $this->width = (int)$option['width'];
        isset($option['bold']) && $this->bold = (bool)$option['bold'];
        isset($option['align']) && $this->align = $option['align'];
        isset($option['autoWrap']) && $this->autoWrap = (bool)$option['autoWrap'];
        isset($option['callback']) && $this->callback = $option['callback'];
    }

    /**
     * @return mixed|string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * @return mixed|string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @return int|mixed
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * @return bool
     */
    public function isBold(): bool
    {
        return $this->bold;
    }

    /**
     * @return string
     */
    public function getAlign(): string
    {
        return $this->align;
    }

    /**
     * @return bool
     */
    public function isAutoWrap(): bool
    {
        return $this->autoWrap;
    }

    /**
     * @option Worksheet $sheet
     * @option string $cellCoordinate
     * @option string $value
     * @option array[] $row
     * @return Closure|null
     */
    public function callback()
    {
        return $this->callback;
    }
}
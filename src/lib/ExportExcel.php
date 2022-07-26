<?php

declare(strict_types=1);

namespace lgdz\lib;

use lgdz\Factory;
use lgdz\object\excel\HeadItem;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Writer\Exception;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class ExportExcel
{
    protected $letter;

    public function __construct()
    {
        $this->letter = range('A', 'Z');
    }

    /**
     * 生成xlsx文件流
     * @param HeadItem[] $head [[string key,string label,int width,bool bold,string align,Closure callback],...]
     * @param array $data [[],...]
     * @param array $option [string sheetTitle,bool debug,array group,bool border,bool bold]
     * @return string
     * @throws Exception
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
    public function build(array $head, array $data, array $option = []): string
    {
        $isDebug = isset($option['debug']) && $option['debug'];
        $bold = (isset($option['bold']) && !$option['bold']) ? false : true;
        $tmpName = Factory::container()->helper->randomName();
        $letter = $this->letter;
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        // 设置sheet的标题
        isset($option['sheetTitle']) && $sheet->setTitle($option['sheetTitle']);
        // 第一行写入字段标题
        if (isset($option['group']) && !empty($option['group'])) {
            $rowNum = count($option['group']) + 1;
            foreach ($option['group'] as $group) {
                foreach ($group as $item) {
                    $coordinate = explode(':', $item['range'])[0];
                    $sheet->setCellValue($coordinate, $item['label'])->mergeCells($item['range']);
                    $cellStyle = $sheet->getStyle($coordinate);
                    $cellStyle->getFont()
                        ->setBold($bold);
                    $cellStyle->getAlignment()
                        ->setVertical(Alignment::VERTICAL_CENTER)
                        ->setHorizontal('center');
                }
            }
        } else {
            $rowNum = 1;
        }
        foreach ($head as $index => $item) {
            $columnNum = $letter[$index];
            // 设置标题
            $sheet->setCellValue($columnNum . $rowNum, $item->getLabel());
            // 设置列宽
            $sheet->getColumnDimension($columnNum)->setWidth($item->getWidth());
            $sheet->getRowDimension($rowNum)->setRowHeight(16);
            $sheet->getStyle($columnNum)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);
            // 单元格样式
            $cellStyle = $sheet->getStyle($columnNum . $rowNum);
            $cellStyle->getFont()
                ->setBold($item->isBold());
            $cellStyle->getAlignment()
                ->setVertical(Alignment::VERTICAL_CENTER)
                ->setHorizontal($item->getAlign());
        }
        // 开始写入数据
        $rowNum++;
        foreach ($data as $row) {
            foreach ($head as $index => $item) {
                $value = $row[$item->getKey()];
                $sheet->setCellValueByColumnAndRow($index + 1, $rowNum, $value);
                $cellCoordinate = $letter[$index] . $rowNum;
                $sheet->getStyle($cellCoordinate)->getAlignment()
                    ->setVertical(Alignment::VERTICAL_CENTER)
                    ->setHorizontal($item->getAlign())
                    ->setWrapText($item->isAutoWrap());
                if (!is_null($item->callback())) {
                    // 自定义单元格处理，可以在这里处理特殊样式
                    $item->callback()($sheet, $cellCoordinate, $value, $row);
                }
            }
            $rowNum++;
        }
        // 是否有边框
        if (isset($option['border']) && $option['border']) {
            $sheet->getStyle(sprintf('A1:%s%s', $this->letter[count($head) - 1], $rowNum - 1))->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN
                    ]
                ]
            ]);
        }
        if ($isDebug) {
            $tmpPath = './debug-' . date('YmdHis') . '.xlsx';
        } else {
            $tmpPath = sys_get_temp_dir() . '/' . $tmpName;
        }
        $writer = new Xlsx($spreadsheet);
        $writer->save($tmpPath);
        $spreadsheet->disconnectWorksheets();
        unset($spreadsheet);
        return $isDebug ? 'debug' : file_get_contents($tmpPath);
    }

    /**
     * 设置单元格背景颜色
     * @param string $color
     * @param Worksheet $sheet
     * @param $cellIndex
     */
    public function setCellBgColor(string $color, Worksheet $sheet, $cellIndex)
    {
        $sheet->getStyle($cellIndex)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB($color);
    }

    /**
     * 设置单元格文本颜色
     * @param string $color
     * @param Worksheet $sheet
     * @param $cellIndex
     */
    public function setCellTextColor(string $color, Worksheet $sheet, $cellIndex)
    {
        $sheet->getStyle($cellIndex)->getFont()->getColor()->setRGB($color);
    }

    /**
     * 设置单元格批注
     * @param string $content
     * @param Worksheet $sheet
     * @param $cellIndex
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
    public function setCellComment(string $content, Worksheet $sheet, $cellIndex)
    {
        $sheet->getComment($cellIndex)->getText()->createText($content);
    }
}
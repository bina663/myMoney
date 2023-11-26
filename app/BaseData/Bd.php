<?php

namespace App\BaseData;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Style\Border;

class Bd
{
    private static $name;
    private static $value;

    public function __construct(String $name = "", $value = "")
    {
        self::$name = $name;
        self::$value = $value;
    }

    public static function save($balance = "")
    {
        if($balance){
            $total = 0.00;
            if(isset($_SESSION["balance"])){
                $total = floatval($_SESSION["balance"]) + floatval($balance);
                $total = self::formatValue($total);
            }else{
                $total = $balance;
            }
            $_SESSION["balance"] = $total;
            return;
        }
        $value =  self::formatValue(self::$value);
        $spent = array(
            "name" => self::$name,
            "value" => $value
        );
        
        if (!isset($_SESSION["spents"]) || $_SESSION["spents"] == null) {
            $_SESSION["spents"] = [];
        }

        array_push($_SESSION["spents"], $spent);

        self::totalSpent();
    }

    public static function deleteAll()
    {
        session_destroy();
    }

    public static function delete($id)
    {
        unset($_SESSION["spents"][$id]);
        $_SESSION["spents"] = array_values($_SESSION["spents"]);
        self::totalSpent();
        return;
    }

    private static function formatValue($value)
    {
        $valueFloat = floatval($value);
        return number_format($valueFloat, 2, '.', '');
    }

    private static function totalSpent()
    {
        $total = 0;
        foreach ($_SESSION["spents"] as $spent) {
            $total += self::formatValue($spent["value"]);
        }
        $_SESSION["total"] = self::formatValue($total);
        return;
    }
    public static function edit($value){
        $_SESSION["balance"] = self::formatValue($value);
        return;
    }
    public static function export()
    {
        try {
            $spreadsheet = new Spreadsheet();
            $activeWorksheet = $spreadsheet->getActiveSheet();

            //Title excel
            $activeWorksheet->setCellValue('A1', "Name");
            $activeWorksheet->getStyle("A1")->getFont()->setBold(true);
            $activeWorksheet->getStyle("A1")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('4C6085');
            $font = $activeWorksheet->getStyle('A1')->getFont();
            $font->getColor()->setARGB(Color::COLOR_WHITE);

            $activeWorksheet->setCellValue('B1', "Value");
            $activeWorksheet->getStyle("B1")->getFont()->setBold(true);
            $activeWorksheet->getStyle("B1")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('4C6085');
            $font = $activeWorksheet->getStyle('B1')->getFont();
            $font->getColor()->setARGB(Color::COLOR_WHITE);

            //Spent and balance
            $total = $_SESSION["total"];
            $balance = $_SESSION["balance"];
            $available = $balance - $total;
            $activeWorksheet->setCellValue('D1', "Spent Total");
            $activeWorksheet->setCellValue('D2', $total);
            $activeWorksheet->getStyle("D1")->getFont()->setBold(true);
            $activeWorksheet->getStyle("D1")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('AF232F');
            $activeWorksheet->getStyle("D2")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('D73B48');
            $font = $activeWorksheet->getStyle('D1')->getFont();
            $font->getColor()->setARGB(Color::COLOR_WHITE);

            $activeWorksheet->setCellValue('E1', "Balance");
            $activeWorksheet->setCellValue('E2', $balance);
            $activeWorksheet->getStyle("E1")->getFont()->setBold(true);
            $activeWorksheet->getStyle("E1")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('209C83');
            $activeWorksheet->getStyle("E2")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('27AD93');
            $font = $activeWorksheet->getStyle('E1')->getFont();
            $font->getColor()->setARGB(Color::COLOR_WHITE);

            $activeWorksheet->setCellValue('F1', "Available");
            $activeWorksheet->setCellValue('F2', $available);
            $activeWorksheet->getStyle("F1")->getFont()->setBold(true);
            $activeWorksheet->getStyle("F1")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('E3AA0E');
            $activeWorksheet->getStyle("F2")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('D9B551');
            $font = $activeWorksheet->getStyle('E1')->getFont();
            $font->getColor()->setARGB(Color::COLOR_WHITE);

            //Insert Spents in file
            $spents = $_SESSION['spents'];
            $startRow = 2;
            foreach ($spents as $index => $spent) {
                $name = $spent['name'];
                $value = self::formatValue($spent['value']);

                $activeWorksheet->setCellValue('A' . $startRow, $name);
                $activeWorksheet->setCellValue('B' . $startRow, $value);

                $activeWorksheet->getStyle('A' . $startRow)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('B2BFD7');
                $border = $activeWorksheet->getStyle('A' . $startRow)->getBorders();
                $border->getAllBorders()->setBorderStyle(Border::BORDER_THIN)->getColor()->setARGB(Color::COLOR_WHITE);

                $activeWorksheet->getStyle('B' . $startRow)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('B2BFD7');
                $border = $activeWorksheet->getStyle('B' . $startRow)->getBorders();
                $border->getAllBorders()->setBorderStyle(Border::BORDER_THIN)->getColor()->setARGB(Color::COLOR_WHITE);

                $startRow++;
            }

            //Create file
            $writer = new Xlsx($spreadsheet);
            $writer->save('myMoney.xlsx');

            //Download File
            $file = 'myMoney.xlsx';
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="'.basename($file).'"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($file));
            readfile($file);

            unlink($file);
            return;

        } catch (\Exception $e) {
            var_dump($e->getMessage());
            die;
        }
    }
    public static function import($file)
    {
        $names = [];
        $values = [];
        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader("Xlsx");
        $spreadsheet = $reader->load($file);
        $sheet = $spreadsheet->getActiveSheet();
        foreach ($sheet->getRowIterator(2) as $row) {
            $cellIterator = $row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(false);
            foreach ($cellIterator as $cell) {
                if (!is_null($cell)) {
                    $value = $cell->getCalculatedValue();
                    if (is_float($value)) {
                        $values[] = $value;
                    } else if (!empty($value)) {
                        $names[] = $value;
                    }
                }
            }
        }
        
        $spents = [];
        foreach ($names as $index => $name) {
            if (isset($values[$index])) {
                $spent = [
                    $name => $values[$index]
                ];
                $spents[] = $spent;
            }
        }
        
        echo "<pre>";
        print_r($spents);
        die;
    }
}

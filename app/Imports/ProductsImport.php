<?php

namespace App\Imports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithProgressBar;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class ProductsImport implements ToModel, WithProgressBar, WithHeadingRow
{
    use Importable;

    private const NONE_VALUE = 'None';

    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        if (is_null($row['name'])) {
            return;
        }


        list($weightUnit, $weightValue) = $this->identifyWeightUnit($row);

        return new Product([
            'name' => $row['name'],
            'sku' => $row['sku'],
            'asin' => $row['asin'],
            'upc' => $row['upc'],
            'size' => $row['size'],
            'weight_unit' => $weightUnit,
            'total_inventory_remaining' => (int) $row['total_inventory_remaining'],
            'manufactured_date' => $this->formatDate($row['manufactured_date']),
            'made_from' => $row['made_from'],
            'retail_price' => $row['retail_price'],
            'reseller_price' => $row['reseller_price'],
            'store_id' => 1,
            'created_by' => 3,
            'updated_by' => 3,
            'weight_value' => $weightValue
        ]);
    }

    private function identifyWeightUnit(array $row)
    {
        if ($row['grams'] !== self::NONE_VALUE) {
            return ['grams', $row['grams']];
        }

        if ($row['ml'] !== self::NONE_VALUE) {
            return ['ml', $row['ml']];
        }

        if ($row['oz'] !== self::NONE_VALUE) {
            return ['oz', $row['oz']];
        }

        return ['fl/oz', $row['floz']];
    }

    private function formatDate(string $date): string
    {
        return Carbon::instance(Date::excelToDateTimeObject($date))->format('Y-m-d');
    }
}

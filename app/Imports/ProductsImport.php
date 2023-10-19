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

    // public function mapping(): array
    // {
    //     return [
    //         'name' => 'A2',
    //         'sku' => 'C2',
    //         'asin' => 'D2',
    //         'upc' => 'E2',
    //         'size' => 'F2',
    //         'weight_unit' => 'G2',
    //         'total_inventory_remaining' => 'K2',
    //         'manufactured_date' => 'L2',
    //         'made_from' => 'M2',
    //         'retail_price' => 'N2',
    //         'reseller_price' => 'O2',
    //     ];
    // }

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

        return new Product([
            'name' => $row['name'],
            'sku' => $row['sku'],
            'asin' => $row['asin'],
            'upc' => $row['upc'],
            'size' => $row['size'],
            'weight_unit' => $row['weight_unit'],
            'total_inventory_remaining' => (int) $row['total_inventory_remaining'],
            'manufactured_date' => $this->formatDate($row['manufactured_date']),
            'made_from' => $row['made_from'],
            'retail_price' => $row['retail_price'],
            'reseller_price' => $row['reseller_price'],
            'store_id' => 1,
            'created_by' => 3,
            'updated_by' => 3,
            'weight_value' => 0
        ]);
    }

    private function formatDate(string $date): string
    {
        return Carbon::instance(Date::excelToDateTimeObject($date))->format('Y-m-d');
    }
}

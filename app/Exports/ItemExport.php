<?php

namespace App\Exports;

use App\Itemajax;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ItemExport implements FromCollection, WithHeadings, WithMapping
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Itemajax::all();
    }
    public function map($ajaxitems): array
    {
        return [
            'name' => $ajaxitems->item_name,
            'descriptions' => strip_tags($ajaxitems->descriptions),
            'manufacture_date' => $ajaxitems->manufacture_date,
            'images' => $ajaxitems->images,
         ];
    }
    public function headings(): array
    {
        return [
            'Item Name',
            'Descriptions',
            'Manufacture Date',
            'Images Name'
        ];
    }
}

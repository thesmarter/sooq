<?php


namespace Modules\Core\Exports;


use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Modules\Core\Entities\Category;

class CategoryReportExport implements FromCollection, WithHeadings
{

    use Exportable;

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Category::withCount(['category_touch'])
        ->orderBy('category_touch_count', 'desc')
        ->latest()
        ->get();
    }

    /**
     * Write code on Method
     *
     * @return response()
     */
    public function headings(): array
    {
        return ["Name", "Email", "Phone", "Registered Date", "Updated Date", "Address", "About Me", "Number of Transaction"];
    }

}

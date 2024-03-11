<?php


namespace Modules\Core\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Modules\Core\Entities\TransactionHeader;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;

class TransactionsExport implements FromCollection, WithHeadings, WithMapping, WithCustomCsvSettings
{
    use Exportable;

    public function getCsvSettings(): array
    {
        return [
            'delimiter' => ',',
            'use_bom' => false,
            'output_encoding' => 'ISO-8859-1',
        ];
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return TransactionHeader::with(['user', 'owner', 'editor', 'shop'])->latest()->get();
    }

    public function map($transaction) : array {
        return [
            $transaction->user?$transaction->user->name:'',
            $transaction->shop?$transaction->shop->name:'',
            $transaction->sub_total_amount,
            $transaction->discount_amount,
            $transaction->coupon_discount_amount,
            $transaction->tax_amount,
            $transaction->tax_percentage,
            $transaction->shipping_amount,
            $transaction->shipping_tax_percentage,
            $transaction->balance_amount,

            $transaction->owner?$transaction->owner->name: '',
            $transaction->added_date->format('M, d Y H:i:s A'),
            $transaction->editor? $transaction->editor->name: '',
            $transaction->updated_date!=null?$transaction->updated_date->format('M, d Y H:i:s A'):'',
        ];
    }

    /**
     * Write code on Method
     *
     * @return response()
     */
    public function headings(): array
    {
        return ["User", "Shop", "Sub Total Amount", "Discount Amount", "Coupon Discount Amount", "Tax Amount", "Tax Percentage", "Shipping Amount", "Shipping Tax Percentage", "Balance Amount", "Added User", "Added Date", "Updated User", "Updated Date"];
    }

}

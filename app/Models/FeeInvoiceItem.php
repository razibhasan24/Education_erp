<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FeeInvoiceItem extends Model
{
    protected $fillable = ['fee_invoice_id', 'fee_category_id', 'amount', 'note'];

    public function invoice()
    {
        return $this->belongsTo(FeeInvoice::class);
    }

    public function category()
    {
        return $this->belongsTo(FeeCategory::class, 'fee_category_id');
    }
}

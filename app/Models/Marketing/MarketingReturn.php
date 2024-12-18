<?php

namespace App\Models\Marketing;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MarketingReturn extends Model
{
    use HasFactory;

    protected $table = 'marketing_returns';

    protected $primaryKey = 'marketing_return_id';

    protected $fillable = [
        'marketing_id',
        'invoice_number',
        'payment_return_status',
        'return_status',
        'total_return',
        'return_at',
    ];

    public function marketing()
    {
        return $this->belongsTo(Marketing::class, 'marketing_id', 'marketing_id');
    }

    public function marketting_return_payments()
    {
        return $this->hasMany(MarketingReturnPayment::class, 'marketing_return_id', 'marketing_return_id');
    }
}

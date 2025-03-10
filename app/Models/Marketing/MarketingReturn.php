<?php

namespace App\Models\Marketing;

use App\Constants;
use App\Models\UserManagement\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MarketingReturn extends Model
{
    use HasFactory;

    protected $table = 'marketing_returns';

    protected $primaryKey = 'marketing_return_id';

    protected $fillable = [
        'marketing_id',
        'is_approved',
        'approver_id',
        'approval_notes',
        'approved_at',
        'invoice_number',
        'payment_return_status',
        'return_status',
        'total_return',
        'return_at',
    ];

    protected $appends = ['is_returned'];

    public function getIsReturnedAttribute()
    {
        return $this->marketing_return_payments
            ->where('verify_status', 2)
            ->sum('payment_nominal');
    }

    public function calculatePaymentStatus()
    {
        $totalReturn   = $this->total_return;
        $totalPayments = $this->is_returned;

        if ($totalReturn < $totalPayments) {
            return array_search('Dibayar Lebih', Constants::MARKETING_PAYMENT_STATUS);
        } elseif ($totalReturn == $totalPayments) {
            return array_search('Dibayar Penuh', Constants::MARKETING_PAYMENT_STATUS);
        } elseif ($totalReturn > $totalPayments && $totalPayments > 0) {
            return array_search('Dibayar Sebagian', Constants::MARKETING_PAYMENT_STATUS);
        } else {
            return array_search('Tempo', Constants::MARKETING_PAYMENT_STATUS);
        }
    }

    public function marketing()
    {
        return $this->belongsTo(Marketing::class, 'marketing_id', 'marketing_id');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approver_id', 'user_id');
    }

    public function marketing_return_payments()
    {
        return $this->hasMany(MarketingReturnPayment::class, 'marketing_return_id', 'marketing_return_id');
    }
}

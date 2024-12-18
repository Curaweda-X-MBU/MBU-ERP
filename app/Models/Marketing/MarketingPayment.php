<?php

namespace App\Models\Marketing;

use App\Models\DataMaster\Bank;
use App\Models\UserManagement\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MarketingPayment extends Model
{
    use HasFactory;

    protected $table = 'marketing_payments';

    protected $priaryKey = 'marketing_payment_id';

    protected $fillable = [
        'payment_method',
        'approver_id',
        'approved_at',
        'approval_notes',
        'bank_id',
        'payment_nominal',
        'payment_reference',
        'transaction_number',
        'payment_at',
        'document_path',
        'notes',
        'marketing_id',
        'verify_status',
    ];

    public function approver()
    {
        return $this->belongsTo(User::class, 'approver_id', 'user_id');
    }

    public function bank()
    {
        return $this->belongsTo(Bank::class, 'bank_id', 'bank_id');
    }

    public function marketing()
    {
        return $this->belongsTo(Marketing::class, 'marketing_id', 'marketing_id');
    }
}

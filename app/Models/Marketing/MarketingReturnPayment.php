<?php

namespace App\Models\Marketing;

use App\Models\DataMaster\Bank;
use App\Models\UserManagement\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MarketingReturnPayment extends Model
{
    use HasFactory;

    protected $table = 'marketing_return_payments';

    protected $primaryKey = 'marketing_return_payment_id';

    protected $fillable = [
        'marketing_return_id',
        'payment_method',
        'is_approved',
        'approver_id',
        'approval_notes',
        'approved_at',
        'bank_id',
        'recipient_bank_id',
        'payment_reference',
        'transaction_number',
        'payment_nominal',
        'bank_admin_fees',
        'payment_at',
        'document_path',
        'notes',
        'verify_status',
    ];

    public function marketing_return()
    {
        return $this->belongsTo(MarketingReturn::class, 'marketing_id', 'marketing_id');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approver_id', 'user_id');
    }

    public function bank()
    {
        return $this->belongsTo(Bank::class, 'bank_id', 'bank_id');
    }

    public function recipient_bank()
    {
        return $this->belongsTo(Bank::class, 'recipient_bank_id', 'bank_id');
    }
}

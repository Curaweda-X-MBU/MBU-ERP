<?php

namespace App\Models\Marketing;

use App\Models\DataMaster\Bank;
use App\Models\UserManagement\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

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

    protected static function boot()
    {
        parent::boot();

        static::updated(function(MarketingReturnPayment $payment) {
            if ($payment->isDirty(['is_approved', 'payment_nominal'])) {
                DB::beginTransaction();
                try {
                    $marketing_return = MarketingReturn::find($payment->marketing_return_id);
                    $marketing_return->update([
                        'payment_return_status' => $marketing_return->calculatePaymentStatus(),
                    ]);
                    $marketing_return->save();

                    DB::commit();
                } catch (\Exception $e) {
                    DB::rollback();
                    throw $e;
                }
            }
        });

        static::deleted(function(MarketingReturnPayment $payment) {
            DB::beginTransaction();
            try {
                $marketing_return = MarketingReturn::find($payment->marketing_return_id);
                $marketing_return->update([
                    'payment_return_status' => $marketing_return->calculatePaymentStatus(),
                ]);
                $marketing_return->save();

                DB::commit();
            } catch (\Exception $e) {
                DB::rollback();
                throw $e;
            }
        });
    }

    public function marketing_return()
    {
        return $this->belongsTo(MarketingReturn::class, 'marketing_return_id', 'marketing_return_id');
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

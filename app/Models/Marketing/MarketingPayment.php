<?php

namespace App\Models\Marketing;

use App\Models\DataMaster\Bank;
use App\Models\UserManagement\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class MarketingPayment extends Model
{
    use HasFactory;

    protected $table = 'marketing_payments';

    protected $primaryKey = 'marketing_payment_id';

    protected $fillable = [
        'payment_method',
        'is_approved',
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

    protected static function boot()
    {
        parent::boot();

        static::updated(function(MarketingPayment $marketingPayment) {
            if ($marketingPayment->isDirty('is_approved') && $marketingPayment->is_approved == 1) {
                self::allocatePayments($marketingPayment);
            } elseif ($marketingPayment->isDirty('payment_nominal') && $marketingPayment->is_approved == 1) {
                self::allocatePayments($marketingPayment);
            }
        });

        static::deleted(function(MarketingPayment $marketingPayment) {
            self::allocatePayments($marketingPayment);
        });
    }

    private static function allocatePayments(MarketingPayment $marketingPayment)
    {
        DB::transaction(function() use ($marketingPayment) {
            $marketingPayment->refresh();
            $marketing = $marketingPayment->marketing;

            if (! $marketing) {
                throw new \Exception('Marketing record not found');
            }

            $totalPaymentNominal = $marketing->marketing_payments()
                ->where('is_approved', 1)
                ->sum('payment_nominal');

            // Reset is_paid of every products to 0
            $marketing->marketing_products()->update(['is_paid' => 0]);

            // Handle NULL is_paid with COALESCE in SQL
            $tax              = $marketing->tax;
            $discount         = $marketing->discount;
            $additionalPrices = $marketing->marketing_addit_prices->sum('price');
            $productCount     = $marketing->marketing_products()->count();

            if ($productCount === 0) {
                throw new \Exception('Marketing products not found');
            }

            $marketingProducts = $marketing->marketing_products()
                ->select(['marketing_product_id', 'total_price', 'is_paid', 'marketing_id'])
                ->whereRaw("(total_price + (total_price * ($tax / 100)) - ($discount / $productCount) + ($additionalPrices / $productCount)) > COALESCE(is_paid, 0)")
                ->orderByRaw("((total_price + (total_price * ($tax / 100)) - ($discount / $productCount) + ($additionalPrices / $productCount)) - COALESCE(is_paid, 0))")
                ->get();

            $remainingPayment = $totalPaymentNominal;

            // Allocate payments
            $marketingProducts->each(function($mp) use (&$remainingPayment) {
                $currentPaid = $mp->is_paid ?? 0;
                $paymentLeft = $mp->grand_total - $currentPaid;

                if ($paymentLeft <= 0 || $remainingPayment <= 0) {
                    return;
                }

                $pay         = min($paymentLeft, $remainingPayment);
                $mp->is_paid = $currentPaid + $pay;
                $remainingPayment -= $pay;

                $mp->save();
            });

            // Allocate remaining payment if any
            if ($remainingPayment > 0 && $marketingProducts->isNotEmpty()) {
                $marketingProducts->first()->update([
                    'is_paid' => DB::raw("COALESCE(is_paid, 0) + $remainingPayment"),
                ]);
            }
        });
    }

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

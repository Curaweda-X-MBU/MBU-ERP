<?php

namespace App\Models\Expense;

use App\Constants;
use App\Helpers\Parser;
use App\Models\DataMaster\Kandang;
use App\Models\DataMaster\Location;
use App\Models\DataMaster\Supplier;
use App\Models\UserManagement\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Expense extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'expenses';

    protected $primaryKey = 'expense_id';

    protected $fillable = [
        'parent_expense_id',
        'id_expense',
        'po_number',
        'transaction_date',
        'is_approved',
        'approval_line',
        'location_id',
        'supplier_id',
        'category',
        'bill_docs',
        'realization_docs',
        'payment_status',
        'expense_status',
        'created_by',
    ];

    protected $appends = [
        'grand_total',
        'is_paid',
        'total_qty',
        'not_paid',
        'is_returned',
        'is_realized',
        'not_realized',
        'is_rejected',
    ];

    public function getGrandTotalAttribute()
    {
        return $this->expense_main_prices->sum('price') + $this->expense_addit_prices->sum('price');
    }

    public function getIsPaidAttribute()
    {
        return $this->expense_disburses->sum('payment_nominal');
    }

    public function getTotalQtyAttribute()
    {
        return $this->expense_main_prices->sum('qty');
    }

    public function getNotPaidAttribute()
    {
        return $this->grand_total - $this->is_paid;
    }

    public function getIsReturnedAttribute()
    {
        return ($this->expense_return->payment_nominal ?? 0)
            + ($this->expense_return->bank_admin_fees ?? 0); // dianggap total pengembalian sudah termasuk admin bank
    }

    public function getIsRealizedAttribute()
    {
        return $this->expense_realizations->sum('price') + $this->is_returned;
    }

    public function getNotRealizedAttribute()
    {
        return $this->grand_total - $this->is_realized;
    }

    public function getIsRejectedAttribute()
    {
        return isset($this->is_approved) && $this->is_approved !== null && $this->is_approved == 0
            ? 1
            : 0;
    }

    public function calculatePaymentStatus()
    {
        $grandTotal    = $this->grand_total;
        $totalPayments = $this->is_paid;

        if ($grandTotal < $totalPayments) {
            return array_search('Dibayar Lebih', Constants::MARKETING_PAYMENT_STATUS);
        } elseif ($grandTotal == $totalPayments) {
            return array_search('Dibayar Penuh', Constants::MARKETING_PAYMENT_STATUS);
        } elseif ($grandTotal > $totalPayments && $totalPayments > 0) {
            return array_search('Dibayar Sebagian', Constants::MARKETING_PAYMENT_STATUS);
        } else {
            return array_search('Tempo', Constants::MARKETING_PAYMENT_STATUS);
        }
    }

    public static function expenseEvent($input)
    {
        try {
            DB::beginTransaction();
            $createdExpense = self::create([
                'location_id'      => $input['location_id'],
                'supplier_id'      => $input['supplier_id'] ?? null,
                'category'         => 1,
                'transaction_date' => $input['trx_date'],
                'payment_status'   => 1,
                'expense_status'   => 5,
                'is_approved'      => 1,
                'approver_id'      => auth()->user()->user_id,
                'approval_notes'   => 'Auto generated by system - '.$input['po_number'],
                'approved_at'      => date('Y-m-d H:i:s'),
                'created_by'       => auth()->user()->user_id,
            ]);

            $expenseID        = $createdExpense->expense_id;
            $selectedKandangs = $input['kandangs'];
            if (count($selectedKandangs) > 0) {
                foreach ($selectedKandangs as $key => $value) {
                    $arrKandang[$key]['expense_id'] = $expenseID;
                    $arrKandang[$key]['kandang_id'] = $value;

                    // assign project_id
                    $project = Kandang::find($value)->project->where('project_status', '!=', 4)->first() ?? null;
                    if ($project) {
                        $arrKandang[$key]['project_id'] = $project->project_id;
                    } else {
                        $arrKandang[$key]['project_id'] = null;
                    }
                }
                ExpenseKandang::insert($arrKandang);
            }

            $arrMainPrices = $input['expense_main_prices'];
            foreach ($arrMainPrices as $key => $value) {
                $qty        = Parser::parseLocale($value['qty']);
                $totalPrice = Parser::parseLocale($value['price']);
                $urlTo      = 'javascript:void(0)';
                if (isset($input['purchase_id'])) {
                    $urlTo = route('purchase.detail', $input['purchase_id']);
                }
                if (isset($input['stock_movement_id'])) {
                    $urlTo = route('inventory.movement.detail', $input['stock_movement_id']);
                }

                ExpenseMainPrice::create([
                    'expense_id'  => $expenseID,
                    'supplier_id' => $value['supplier_id'] ?? null,
                    'nonstock_id' => $value['nonstock_id'],
                    'qty'         => $qty,
                    'price'       => $totalPrice,
                    'notes'       => "<a href='".$urlTo."' target='_blank'>".$input['po_number'].'</a>',
                ]);
            }

            $incrementId = self::where('id_expense', 'LIKE', 'BOP.%')->withTrashed()->count() + 1;
            $idExpense   = "BOP.{$incrementId}";
            $location    = Location::with('company')->find($input['location_id']);
            $alias       = $location->company->alias ?? 'N/A';
            $incrementPo = str_pad($expenseID + 1, 5, '0', STR_PAD_LEFT);

            $createdExpense->update([
                'id_expense' => $idExpense,
                'po_number'  => "PO-{$alias}-BOP-{$incrementPo}",
            ]);

            DB::commit();

            return [
                'success' => true,
                'message' => 'Success insert expense',
            ];
        } catch (\Exception $e) {
            DB::rollback();

            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }

    }

    public function created_user()
    {
        return $this->belongsTo(User::class, 'created_by', 'user_id');
    }

    public function location()
    {
        return $this->belongsTo(Location::class, 'location_id', 'location_id');
    }

    public function expense_kandang()
    {
        return $this->hasMany(ExpenseKandang::class, 'expense_id', 'expense_id');
    }

    public function expense_main_prices()
    {
        return $this->hasMany(ExpenseMainPrice::class, 'expense_id', 'expense_id');
    }

    public function expense_addit_prices()
    {
        return $this->hasMany(ExpenseAdditPrice::class, 'expense_id', 'expense_id');
    }

    public function expense_disburses()
    {
        return $this->hasMany(ExpenseDisburse::class, 'expense_id', 'expense_id');
    }

    public function expense_realizations()
    {
        return $this->hasMany(ExpenseRealization::class, 'expense_id', 'expense_id');
    }

    public function parent_expense()
    {
        return $this->belongsTo(Expense::class, 'parent_expense_id', 'expense_id');
    }

    public function child_expense()
    {
        return $this->hasOne(Expense::class, 'parent_expense_id', 'expense_id');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id', 'supplier_id');
    }

    public function expense_return()
    {
        return $this->hasOne(ExpenseReturnPayment::class, 'expense_id', 'expense_id');
    }
}

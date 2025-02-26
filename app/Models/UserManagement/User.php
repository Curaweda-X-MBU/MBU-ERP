<?php

namespace App\Models\UserManagement;

use App\Models\DataMaster\Customer;
use App\Models\DataMaster\Department;
use App\Models\DataMaster\Kandang;
use App\Models\Expense\Expense;
use App\Models\Expense\ExpenseReturnPayment;
use App\Models\Marketing\Marketing;
use App\Models\Marketing\MarketingDeliveryVehicle;
use App\Models\Marketing\MarketingPayment;
use App\Models\Marketing\MarketingReturnPayment;
use App\Models\Ph\PhComplaint;
use App\Models\Project\Project;
use App\Models\Purchase\Purchase;
use App\Notifications\ResetPassword;
use Illuminate\Contracts\Auth\CanResetPassword;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements CanResetPassword
{
    use HasFactory;
    use HasRoles;
    use Notifiable;
    use SoftDeletes;

    protected $table = 'users';

    protected $primaryKey = 'user_id';

    protected $fillable = [
        'npk',
        'name',
        'email',
        'image',
        'password',
        'phone',
        'department_id',
        'role_id',
        'is_active',
        'created_at',
        'created_by',
    ];

    protected $hidden = [
        'password',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    public function customers()
    {
        return $this->hasMany(Customer::class, 'assign_to', 'user_id');
    }

    public function kandangs()
    {
        return $this->hasMany(Kandang::class, 'pic', 'user_id');
    }

    public function ph_complaint()
    {
        return $this->hasMany(PhComplaint::class, 'created_by', 'user_id');
    }

    public function project()
    {
        return $this->hasMany(Project::class, 'created_by', 'user_id');
    }

    public function purchase()
    {
        return $this->hasMany(Purchase::class, 'created_by', 'user_id');
    }

    public function cullingpic()
    {
        return $this->hasMany(PhComplaint::class, 'culling_pic', 'user_id');
    }

    public function sender_marketing_delivery_vehicles()
    {
        return $this->hasMany(MarketingDeliveryVehicle::class, 'sender_id', 'user_id');
    }

    public function customer_marketings()
    {
        return $this->hasMany(Marketing::class, 'customer_id', 'user_id');
    }

    public function sales_marketings()
    {
        return $this->hasMany(Marketing::class, 'sales_id', 'sales_id');
    }

    public function approve_marketings()
    {
        return $this->hasMany(Marketing::class, 'approver_id', 'user_id');
    }

    public function approve_marketing_payments()
    {
        return $this->hasMany(MarketingPayment::class, 'approver_id', 'user_id');
    }

    public function approve_marketing_returns()
    {
        return $this->hasMany(Marketing::class, 'approver_id', 'user_id');
    }

    public function approve_marketing_return_payments()
    {
        return $this->hasMany(MarketingReturnPayment::class, 'approver_id', 'user_id');
    }

    public function approve_expense()
    {
        return $this->hasMany(Expense::class, 'approver_id', 'user_id');
    }

    public function expense_created_by()
    {
        return $this->hasMany(Expense::class, 'created_by', 'user_id');
    }

    public function expense_return_created_by()
    {
        return $this->hasMany(ExpenseReturnPayment::class, 'created_by', 'user_id');
    }

    public function marketing_created_by()
    {
        return $this->hasMany(Marketing::class, 'created_by', 'user_id');
    }

    public static function getData($all, $active, $whereClause = false)
    {
        $user = self::query();
        if ($active) {
            $user->active();
        }

        $user->with([
            'department' => function($query) {
                $query->with(['company', 'location']);
            },
            'role',
        ]);

        if ($whereClause) {
            return $all ? $user->where($whereClause)->get() : $user->where($whereClause)->first();
        }

        return $user->get();
    }

    public static function authUser($email)
    {
        return self::with('role')
            ->where('email', $email)->first();
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPassword($token));
    }
}

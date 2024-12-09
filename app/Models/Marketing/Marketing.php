<?php

namespace App\Models\Marketing;

use App\Models\DataMaster\Company;
use App\Models\DataMaster\Location;
use App\Models\UserManagement\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Marketing extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'marketings';

    protected $primaryKey = 'marketing_id';

    protected $fillable = [
        'id_marketing',
        'approver_id',
        'approval_notes',
        'company_id',
        'location_id',
        'customer_id',
        'sold_at',
        'doc_reference',
        'notes',
        'sales_id',
        'tax',
        'discount',
        'sub_total',
        'grand_total',
        'payment_status',
        'marketing_status',
        'created_by',
    ];

    public function approver()
    {
        return $this->belongsTo(User::class, 'approver_id', 'user_id');
    }

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id', 'company_id');
    }

    public function location()
    {
        return $this->belongsTo(Location::class, 'location_id', 'location_id');
    }

    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id', 'user_id');
    }

    public function sales()
    {
        return $this->belongsTo(User::class, 'sales_id', 'user_id');
    }

    public function addit_prices()
    {
        return $this->hasMany(MarketingAdditPrice::class, 'marketing_id', 'marketing_id');
    }

    public function marketing_products()
    {
        return $this->hasMany(MarketingProduct::class, 'marketing_id', 'marketing_id');
    }
}

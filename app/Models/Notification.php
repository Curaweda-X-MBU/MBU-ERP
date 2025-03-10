<?php

namespace App\Models;

use App\Models\UserManagement\Role;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Notification extends Model
{
    use HasFactory;

    protected $table = 'notifications';

    protected $primaryKey = 'notification_id';

    protected $fillable = [
        'role_id',
        'message',
        'url',
        'module',
        'foreign_id',
        'is_done',
        'location_id',
    ];

    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    /**
     * notify
     *
     * @param mixed{role_id: int, from: string, description: string, url: string, module: string, foreign_id: int location_id: int} $input
     * @return array{success: bool, message: string}
     */
    public static function notify($input)
    {
        try {
            DB::beginTransaction();

            $roleName = Role::where('role_id', $input['role_id'])->first()->name;

            self::create([
                'role_id'     => $input['role_id'],
                'message'     => 'Persetujuan '.$input['from'].' oleh '.$roleName.$input['description'] ?? '',
                'url'         => $input['url'],
                'module'      => $input['module'],
                'foreign_id'  => $input['foreign_id'],
                'location_id' => $input['location_id'],
            ]);

            DB::commit();

            return [
                'success' => true,
                'message' => 'Success notify',
            ];
        } catch (\Exception $e) {
            DB::rollback();

            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * dismiss
     *
     * @param  string  $url
     * @return array{success: bool, message: string}
     */
    public static function dismiss($url)
    {
        try {
            DB::beginTransaction();

            self::where('url', $url)->update(['is_done' => 1]);

            DB::commit();

            return [
                'success' => true,
                'message' => 'Success dismiss',
            ];
        } catch (\Exception $e) {
            DB::rollback();

            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }
}

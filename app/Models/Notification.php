<?php

namespace App\Models;

use App\Constants;
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
     * @param mixed{role: string, module: string, foreign_id: int, url: string, location_id: int, description: string} $input
     * @return array{success: bool, message: string}
     */
    public static function notify($input)
    {
        try {
            DB::beginTransaction();

            $requiredKeys = ['role', 'description', 'url', 'module', 'foreign_id', 'location_id'];

            foreach ($requiredKeys as $key) {
                if (! isset($input[$key])) {
                    return ['success' => false, 'message' => "Notify: Missing required field: $key"];
                }
            }

            $moduleMappings = Constants::NOTIFICATION_MODULE;

            if (! array_key_exists($input['module'], $moduleMappings)) {
                return ['success' => false, 'message' => "Notify: Invalid 'module' key"];
            }

            $role = Role::where('name', $input['role'])->get()->first();

            if (! $role) {
                return ['success' => false, 'message' => "Notify: Role not found from 'role_id'"];
            }

            $exists = self::where('role_id', $role->role_id)
                ->where('module', $input['module'])
                ->where('foreign_id', $input['foreign_id'])
                ->where('is_done', 0)
                ->exists();

            $from = $moduleMappings[$input['module']];

            if (! $exists) {
                self::create([
                    'role_id' => $role->role_id,
                    'message' => sprintf(
                        'Persetujuan %s oleh %s\n%s',
                        $from,
                        $role->name,
                        $input['description'],
                    ),
                    'url'         => $input['url'],
                    'module'      => $input['module'],
                    'foreign_id'  => $input['foreign_id'],
                    'location_id' => $input['location_id'],
                ]);
            }

            DB::commit();

            return [
                'success' => true,
                'message' => 'Notify: Created',
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
     * @param  int  $role_id
     * @param  string  $module
     * @param  int  $foreign_id
     * @return array{success: bool, message: string}
     */
    public static function dismiss($role_id, $module, $foreign_id)
    {
        try {
            DB::beginTransaction();

            $role = Role::find($role_id);

            $notification = self::where('module', $module)
                ->where('foreign_id', $foreign_id)
                ->where('is_done', 0);

            if (strtolower($role->name) !== 'super admin') {
                $notification->where('role_id', $role_id);
            }

            $exists = (clone $notification)->exists();

            if (! $exists) {
                return ['success' => false, 'message' => 'Notify Dismiss: Notification not found'];
            }

            $notification->update(['is_done' => 1]);

            DB::commit();

            return [
                'success' => true,
                'message' => 'Notify Dismiss: Success',
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

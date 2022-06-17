<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;

class UserRole
{
    public int $id;
    public string $name;

    public function __construct(int $id = null, string $name = null) {
        $this->id = $id;
        $this->name = $name;
    }

    /**
     * Protected functions
     */
    
    protected static function fetchUserRole($role_id) {
        $user_role = DB::table('roles')->where('id', $role_id)->first();
        if ($user_role) {
            return new UserRole($user_role->id, $user_role->name);
        }

        return null;
    }

     /**
     * Public static functions
     */

    public static function getUserRole($role_id) {
        return self::fetchUserRole($role_id);
    }
}

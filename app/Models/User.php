<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;

class User
{
    public int $id;
    public string $username;
    public string $password;
    public string $email;
    public UserRole $role;

    public function __construct(int $id = null, string $username = null, string $password = null, string $email = null, UserRole $role = null) {
        $this->id = $id;
        $this->username = $username;
        $this->password = $password;
        $this->email = $email;
        $this->role = $role;
    }

    /**
     * Protected functions
     */
    
    protected static function createUserFromToken($token) {
        $user = DB::table('users')
            ->where('token', $token)
            ->leftJoin('roles', 'users.role_id', '=', 'roles.id')
            ->select('users.id', 'users.username', 'users.password', 'users.email', 'roles.id as role_id', 'roles.name as role_name')
            ->first();

        if ($user) {
            return new User($user->id, $user->username, $user->password, $user->email, new UserRole($user->role_id, $user->role_name));
        }
        return null;
    }

    protected static function fetchUsers() {
        $users = DB::table('users')
            ->get();

        return response()->json($users);
    }

     /**
     * Public static functions
     */

    public static function getUserFromToken($token) {
        return self::createUserFromToken($token);
    }

    public static function getUsers() {
        return self::fetchUsers();
    }

}

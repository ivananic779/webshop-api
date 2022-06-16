<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;

class User
{
    public int $id;
    public string $username;
    public string $password;
    public string $email;
    public int $role_id;

    public function __construct($id = null, $username = null, $password = null, $email = null, $role_id = null) {
        $this->id = $id;
        $this->username = $username;
        $this->password = $password;
        $this->email = $email;
        $this->role_id = $role_id;
    }

    /**
     * Protected functions
     */
    
    protected static function createUserFromToken($token) {
        $user = DB::table('users')->where('token', $token)->first();
        if ($user) {
            return new User($user->id, $user->username, $user->password, $user->email, $user->role_id);
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

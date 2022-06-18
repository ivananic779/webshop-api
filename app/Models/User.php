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
    * Public static functions
    */

    public static function getUserFromToken($token) {
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

    public static function getUsers() {
        $ret = [
            'status' => 'OK',
            'message' => 'Success',
            'data' => []
        ];

        try {
            $ret['data'] = DB::table('users')->get();
        } catch (\Exception $e) {
            $ret['status'] = 'NOT OK';
            $ret['message'] = $e->getMessage();
        }

        return response()->json($ret);
    }

}

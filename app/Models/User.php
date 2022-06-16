<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\http\Request;

class User
{
    public int $id;
    public string $username;
    public string $password;
    public string $email;

    public function __construct($id = null, $username = null, $password = null, $email = null) {
        $this->id = $id;
        $this->username = $username;
        $this->password = $password;
        $this->email = $email;
    }

     /**
     * Public static functions
     */

    public static function createUserFromToken($token) {
        $user = DB::table('users')
            ->where('token', $token)
            ->first();

        if ($user) {
            return new User($user->id, $user->username, $user->password, $user->email);
        }

        return null;
    }

    public static function getUsers(Request $request) {
        $users = DB::table('users')
            ->get();

        return response()->json($users);
    }

}

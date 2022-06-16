<?php

namespace App\Models;

use DateTime;
use Illuminate\Support\Facades\DB;

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

    public static function isValidToken($token) {
        return DB::table('users')
            ->where('token', $token)
            //->where('token_expires', '>', DateTime::createFromFormat('Y-m-d H:i:s', date('Y-m-d H:i:s')))
            ->exists();
    }

    public static function createUserFromToken($token) {
        $user = DB::table('users')
            ->where('token', $token)
            ->first();

        if ($user) {
            return new User($user->id, $user->username, $user->password, $user->email);
        }

        return null;
    }

}

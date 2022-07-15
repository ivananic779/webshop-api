<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;

class User
{
    public int $id;
    public string $username;
    public string $email;
    public int $language_id;
    public ?string $password;
    public ?string $first_name;
    public ?string $last_name;
    public ?string $company_name;
    public ?UserRole $role;
    // role_id is always 4 because we only make users this way for security purposes
    public int $role_id = 4;

    public function __construct(
        int $id,
        string $username,
        string $email,
        int $language_id = 1,
        ?string $password = null,
        ?string $first_name = null,
        ?string $last_name = null,
        ?string $company_name = null,
        ?UserRole $role = null,
    ) {
        $this->id = $id;
        $this->username = $username;
        $this->email = $email;
        $this->language_id = $language_id;
        $this->password = $password;
        $this->first_name = $first_name;
        $this->last_name = $last_name;
        $this->company_name = $company_name;
        $this->role = $role;
    }

    /**
     * Public functions
     */

    public function save($edit_existing = false): bool
    {
        // Check if user exists
        $user = DB::table('users')
            ->where('id', $this->id)
            ->orWhere('username', $this->username)
            ->orWhere('email', $this->email)
            ->first();

        if ($user) {
            if ($edit_existing) {
                // If user exists and we're editing, update the user
                DB::table('users')->where('id', $this->id)->update([
                    'username' => $this->username,
                    'email' => $this->email,
                    'language_id' => $this->language_id,
                    'first_name' => $this->first_name ?? "",
                    'last_name' => $this->last_name ?? "",
                    'company_name' => $this->company_name ?? "",
                ]);

                return true;
            }

            return false;
        } else {
            // If user doesn't exist, create it
            DB::table('users')->insert([
                'username' => $this->username,
                'password' => $this->password,
                'email' => $this->email,
                'language_id' => $this->language_id,
                'first_name' => $this->first_name ?? "",
                'last_name' => $this->last_name ?? "",
                'company_name' => $this->company_name ?? "",
                'role_id' => $this->role_id,
            ]);

            return true;
        }
    }

    /**
     * Public static functions
     */

    public static function getUserFromToken($token): ?User
    {
        $user = DB::table('users')
            ->where('token', $token)
            ->where('token_expires', '>', Date('y:m:d'))
            ->where('enabled', true)
            ->leftJoin('roles', 'users.role_id', '=', 'roles.id')
            ->first(
                array(
                    'users.id', 
                    'users.username', 
                    'users.password', 
                    'users.language_id', 
                    'users.first_name', 
                    'users.last_name', 
                    'users.company_name', 
                    'users.email', 
                    'roles.id as role_id', 
                    'roles.name as role_name'
                )
            );

        if ($user) {
            return new User(
                id:             $user->id,
                username:       $user->username,
                email:          $user->email,
                language_id:    $user->language_id,
                password:       $user->password,
                first_name:     $user->first_name,
                last_name:      $user->last_name,
                company_name:   $user->company_name,
                role:           new UserRole($user->role_id, $user->role_name)
            );
        }

        return null;
    }

    public static function generateTokenForUser($id): string
    {
        $token = null;
        $token_expires = Date('y:m:d', strtotime('+45 days'));

        $user = DB::table('users')
            ->where('id', $id)
            ->first();

        if ($user) {
            $token = md5(uniqid(mt_rand(), true));
            DB::table('users')->where('id', $id)->update([
                'token' => $token,
                'token_created' => Date('y:m:d'),
                'token_expires' => $token_expires
            ]);
        } else {
            return null;
        }

        return $token;
    }
}

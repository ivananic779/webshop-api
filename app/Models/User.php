<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;

class User
{
    public int $id;
    public string $username;
    public string $password;
    public string $email;
    public int $language_id;
    public ?string $first_name;
    public ?string $last_name;
    public ?string $company_name;
    public ?UserRole $role;
    // role_id is always 4 because we only make users this way for security purposes
    public int $role_id = 4;

    public function __construct(
        int $id,
        string $username,
        string $password,
        string $email,
        int $language_id,
        ?string $first_name = null,
        ?string $last_name = null,
        ?string $company_name = null,
        ?UserRole $role = null,
    ) {
        $this->id = $id;
        $this->username = $username;
        $this->password = $password;
        $this->email = $email;
        $this->language_id = $language_id;
        $this->first_name = $first_name;
        $this->last_name = $last_name;
        $this->company_name = $company_name;
        $this->role = $role;
    }

    /**
     * Protected functions
     */
    protected function save($edit_existing = false): bool
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
                    'password' => $this->password,
                    'email' => $this->email,
                    'language_id' => $this->language_id,
                    'first_name' => $this->first_name,
                    'last_name' => $this->last_name,
                    'company_name' => $this->company_name,
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
                'first_name' => $this->first_name,
                'last_name' => $this->last_name,
                'company_name' => $this->company_name,
                'language_id' => $this->language_id,
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
            ->leftJoin('roles', 'users.role_id', '=', 'roles.id')
            ->select('users.id', 'users.username', 'users.password', 'users.language_id', 'users.first_name', 'users.last_name', 'users.company_name', 'users.email', 'roles.id as role_id', 'roles.name as role_name')
            ->first();

        if ($user) {
            return new User($user->id, $user->username, $user->password, $user->email, $user->language_id, $user->first_name, $user->last_name, $user->company_name, new UserRole($user->role_id, $user->role_name));
        }
        return null;
    }

    /**
     * API routes
     */

    public static function getUsers($request)
    {
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

    public static function createUser($request)
    {
        $ret = [
            'status' => 'OK',
            'message' => 'Success',
            'data' => []
        ];

        // Try to create new user
        try {
            $user = new User(
                $request->input('id'),
                $request->input('username'),
                $request->input('password'),
                $request->input('email'),
                $request->input('language_id'),
                $request->input('first_name'),
                $request->input('last_name'),
                $request->input('company_name'),
            );
        } catch (\Exception $e) {
            $ret['status'] = 'NOT OK';
            $ret['message'] = $e->getMessage();
        }

        // Try to save/update user to database
        try {
            $user->save(true);
        } catch (\Exception $e) {
            $ret['status'] = 'NOT OK';
            $ret['message'] = $e->getMessage();
        }

        return response()->json($ret);
    }
}

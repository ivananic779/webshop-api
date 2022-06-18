<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;

class User
{
    public int $id;
    public string $username;
    public string $password;
    public ?string $first_name;
    public ?string $last_name;
    public ?string $company_name;
    public string $email;
    public int $role_id;
    public ?UserRole $role;

    // role_id is always 4 because we only make admins in the database for security purposes
    public function __construct(int $id, string $username, string $password, string $first_name = null, string $last_name = null, string $company_name = null, string $email, UserRole $role = null, int $role_id = 4) {
        $this->id = $id;
        $this->username = $username;
        $this->password = $password;
        $this->first_name = $first_name;
        $this->last_name = $last_name;
        $this->company_name = $company_name;
        $this->email = $email;
        $this->role = $role;
        $this->role_id = $role_id;
    }

    /**
     * Protected functions
     */
    protected function save($edit_existing = false): bool {
        // Check if user exists
        $user = DB::table('users')
            ->where('username', $this->username)
            ->orWhere('email', $this->email)
            ->first();

        if ($user) {
            if ($edit_existing) {
                // If user exists and we're editing, update the user
                DB::table('users')->where('id', $this->id)->update([
                    'username' => $this->username,
                    'password' => $this->password,
                    'first_name' => $this->first_name,
                    'last_name' => $this->last_name,
                    'company_name' => $this->company_name,
                    'email' => $this->email
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
                'role_id' => $this->role_id
            ]);

            return true;            
        }
    }

    /**
     * Public static functions
     */

    public static function getUserFromToken($token): ?User {
        $user = DB::table('users')
            ->where('token', $token)
            ->leftJoin('roles', 'users.role_id', '=', 'roles.id')
            ->select('users.id', 'users.username', 'users.password', 'users.first_name', 'users.last_name', 'users.company_name', 'users.email', 'roles.id as role_id', 'roles.name as role_name')
            ->first();

        if ($user) {
            return new User($user->id, $user->username, $user->password, $user->first_name, $user->last_name, $user->company_name, $user->email, new UserRole($user->role_id, $user->role_name));
        }
        return null;
    }

    /**
     * API routes
     */

    public static function getUsers($request) {
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

    public static function createUser($request) {
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
                $request->input('first_name'),
                $request->input('last_name'),
                $request->input('company_name'),
                $request->input('email')
            );            
        } catch (\Exception $e) {
            $ret['status'] = 'NOT OK';
            $ret['message'] = $e->getMessage();
        }

        // Try to save user to database
        try {
            $user->save();
        } catch (\Exception $e) {
            $ret['status'] = 'NOT OK';
            $ret['message'] = $e->getMessage();
        }

        return response()->json($ret);
    }

}

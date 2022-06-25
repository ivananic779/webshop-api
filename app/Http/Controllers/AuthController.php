<?php

namespace App\Http\Controllers;

use App\Models\ResponseObject;
use App\Models\User;
use Illuminate\Http\Request;

use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\DB;

class AuthController extends BaseController
{
    private $db;
    private $request;
    private $response;

    public function __construct(DB $db, Request $request, ResponseObject $response)
    {
        $this->db = $db;
        $this->request = $request;
        $this->response = $response;
    }

    public function login()
    {
        if (!$this->request->filled(['username', 'password'])) {
            return $this->response::MISSING_INPUT();
        }

        try {
            $user = $this->db::table('users')
                ->leftJoin('roles', 'users.role_id', '=', 'roles.id')
                ->where('username', $this->request->input('username'))
                ->where('password', $this->request->input('password'))
                ->where('enabled', true)
                ->select('users.id', 'roles.name as role_name')
                ->first();

            if (!$user) {
                return $this->response::NOT_FOUND('login_user_not_found');
            }
        } catch (\Exception $e) {
            return $this->response::ERROR('login_error');
        }

        try {
            $token = User::generateTokenForUser($user->id);
        } catch (\Exception $e) {
            return $this->response::ERROR('generate_token_error');
        }

        if (!$token) {
            return $this->response::ERROR('generate_token_empty');
        }

        return $this->response::OK([
            'token' => $token,
            'role_name' => $user->role_name
        ]);
    }
}

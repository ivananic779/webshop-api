<?php

namespace App\Http\Controllers;

use App\Models\ResponseObject;
use Illuminate\Http\Request;

use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\DB;

use App\Models\User;

class UserController extends BaseController
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

    public function getUsers()
    {
        $data = [];

        try {
            $data = DB::table('users')->get();
        } catch (\Exception $e) {
            return $this->response::ERROR();
        }

        return $this->response::OK($data);
    }

    public function postUser()
    {
        // Try to create new user
        try {
            $user = new User(
                $this->request->input('id'),
                $this->request->input('username'),
                $this->request->input('password'),
                $this->request->input('email'),
                $this->request->input('language_id'),
                $this->request->input('first_name'),
                $this->request->input('last_name'),
                $this->request->input('company_name'),
            );
        } catch (\Exception $e) {
            return $this->response::ERROR();
        }

        // Try to save/update user to database
        try {
            if (!$user->save(true)) {
                return $this->response::ERROR("user_not_saved_false");
            }
        } catch (\Exception $e) {
            return $this->response::ERROR("user_not_saved_error");
        }

        return $this->response::OK(null, "user_saved");
    }

    public function deleteUser()
    {
        try {
            $user = $this->db::table('users')
                ->where('id', $this->request->id)
                ->first();

            if ($user) {
                // If user exists, delete it
                $this->db::table('users')->where('id', $this->request->id)->delete();
            } else {
                return $this->response::ERROR('user_not_deleted_no_exists');
            }
        } catch (\Exception $e) {
            return $this->response::ERROR('user_not_deleted_error');
        }

        return $this->response::OK(null, 'user_deleted');
    }
}

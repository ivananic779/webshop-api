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
            $data = DB::table('users')
                ->leftJoin('roles', 'users.role_id', '=', 'roles.id')
                ->get(
                    array(
                        'users.id', 
                        'users.username', 
                        'users.email', 
                        'users.role_id', 
                        'users.first_name', 
                        'users.last_name', 
                        'users.company_name',
                        'roles.name as role_name',
                        'roles.description as role_description'
                    )
                );
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
                id:             $this->request->input('id'),
                username:       $this->request->input('username'),
                email:          $this->request->input('email'),
                language_id:    $this->request->input('language_id'),
                password:       $this->request->input('password'),
                first_name:     $this->request->input('first_name'),
                last_name:      $this->request->input('last_name'),
                company_name:   $this->request->input('company_name'),
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

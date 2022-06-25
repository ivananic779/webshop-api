<?php

namespace App\Http\Controllers;

use App\Models\ResponseObject;
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

        return $this->response::OK();
    }
}

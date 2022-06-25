<?php

namespace App\Http\Controllers;

use App\Models\ResponseObject;
use Illuminate\Http\Request;

use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\DB;

class LanguageController extends BaseController
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

    public function getLanguages()
    {
        $data = [];

        try {
            $data = DB::table('languages')->get();
        } catch (\Exception $e) {
            return $this->response::ERROR("get_langauges_error");
        }

        return $this->response::OK($data);
    }
}

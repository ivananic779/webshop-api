<?php

namespace App\Models;

use stdClass;

class ResponseObject
{
    public static function OK($data = null, string $msg = "request_success")
    {
        $response = new stdClass;
        $response->status = "OK";
        $response->msg = $msg;
        $response->data = $data;

        return response()->json($response);
    }

    public static function ERROR($data = null, string $msg = "request_error")
    {
        $response = new stdClass;
        $response->status = "NOT OK";
        $response->msg = $msg;
        $response->data = $data;

        return response()->json($response);
    }

    public static function NOT_FOUND(string $msg = "data_not_found", $data = null)
    {
        $response = new stdClass;
        $response->status = "NOT FOUND";
        $response->msg = $msg;
        $response->data = $data;

        return response()->json($response);
    }

    public static function MISSING_INPUT(string $msg = "missing_input_in_request", $data = null)
    {
        $response = new stdClass;
        $response->status = "NO INPUT";
        $response->msg = $msg;
        $response->data = $data;

        return response()->json($response);
    }
}

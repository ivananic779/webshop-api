<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;

class Language
{
    public int $id;
    public string $name;
    public ?string $country;

    public function __construct(
        int $id,
        string $name,
        ?string $country = null,
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->country = $country;
    }

    /**
     * Protected functions
     */

    /**
     * Public static functions
     */

    /**
     * API routes
     */

    public static function getLanguages($request)
    {
        $ret = [
            'status' => 'OK',
            'message' => 'Success',
            'data' => []
        ];

        try {
            $ret['data'] = DB::table('languages')->get();
        } catch (\Exception $e) {
            $ret['status'] = 'NOT OK';
            $ret['message'] = $e->getMessage();
        }

        return response()->json($ret);
    }
}

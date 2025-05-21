<?php

namespace Jazer\Chatify\Http\Controllers\Fetch;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class Single extends Controller
{
    public static function single($people_refid) {
        $source = DB::connection("conn_chatify")
        ->table("people")
        ->select("project_refid", "people_group_refid", "people_refid", "firstname", "lastname", "email", "blocked", "active")
        ->where([
            "project_refid"     => config('usersconfig.project_refid'),
            "people_refid"      => $people_refid
        ])
        ->get();

        if(count($source) > 0) {
            return $source[0];
        }
        else {
            return [];
        }
    }
}



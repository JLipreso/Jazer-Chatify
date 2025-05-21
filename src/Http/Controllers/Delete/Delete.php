<?php

namespace Jazer\Chatify\Http\Controllers\Delete;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class Delete extends Controller
{
    public static function delete($people_refid) {
        $deleted = DB::connection("conn_chatify")->table("people")
            ->where([
                "project_refid"     => config('usersconfig.project_refid'),
                "people_refid"      => $people_refid
            ])
            ->delete();

        if($deleted) {
            DB::connection("conn_chatify")->table("people_auth")
            ->where([
                "project_refid"     => env('project_refid'),
                "people_refid"      => $people_refid
            ])
            ->delete();
            return [
                "success"   => true
            ];
        }
        else {
            return [
                "success"   => false
            ];
        }
    }
}
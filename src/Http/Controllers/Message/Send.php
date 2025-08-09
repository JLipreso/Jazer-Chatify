<?php

namespace Jazer\Chatify\Http\Controllers\Message;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Jazer\Chatify\Http\Events\ChatEvent;

class Send extends Controller
{
    public static function send(Request $request) {
        try {

            $project_refid      = $request->project_refid;
            $convo_refid        = $request->convo_refid;
            $message            = $request->message;
            $user_refid         = $request->user_refid;
            
            if (empty(trim($message))) {
                return response()->json([
                    'error' => 'Message cannot be empty'
                ], 400);
            }
            
            event(new ChatEvent($project_refid, $convo_refid, $message, $user_refid));
            
            return response()->json([
                'status' => 'success',
                'message' => 'Message sent successfully!',
                'data' => [
                    'project_refid' => $project_refid,
                    'convo_refid'   => $convo_refid,
                    'message'       => $message,
                    'user_refid'    => $user_refid,
                    'timestamp'     => now()->toDateTimeString()
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to send message',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
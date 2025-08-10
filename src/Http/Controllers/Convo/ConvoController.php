<?php

namespace Jazer\Chatify\Http\Controllers\Convo;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Jazer\Chatify\Http\Controllers\Utility\ReferenceID;

class ConvoController extends Controller
{
    public function createConvo(Request $request)
    {
        try {

            $project_refid  = config('jtchatifyconfig.project_refid');
            $convo_refid    = ReferenceID::create('CVR');

            if ($request->has('project_refid')) {
                $project_refid = $request->input('project_refid');
            }

            $inserted = DB::connection("conn_chatify")->table("chatify_convo")->insert([
                'project_refid'     => config('jtchatifyconfig.project_refid'),
                'convo_refid'       => $convo_refid,
                'convo_theme'       => $request->input('convo_theme'),
                'name'              => $request->input('name'),
                'created_by'        => $request->input('created_by'),
                'created_at'        => now(),
                'last_activity'     => now(),
                'last_message'      => $request->input('last_message'),
                'active'            => $request->input('active', '1')
            ]);

            if ($inserted) {
                return response()->json([
                    'success'       => true,
                    'message'       => 'Conversation created successfully',
                    'convo_refid'   => $convo_refid
                ], 201);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to create conversation'
                ], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error creating conversation: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update an existing conversation
     */
    public function updateConvo(Request $request, $convo_refid, $dataid)
    {
        try {
            $updateData = [];
            
            // Only update fields that are provided in the request
            if ($request->has('convo_theme')) {
                $updateData['convo_theme'] = $request->input('convo_theme');
            }
            if ($request->has('name')) {
                $updateData['name'] = $request->input('name');
            }
            if ($request->has('last_message')) {
                $updateData['last_message'] = $request->input('last_message');
            }
            if ($request->has('active')) {
                $updateData['active'] = $request->input('active');
            }
            
            // Always update last_activity when conversation is updated
            $updateData['last_activity'] = now();

            $updated = DB::connection("conn_chatify")->table("chatify_convo")
                ->where([
                    'project_refid' => config('jtchatifyconfig.project_refid'),
                    'convo_refid'   => $convo_refid,
                    'dataid'        => $dataid
                ])
                ->update($updateData);

            if ($updated) {
                return response()->json([
                    'success' => true,
                    'message' => 'Conversation updated successfully'
                ], 200);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Conversation not found or no changes made'
                ], 404);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating conversation: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete a conversation
     */
    public function deleteConvo($convo_refid)
    {
        try {
            $deleted = DB::connection("conn_chatify")->table("chatify_convo")
                ->where([
                    'project_refid' => config('jtchatifyconfig.project_refid'),
                    'convo_refid' => $convo_refid
                ])
                ->delete();

            if ($deleted) {
                /** Also delete related members */
                DB::connection("conn_chatify")->table("chatify_member")
                    ->where([
                        'project_refid' => config('jtchatifyconfig.project_refid'),
                        'convo_refid' => $convo_refid
                    ])
                    ->delete();
                
                /** Also delete related messages */
                DB::connection("conn_chatify")->table("chatify_messages")
                    ->where([
                        'project_refid' => config('jtchatifyconfig.project_refid'),
                        'convo_refid' => $convo_refid
                    ])
                    ->delete();

                return response()->json([
                    'success' => true,
                    'message' => 'Conversation deleted successfully'
                ], 200);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Conversation not found'
                ], 404);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting conversation: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Fetch all conversations for a specific user (paginated)
     */
    public function fetchConvo(Request $request, $user_refid)
    {
        try {
            $perPage = $request->input('per_page', config('jtchatifyconfig.fetch_paginate_max', 25));
            
            // Get conversations where the user is a member
            $conversations = DB::connection("conn_chatify")
                ->table("chatify_convo")
                ->select([
                    'chatify_convo.dataid',
                    'chatify_convo.project_refid',
                    'chatify_convo.convo_refid',
                    'chatify_convo.convo_theme',
                    'chatify_convo.name',
                    'chatify_convo.created_by',
                    'chatify_convo.created_at',
                    'chatify_convo.last_activity',
                    'chatify_convo.last_message',
                    'chatify_convo.active'
                ])
                ->join('chatify_member', function($join) {
                    $join->on('chatify_convo.convo_refid', '=', 'chatify_member.convo_refid')
                         ->on('chatify_convo.project_refid', '=', 'chatify_member.project_refid');
                })
                ->where([
                    'chatify_convo.project_refid' => config('jtchatifyconfig.project_refid'),
                    'chatify_member.user_refid' => $user_refid,
                    'chatify_convo.active' => '1'
                ])
                ->orderBy('chatify_convo.last_activity', 'DESC')
                ->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $conversations
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching conversations: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Fetch a single conversation by convo_refid
     */
    public function fetchSingleConvo($convo_refid)
    {
        try {
            $conversation = DB::connection("conn_chatify")->table("chatify_convo")
                ->select([
                    'dataid',
                    'project_refid',
                    'convo_refid',
                    'convo_theme',
                    'name',
                    'created_by',
                    'created_at',
                    'last_activity',
                    'last_message',
                    'active'
                ])
                ->where([
                    'project_refid' => config('jtchatifyconfig.project_refid'),
                    'convo_refid' => $convo_refid
                ])
                ->first();

            if ($conversation) {
                return response()->json([
                    'success' => true,
                    'data' => $conversation
                ], 200);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Conversation not found'
                ], 404);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching conversation: ' . $e->getMessage()
            ], 500);
        }
    }
}
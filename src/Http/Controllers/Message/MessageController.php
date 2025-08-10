<?php

namespace Jazer\Chatify\Http\Controllers\Message;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Jazer\Chatify\Http\Events\ChatEvent;
use Illuminate\Support\Facades\Validator;

class MessageController extends Controller
{
    /**
     * Create a new message
     */
    public function createMessage(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'convo_refid' => 'required|string|max:26',
                'content_type' => 'required|in:REG_TXT,SGL_IMG,MTL_IMG,REG_LINK,WEB_LINK,FORWARDED,REPLY',
                'content_json' => 'required|json',
                'created_by' => 'required|string|max:26'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $project_refid = $request->input('project_refid', config('jtchatifyconfig.project_refid'));

            // Check if conversation exists
            $conversationExists = DB::connection("conn_chatify")->table("chatify_convo")
                ->where([
                    'project_refid' => $project_refid,
                    'convo_refid' => $request->input('convo_refid'),
                    'active' => '1'
                ])
                ->exists();

            if (!$conversationExists) {
                return response()->json([
                    'success' => false,
                    'message' => 'Conversation not found or inactive'
                ], 404);
            }

            $messageId = DB::connection("conn_chatify")->table("chatify_messages")->insertGetId([
                'project_refid' => $project_refid,
                'convo_refid' => $request->input('convo_refid'),
                'content_type' => $request->input('content_type'),
                'content_json' => $request->input('content_json'),
                'created_at' => now(),
                'created_by' => $request->input('created_by'),
                'pinned' => $request->input('pinned', '0')
            ]);

            if ($messageId) {
                // Update conversation's last activity and last message
                $contentData = json_decode($request->input('content_json'), true);
                $lastMessage = isset($contentData['text']) ? substr($contentData['text'], 0, 60) : 'Media message';
                
                DB::connection("conn_chatify")->table("chatify_convo")
                    ->where([
                        'project_refid' => $project_refid,
                        'convo_refid' => $request->input('convo_refid')
                    ])
                    ->update([
                        'last_activity' => now(),
                        'last_message' => $lastMessage
                    ]);

                // Broadcast the message
                event(new ChatEvent(
                    $project_refid,
                    $request->input('convo_refid'),
                    $request->input('content_json'),
                    $request->input('created_by')
                ));

                return response()->json([
                    'success' => true,
                    'message' => 'Message created successfully',
                    'message_id' => $messageId
                ], 201);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to create message'
                ], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error creating message: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update an existing message
     */
    public function updateMessage(Request $request, $messageId)
    {
        try {
            $validator = Validator::make($request->all(), [
                'content_json' => 'sometimes|required|json',
                'pinned' => 'sometimes|in:0,1'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $updateData = [];
            
            if ($request->has('content_json')) {
                $updateData['content_json'] = $request->input('content_json');
            }
            if ($request->has('pinned')) {
                $updateData['pinned'] = $request->input('pinned');
            }

            if (empty($updateData)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No data provided for update'
                ], 400);
            }

            $updated = DB::connection("conn_chatify")->table("chatify_messages")
                ->where([
                    'dataid' => $messageId,
                    'project_refid' => config('jtchatifyconfig.project_refid')
                ])
                ->update($updateData);

            if ($updated) {
                return response()->json([
                    'success' => true,
                    'message' => 'Message updated successfully'
                ], 200);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Message not found or no changes made'
                ], 404);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating message: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete a message
     */
    public function deleteMessage($messageId)
    {
        try {
            $deleted = DB::connection("conn_chatify")->table("chatify_messages")
                ->where([
                    'dataid' => $messageId,
                    'project_refid' => config('jtchatifyconfig.project_refid')
                ])
                ->delete();

            if ($deleted) {
                return response()->json([
                    'success' => true,
                    'message' => 'Message deleted successfully'
                ], 200);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Message not found'
                ], 404);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting message: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Fetch messages for a conversation (paginated)
     */
    public function fetchMessages(Request $request, $convo_refid)
    {
        try {
            $perPage = $request->input('per_page', config('jtchatifyconfig.fetch_paginate_max', 25));

            $messages = DB::connection("conn_chatify")->table("chatify_messages")
                ->select([
                    'dataid',
                    'project_refid',
                    'convo_refid',
                    'content_type',
                    'content_json',
                    'created_at',
                    'created_by',
                    'pinned'
                ])
                ->where([
                    'project_refid' => config('jtchatifyconfig.project_refid'),
                    'convo_refid' => $convo_refid
                ])
                ->orderBy('dataid', 'desc')
                ->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $messages
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching messages: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Fetch a single message
     */
    public function fetchSingleMessage($messageId)
    {
        try {
            $message = DB::connection("conn_chatify")->table("chatify_messages")
                ->select([
                    'dataid',
                    'project_refid',
                    'convo_refid',
                    'content_type',
                    'content_json',
                    'created_at',
                    'created_by',
                    'pinned'
                ])
                ->where([
                    'dataid' => $messageId,
                    'project_refid' => config('jtchatifyconfig.project_refid')
                ])
                ->first();

            if ($message) {
                return response()->json([
                    'success' => true,
                    'data' => $message
                ], 200);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Message not found'
                ], 404);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching message: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Pin/Unpin a message
     */
    public function togglePinMessage($messageId)
    {
        try {
            $message = DB::connection("conn_chatify")->table("chatify_messages")
                ->select('pinned')
                ->where([
                    'dataid'            => $messageId,
                    'project_refid'     => config('jtchatifyconfig.project_refid')
                ])
                ->first();

            if (!$message) {
                return response()->json([
                    'success' => false,
                    'message' => 'Message not found'
                ], 404);
            }

            $newPinnedStatus = $message->pinned === '1' ? '0' : '1';

            $updated = DB::connection("conn_chatify")->table("chatify_messages")
                ->where([
                    'dataid' => $messageId,
                    'project_refid' => config('jtchatifyconfig.project_refid')
                ])
                ->update(['pinned' => $newPinnedStatus]);

            if ($updated) {
                return response()->json([
                    'success' => true,
                    'message' => $newPinnedStatus === '1' ? 'Message pinned successfully' : 'Message unpinned successfully',
                    'pinned' => $newPinnedStatus
                ], 200);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update message'
                ], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error toggling pin status: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Fetch pinned messages for a conversation
     */
    public function fetchPinnedMessages($convo_refid)
    {
        try {
            $pinnedMessages = DB::connection("conn_chatify")->table("chatify_messages")
                ->select([
                    'dataid',
                    'project_refid',
                    'convo_refid',
                    'content_type',
                    'content_json',
                    'created_at',
                    'created_by',
                    'pinned'
                ])
                ->where([
                    'project_refid' => config('jtchatifyconfig.project_refid'),
                    'convo_refid' => $convo_refid,
                    'pinned' => '1'
                ])
                ->orderBy('created_at', 'DESC')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $pinnedMessages
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching pinned messages: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Search messages in a conversation
     */
    public function searchMessages(Request $request, $convo_refid)
    {
        try {
            $searchTerm = $request->input('search');
            $contentType = $request->input('content_type');
            $perPage = $request->input('per_page', config('jtchatifyconfig.fetch_paginate_max', 25));

            if (empty($searchTerm)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Search term is required'
                ], 400);
            }

            $query = DB::connection("conn_chatify")->table("chatify_messages")
                ->select([
                    'dataid',
                    'project_refid',
                    'convo_refid',
                    'content_type',
                    'content_json',
                    'created_at',
                    'created_by',
                    'pinned'
                ])
                ->where([
                    'project_refid' => config('jtchatifyconfig.project_refid'),
                    'convo_refid' => $convo_refid
                ])
                ->where('content_json', 'LIKE', '%' . $searchTerm . '%');

            if ($contentType) {
                $query->where('content_type', $contentType);
            }

            $messages = $query->orderBy('created_at', 'DESC')
                            ->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $messages
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error searching messages: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get message count for a conversation
     */
    public function getMessageCount($convo_refid)
    {
        try {
            $count = DB::connection("conn_chatify")->table("chatify_messages")
                ->where([
                    'project_refid' => config('jtchatifyconfig.project_refid'),
                    'convo_refid' => $convo_refid
                ])
                ->count();

            return response()->json([
                'success' => true,
                'data' => [
                    'convo_refid' => $convo_refid,
                    'message_count' => $count
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error getting message count: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bulk delete messages
     */
    public function bulkDeleteMessages(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'message_ids' => 'required|array|min:1',
                'message_ids.*' => 'integer'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $messageIds = $request->input('message_ids');

            $deleted = DB::connection("conn_chatify")->table("chatify_messages")
                ->where('project_refid', config('jtchatifyconfig.project_refid'))
                ->whereIn('dataid', $messageIds)
                ->delete();

            return response()->json([
                'success' => true,
                'message' => "Successfully deleted {$deleted} messages",
                'deleted_count' => $deleted
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error bulk deleting messages: ' . $e->getMessage()
            ], 500);
        }
    }
}
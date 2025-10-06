<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use Illuminate\Http\Request;

class ConversationController extends Controller
{
    public function index(Request $request)
    {
        // return response()->json(['message' => 'testest', 'userId' => $request->userId]);
        $conversations = Conversation::with(['user1Data', 'user2Data'])->where('user1', $request->userId)->orWhere('user2', $request->userId)->get()->map(function ($conversation) use ($request) {
            $conversation->other_user = $conversation->user1 == $request->userId ? $conversation->user2Data : $conversation->user1Data;
            return $conversation->makeHidden(['user1Data', 'user2Data']);
        });
        return response()->json(['message' => 'the conversations has been retrived succssfully', 'conversations' => $conversations]);
    }
    public function conversationMessages(Request $request)
    {
        $conversation = Conversation::with('messages')
            ->where(function ($query) use ($request) {
                $query->where('user1', $request->authUserId)
                    ->where('user2', $request->textUserId);
            })
            ->orWhere(function ($query) use ($request) {
                $query->where('user1', $request->textUserId)
                    ->where('user2', $request->authUserId);
            })
            ->get();
        // return response()->json(['test user getting :' => $request->textUserId.'^^^^'.$request->authUserId]);
        return response()->json(['message' => 'the conversation has been retrived succssfully', 'conversation' => $conversation]);
    }
}

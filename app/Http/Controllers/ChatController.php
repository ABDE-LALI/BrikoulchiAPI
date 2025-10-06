<?php

namespace App\Http\Controllers;

use App\Models\ChatMessage;
use App\Events\ChatMessageSent;
use App\Events\MessageSent;
use App\Models\Conversation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    public function index()
    {
        return ChatMessage::with('user')->latest()->take(50)->get();
    }

    public function store(Request $request): Response
    {
        ChatMessage::create($request->toArray());
        $receiver = User::find($request->receiver);
        $sender = User::find($request->sender);
        $conversation = Conversation::find($request->conversation_id);
        broadcast(new MessageSent($receiver, $sender, $conversation, $request->message));
        return response()->noContent();
        // $request->validate([
        //     'message' => 'required|string|max:1000',
        // ]);

        // $message = ChatMessage::create([
        //     'user_id' => Auth::id(),
        //     'message' => $request->message,
        // ]);

        // broadcast(new ChatMessageSent($message))->toOthers();

    }
}


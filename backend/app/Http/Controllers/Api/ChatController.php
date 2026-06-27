<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function conversations()
    {
        $user = auth()->user();

        if ($user->isShopOwner() && $user->shop) {
            $conversations = Conversation::with(['user', 'shop', 'latestMessage', 'product'])
                ->where('shop_id', $user->shop->id)
                ->orderBy('last_message_at', 'desc')
                ->paginate(20);
        } else {
            $conversations = Conversation::with(['shop', 'latestMessage', 'product'])
                ->where('user_id', $user->id)
                ->orderBy('last_message_at', 'desc')
                ->paginate(20);
        }

        return response()->json($conversations);
    }

    public function startConversation(Request $request)
    {
        $request->validate([
            'shop_id' => 'required|exists:shops,id',
            'product_id' => 'nullable|exists:products,id',
            'message' => 'required|string',
        ]);

        $user = auth()->user();

        $conversation = Conversation::firstOrCreate(
            ['user_id' => $user->id, 'shop_id' => $request->shop_id],
            ['product_id' => $request->product_id]
        );

        $message = Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $user->id,
            'message' => $request->message,
            'type' => 'text',
        ]);

        $conversation->update(['last_message_at' => now()]);

        return response()->json([
            'conversation' => $conversation->load(['shop', 'latestMessage']),
            'message' => $message,
        ]);
    }

    public function messages($conversationId)
    {
        $user = auth()->user();
        $conversation = Conversation::findOrFail($conversationId);

        $isOwner = $user->id === $conversation->user_id;
        $isShop = $user->shop && $user->shop->id === $conversation->shop_id;

        if (!$isOwner && !$isShop) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        Message::where('conversation_id', $conversationId)
            ->where('sender_id', '!=', $user->id)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        $messages = Message::with('sender')
            ->where('conversation_id', $conversationId)
            ->orderBy('created_at', 'asc')
            ->paginate(50);

        return response()->json($messages);
    }

    public function sendMessage(Request $request, $conversationId)
    {
        $user = auth()->user();
        $conversation = Conversation::findOrFail($conversationId);

        $isOwner = $user->id === $conversation->user_id;
        $isShop = $user->shop && $user->shop->id === $conversation->shop_id;

        if (!$isOwner && !$isShop) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $request->validate([
            'message' => 'nullable|string',
            'image' => 'nullable|image|max:5120',
            'voice_note' => 'nullable|file|max:10240',
            'shared_product_id' => 'nullable|exists:products,id',
            'type' => 'nullable|in:text,image,voice,product_share',
        ]);

        $data = [
            'conversation_id' => $conversationId,
            'sender_id' => $user->id,
            'message' => $request->message,
            'type' => $request->get('type', 'text'),
            'shared_product_id' => $request->shared_product_id,
        ];

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('chat/images', 'public');
            $data['type'] = 'image';
        }
        if ($request->hasFile('voice_note')) {
            $data['voice_note'] = $request->file('voice_note')->store('chat/voice', 'public');
            $data['type'] = 'voice';
        }

        $message = Message::create($data);
        $conversation->update(['last_message_at' => now()]);

        return response()->json(['message' => $message->load('sender')]);
    }

    public function unreadCount()
    {
        $user = auth()->user();

        if ($user->isShopOwner() && $user->shop) {
            $conversationIds = Conversation::where('shop_id', $user->shop->id)->pluck('id');
        } else {
            $conversationIds = Conversation::where('user_id', $user->id)->pluck('id');
        }

        $count = Message::whereIn('conversation_id', $conversationIds)
            ->where('sender_id', '!=', $user->id)
            ->where('is_read', false)
            ->count();

        return response()->json(['unread_count' => $count]);
    }
}

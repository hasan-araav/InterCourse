<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class PushSubscriptionController extends Controller
{
    /**
     * Get the VAPID public key.
     */
    public function vapidKey(): JsonResponse
    {
        return response()->json([
            'publicKey' => config('webpush.vapid.public_key')
        ]);
    }

    /**
     * Store a push subscription.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'endpoint' => 'required',
            'keys.auth' => 'required',
            'keys.p256dh' => 'required'
        ]);

        $endpoint = $request->endpoint;
        $key = $request->keys['p256dh'];
        $token = $request->keys['auth'];
        $contentEncoding = $request->content_encoding ?? 'aesgcm';

        $request->user()->updatePushSubscription($endpoint, $key, $token, $contentEncoding);

        return response()->json(['success' => true]);
    }

    /**
     * Delete a push subscription.
     */
    public function destroy(Request $request): JsonResponse
    {
        $request->validate(['endpoint' => 'required']);

        $request->user()->deletePushSubscription($request->endpoint);

        return response()->json(['success' => true], 204);
    }
}

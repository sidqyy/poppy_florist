<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\PushSubscription;
use Minishlink\WebPush\WebPush;
use Minishlink\WebPush\Subscription;

class PushNotificationController extends Controller
{
    public function subscribe(Request $request)
    {
        $request->validate([
            'endpoint' => 'required',
            'public_key' => 'required',
            'auth_token' => 'required'
        ]);

        PushSubscription::updateOrCreate(
            ['endpoint' => $request->endpoint],
            [
                'user_id' => Auth::id(),
                'public_key' => $request->public_key,
                'auth_token' => $request->auth_token
            ]
        );

        return response()->json(['success' => true]);
    }

    public function unsubscribe(Request $request)
    {
        PushSubscription::where('endpoint', $request->endpoint)->delete();
        return response()->json(['success' => true]);
    }

    public function notifyFlorist(Request $request)
    {
        $subscriptions = PushSubscription::all();
        
        $auth = [
            'VAPID' => [
                'subject' => 'https://' . ($_SERVER['HTTP_HOST'] ?? 'localhost'),
                'publicKey' => env('VAPID_PUBLIC_KEY'),
                'privateKey' => env('VAPID_PRIVATE_KEY'),
            ],
        ];

        $webPush = new WebPush($auth);

        foreach ($subscriptions as $subscription) {
            $webPush->queueNotification(
                Subscription::create([
                    'endpoint' => $subscription->endpoint,
                    'keys' => [
                        'p256dh' => $subscription->public_key,
                        'auth' => $subscription->auth_token,
                    ],
                ]),
                json_encode([
                    'title' => 'Pesanan Baru!',
                    'body' => 'Ada pesanan baru yang perlu dirangkai',
                    'icon' => '/favicon.ico'
                ], JSON_THROW_ON_ERROR)
            );
        }

        $webPush->flush();
        
        return response()->json(['success' => true]);
    }
} 
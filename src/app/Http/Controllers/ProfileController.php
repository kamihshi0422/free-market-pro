<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\ProfileRequest;
use App\Models\User;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Transaction;
use App\Models\Rating;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function showProfile(Request $request)
    {
        $user = Auth::user();
        $page = $request->query('page','sell');

        // 出品した商品
        $myProducts = Product::where('user_id', $user->id)->get();

        // 購入した商品（取引中 + 完了）
        $purchases = Transaction::with('product')
            ->where('buyer_id', $user->id)
            ->whereIn('status', [0, 1]) // 0:取引中, 1:完了
            ->get();

        // 取引中の商品（ステータス0のみ）
        $transactions = Transaction::with('product')
            ->withCount([
                'messages as unread_messages_count' => function ($messageQuery) {
                    $messageQuery->where('is_read', false);
                }
            ])
            ->with(['messages' => function ($messageQuery) {
                $messageQuery->latest()->limit(1);
            }])
            ->pendingForUser($user->id)
            ->get()
            ->sortByDesc(function ($transaction) {
                return $transaction->messages->first()?->created_at ?? $transaction->updated_at;
            });

        $totalUnreadMessages = $transactions->sum('unread_messages_count');

        // 評価平均
        $ratingStats = Rating::where('to_user_id', $user->id)
            ->select(
                DB::raw('COUNT(*) as count'),
                DB::raw('ROUND(AVG(score)) as average')
            )
            ->first();

        return view('profile', compact(
            'user',
            'page',
            'myProducts',
            'purchases',
            'transactions',
            'totalUnreadMessages',
            'ratingStats'
        ));
    }

    public function editProfile()
    {
        $user = Auth::user();
        $page = null;

        return view('edit_profile', compact('user','page'));
    }

    public function updateProfile(ProfileRequest $request)
    {
        $user = Auth::user();
        $data = $request->only(['name','postcode','address','building']);

        if ($request->hasFile('img')) {
            if ($user->img_url && \Storage::disk('public')->exists($user->img_url)) {
                \Storage::disk('public')->delete($user->img_url);
            }

            $image = $request->file('img')->store('user_images', 'public');
            $data['img_url'] = $image;
        }

        $isFirstProfile = empty($user->postcode) && empty($user->address);
        $user->update($data);

        if ($isFirstProfile) {
            return redirect()->route('top.show');
        }

        return redirect()->route('profile.show');
    }
}
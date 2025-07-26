<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Micropost;
use App\Models\User;

class FavoritesController extends Controller
{
    public function store(string $id)
    {
        // 認証済みユーザー（閲覧者）が、 投稿をお気に入りにする
        \Auth::user()->favorite(intval($id));
        // 前のURLへリダイレクトさせる
        return back();
    }

    public function destroy(string $id)
    {
        // 認証済みユーザー（閲覧者）が、 投稿のをお気に入りを解除する
        \Auth::user()->unfavorite(intval($id));
        // 前のURLへリダイレクトさせる
        return back();
    }

    public function favorites(string $id)
    {
        $user = User::findOrFail($id);
        $favorites = $user->favorites()->paginate(10);
        $user->loadRelationshipCounts();

        return view('users.favorites', [
            'user' => $user,
            'microposts' => $favorites,
        ]);
    }
}

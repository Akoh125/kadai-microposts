<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserFollowController extends Controller
{
    /**
     * ユーザーをフォローするアクション。
     *
     * @param  $id  相手ユーザーのid
     * @return \Illuminate\Http\Response
     */
    public function store(string $id) //例）{id}id=3の場合 web.phpの29行目から渡ってきた{id}の引数が$idの中に代入される。代入されたものを受け取って18行目へ。
    {
        // 認証済みユーザー（閲覧者）が、 idのユーザーをフォローする
        \Auth::user()->follow(intval($id)); //Auth::user()でログインしているuserを取り出す。そのuserが持っているfollowという機能を使いid番号3番をfollowしてね。userモデル(user.php)に作成されているメソッド。
        // 前のURLへリダイレクトさせる
        return back();
    }

    /**
     * ユーザーをアンフォローするアクション。
     *
     * @param  $id  相手ユーザーのid
     * @return \Illuminate\Http\Response
     */
    public function destroy(string $id)
    {
        // 認証済みユーザー（閲覧者）が、 idのユーザーをアンフォローする
        \Auth::user()->unfollow(intval($id));
        // 前のURLへリダイレクトさせる
        return back();
    }
}

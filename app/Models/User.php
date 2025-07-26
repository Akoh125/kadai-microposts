<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /**
     * このユーザーが所有する投稿。（ Micropostモデルとの関係を定義）
     */
    public function microposts()
    {
        return $this->hasMany(Micropost::class);
    }

    /**
     * このユーザーに関係するモデルの件数をロードする。
     */
    public function loadRelationshipCounts()
    {
        $this->loadCount(['microposts', 'followings', 'followers','favorites']);
    }
    
    /**
     * このユーザーがフォロー中のユーザー。（Userモデルとの関係を定義）
     */
    public function followings()
    {
        return $this->belongsToMany(User::class, 'user_follow', 'user_id', 'follow_id')->withTimestamps();
    }
    
    /**
     * このユーザーをフォロー中のユーザー。（Userモデルとの関係を定義）
     */
    public function followers()
    {
        return $this->belongsToMany(User::class, 'user_follow', 'follow_id', 'user_id')->withTimestamps();
    }

    /*＊
     * $userIdで指定されたユーザーをフォローする。
     *
     * @param  int  $userId
     * @return bool
     */

    public function follow(int $userId) //$userIdにid3番が代入される(UserFollowControllerの18行目のfollowで引き渡されたid番号のこと)
    {
        $exist = $this->is_following($userId); //is_followingの関数にも$userIdに3番が入っているため、is_followingを実行しながら3番を渡している。57行目のif文により、呼び出した$existには$this->is_following($userId)のtrueもしくはfalseの値が入っている。
        $its_me = $this->id == $userId; //$this->id今呼出しているuserのidなのでログイン中のidということ。userモデルではthisはuserのインスタンスのこと。ログイン中のidと受け取ったuseridが同じ(3番)かどうか。同じなら$this->id == $userId;がtrue、異なるならfalse。
        
        if ($exist || $its_me) { //あった場合92行目の$thisから>exists();までがtrueになり、ない場合false。→次に54行目へ戻る。$existがtrueであるか(||=orの意)$its_meがtrueであるか。
            return false; //何もしない
        } else {
            $this->followings()->attach($userId); //$this=ログイン中のuser。followings関数を実行し呼び出しつつattachでデータベースで登録。$this(ログイン中のuser)と$userId(3番)が同じならreturn trueへ。
            return true;
        }
    }
    
    /**
     * $userIdで指定されたユーザーをアンフォローする。
     * 
     * @param  int $userId
     * @return bool
     */
    public function unfollow(int $userId)
    {
        $exist = $this->is_following($userId);
        $its_me = $this->id == $userId;
        
        if ($exist && !$its_me) {
            $this->followings()->detach($userId);
            return true;
        } else {
            return false;
        }
    }
    
    /**
     * 指定された$userIdのユーザーをこのユーザーがフォロー中であるか調べる。フォロー中ならtrueを返す。
     * 
     * @param  int $userId
     * @return bool
     */
    public function is_following(int $userId)
    {
        return $this->followings()->where('follow_id', $userId)->exists(); //followings関数を実行しながらfollow_idの列に3番に値するものがあるかどうか探している関数がwhere。existsで有無を判定。→次に57行目の$existsへ。
    }

    /**
     * このユーザーとフォロー中ユーザーの投稿に絞り込む。
     */
    public function feed_microposts()
    {
        // このユーザーがフォロー中のユーザーのidを取得して配列にする
        $userIds = $this->followings()->pluck('users.id')->toArray();
        // このユーザーのidもその配列に追加
        $userIds[] = $this->id;
        // それらのユーザーが所有する投稿に絞り込む
        return Micropost::whereIn('user_id', $userIds);
    }

    public function favorites()
    {
        return $this->belongsToMany(Micropost::class, 'favorites', 'user_id', 'micropost_id')->withTimestamps();
    }

    public function favorite(int $micropostId) //$micropostIdになるのはお気に入り機能の対象が投稿だから。
    {
        $exist = $this->is_favorite($micropostId);
        //$its_me = $this->id == $userId; $its_me自分自身か？のチェックは不要。投稿idが対象なのでuseridとの比較が不要。
        
        if ($exist) {
            return false;
        } else {
            $this->favorites()->attach($micropostId);
            return true;
        }
    }
    
    public function unfavorite(int $micropostId)
    {
        $exist = $this->is_favorite($micropostId);
        //$its_me = $this->id == $userId;　116行目と同様の理由
        
        if ($exist) {
            $this->favorites()->detach($micropostId);
            return true;
        } else {
            return false;
        }
    }

    public function is_favorite($micropostId)
    {
    return $this->favorites()->where('micropost_id', $micropostId)->exists();
    }
}
<?php

//use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\UsersController;
use App\Http\Controllers\MicropostsController;
use App\Http\Controllers\UserFollowController;
use App\Http\Controllers\FavoritesController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', [MicropostsController::class, 'index']);

Route::get('/dashboard', [MicropostsController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

Route::group(['middleware' => ['auth']], function () {

    Route::prefix('users/{id}')->group(function () { //29-32共通部分を前に出している　※{id}部分にはid=1、２、３など数字が入る(固定ではないid番号が入る)。例）{id}id=3の場合で順序を読み解く
        Route::post('follow', [UserFollowController::class, 'store'])->name('user.follow'); //users/{id}/follow 
        Route::delete('unfollow', [UserFollowController::class, 'destroy'])->name('user.unfollow'); ///users/{id}/unfollow
        Route::get('followings', [UsersController::class, 'followings'])->name('users.followings'); ///users/{id}/followings
        Route::get('followers', [UsersController::class, 'followers'])->name('users.followers'); ///users/{id}/follows
        Route::get('favorites', [FavoritesController::class, 'favorites'])->name('users.favorites'); ///users/{id}/favorites お気に入り投稿一覧ページ
    });

    Route::resource('users', UsersController::class, ['only' => ['index', 'show']]);
    //Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    //Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    //Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    Route::resource('microposts', MicropostsController::class, ['only' => ['store', 'destroy']]);

    Route::prefix('microposts/{id}')->group(function () {
        Route::post('favorites', [FavoritesController::class, 'store'])->name('favorites.favorite');
        Route::delete('unfavorite', [FavoritesController::class, 'destroy'])->name('favorites.unfavorite');
    });
});

require __DIR__.'/auth.php';

//補足
//route：post、delete、get ブラウザを共有して何かのページを頂戴というときのRQの送り方に種類がある
//post：このデータを与えるのでその作業の結果のページを頂戴
//get：このURLに該当するページを頂戴
//29行目の読み砕き文：例.{id}id=3の場合users/3/followに対してpostのRQがあったらUserFollowControllerのstoreのアクションを実行してね。
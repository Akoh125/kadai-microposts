@extends('layouts.app')

@section('content')
    @if (Auth::check())
        <div class="sm:grid sm:grid-cols-3 sm:gap-10">
            <aside class="mt-4">
                {{-- ユーザー情報 --}}
                @include('users.card')
            </aside>
            <div class="sm:col-span-2">
                {{-- 投稿フォーム --}}
                @include('microposts.form')
                {{-- 投稿一覧 --}}
                @include('microposts.microposts')
        </div>
    @else
        <div class="prose hero bg-base-200 mx-auto max-w-full rounded">
            <div class="hero-content text-center my-10">
                <div class="max-w-md mb-10">
                    <h2>Welcome to the Microposts</h2>
                    {{-- ユーザー登録ページへのリンク --}}
                    <a class="btn btn-primary btn-lg normal-case" href="{{ route('register') }}">Sign up now!</a>
                </div>
            </div>
        </div>
    @endif
@endsection

{{-- @include('microposts.microposts') --}}

{{-- Blade テンプレートの記法で --}}
{{-- ・最初の microposts はフォルダ名（resources/views/microposts） --}}
{{-- ・2つ目の microposts はファイル名（microposts.blade.php） --}}
{{-- ・.blade.php の拡張子は省略して書くのがルール --}}
@extends('layouts.app')

@section('content')
    <front-cart></front-cart>

    {{-- 測試登出 --}}
    {{-- <form method="POST" action="/logout">
        @csrf
        <button type="submit">Logout</button>
    </form> --}}
@endsection

@section('inline_js')
    @parent
@endsection

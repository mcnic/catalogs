@extends('layouts.vue')

@section('title')
    {{ config('app.name') }} - main 
@endsection

@section('sidebar')
    @parent
    <p>Это дополнение к основной боковой панели.</p>
@endsection

@section('content')
    <p>main page</p>
    <a href= {{ route('home') }}> home </a>
@endsection
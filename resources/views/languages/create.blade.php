@extends('layouts.app')

@section('content')
    <h1 class="text-center">Language Create</h1>

    <form action="/languages" method="POST">
        {{ csrf_field() }}
        @include('languages.form')
        <div class="medium-4 columns centered">
            <input type="submit" class="button">
        </div>
    </form>

@endsection

@section('footer')

@endsection
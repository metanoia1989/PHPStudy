@extends('../common/layout')
@section('navbar')
    @parent
    <p>This is the append navbar.</p>
@endsection
@section('content')
    <div class="">
        this is the content!!!
        @include('../common/sidebar')
    </div>
@endsection

@extends('frontend.home')

@section('main-content')
    <div class="container">
        <h1>Chat Rooms</h1>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <a href="{{ route('rooms.create') }}" class="btn btn-primary">Create Room</a>
        
        <ul>
            @foreach($rooms as $room)
                <li>
                    <a href="{{ route('rooms.show', $room->id) }}">{{ $room->name }}</a>
                </li>
            @endforeach
        </ul>
    </div>
@endsection

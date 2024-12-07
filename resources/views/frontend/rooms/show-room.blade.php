@extends('frontend.home')

@section('main-content')
    <div class="container">
        <h1>Room: {{ $room->name }}</h1>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="messages">
            @foreach($messages as $message)
                <p><strong>{{ $message->user->name }}:</strong> {{ $message->message }}</p>
            @endforeach
        </div>

        <form action="{{ route('rooms.sendMessage', $room->id) }}" method="POST">
            @csrf
            <div class="form-group">
                <input type="text" name="message" class="form-control" placeholder="Type a message..." required>
            </div>
            <button type="submit" class="btn btn-primary">Send</button>
        </form>

        <form action="{{ route('rooms.addUser', $room) }}" method="POST">
            @csrf
            <input type="text" name="user_id" placeholder="Enter user ID">
            <button type="submit">Add User to Room</button>
        </form>
    </div>
@endsection

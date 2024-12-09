@extends('frontend.home')

@section('main-content')
<div class="container">
    <h2>{{ $room->name }}</h2>
    
    <!-- Hiển thị thông tin phòng -->
    <p><strong>Thành viên:</strong></p>
    <ul>
        @foreach ($room->users as $user)
            <li>{{ $user->name }}</li>
        @endforeach
    </ul>

    <h4>Thông báo</h4>
    <div class="messages">
        @foreach ($room->messages as $message)
            <div class="message">
                <p><strong>{{ $message->user->name }}:</strong> {{ $message->message }}</p>
                <p><small>{{ $message->created_at->format('H:i d/m/Y') }}</small></p>
            </div>
        @endforeach
    </div>

    <!-- Form gửi tin nhắn -->
    <form action="{{ route('rooms.send_message', $room->id) }}" method="POST">
        @csrf
        <div class="form-group">
            <textarea class="form-control" name="message" rows="3" placeholder="Nhập tin nhắn..." required></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Gửi tin nhắn</button>
    </form>
</div>
@endsection

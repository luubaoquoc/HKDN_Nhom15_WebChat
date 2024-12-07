@extends('frontend.home')

@section('main-content')
    <div class="container">
        <h1>Create Room</h1>

        <form action="{{ route('rooms.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="name">Room Name</label>
                <input type="text" class="form-control" id="name" name="name" required>
            </div>
            <button type="submit" class="btn btn-success">Create Room</button>
        </form>
    </div>
@endsection

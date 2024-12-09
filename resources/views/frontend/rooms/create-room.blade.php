@extends('frontend.home')

@section('main-content')
<div class="container">
    <h2>Tạo phòng mới</h2>
    <form action="{{ route('rooms.store') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="name">Tên phòng</label>
            <input type="text" class="form-control" id="name" name="name" required>
        </div>

        <div class="form-group">
            <label for="members">Chọn thành viên</label>
            <select multiple class="form-control" id="members" name="members[]">
                @foreach ($users as $user)
                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                @endforeach
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Tạo phòng</button>
    </form>
</div>
@endsection

@extends('admin.master')
@section('title','Trang Danh Sách')

@section('main-content')
<h1>Chỉnh sửa phòng</h1>

<form action="{{ route('rooms.update', $room->id) }}" method="POST">
    @csrf
    @method('PUT')

    <label for="name">Tên phòng:</label>
    <input type="text" name="name" id="name" value="{{ $room->name }}" required>

    <label for="create_by">Người tạo:</label>
    <select name="create_by" id="create_by" required>
        @foreach ($users as $user)
            <option value="{{ $user->id }}" @if ($user->id == $room->create_by) selected @endif>
                {{ $user->name }}
            </option>
        @endforeach
    </select>

    <button type="submit">Cập nhật</button>
</form>
@endsection

@extends('admin.master')
@section('title','Trang Danh Sách')

@section('main-content')
<h1>Tạo mới phòng</h1>

<form action="{{ route('rooms.store') }}" method="POST">
    @csrf
    <label for="name">Tên phòng:</label>
    <input type="text" name="name" id="name" required>

    <label for="name">Mô tả:</label>
    <input type="text" name="description" id="description" required>

    <label for="create_by">Người tạo:</label>
    <select name="create_by" id="create_by" required>
        @foreach ($users as $user)
            <option value="{{ $user->id }}">{{ $user->name }}</option>
        @endforeach
    </select>

    <button type="submit">Tạo phòng</button>
</form>
@endsection

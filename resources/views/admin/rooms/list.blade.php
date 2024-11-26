@extends('admin.master')
@section('title','Trang Danh Sách')

@section('main-content')
<h1>Danh sách phòng</h1>
<a href="{{ route('rooms.create') }}" class="btn btn-primary">Thêm mới</a>

@if (session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<table class="table">
    <thead>
        <tr>
            <th>ID</th>
            <th>Tên phòng</th>
            <th>Người tạo</th>
            <th>Hành động</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($rooms as $room)
        <tr>
            <td>{{ $room->id }}</td>
            <td>{{ $room->name }}</td>
            <td>{{ $room->creator->name }}</td>
            <td>
                <a href="{{ route('rooms.show', $room->id) }}" class="btn btn-info">Xem</a>
                <a href="{{ route('rooms.edit', $room->id) }}" class="btn btn-warning">Sửa</a>
                <form action="{{ route('rooms.destroy', $room->id) }}" method="POST" style="display:inline-block;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger" onclick="return confirm('Bạn có chắc muốn xóa?')">Xóa</button>
                </form>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

{{ $rooms->links() }}
@endsection

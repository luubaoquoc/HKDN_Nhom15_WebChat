@extends('admin.master')
@section('title','Trang Danh Sách')

@section('main-content')
<h1>Chi tiết phòng</h1>

<p><strong>ID:</strong> {{ $room->id }}</p>
<p><strong>Tên phòng:</strong> {{ $room->name }}</p>
<p><strong>Người tạo:</strong> {{ $room->creator->name ?? 'Không xác định' }}</p>
<p><strong>Thời gian tạo:</strong> {{ $room->created_at->format('d/m/Y H:i:s') }}</p>
<p><strong>Cập nhật lần cuối:</strong> {{ $room->updated_at->format('d/m/Y H:i:s') }}</p>

<a href="{{ route('rooms.edit', $room->id) }}">Chỉnh sửa</a>
<a href="{{ route('rooms.index') }}">Quay lại danh sách</a>
@endsection

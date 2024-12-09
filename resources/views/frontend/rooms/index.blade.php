@extends('frontend.home')

@section('main-content')
<div class="container">
    <h2>Danh sách phòng</h2>

    <!-- Button to trigger modal -->
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createRoomModal">
        Create Room
    </button>

    <!-- Modal -->
    <div class="modal fade" id="createRoomModal" tabindex="-1" aria-labelledby="createRoomModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createRoomModalLabel">Tạo Phòng Mới</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('rooms.store') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="room_name">Tên phòng</label>
                            <input type="text" class="form-control" name="name" id="room_name" placeholder="Nhập tên phòng" required>
                        </div>

                        <div class="form-group mt-3">
                            <label for="users">Chọn thành viên</label>
                            <select multiple class="form-control" name="users[]" id="users" required>
                                @foreach ($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group mt-3">
                            <button type="submit" class="btn btn-success">Tạo phòng</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Hiển thị danh sách phòng -->
    <div class="mt-3">
        <h4>Danh sách phòng</h4>
        <div class="list-group">
            @foreach ($rooms as $room)
                <a href="{{ route('rooms.show', $room->id) }}" class="list-group-item list-group-item-action">
                    {{ $room->name }}
                </a>
            @endforeach
        </div>
    </div>
</div>
@endsection
{{-- @section('scripts')
<!-- Include Bootstrap JS and other necessary scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
@endsection --}}
@section('scripts')
<!-- Include jQuery (nếu chưa có) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
$(document).ready(function(){
    // Gửi dữ liệu form bằng AJAX khi tạo phòng mới
    $("#createRoomForm").submit(function(e){
        e.preventDefault();

        var form = $(this);
        $.ajax({
            url: form.attr("action"),
            type: "POST",
            data: form.serialize(),
            success: function(response) {
                // Đóng modal sau khi tạo phòng thành công
                $('#createRoomModal').modal('hide');
                
                // Thêm phòng mới vào danh sách mà không reload trang
                var newRoom = '<a href="/rooms/' + response.room.id + '" class="list-group-item list-group-item-action">' + response.room.name + '</a>';
                $("#roomList").append(newRoom);
            },
            error: function(xhr) {
                alert("Có lỗi xảy ra. Vui lòng thử lại.");
            }
        });
    });
});
</script>
@endsection

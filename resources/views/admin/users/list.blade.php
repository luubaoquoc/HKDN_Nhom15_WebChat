@extends('admin.master')
@section('title','Trang Danh Sách')

@section('main-content')
<div class="container-fluid">

    <div class="row">
        <h1 style="text-align: center">Thêm mới người dùng </h1>
        <div class="col-md-12">
            <a href="{{route('users.create')}}" class="btn btn-primary" data-color="green">Thêm Người Dùng</a>
        </div>
        <div class="col-md-12">
            <div class="card">
            <div class="card-header card-header-primary">
                <h4 class="card-title ">Danh Sách Người Dùng</h4>
                {{-- <p class="card-category"> Here is a subtitle for this table</p> --}}
            </div>
            <div class="card-body">
                <div class="table-responsive table-hover">
                <table class="table">
                    <thead class=" text-primary">
                        {{-- <th>ID</th> --}}
                        <th>Tên Người Dùng</th>
                        <th>Email</th>
                        <th>Vai Trò</th>
                        <th></th>
                    </thead>
                    <tbody>
                    @foreach ($data as $item)
                    <tr>
                        {{-- <td>{{$item->id}}</td> --}}
                        <input type="hidden" value="{{$item->id}}" class="id_delete">
                        <td>{{$item->name}}</td>
                        <td>{{$item->email}}</td>   
                        @php
                            $roles = [0 => 'Normal User', 1 => 'Moderate User', 2 => 'Admin'];
                        @endphp
                        <td>{{ $roles[$item->role] ?? 'Unknown' }}</td>                      
                        <td>
                            <div class="dropdown">
                                <button class="btn btn-primary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                  ...
                                </button>
                                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                    <form action="{{ route('users.edit', $item->id) }}" method="GET" style="display: inline;">
                                        <button type="submit" class="dropdown-item">Sửa</button>
                                    </form>
                                    @if ($item->role != 2)
                                    <form action="{{ route('users.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa người dùng này không?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger">Xóa</button>
                                    </form>
                                    @endif
                                </div>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                    </tbody>
                </table>
                </div>
            </div>
            </div>
            <div class="row">
                <div class="col-md-3 offset-md-4">
                  {{ $data->render("pagination::bootstrap-4") }}
                </div>
            </div>
        </div>
    </div>
  </div>
@endsection

@section('script')
<script>
    $(document).ready(function () {
        $('.button-delete').click(function (e) {
            e.preventDefault();
            var deleteId = $(this).closest('tr').find('.id_delete').val();
            var token = $('input[name=_token]').val();
            // alert(token);
            swal({
                title: "Bạn có chắc sẽ xóa người dùng này",
                icon: "warning",
                buttons: true,
                dangerMode: true,
                })
            .then((willDelete) => {
                if (willDelete) {
                    $.ajax({
                        type: 'DELETE',
                        url: '/admin/users/'+deleteId,
                        data: {
                            '_token': token,
                            'id': deleteId,
                        },
                        success: function (response) {
                            swal(response.msgSuccess, {
                                icon: "success",
                            })
                            .then((willDelete) => location.reload())
                        }
                    })
                }
            });
        })
    })
</script>
@endsection


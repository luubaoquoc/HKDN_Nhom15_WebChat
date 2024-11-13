@extends('admin.master')
@section('title','Trang thông tin cá nhân')

@section('main-content')
<div class="container-fluid">
    
    <div class="row">
        <div class="col-md-12">
            <div class="card">
            <div class="card-header card-header-primary">
                <h2 class="card-title " style="text-align: center">Thông Tin Cá Nhân</h2>
                {{-- <p class="card-category"> Here is a subtitle for this table</p> --}}
            </div>
            <div class="card-body">
                <div class="table-responsive table-hover">
                <table class="table">
                    <tbody>
                    <tr>
                        <td>Họ Tên</td>
                        <td class="text-primary">{{$data->name}}</td>
                    </tr>
                    <tr>
                        <td>Email</td>
                        <td class="text-primary">{{$data->email}}</td>
                    </tr>
                    <tr>
                        <td>Vai Trò</td>
                        <td class="text-primary">
                            @if ($data->role == 2)
                                Admin
                                @elseif ($data->role == 3)
                                Quản lý sản phẩm
                                @elseif ($data->role_id == 4)
                                Quản lý bài viết
                                @else
                                ""
                            @endif
                        </td>
                    </tr>
                    </tbody>
                </table>
                <a href="{{route('admin.profile.update')}}" class="btn btn-primary">Đổi Thông Tin</a>
                </div>
            </div>
            </div>
        </div>
    </div>
  </div>
@endsection
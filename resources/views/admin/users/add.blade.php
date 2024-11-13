@extends('admin.master')
@section('title','Trang Danh Sách')

@section('main-content')
<div class="content">
    <div class="container-fluid">

      <form action="{{ route('users.index') }}" method="POST">
        @csrf
      <div class="row">
        <div class="col-md-12">
          <div class="card">
            <div class="card-header card-header-primary">
              <h2 class="card-title" style="text-align: center">Thêm Người Dùng</h2>
            </div>
            <div class="card-body">
                <div class="row">
                  <div class="col-md-12">
                    <div class="form-group">
                      <label class="bmd-label-floating">Tên người dùng <span class="text-danger">*</span></label>
                      <input type="text" value="{{ old('name') }}" name="name" class="form-control" placeholder="Họ Tên">
                      @error('name')
                          <span class="text-danger">{{$message}}</span>
                      @enderror
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-12">
                    <div class="form-group">
                      <label class="bmd-label-floating">Email <span class="text-danger">*</span></label>
                      <input type="email" value="{{ old('email') }}" name="email" class="form-control"  placeholder="Email">
                      @error('email')
                          <span class="text-danger">{{$message}}</span>
                      @enderror
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-12">
                    <div class="form-group">
                      <label class="bmd-label-floating">Mật Khẩu <span class="text-danger">*</span></label>
                      <input type="password"  name="password" class="form-control"  placeholder="Mật khẩu">
                      @error('password')
                          <span class="text-danger">{{$message}}</span>
                      @enderror
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-12">
                    <div class="form-group">
                      <label class="bmd-label-floating">Xác Nhận Mật Khẩu <span class="text-danger">*</span></label>
                      <input type="password"  name="confirm-password" class="form-control"  placeholder="Xác Nhận Mật khẩu">
                      @error('confirm-password')
                          <span class="text-danger">{{$message}}</span>
                      @enderror
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-12">
                    <div class="form-group">
                      <label class="bmd-label-floating">Vai Trò <span class="text-danger">*</span></label>
                      <select name="role" class="form-control">
                        <option value="2">Admin</option>
                        <option value="1">Moderate User</option>
                        <option value="0">Normal User</option>
                      </select>
                    </div>
                  </div>
                </div>
          <button type="submit" class="btn btn-primary pull-right">Thêm Người Dùng</button>
          <a href="{{ route('users.index') }}" class="btn btn-primary pull-right">Danh Sách Người Dùng</a>
        <div class="clearfix"></div>
      </div>
      </form>
    </div>
  </div>
@endsection
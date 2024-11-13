@extends('admin.master')
@section('title','Trang chỉnh sửa thông tin cá nhân')

@section('main-content')
<div class="content">
    <div class="container-fluid">

      <form action="{{route('admin.profile.update')}}" method="POST">
        @method('PATCH')
        @csrf
      <div class="row">
        <div class="col-md-12">
          <div class="card">
            <div class="card-header card-header-primary">
              <h2 class="card-title" style="text-align: center">Sửa Thông Tin Cá Nhân</h2>
              {{-- <p class="card-category">Complete your profile</p> --}}
            </div>
            <div class="card-body">
                <div class="row">
                  <div class="col-md-12">
                    <div class="form-group">
                      <label class="bmd-label-floating">Họ Tên <span class="text-danger">*</span></label>
                      <input type="text" name="name" value="{{$data->name}}" class="form-control">
                      @error('name')
                          <span class="text-danger">{{$message}}</span>
                      @enderror
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-12">
                    <div class="form-group">
                      <label class="bmd-label-floating">Email</label>
                      <input type="email" name="email" value="{{$data->email}}" class="form-control" readonly>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-12">
                    <div class="form-group">
                      <label class="bmd-label-floating">Mật Khẩu Cũ <span class="text-danger">*</span></label>
                      <input type="password" name="password_old" class="form-control" placeholder="Mật khẩu cũ">
                      @error('password_old')
                          <span class="text-danger">{{$message}}</span>
                      @enderror
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-12">
                    <div class="form-group">
                      <label class="bmd-label-floating">Mật Khẩu Mới <span class="text-danger">*</span></label>
                      <input type="password" name="password" class="form-control"  placeholder="Mật khẩu mới">
                      @error('password')
                          <span class="text-danger">{{$message}}</span>
                      @enderror
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-12">
                    <div class="form-group">
                      <label class="bmd-label-floating">Nhắc Lại Mật Khẩu <span class="text-danger">*</span></label>
                      <input type="password" name="confirm-password" class="form-control" placeholder="Xác nhận mật khẩu mới">
                      @error('confirm-password')
                          <span class="text-danger">{{$message}}</span>
                      @enderror
                    </div>
                  </div>
                </div>
        <button type="submit" class="btn btn-primary pull-right">Update Thông Tin</button>
        <div class="clearfix"></div>
      </div>
      </form>

    </div>
  </div>
@endsection

@section('script')
  <script>
    function chosseFile(file){
        if(file && file.files[0]){
            var reader = new FileReader()
            reader.onload = function(e){
                $("#image").attr('src', e.target.result)
            }
            reader.readAsDataURL(file.files[0])
        }
    }
  </script>
@endsection
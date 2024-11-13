@extends('admin.master')
@section('title','Trang Danh Sách')

@section('main-content')
<div class="content">
    <div class="container-fluid">

      <form action="{{ route('users.update', $data->id) }}" method="POST">
        @method('PATCH')
        @csrf
      <div class="row">
        <div class="col-md-12">
          <div class="card">
            <div class="card-header card-header-primary">
              <h2 style="text-align: center" class="card-title">Sửa Vai Trò</h2>
            </div>
            <div class="card-body">
              <div class="row">
                <div class="col-md-12">
                  <div class="form-group">
                    <label class="bmd-label-floating">Tên người dùng <span class="text-danger">*</span></label>
                    <input type="text" name="name" value="{{$data->name}}" class="form-control" readonly>
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
                    <input type="email" name="email" value="{{$data->email}}" class="form-control" readonly>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-md-12">
                  <div class="form-group">
                    <label class="bmd-label-floating">Vai trò <span class="text-danger">*</span></label>
                    <select name="role" class="form-control">
                      <option value="2" @if ($data->role == 2)
                          {{"selected"}}
                      @endif>Admin</option>
                      <option value="1" @if ($data->role == 1)
                        {{"selected"}}
                      @endif>Moderate User</option>
                      <option value="0" @if ($data->role == 0)
                        {{"selected"}}
                    @endif>Normal User</option>
                    </select>
                  </div>
                </div>
              </div>
            <button type="submit" class="btn btn-primary pull-right">Sửa Vai Trò</button>
            <a href="{{ route('users.index') }}" class="btn btn-primary pull-right">Danh Sách Người Dùng</a>
        <div class="clearfix"></div>
      </div>
      </form>
    </div>
  </div>
@endsection
<!DOCTYPE html>
<html lang="en" data-bs-theme="">


<!-- Mirrored from connectme-html.themeyn.com/login.html by HTTrack Website Copier/3.x [XR&CO'2014], Sun, 03 Nov 2024 06:23:49 GMT -->
<!-- Added by HTTrack --><meta http-equiv="content-type" content="text/html;charset=utf-8" /><!-- /Added by HTTrack -->
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="images/favicon.ico">
    <!-- Page Title -->
    <title>ResetPassword</title>
    <!-- Page Stylesheets -->
    <link rel="stylesheet" href="{{asset('frontend-assets')}}/assets/css/bundle0ae1.css?v1310">
    <link rel="stylesheet" href="{{asset('frontend-assets')}}/assets/css/app0ae1.css?v1310">
</head>

<body class="tyn-body">
    
    <div class="tyn-root">
        <div class="tyn-content tyn-auth tyn-auth-centered">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-xl-4 col-lg-5 col-md-7 col-sm-9">
                        <p style="color: #c43b68; font-size: 2em">Đặt lại mật khẩu</p>
                            <form action="{{ route('password.update', ['token' => $token]) }}" method="POST">
                            @csrf
                            <input type="hidden" name="token" value="{{ $token }}">

        {{-- <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" class="form-control" required>
            @error('email')
                <span class="text-danger">{{$message}}</span>
            @enderror
        </div> --}}
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="password">Mật khẩu mới:</label>
                                    <input type="password" id="password" name="password" class="form-control" required>
                                    @error('password')
                                        <span class="text-danger">{{$message}}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-group">
                                    <label for="password_confirmation">Xác nhận mật khẩu:</label>
                                    <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" required>
                                </div>
                            </div>

                            <button type="submit" class="btn">Đặt lại mật khẩu</button>
                            </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
</div>
</div><!-- .tyn-root -->

<!-- Page Scripts -->
<script src="{{asset('frontend-assets')}}/assets/js/bundle0ae1.js?v1310"></script>
<script src="{{asset('frontend-assets')}}/assets/js/app0ae1.js?v1310"></script>
{{-- @include('sweetalert::alert') --}}
</body>


<!-- Mirrored from connectme-html.themeyn.com/login.html by HTTrack Website Copier/3.x [XR&CO'2014], Sun, 03 Nov 2024 06:23:49 GMT -->
</html>
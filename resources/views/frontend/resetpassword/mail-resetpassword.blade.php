<div class="container" style="margin-top: 50px; margin-bottom: 50px">
    <div class="row">
<div class="col-md-5">
    <p style="color: #c43b68; font-size: 2em">Quên mật khẩu</p>
    <form action="{{ route('password.email') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" class="form-control" required>
            @error('email')
                <span>{{ $message }}</span>
            @enderror
        </div>
        <button type="submit" class="btn">Gửi link đặt lại mật khẩu</button>
    </form>
</div>
</div>
</div>
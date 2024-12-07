<p>Xin chào {{ $passwordreset->name }},</p>
<p>Vui lòng nhấn vào liên kết bên dưới để reser password:</p>
<a href="{{ route('password.reset', $token) }}">Xác nhận thay đổi password</a>
<p>Nếu bạn không yêu cầu, vui lòng bỏ qua email này.</p>
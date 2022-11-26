@component('mail::message')
# {{ $title }}

Xin chào, {{ $user }}
<br>

Mật khẩu của quý khách đã có sự thay đổi gần đây.
<br>

Nếu yêu cầu thay đổi này không được thực hiện bởi quý khách, vui lòng quý khách nhanh chóng liên lạc với quản trị viên ngay lập tức để tránh gây ra những rủi ro không mong muốn trong tương lai.

Thân ái,<br>
{{ config('app.name') }}
@endcomponent

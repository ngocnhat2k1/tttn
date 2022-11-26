@component('mail::message')
# {{ $title }}

Xin chào, {{ $user }}
<br>

Đây là mã để đổi mật khẩu: <strong> {{ $resetCode }} </strong>
<br>

<strong> Lưu ý: mã chỉ có tác dụng trong vòng 10 phút, nếu quá thời gian nêu trên quý khách bắt buộc phải thực hiện gửi lại yêu cầu khác </strong>
<br>

Nếu yêu cầu thay đổi này không được thực hiện bởi quý khách, vui lòng quý khách nhanh chóng thực hiện đổi mật khẩu ngay lập tức để tránh gây ra những rủi ro không mong muốn trong tương lai.

Thân ái,<br>
{{ config('app.name') }}
@endcomponent

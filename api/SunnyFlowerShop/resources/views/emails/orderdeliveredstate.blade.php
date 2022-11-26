@component('mail::message')
# {{ $title }}

Xin chào, {{ $user }}
<br>

Đơn hàng đã được giao cho khách hàng thành công. Vui lòng kiểm tra đơn hàng trước khi thanh toán.
<br>

Nếu quý khách cảm thấy hài lòng với dịch vụ của shop thì đừng quên cho 5 sao những sản phẩm mà quý khách đã mua nhé.
<br>

<strong>Thông tin đơn hàng:</strong>
<br>

Mã vận chuyển: <strong>{{ $idDelivery }}</strong>
<br>

Tổng giá trị đơn hàng: <strong>{{ $price }}</strong>
<br>

Nếu yêu cầu đặt hàng này không được thực hiện bởi quý khách, vui lòng quý khách nhanh chóng liên lạc với quản trị viên ngay lập tức để
xác thực vế vấn đề này và tránh gây ra những rủi ro không mong muốn trong tương lai.

<strong>Cảm ơn quý khách đã mua hàng ở NPCamera Shop.</strong>
<br>

Thân ái,<br>
{{ config('app.name') }}
@endcomponent

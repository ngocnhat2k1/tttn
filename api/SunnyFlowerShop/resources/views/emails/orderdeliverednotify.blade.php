@component('mail::message')
# {{ $title }}

Xin chào, {{ $user }}
<br>

Đừng quên bấm nút <strong>Xác nhận đơn hàng</strong> trong lịch sử đơn hàng nhé.
<br>

Nếu quý khách cảm thấy hài lòng với dịch vụ của shop thì đừng quên cho 5 sao những sản phẩm mà quý khách đã mua nhé.
<br>

<strong>Thông tin đơn hàng:</strong>
<br>

Mã vận chuyển: <strong>{{ $idDelivery }}</strong>
<br>

Tổng giá trị đơn hàng: <strong>{{ $price }}</strong>
<br>

Nếu quý khách thấy cảm thấy không hài lòng hoặc sản phẩm có vấn đề gì thì quý khách hãy liên hệ với quản trị viên để giải quyết và đổi trả nếu quý khách mong muốn
<br>

Nếu yêu cầu đặt hàng này không được thực hiện bởi quý khách, vui lòng quý khách nhanh chóng liên lạc với quản trị viên ngay lập tức để
xác thực vế vấn đề này và tránh gây ra những rủi ro không mong muốn trong tương lai.
<br>

<strong>Cảm ơn quý khách đã mua hàng ở NPCamera Shop.</strong>
<br>
Thân ái,<br>
{{ config('app.name') }}
@endcomponent

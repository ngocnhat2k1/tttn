@component('mail::message')
# {{ $title }}

Xin chào, {{ $user }} 
<br>

Đơn hàng sẽ được giao cho quý khách sớm nhất có thể.
<br>

<strong> Thông tin đơn hàng: </strong>

Mã vận chuyển: <strong> {{ $idDelivery }} </strong>
@component("mail::table")
| ID  | Name           | Quantity | Price        |
| --  | -------------- | :------: | -----------: |
@for ($i = 0; $i < sizeof($listProducts); $i++)
| {{ $listProducts[$i]['id'] }} | {{ $listProducts[$i]['name'] }} | {{ $listProducts[$i]['quantity'] }} | {{ $listProducts[$i]['price'] }} |
@endfor ($listProducts)
@endcomponent

Tổng giá trị đơn hàng: <strong> {{ $priceOrder }} </strong>
<br>

Nếu yêu cầu này không được thực hiện bởi quý khách, vui lòng quý khách nhanh chóng liên lạc với quản trị viên ngay lập tức để tránh gây ra những rủi ro không mong muốn trong tương lai.

Thân ái,<br>
{{ config('app.name') }}
@endcomponent

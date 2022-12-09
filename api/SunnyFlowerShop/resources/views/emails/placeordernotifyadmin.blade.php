@component('mail::message')

{{ $title }}
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

{{ $text }}

Thân ái,<br>
{{ config('app.name') }}
@endcomponent

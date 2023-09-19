@component('mail::message')
@if (!empty($order['order_number']))
# New Order Created [{{ $order['order_number'] }}]
@else
# New Order Created
@endif

{{ $order['data']['first_name'] }} {{ $order['data']['last_name'] }},

Thank you for placing your order with {{ config('app.company.name') }}! The details of your order are below:

@component('mail::table')
| Shipping address                   | Billing address                    |
| :--------------------------------- | :--------------------------------- |
| {!! $order['data']['shipping_address'] !!} | {!! $order['data']['billing_address'] !!}  |
@endcomponent

<p><strong>Email:</strong> {{ $order['notification_email'] }}</p>
<p><strong>Phone:</strong> {{ $order['data']['phone'] }}</p>

@component('mail::table')
| Product                   | Qty                      | Price                                     |
| :------------------------ | -----------------------: | ----------------------------------------: |
@if (!empty($order['shipping_lines']))
@foreach ($order['product_lines'] as $line)
| {!! $line['comment'] !!}  | {{ $line['quantity'] }}  | {{ $line['formatted']['display_price'] }} |
@endforeach
@endif
@if (!empty($order['shipping_lines']))
@foreach ($order['shipping_lines'] as $line)
| {!! $line['comment'] !!}  | {{ $line['quantity'] }}  | {{ $line['formatted']['display_price'] }} |
@endforeach
@endif
@endcomponent

@component('mail::table')
|                           |                                   |
| ------------------------: | --------------------------------: |
| <strong>Subtotal</strong> | {{ $order['product_subtotal'] }}  |
| <strong>Shipping</strong> | {{ $order['shipping_subtotal'] }} |
| <strong>Tax</strong>      | {{ $order['tax'] }}               |
| <strong>Total</strong>    | {{ $order['total'] }}             |
@endcomponent

Thanks,<br />
{{ config('app.name') }}
@endcomponent

<!DOCTYPE html>
<html>
<head>
    <title>
        {{ $code }}
    </title>
    <style>
        body {
            font-family: 'Roboto', Arial, sans-serif;
            font-size: 15px;
            src: url('/public/fonts/roboto.ttf')  format('truetype')
        }
        .container {
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .content {
            font-size: 12px;
        }

        table {
            width: 100%;
            border-spacing:0;
            border-collapse: collapse;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .text-left {
            text-align: left;
        }

        .text-bold {
            font-weight: bold;
        }

        .pb-1 {
            padding-bottom: .25em;
        }

        .pt-1 {
            padding-top: .25em;
        }

        .big-font {
            font-size: 1.05em;
        }

        .small-font {
            font-size: .9em;
        }

        .brd-bottom {
            border-bottom: 1px solid;
        }
    </style>
</head>
<body>
    <div class="container">
        <table>
            <tr>
                <td class="text-center text-bold big-font" colspan="2">
                    <!-- TODO: RESIZE -->
                    @if($entity['image_url'] != null && $entity['image_url'] != '' && $entity['id'] == 1)
                        <img src="{{ url('/' . $entity['image_url']) }}" alt="Image" width="105" height="105"/>
                    @elseif($entity['image_url'] != null && $entity['image_url'] != '' && $entity['id'] > 1)
                        <img src="{{ url('/' . $entity['image_url']) }}" alt="Image" width="98" height="50"/>
                    @else
                        <img src="{{url('/logo_2.jpeg')}}" alt="Image" width="105" height="105"/>
                    @endif
                    <!-- {{ $entity['name'] }} --> <!-- TODO: Change to images -->
                </td>
            </tr>
            <tr>
                <td class="text-center" colspan="2">
                    {{ $location['name'] }}
                </td>
            </tr>
            <tr>
                <td class="text-center" colspan="2">
                    {{ $location['full_address'] }}
                </td>
            </tr>
            <tr>
                <td class="text-center pb-1" colspan="2">
                    Telp / Wa 0{{ $location['backoffice_phone_number'] }}
                </td>
            </tr>
            <tr>
                <td class="text-bold brd-bottom">
                    No
                </td>
                <td class="text-right text-bold brd-bottom">
                    {{ $sales_no }}
                </td>
            </tr>
            <tr>
                <td class="text-bold pt-1">
                    Tanggal
                </td>
                <td class="text-right pt-1">
                    {{ \Carbon\Carbon::parse($local_paid_at)->format('d/m/Y h:i') }}
                </td>
            </tr>
            <tr>
                <td class="text-bold">
                    Kasir
                </td>
                <td class="text-right">
                    {{ $employee_sales['first_name'] . ' '. $employee_sales['last_name'] }}
                </td>
            </tr>
            <tr>
                <td class="text-bold">
                    Member
                </td>
                <td class="text-right">
                    {{ $customer == null ? '-' : $customer['first_name'] . ' '. $customer['last_name'] }}
                </td>
            </tr>
            <tr>
                <td colspan="2" class="pb-1 brd-bottom">
                <!-- &nbsp; -->
                </td>
            </tr>
            <tr>
                <td colspan="2" class="pt-1">
                <!-- &nbsp; -->
                </td>
            </tr>
            @foreach($sale_transaction_details as $detail)
                <tr>
                    <td colspan="2">
                        {{ $detail['product_name'] }}
                    </td>
                </tr>
                <tr>
                    <td class="text-right small-font">
                        {{ $detail['quantity'] }} x {{ App\Helpers\NumberFormat::money($detail['sell_price']) }}
                    </td>
                    <td class="text-right text-bold">{{ App\Helpers\NumberFormat::money($detail['sell_price'] * $detail['quantity']) }}</td>
                    @if($detail['promo_amount'] > 0 || $detail['discount_amount'] > 0)
                        <tr>
                            <td class="text-right">
                                Diskon
                            </td>
                            <td class="text-right">
                                {{ App\Helpers\NumberFormat::money(($detail['promo_amount'] + $detail['discount_amount']) * -1) }}
                            </td>
                        </tr>
                    @endif
                </tr>
            @endforeach
            <tr>
                <td colspan="2" class="pb-1 brd-bottom">
                <!-- &nbsp; -->
                </td>
            </tr>
            <tr>
                <td>
                    Subtotal
                </td>
                <td class="text-right">{{ App\Helpers\NumberFormat::money($subtotal) }}</td>
            </tr>
            <tr>
                <td>
                    Total Barang
                </td>
                <td class="text-right">{{ App\Helpers\NumberFormat::money($sale_transaction_details_total_item[0]['quantity']) }}</td>
            </tr>
            @if($discount_amount > 0 || $promo_amount > 0)
                <tr>
                    <td>
                        Diskon
                    </td>
                    <td class="text-right">{{ App\Helpers\NumberFormat::money(($discount_amount + $promo_amount) * -1) }}</td>
                </tr>
            @endif
            @if($surcharge_amount > 0)
                <tr>
                    <td>
                        Penyesuaian
                    </td>
                    <td class="text-right">{{ App\Helpers\NumberFormat::money($surcharge_amount) }}</td>
                </tr>
            @endif
            @if($payment_platform_fee > 0)
                <tr>
                    <td>
                        Biaya Pembayaran
                    </td>
                    <td class="text-right">{{ App\Helpers\NumberFormat::money($payment_platform_fee) }}</td>
                </tr>
            @endif
            <tr>
                <td class="text-bold big-font">
                    Total
                </td>
                <td class="text-right text-bold big-font">{{ App\Helpers\NumberFormat::money($net_sales_after_tax + $payment_platform_fee) }}</td>
            </tr>
            <tr>
                <td colspan="2" class="pb-1 brd-bottom">
                <!-- &nbsp; -->
                </td>
            </tr>
            <tr>
                <td class="text-left text-bold pt-1 pb-1" colspan="2">
                    Pembayaran
                </td>
            </tr>
            @foreach($sale_transaction_payments as $payment)
                <tr>
                    <td>
                        {{ $payment['payment_method_name'] }}
                    </td>
                    <td class="text-right">
                        {{ App\Helpers\NumberFormat::money($payment['amount_receive']) }}
                    </td>
                </tr>
                @if($payment['change'] > 0)
                    <tr>
                        <td>
                            Kembalian
                        </td>
                        <td class="text-right">
                            {{ App\Helpers\NumberFormat::money($payment['change']) }}
                        </td>
                    </tr>
                @endif
            @endforeach
            <tr>
                <td colspan="2" class="pb-1 brd-bottom">
                <!-- &nbsp; -->
                </td>
            </tr>
            @if($location['footer'] != null)
                <tr>
                    <td colspan="2" class="text-center text-bold pt-1">
                        {{ $location['footer'] }}
                    </td>
                </tr>
            @endif
            <tr>
                <td colspan="2" class="text-center text-bold pt-1">
                    Lunas
                </td>
            </tr>
            <tr>
                <td colspan="2" class="text-center">
                    {{ \Carbon\Carbon::parse($local_paid_at)->format('d/M/Y h:i') }}
                </td>
            </tr>
        </table>
    </div>
</body>
<script type="text/javascript">
    window.onload = function() { 
        window.print();
        setTimeout(function() { window.close();}, 100);
    }
 </script>
</html>
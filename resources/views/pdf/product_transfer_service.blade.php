@php
    $total_quantity = 0;
    $total_line_amount = 0;
@endphp

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
                <td class="text-center text-bold big-font" colspan="4">
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
                <td colspan="4" class="text-center big-font pb-1 pt-1">
                    Pindah Stock
                </td>
            </tr>
            <tr>
                <td colspan="4" class="pt-1">
                <!-- &nbsp; -->
                </td>
            </tr>
            <tr>
                <td colspan="2" class="text-bold pt-1">
                    No
                </td>
                <td colspan="2" class="text-bold pt-1 text-right">
                    {{ $code }}
                </td>
            </tr>
            <!-- <tr>
                <td colspan="2" class="text-bold pt-1">
                    No
                </td>
                <td colspan="2" class="text-bold pt-1">
                    {{ $code }}
                </td>
            </tr> -->
            <tr>
                <td colspan="2" class="text-bold pt-1">
                    Tanggal
                </td>
                <td colspan="2" class="text-bold pt-1 text-right">
                    Catatan
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    {{ \Carbon\Carbon::parse($local_requested_at)->format('d/m/Y h:i') }}
                </td>
                <td colspan="2" class=" text-right">
                    {{ $request_note }}
                </td>
            </tr>
            <tr>
                <td colspan="2" class="text-bold pt-1">
                    Dari Lokasi
                </td>
                <td colspan="2" class="text-bold pt-1 text-right">
                    Lokasi Tujuan
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    {{ $from_location['name'] }}
                </td>
                <td colspan="2" class=" text-right">
                    {{ $to_location['name'] }}
                </td>
            </tr>
            <tr>
                <td colspan="4" class="pt-1">
                <!-- &nbsp; -->
                </td>
            </tr>
            <tr>
                <th>
                    Barcode
                </th>
                <th>
                    Nama
                </th>
                <th>
                    Harga
                </th>
                <th>
                    Total
                </th>
            </tr>
            @foreach($product_transfer_service_details as $detail)
                <tr>
                    <td>
                        {{ $detail['product_code'] }}
                    </td>
                    <td>
                        {{ $detail['product_name'] }}
                    </td>
                    <td class="text-right">
                        {{ $detail['transfered_quantity'] }} x {{ App\Helpers\NumberFormat::money($detail['product']['sell_price']) }}
                    </td>
                    <td class="text-right">
                        {{ App\Helpers\NumberFormat::money($detail['product']['sell_price'] * $detail['transfered_quantity']) }}
                    </td>
                </tr>

                @php
                    $total_quantity += $detail['transfered_quantity'];
                    $total_line_amount += ($detail['product']['sell_price'] * $detail['transfered_quantity']);
                @endphp
            @endforeach
            <tr>
                <td colspan="2" class="text-right text-bold">
                    Total
                </td>
                <td class="text-right text-bold">
                    {{ $total_quantity }}
                </td>
                <td class="text-right text-bold">
                    {{ App\Helpers\NumberFormat::money($total_line_amount) }}
                </td>
            </tr>
            <tr>
                <td colspan="4" class="pt-1">
                <!-- &nbsp; -->
                </td>
            </tr>
            <tr>
                <td class="text-bold">
                    Di Buat Oleh
                </td>
                <td colspan="3">
                    : {{ $employee_requested_by['first_name'] . ' '. $employee_requested_by['last_name'] }}
                </td>
            </tr>
            @if($local_approved_at != null)
                <tr>
                    <td class="text-bold">
                        Di Setujui Oleh
                    </td>
                    <td colspan="3">
                        : {{ $employee_approved_by['first_name'] . ' '. $employee_approved_by['last_name'] }}
                    </td>
                </tr>
            @endif
            @if($local_rejected_at != null)
                <tr>
                    <td class="text-bold">
                        Di Tolak Oleh
                    </td>
                    <td colspan="3">
                        : {{ $employee_rejected_by['first_name'] . ' '. $employee_rejected_by['last_name'] }}
                    </td>
                </tr>
            @endif
            @if($local_cancelled_at != null)
                <tr>
                    <td class="text-bold">
                        Di Batalkan Oleh
                    </td>
                    <td colspan="3">
                        : {{ $employee_cancelled_by['first_name'] . ' '. $employee_cancelled_by['last_name'] }}
                    </td>
                </tr>
            @endif
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
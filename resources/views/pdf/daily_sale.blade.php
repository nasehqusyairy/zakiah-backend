<!DOCTYPE html>
<html>
<head>
    <title>
        Rekapan
    </title>
    <style>
        body {
            font-family: 'Arial, sans-serif';
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
            padding-bottom: 1em;
        }

        .pt-1 {
            padding-top: 1em;
        }

        .big-font {
            font-size: 1.25em;
        }
    </style>
</head>
<body>
    <div class="container">
        <table>
            <tr>
                <td class="text-center text-bold big-font" colspan="4">
                    {{ $location->name }}
                </td>
            </tr>
            <tr>
                <td class="text-center" colspan="4">
                    {{ $location['full_address'] }}
                </td>
            </tr>
            <tr>
                <td class="text-center pb-1" colspan="4">
                    Telp / Wa 0{{ $location['backoffice_phone_number'] }}
                </td>
            </tr>
            <tr>
                <td>Tanggal Penutupan</td>
                <td colspan="3">
                    {{ \Carbon\Carbon::parse($local_taking_at)->format('Y-M-d H:i:s') }}
                </td>
            </tr>
            <tr>
                <td>Nama Kasir</td>
                <td colspan="3">
                    {{ $employee_first_name }} {{ $employee_last_name }}
                </td>
            </tr>
            <tr>
                <td colspan="4" class="pb-1" style="border-bottom: 1px dashed;">
                <!-- &nbsp; -->
                </td>
            </tr>
            <tr>
                <td class="pt-1 text-bold" colspan="4">
                    Pembayaran
                </td>
            </tr>
            @foreach($taking_payment_details as $detail)
                <tr>
                    <td>
                        {{ $detail['payment_method']['name'] }}
                    </td>
                    <td class="text-right">
                        {{ App\Helpers\NumberFormat::money($detail['recorded_amount']) }}
                    </td>
                    <td class="text-right">
                        {{ App\Helpers\NumberFormat::money($detail['counted_amount']) }}
                    </td>
                    <td class="text-right">
                        {{ App\Helpers\NumberFormat::money($detail['difference_amount']) }}
                    </td>
                </tr>
            @endforeach
            <tr>
                <td colspan="4" class="pb-1" style="border-bottom: 1px dashed;">
                <!-- &nbsp; -->
                </td>
            </tr>
            <tr>
                <td colspan="4" class="pt-1 text-bold">
                    Rekapan Penjualan
                </td>
            </tr>
            <tr>
                <td>Jumlah Penjualan</td>
                <td colspan="3" class="text-right">
                    {{ App\Helpers\NumberFormat::money($sales_count) }}
                </td>
            </tr>
            <tr>
                <td>Jumlah Pengembalian</td>
                <td colspan="3" class="text-right">
                    {{ App\Helpers\NumberFormat::money($refund_count) }}
                </td>
            </tr>
            <tr>
                <td>Penjualan Kotor</td>
                <td colspan="3" class="text-right">
                    {{ App\Helpers\NumberFormat::money($gross_sales) }}
                </td>
            </tr>
            <tr>
                <td>Pengembalian Kotor</td>
                <td colspan="3" class="text-right">
                    {{ App\Helpers\NumberFormat::money($gross_refund) }}
                </td>
            </tr>
            <tr>
                <td>Total Diskon</td>
                <td colspan="3" class="text-right">
                    {{ App\Helpers\NumberFormat::money((($discount_amount - $discount_amount_refund) + ($promo_amount - $promo_amount_refund)) * -1) }}
                </td>
            </tr>
            <tr>
                <td>Total Penyesuaian</td>
                <td colspan="3" class="text-right">
                    {{ App\Helpers\NumberFormat::money($surcharge_amount - $surcharge_amount_refund) }}
                </td>
            </tr>
            <tr>
                <td class="text-bold">Penjualan Bersih</td>
                <td colspan="3" class="text-right">
                    {{ App\Helpers\NumberFormat::money($net_sales - $net_sales_refund) }}
                </td>
            </tr>
            <tr>
                <td>Total Pajak</td>
                <td colspan="3" class="text-right">
                    {{ App\Helpers\NumberFormat::money($tax_amount - $tax_amount_refund) }}
                </td>
            </tr>
            <tr>
                <td class="text-bold">Penjualan Total</td>
                <td colspan="3" class="text-right">
                    {{ App\Helpers\NumberFormat::money($net_sales_after_tax - $net_sales_after_tax_refund) }}
                </td>
            </tr>
        </table>
    </div>
</body>
<script type="text/javascript">
    window.onload = function() { 
        window.print();
        setTimeout(function() { window.close();}, 1500);
    }
 </script>
</html>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Invoice</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }

        .header,
        .footer {
            text-align: center;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table,
        th,
        td {
            border: 1px solid black;
        }

        th,
        td {
            padding: 10px;
            text-align: left;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>Invoice</h1>
        <p>No Transaksi: {{ $transaksi->no_transaksi }}</p>
        <p>Tanggal: {{ $transaksi->tgl_transaksi->format('d M Y') }}</p>
    </div>
    <table>
        <thead>
            <tr>
                <th>Nama Barang</th>
                <th>Harga</th>
                <th>Kuantitas</th>
                <th>Total Harga</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($transaksi->barangTransaksi as $item)
            <tr>
                <td>{{ $item->barang->nama_barang }}</td>
                <td>Rp. {{ number_format($item->harga_barang, 2, ',', '.') }}</td>
                <td>{{ $item->quantity }}</td>
                <td>Rp. {{ number_format($item->total_harga, 2, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="3"><strong>Grand Total</strong></td>
                <td><strong>Rp. {{ number_format($transaksi->grand_total, 2, ',', '.') }}</strong></td>
            </tr>
        </tfoot>
    </table>
</body>

</html>
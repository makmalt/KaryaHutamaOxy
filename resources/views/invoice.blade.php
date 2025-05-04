<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Struk Belanja</title>
    <style>
        body {
            font-family: "Courier New", monospace;
            width: 100%;
            max-width: 100%;
            margin: 0;
            padding: 10px;
            font-size: 12px;
            box-sizing: border-box;
        }

        .header,
        .footer {
            text-align: center;
        }

        .header h2 {
            margin: 0;
            font-size: 16px;
        }

        .divider {
            border-top: 1px dashed #000;
            margin: 8px 0;
        }

        .item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 4px;
        }

        .item-name {
            flex: 1;
        }

        .item-right {
            text-align: right;
            white-space: nowrap;
        }

        .total {
            font-weight: bold;
            margin-top: 10px;
        }

        .footer {
            margin-top: 10px;
            font-size: 11px;
        }

        .no-print {
            margin: 10px 0;
            text-align: center;
        }

        @media print {
            .no-print {
                display: none;
            }

            body {
                width: 100%;
                margin: 0;
            }
        }
    </style>
</head>

<body>
    <div class="header">
        <h2>Karya Hutama Oxygen</h2>
        <p>Jl. Diponegoro No.122<br>Mojosari, Mojokerto<br>(0321)593940</p>
        <p>No: {{ $transaksi->no_transaksi }}<br>
            Tgl: {{ $transaksi->tgl_transaksi->format('d/m/Y H:i') }}
        </p>
    </div>

    <div class="divider"></div>

    @foreach ($transaksi->barangTransaksi as $item)
    <div class="item">
        <div class="item-name">
            {{ $item->barang->nama_barang }}<br>
            {{ $item->quantity }} x Rp{{ number_format($item->harga_barang, 0, ',', '.') }}
        </div>
        <div class="item-right">
            Rp{{ number_format($item->total_harga, 0, ',', '.') }}
        </div>
    </div>
    @endforeach

    <div class="divider"></div>

    <div class="item total">
        <div class="item-name">TOTAL</div>
        <div class="item-right">Rp{{ number_format($transaksi->grand_total, 0, ',', '.') }}</div>
    </div>

    <div class="divider"></div>
    <!-- Tambahan kecil untuk Bayar/Kembalian -->
    <div style="display: grid; grid-template-columns: auto 1fr; margin-top: 4px;">
        <div style="text-align: left;">Bayar:</div>
        <div style="text-align: right;">Rp{{ number_format($transaksi->uang_pembayaran, 0, ',', '.') }}</div>
        <div style="text-align: left;">Kembalian:</div>
        <div style="text-align: right;">Rp{{ number_format($transaksi->uang_kembalian, 0, ',', '.') }}</div>
    </div>


    <div class="divider"></div>

    <div class="footer">
        <p>Terima kasih telah berbelanja</p>
    </div>
</body>

</html>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Struk Belanja</title>
    <style>
        @page {
            margin: 0;
            size: 58mm auto;
        }
        body {
            font-family: Arial, Helvetica, sans-serif;
            width: 48mm;
            max-width: 160px;
            margin: 0;
            padding: 20px 0 25px 1;
            font-size: 3px; /* Font dasar sangat kecil sesuai permintaan */
            line-height: 1;
            box-sizing: border-box;
        }
        .divider {
            border-top: 1px dashed #000;
            margin: 8px 0;
        }
        .center {
            text-align: center;
        }
        .bold {
            font-weight: bold;
        }
        .company-name {
            font-size: 6px; /* Font judul lebih besar agar tetap bisa dibaca */
            font-weight: bold;
            margin: 0 0 5px 0;
            padding: 0;
        }
        .header-section {
            margin-bottom: 8px;
        }
        .item-container {
            min-height: 80px;
            margin: 8px 0;
        }
        .item-row {
            display: flex;
            margin-bottom: 1px;
        }
        .item-name {
            flex: 1;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            margin-right: 0;
            font-size: 3px;
        }
        .item-price {
            width: auto;
            text-align: right;
            font-size: 3px;
        }
        .item-detail {
            font-size: 3px;
            margin-bottom: 2px;
        }
        .total-section {
            margin: 8px 0;
        }
        .total-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 2px;
            font-size: 3px;
        }
        .total-row .bold {
            font-size: 4px; /* Total sedikit lebih besar */
        }
        .footer {
            margin-top: 10px;
            margin-bottom: 5px;
            font-size: 3px;
        }
        /* Print settings */
        @media print {
            body {
                margin: 0;
                -webkit-print-color-adjust: exact;
            }
        }
    </style>
</head>
<body onload="window.print()">
    <div class="center header-section">
        <h1 class="company-name">Karya Hutama Oxygen</h1>
        <div>Jl. Diponegoro No.122</div>
        <div>Mojosari, Mojokerto</div>
        <div>(0321)593940</div>
        <div style="margin-top: 5px;">
            <div>No: {{ $transaksi->no_transaksi }}</div>
            <div>Tgl: {{ $transaksi->tgl_transaksi->format('d/m/Y H:i') }}</div>
        </div>
    </div>
    
    <div class="divider"></div>
    
    <div class="item-container">
        @foreach ($transaksi->barangTransaksi as $item)
        <div>
            <div class="item-row">
                <div class="item-name bold">{{ substr($item->barang->nama_barang, 0, 15) }}</div>
                <div class="item-price bold">Rp{{ number_format($item->total_harga, 0, ',', '.') }}</div>
            </div>
            <div class="item-detail">{{ $item->quantity }}xRp{{ number_format($item->harga_barang, 0, ',', '.') }}</div>
        </div>
        @endforeach
        
        <!-- Tambahan space jika item kurang -->
        @if(count($transaksi->barangTransaksi) < 3)
        <div style="height: {{ (3 - count($transaksi->barangTransaksi)) * 30 }}px;"></div>
        @endif
    </div>
    
    <div class="divider"></div>
    
    <div class="total-section">
        <div class="total-row">
            <div class="bold">TOTAL</div>
            <div class="bold">Rp{{ number_format($transaksi->grand_total, 0, ',', '.') }}</div>
        </div>
        <div class="total-row">
            <div>BAYAR</div>
            <div>Rp{{ number_format($transaksi->uang_pembayaran, 0, ',', '.') }}</div>
        </div>
        <div class="total-row">
            <div>KEMBALIAN</div>
            <div>Rp{{ number_format($transaksi->uang_kembalian, 0, ',', '.') }}</div>
        </div>
    </div>
    
    <div class="divider"></div>
    
    <div class="center footer">
        <div style="font-weight: bold;">Terima kasih telah berbelanja!</div>
    </div>
</body>
</html>
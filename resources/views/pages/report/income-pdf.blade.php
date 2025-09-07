<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Pemasukan</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
        }
        h2, h4 {
            text-align: center;
            margin: 0;
            padding: 4px;
        }
        .chart {
            text-align: center;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        table th, table td {
            border: 1px solid #000;
            padding: 6px;
            text-align: center;
        }
        table th {
            background: #f2f2f2;
        }
        table tfoot td {
            font-weight: bold;
            background: #e6e6e6;
        }
        .rekap {
            border: 1px solid #000;
            padding: 10px;
            margin-top: 15px;
        }
    </style>
</head>
<body>
    <h2>Laporan Pemasukan</h2>
    <h4>Tanggal Cetak: {{ now()->format('d-m-Y H:i') }}</h4>

    {{-- Grafik --}}
    @if(!empty($chartImage))
    <div class="chart">
        <img src="{{ $chartImage }}" alt="Grafik Pemasukan" style="max-width: 100%; height: auto;">
    </div>
    @endif

    {{-- Tabel Data --}}
    <table>
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Jumlah Transaksi</th>
                <th>Total Pemasukan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $row)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($row->tanggal)->format('d-m-Y') }}</td>
                    <td>{{ $row->jumlah_transaksi }}</td>
                    <td>Rp {{ number_format($row->total_income, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td>Total</td>
                <td>{{ $grandTotalTransaksi }}</td>
                <td>Rp {{ number_format($grandTotalIncome, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>

    {{-- Rekapan --}}
    <div class="rekap">
        <strong>Rekapan:</strong><br>
        - Total Transaksi: {{ $grandTotalTransaksi }} <br>
        - Total Pemasukan: Rp {{ number_format($grandTotalIncome, 0, ',', '.') }}
    </div>
</body>
</html>

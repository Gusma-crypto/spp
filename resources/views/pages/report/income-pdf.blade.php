<body>
    <h3 style="text-align: center;">Laporan Pemasukan</h3>

    @if(!empty($chartImage))
        <div style="text-align: center; margin-bottom: 20px;">
            <img src="{{ $chartImage }}" alt="Grafik Pemasukan" style="max-width: 100%; height: auto;">
        </div>
    @endif

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Total Pemasukan</th>
                <th>Jumlah Transaksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $index => $item)
            <tr>
                <td>{{ $index+1 }}</td>
                <td>{{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') }}</td>
                <td>Rp {{ number_format($item->total_income, 0, ',', '.') }}</td>
                <td>{{ $item->jumlah_transaksi }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>

@extends('layouts.app')

@section('title', 'Laporan Pemasukan')

@section('content')
    {{-- Card Grafik --}}
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">Grafik Pemasukan</h5>
        </div>
        <div class="card-body">
            <canvas id="incomeChart"></canvas>
        </div>
    </div>

    {{-- Card Tabel --}}
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Tabel Laporan Pemasukan</h5>
             <div class="d-flex gap-2 mb-3">
                {{-- Export PDF --}}
                <form action="{{ route('report.export.incomePdf') }}" method="POST" id="exportPdfForm">
                    @csrf
                    <input type="hidden" name="chart_image" id="chartImageInput">
                    <button type="button" id="exportPdfBtn" class="btn btn-outline-danger">
                        <i class="bi bi-file-earmark-pdf"></i> Export PDF
                    </button>
                </form>

                {{-- Export Excel --}}
                <form action="{{ route('report.export.incomeexcel') }}" method="POST" id="exportExcelForm">
                    @csrf
                    <input type="hidden" name="chart_image" id="chartImageInputExcel">
                    <button type="submit" class="btn btn-outline-success">
                        <i class="bi bi-file-excel"></i> Export Rekap Excel
                    </button>
                </form>
            </div>

        </div>

        <div class="card-body">
            <table id="table-data" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Tanggal</th>
                        <th>Total Pemasukan</th>
                        <th>Jumlah Transaksi</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($data as $index => $item)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') }}</td>
                            <td>Rp {{ number_format($item->total_income, 0, ',', '.') }}</td>
                            <td>{{ $item->jumlah_transaksi }}</td>
                            <td>
                                <a href="{{ route('report.income.detail', ['date' => $item->tanggal]) }}" 
                                   class="btn btn-outline-success">
                                   <span class="bi bi-search"></span> Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center">Tidak ada data.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection

@section('script')
    {{-- ChartJS --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx = document.getElementById('incomeChart').getContext('2d');
        const incomeChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($data->pluck('tanggal')) !!},
                datasets: [{
                    label: 'Total Pemasukan',
                    data: {!! json_encode($data->pluck('total_income')) !!},
                    backgroundColor: 'rgba(54, 162, 235, 0.6)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: true }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return 'Rp ' + value.toLocaleString('id-ID');
                            }
                        }
                    }
                }
            }
        });

        // Export PDF -> ambil chart jadi image & submit form
        document.getElementById("exportPdfBtn").addEventListener("click", function () {
            const canvas = document.getElementById("incomeChart");
            const chartImage = canvas.toDataURL("image/png"); 
            document.getElementById("chartImageInput").value = chartImage;
            document.getElementById("exportPdfForm").submit();
        });

        // Export Excel - inject chart base64 ke hidden input
        document.getElementById("exportExcelForm").addEventListener("submit", function () {
            const canvas = document.getElementById("incomeChart");
            const chartImage = canvas.toDataURL("image/png");
            document.getElementById("chartImageInputExcel").value = chartImage;
        });
    </script>
@endsection

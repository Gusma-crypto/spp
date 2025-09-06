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
            <div>
                <button id="exportPdfBtn" class="btn btn-outline-danger">
                    <i class="bi bi-file-earmark-pdf"></i> Export PDF
                </button>
                <button type="button" class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#yearModal">
                    <span class="bi bi-file-earmark-excel"></span> Export Excel
                </button>
            </div>
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
                                   class="btn btn-outline-success"><span class="bi bi-search"> Detail
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

        // Existing scripts
        $(document).ready(function () {
            $(".data-select").select2({
                width: "resolve"
            });

            @if(Session::has('success'))
                notyf.success(@json(Session::get('success')));
            @endif

            @if ($errors->any())
                @foreach ($errors->all() as $error)
                    notyf.error(@json($error));
                @endforeach
            @endif

            @if (Session::has('error'))
                notyf.error(@json(Session::get('error')));
            @endif
        });
    </script>

    
    <script>
        // export PDF
        // Handle export PDF button click
        document.getElementById("exportPdfBtn").addEventListener("click", function () {
            const canvas = document.getElementById("incomeChart");
            const chartImage = canvas.toDataURL("image/png"); // convert chart jadi base64 image

            // kirim pakai form POST
            const form = document.createElement("form");
            form.method = "POST";
            form.action = "{{ route('report.export.incomePdf') }}";

            // CSRF token
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute("content");
            const csrfInput = document.createElement("input");
            csrfInput.type = "hidden";
            csrfInput.name = "_token";
            csrfInput.value = csrfToken;
            form.appendChild(csrfInput);

            // data chart image
            const input = document.createElement("input");
            input.type = "hidden";
            input.name = "chart_image";
            input.value = chartImage;
            form.appendChild(input);

            document.body.appendChild(form);
            form.submit();
        });
    </script>
@endsection

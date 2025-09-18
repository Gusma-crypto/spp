@extends('layouts.app')

@section('title', 'Transaksi SPP')

@php
    $role = Auth::check() ? App\Models\Role::find(Auth::user()->role_id) : null;
@endphp

@section('content')
<div class="card-body">
    {{-- ALERT STATUS DARI MIDTRANS --}}
    @if(request()->get('status') === 'finish')
        <div class="alert alert-success">Pembayaran berhasil!</div>
    @elseif(request()->get('status') === 'unfinish')
        <div class="alert alert-warning">Pembayaran belum selesai.</div>
    @elseif(request()->get('status') === 'error')
        <div class="alert alert-danger">Terjadi kesalahan saat pembayaran.</div>
    @endif

    <div class="container">
        <div class="row mb-2">
            <div class="col-sm-12 col-md-6">
                <h6>Nama Lengkap Siswa : {{ $student->first_name . ' ' . $student->last_name }}</h6>
                <h6>Kelas : {{ $student->class_name }}</h6>
                <h6>Tahun Ajaran : {{ $year ?? $student->year }}</h6>
            </div>
            <div class="col d-md-flex justify-content-end align-items-center me-3 mt-3 mt-md-0">
                <div class="btn-group" role="group">
                    <button id="btnGroupDrop1" type="button" class="btn btn-outline-primary dropdown-toggle" data-bs-toggle="dropdown">
                        Tahun Ajaran
                    </button>
                    <ul class="dropdown-menu shadow overflow-scroll" style="max-height: 120px;">
                        @foreach ($academicYears as $x)
                            <li>
                                <a class="dropdown-item @if(($student->year === $x->year && !$year) || ($year === $x->year)) active @endif"
                                   href="{{ route('spp.transaction.show', ['id' => $student->id, 'year' => urlencode($x->year)]) }}">
                                   {{ $x->year }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <table id="table-data" class="table table-bordered">
        <thead>
            <tr>
                <th>No</th>
                <th>Bulan</th>
                <th>Harga</th>
                <th>Jenis Pembayaran</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data as $key => $x)
                <tr>
                    <td>{{ $x->no }}</td>
                    <td>{{ \Carbon\Carbon::parse($x->date)->locale('id')->isoFormat('MMMM YYYY') }}</td>
                    <td>{{ 'Rp ' . number_format($x->price, 0, ',', '.') }}</td>
                    <td>{{ $x->type }}</td>
                    <td>{{ $x->status }}</td>
                    <td class="d-sm-block d-md-grid gap-2" id="action-buttons-{{ $x->id }}">
                        {{-- BELUM LUNAS / EXPIRED --}}
                        @if ($x->status === "Belum Lunas" || $x->status === "Expired")
                            @if ($role->name === "Super Admin" || $role->name === "Bendahara")
                                <button type="button" class="btn btn-outline-primary manualButton" data-id="{{ $x->id }}" data-bs-toggle="modal" data-bs-target="#exampleModal">
                                    Bayar Manual
                                </button>
                            @endif
                            @if ($role->name === "Super Admin" || $role->name === "Siswa")
                                <button type="button" class="btn btn-outline-warning gatewayButton" data-id="{{ $x->id }}">
                                    Bayar Via Payment Gateway
                                </button>
                            @endif
                        @endif

                        {{-- PENDING --}}
                        @if ($x->status === "Pending")
                            <span class="text-danger d-block mb-1 small">* Menunggu Validasi Pembayaran</span>
                            <span id="countdown-{{ $x->id }}" class="fw-bold text-primary small" data-expired="{{ $x->expired_at }}"></span>
                            @if ($x->snap_token)
                                <button class="btn btn-outline-success mt-2 payButton" data-snap-token="{{ $x->snap_token }}">
                                    Lanjutkan Pembayaran
                                </button>
                            @endif
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

{{-- Modal Manual Payment --}}
<div class="modal fade" id="exampleModal" data-bs-backdrop="static" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Pembayaran SPP Manual</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="idTransaction" />
                <div class="mb-3">
                    <label class="form-label">Bulan</label>
                    <input type="text" id="month" class="form-control" disabled />
                </div>
                <div class="mb-3">
                    <label class="form-label">Harga</label>
                    <input type="text" id="price" class="form-control" disabled />
                </div>
                <div class="mb-3">
                    <label class="form-label"><span class="text-danger">*</span> Total Bayar</label>
                    <input type="text" id="purchase" class="form-control" />
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-outline-danger" data-bs-dismiss="modal">Tutup</button>
                <button class="btn btn-outline-success" id="btnSave">Simpan</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}"></script>
<script>
$(document).ready(function() {
    // Gateway Payment
    $(document).on("click", ".gatewayButton", function() {
        let transactionId = $(this).data("id");
        $.post("{{ route('spp.transaction.store') }}", {
            _token: "{{ csrf_token() }}",
            transaction_id: transactionId
        }).done(function(result) {
            snap.pay(result.snap_token, snapOptions(transactionId));
        }).fail(function(xhr) {
            alert("Terjadi kesalahan.");
        });
    });

    // Lanjutkan Pembayaran
    $(document).on("click", ".payButton", function() {
        let snapToken = $(this).data("snap-token");
        snap.pay(snapToken, snapOptions());
    });

    // Manual Payment Modal
    $(document).on("click", ".manualButton", function() {
        let row = $(this).closest("tr");
        let transactionId = $(this).data("id");
        let month = row.find("td").eq(1).text().trim();
        let price = row.find("td").eq(2).text().replace("Rp","").replace(/\./g,"").trim();

        $("#idTransaction").val(transactionId);
        $("#month").val(month);
        $("#price").val(price);
        $("#purchase").val(price);
    });

    // Save Manual Payment
    $("#btnSave").on("click", function() {
        const transactionId = $("#idTransaction").val();
        const price = $("#price").val();
        const purchase = $("#purchase").val();

        if (purchase < price) {
            alert("Total Bayar kurang dari harga SPP.");
            return;
        }

        $.post("{{ route('spp.transaction.manualStore') }}", {
            _token: "{{ csrf_token() }}",
            transaction_id: transactionId,
            price: price,
            purchase: purchase
        }).done(function(result) {
            alert("Pembayaran SPP berhasil disimpan.");
            location.reload();
        }).fail(function(xhr) {
            alert("Gagal simpan pembayaran.");
        });
    });

    // Countdown per transaksi
    document.querySelectorAll('[id^="countdown-"]').forEach(function(countdownEl) {
        let expiredAt = new Date(countdownEl.dataset.expired).getTime();

        let timer = setInterval(function() {
            let now = new Date().getTime();
            let distance = expiredAt - now;

            if (distance <= 0) {
                clearInterval(timer);
                countdownEl.textContent = "Expired";

                // Tampilkan kembali tombol bayar manual & gateway
                let actionEl = document.getElementById("action-buttons-" + countdownEl.id.split("-")[1]);
                actionEl.innerHTML = `
                    @if($role->name === "Super Admin" || $role->name === "Bendahara")
                        <button type="button" class="btn btn-outline-primary manualButton" data-bs-toggle="modal" data-bs-target="#exampleModal">Bayar Manual</button>
                    @endif
                    @if($role->name === "Super Admin" || $role->name === "Siswa")
                        <button type="button" class="btn btn-outline-warning gatewayButton">Bayar Via Payment Gateway</button>
                    @endif
                `;

                // Redirect ke halaman siswa
                window.location.href = "/dashboard/transaksi-spp/show/{{ $student->id }}";
            } else {
                let minutes = Math.floor(distance / (1000*60));
                let seconds = Math.floor((distance % (1000*60)) / 1000);
                countdownEl.textContent = `${minutes}:${seconds.toString().padStart(2,'0')}`;
            }
        }, 1000);
    });
});

// Snap options
function snapOptions(transactionId) {
    return {
        onSuccess: function(result) { location.reload(); },
        onPending: function(result) { console.log("pending", result); },
        onError: function(result) { alert("Pembayaran gagal."); },
        onClose: function() { alert("Popup ditutup tanpa membayar."); location.reload(); }
    };
}
</script>
@endsection

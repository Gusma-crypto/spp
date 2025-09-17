@extends('layouts.app')

@section('title', 'Transaksi SPP')

@php
    if (Auth::check()) {
        $role = App\Models\Role::find(Auth::user()->role_id);
    }
@endphp

@section('content')
    <div class="card-body">
        <div class="container">
            <div class="row mb-5">
                <div class="col-sm-12 col-md-6">
                    <h6>Nama Lengkap Siswa : {{ $student->first_name . ' ' . $student->last_name }}</h6>
                    <h6>Kelas : {{ $student->class_name }}</h6>
                    <h6>Tahun Ajaran : {{ !$year ? $student->year : $year }}</h6>
                </div>
                <div class="col d-md-flex justify-content-end align-items-center me-3 mt-3 mt-md-0">
                    <div class="btn-group" role="group">
                        <button id="btnGroupDrop1" type="button" class="btn btn-outline-primary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                            Tahun Ajaran
                        </button>
                        <ul class="dropdown-menu shadow overflow-scroll" aria-labelledby="btnGroupDrop1" style="max-height: 120px;">
                            @foreach ($academicYears as $x)
                                <li>
                                    <a class="dropdown-item @if ($student->year === $x->year && !$year) active @elseif ($year === $x->year) active @endif"
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

        <table id="table-data">
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
                        <td class="d-sm-block d-md-grid gap-2">
                            {{-- BELUM LUNAS / EXPIRED --}}
                            @if ($x->status === "Belum Lunas" || $x->status === "Expired")
                                @if ($role->name === "Super Admin" || $role->name === "Bendahara")
                                    <input type="hidden" value="{{ $x->id }}" />
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
                                <span id="countdown-{{ $x->id }}" class="fw-bold text-primary small"></span>
                                @if ($x->snap_token)
                                    <button class="btn btn-outline-success mt-2 payButton"
                                        data-snap-token="{{ $x->snap_token }}">
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
    <div class="modal fade" id="exampleModal" data-bs-backdrop="static" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
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
    {{-- Midtrans Snap --}}
    <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}"></script>
    <script>
        (function() {
            let timeLeft = {{ \Carbon\Carbon::parse($x->expired_at)->timestamp - now()->timestamp }};
            let countdownEl = document.getElementById('countdown-{{ $x->id }}');
            let buttonsEl = document.getElementById('payment-buttons-{{ $x->id }}');

            // Snap payment button
            let payBtn = document.getElementById("pay-button-{{ $x->id }}");
            if (payBtn) {
                payBtn.addEventListener("click", function () {
                    snap.pay("{{ $x->snap_token }}", {
                        onSuccess: function(result){ location.reload(); },
                        onPending: function(result){ console.log("pending", result); },
                        onError: function(result){ alert("Pembayaran gagal, coba lagi."); },
                        onClose: function(){ alert("Popup ditutup tanpa membayar."); }
                    });
                });
            }

            // Countdown timer
            let timer = setInterval(() => {
                if(timeLeft <= 0){
                    clearInterval(timer);
                    countdownEl.textContent = "Expired";

                    // Ubah tombol: tampilkan kembali opsi bayar manual & gateway
                    buttonsEl.innerHTML = `
                        @if($role->name === "Super Admin" || $role->name === "Bendahara")
                        <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#exampleModal">
                            Bayar Manual
                        </button>
                        @endif
                        @if($role->name === "Super Admin" || $role->name === "Siswa")
                        <button type="button" class="btn btn-outline-warning">Bayar Via Payment Gateway</button>
                        @endif
                    `;
                } else {
                    let minutes = Math.floor(timeLeft / 60);
                    let seconds = timeLeft % 60;
                    countdownEl.textContent = `${minutes}:${seconds.toString().padStart(2, '0')}`;
                    timeLeft--;
                }
            }, 1000);
        })();
    </script>

    <script>
        $(document).ready(function() {
            // Flash message
            @if (Session::has('success'))
                notyf.success(@json(Session::get('success')));
            @endif
            @if ($errors->any())
                @foreach ($errors->all() as $error)
                    notyf.error(@json($error));
                @endforeach
            @endif

            // Gateway Payment
            $(document).on("click", ".gatewayButton", function() {
                let transactionId = $(this).data("id");
                $.post("{{ route('spp.transaction.store') }}", {
                    _token: "{{ csrf_token() }}",
                    transaction_id: transactionId
                }).done(function(result) {
                    snap.pay(result.snap_token, snapOptions(transactionId));
                }).fail(function(xhr) {
                    notyf.error(xhr.responseText || "Terjadi kesalahan.");
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
                let price = row.find("td").eq(2).text().replace("Rp", "").replace(/\./g, "").trim();

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
                    notyf.error("Total Bayar kurang dari harga SPP.");
                    return;
                }

                $.post("{{ route('spp.transaction.manualStore') }}", {
                    _token: "{{ csrf_token() }}",
                    transaction_id: transactionId,
                    price: price,
                    purchase: purchase
                }).done(function(result) {
                    if (result && result.status === "OK") {
                        notyf.success("Pembayaran SPP berhasil disimpan.");
                        location.reload();
                    }
                }).fail(function(xhr) {
                    notyf.error(xhr.responseText || "Gagal simpan pembayaran.");
                });
            });

            // Reload halaman ketika modal ditutup
            $('#exampleModal').on('hidden.bs.modal', function() {
                location.reload();
            });
        });

        // Snap Options
        function snapOptions(transactionId) {
            return {
                onSuccess: function(result) {
                    notyf.success("Pembayaran berhasil!");
                    location.reload();
                },
                onPending: function(result) {
                    notyf.warning("Menunggu pembayaran...");
                },
                onError: function(result) {
                    notyf.error("Pembayaran gagal, coba lagi.");
                },
                onClose: function() {
                    notyf.error("Kamu menutup popup tanpa menyelesaikan pembayaran.");
                    location.reload();
                }
            };
        }
    </script>
@endsection

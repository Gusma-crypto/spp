@extends('layouts.app')

@section('title', 'Transaksi SPP')

@php
    if (Auth::check()) {
        $role = App\Models\Role::find(Auth::user()->role_id);
    }
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
                                    <a class="dropdown-item @if ($student->year === $x->year && !$year) active @elseif ($year === $x->year) active @endif" href="{{ route('spp.transaction.show', ['id' => $student->id, 'year' => urlencode($x->year)]) }}">
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
                            @if ($x->status === "OK")
                                <span class="badge bg-success">Lunas</span>
                            @elseif ($x->status === "Belum Lunas")
                                @if($role->name === "Super Admin" || $role->name === "Bendahara")
                                    <input type="hidden" id="transactionId" value="{{ $x->id }}" />
                                    <button type="button" id="manualButton" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#exampleModal">
                                        Bayar Manual
                                    </button>
                                @endif

                                @if ($role->name === "Super Admin" || $role->name === "Siswa")
                                    <input type="hidden" id="transactionId" value="{{ $x->id }}" />
                                    <button type="button" id="gatewayButton-{{ $x->id }}" class="btn btn-outline-warning" data-transaction-id="{{ $x->id }}">
                                        Bayar Via Payment Gateway
                                    </button>
                                @endif
                            @elseif ($x->status === "Pending")
                                @php
                                    $showGatewayButton = false;
                                    $showContinueButton = true;
                                    $timeRemaining = 0;
                                    
                                    if ($x->expired_at) {
                                        $now = \Carbon\Carbon::now();
                                        $expiresAt = \Carbon\Carbon::parse($x->expired_at);
                                        
                                        if ($now->lt($expiresAt)) {
                                            // Still within timeout period - show continue button
                                            $showGatewayButton = false;
                                            $showContinueButton = true;
                                            $timeRemaining = $expiresAt->diffInSeconds($now);
                                        } else {
                                            // Timeout expired - show original gateway button to allow restart
                                            $showGatewayButton = true;
                                            $showContinueButton = false;
                                        }
                                    }
                                @endphp
                                
                                @if($showContinueButton)
                                    <button type="button" id="continueButton-{{ $x->id }}" class="btn btn-outline-info" data-transaction-id="{{ $x->id }}" data-time-remaining="{{ $timeRemaining }}">
                                        Lanjutkan Pembayaran
                                    </button>
                                @else
                                    <button type="button" id="gatewayButton-{{ $x->id }}" class="btn btn-outline-warning" data-transaction-id="{{ $x->id }}">
                                        Bayar Via Payment Gateway
                                    </button>
                                @endif
                            @else
                                <span class="badge bg-secondary">{{ $x->status }}</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="modal fade" id="exampleModal" data-bs-backdrop="static" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Pembayaran SPP Manual</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="idTransaction" />

                    <div class="mb-3">
                        <label for="month" class="form-label">Bulan</label>
                        <input type="text" name="month" id="month" class="form-control" disabled />
                    </div>

                    <div class="mb-3">
                        <label for="price" class="form-label">Harga</label>
                        <input type="text" name="price" id="price" class="form-control" disabled />
                    </div>

                    <div class="mb-3">
                        <label for="purchase" class="form-label"><span class="text-danger">* </span>Total Bayar</label>
                        <input type="text" name="purchase" id="purchase" class="form-control" disabled />
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-danger" data-bs-dismiss="modal">Tutup</button>
                    <button type="button" class="btn btn-outline-success" id="btnSave">Simpan</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}"></script>
    <script>
        $(document).ready(function() {
            @if (Session::has('success'))
                notyf.success(@json(Session::get('success')));
            @endif

            @if ($errors->any())
                @foreach ($errors->all() as $error)
                    notyf.error(@json($error));
                @endforeach
            @endif
            
            // Check for expired timeouts when page loads
            $('.btn[id^="continueButton-"], .btn[id^="gatewayButton-"]').each(function() {
                const transactionId = $(this).data('transaction-id');
                if (transactionId) {
                    checkTransactionTimeout(transactionId);
                }
            });

            // Function to handle payment gateway click
            $(document).on("click", "[id^='gatewayButton-']", function () {
                let transactionId = $(this).data('transaction-id');

                try {
                    $.ajax({
                        url: "{{ route('spp.transaction.store') }}",
                        type: 'POST',
                        data: {
                            _token: "{{ csrf_token() }}",
                            transaction_id: transactionId
                        },
                    
                        success: function (result) {
                            snap.pay(result.snap_token, {
                                onSuccess: function(result){
                                    if (result) {
                                        $.ajax({
                                            url: `/dashboard/transaksi-spp/update/${transactionId}`,
                                            type: 'PATCH',
                                            data: {
                                                _token: "{{ csrf_token() }}"
                                            },
                                            success: function () {
                                                location.reload();
                                            },
                                            error: function (error) {
                                                notyf.error(error);
                                            }
                                        });
                                    }
                                },
                                onPending: function(result){
                                    // Payment is pending, keep showing the same status
                                    console.log('Payment pending:', result);
                                },
                                onError: function(result){
                                    notyf.error("Pembayaran gagal. Silakan coba lagi.");
                                    // Reload page to reset the transaction state after error
                                    location.reload();
                                },
                                onClose: function(){
                                    // Reload page when user closes the popup without completing payment
                                    location.reload();
                                }
                            });
                        },
                        error: function (error) {
                            notyf.error("Gagal menginisiasi pembayaran. Silakan coba lagi.");
                            // Reload page on AJAX error too
                            location.reload();
                        }
                    });
                } catch (error) {
                    notyf.error("Terjadi kesalahan. Silakan coba lagi.");
                    location.reload();
                }
            });

            // Function to handle continue payment click
            $(document).on("click", "[id^='continueButton-']", function () {
                let transactionId = $(this).data('transaction-id');

                // Continue with the existing snap token without creating a new transaction
                $.ajax({
                    url: `/dashboard/transaksi-spp/get-transaction/${transactionId}`,
                    type: 'GET',
                    success: function (transaction) {
                        if (transaction.snap_token) {
                            snap.pay(transaction.snap_token, {
                                onSuccess: function(result){
                                    if (result) {
                                        $.ajax({
                                            url: `/dashboard/transaksi-spp/update/${transactionId}`,
                                            type: 'PATCH',
                                            data: {
                                                _token: "{{ csrf_token() }}"
                                            },
                                            success: function () {
                                                location.reload();
                                            },
                                            error: function (error) {
                                                notyf.error(error);
                                            }
                                        });
                                    }
                                },
                                onPending: function(result){
                                    // Payment is pending, keep showing the same status
                                    console.log('Payment pending:', result);
                                },
                                onError: function(result){
                                    notyf.error("Pembayaran gagal. Silakan coba lagi.");
                                    // Reload page to reset the transaction state after error
                                    location.reload();
                                },
                                onClose: function(){
                                    // Reload page when user closes the popup without completing payment
                                    location.reload();
                                }
                            });
                        } else {
                            // If no snap token exists, initiate new transaction
                            $("#gatewayButton-"+transactionId).click();
                        }
                    },
                    error: function (error) {
                        notyf.error("Gagal mendapatkan informasi pembayaran. Silakan coba lagi.");
                        location.reload();
                    }
                });
            });

            // Function to check transaction timeout
            function checkTransactionTimeout(transactionId) {
                $.ajax({
                    url: "{{ route('spp.transaction.checkTimeout') }}",
                    type: 'POST',
                    data: {
                        _token: "{{ csrf_token() }}",
                        transaction_id: transactionId
                    },
                    success: function(response) {
                        if (response.status === 'timeout_expired') {
                            // Timeout has expired, reload the page to update UI
                            location.reload();
                        }
                    },
                    error: function(xhr, status, error) {
                        console.log('Error checking timeout:', error);
                    }
                });
            }

            // Handle countdown for timeout
            function startCountdown(transactionId, timeRemaining) {
                const button = $('#continueButton-' + transactionId);
                if (button.length && timeRemaining > 0) {
                    let count = timeRemaining;
                    const originalText = button.text();
                    const countdownInterval = setInterval(function() {
                        const minutes = Math.floor(count / 60);
                        const seconds = count % 60;
                        button.text(`Lanjutkan Pembayaran (${minutes}:${seconds.toString().padStart(2, '0')})`);
                        
                        if (count <= 0) {
                            clearInterval(countdownInterval);
                            // Check timeout and update status
                            checkTransactionTimeout(transactionId);
                        }
                        count--;
                    }, 1000);
                }
            }

            // Initialize countdowns for all continue buttons
            $("[id^='continueButton-']").each(function() {
                const transactionId = $(this).data('transaction-id');
                const timeRemaining = $(this).data('time-remaining');
                if (timeRemaining && timeRemaining > 0) {
                    startCountdown(transactionId, timeRemaining);
                }
            });

            $(document).on("click", "#manualButton", function () {
                let row = $(this).closest("tr");
                let transactionId = $(this).closest("td").find("#transactionId").val();

                const month = row.find("td").eq(1).text().trim();
                const price = row.find("td").eq(2).text().replace("Rp", "").replace(/\./g, "").trim();
                

                $("#month").val(month);
                $("#price").val(price);
                $("#purchase").val(price);
                $("#idTransaction").val(transactionId);

                console.log('month :>> ', month);
            });

            $("#btnSave").on("click", function () {
                const price = $("#price").val();
                const purchased = $("#purchase").val();
                const transactionId = $("#idTransaction").val();

                if (purchased < price) {
                    notyf.error("Total Bayar Kurang dari Harga SPP.");
                } else {
                    $.ajax({
                        url: "{{ route('spp.transaction.manualStore') }}",
                        type: 'POST',
                        data: {
                            _token: "{{ csrf_token() }}",
                            transaction_id: transactionId,
                            price: price,
                            purchase: purchased,
                            // ⬅️ kirim ke backend
                        },
                        error: function (error) {
                            notyf.error(error);
                        },
                        success: function (result) {
                            if (result && result.status === "OK") {
                                notyf.success("Pembayaran SPP Berhasil Disimpan.");
                                location.reload();
                            }
                        }
                    });
                } 
            });
        });
    </script>
@endsection
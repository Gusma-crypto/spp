<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SPP Payment Gateway | @yield('title')</title>
    <link rel="shortcut icon" href="{{ asset('assets/static/images/favicon.ico') }}" type="image/x-icon" />
    <link rel="stylesheet" crossorigin href="{{ asset('assets/compiled/css/app.css') }}">
    <link rel="stylesheet" crossorigin href="{{ asset('assets/compiled/css/app-dark.css') }}">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css" />
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.css" />
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.dataTables.min.css">
    <style>
        .btn-outline-info:hover {
            color: white !important;
        }

        table.dataTable {
            width: 100% !important;
            border-collapse: collapse !important;
        }

        div.dataTables_wrapper {
            overflow-x: auto;
        }

        .dataTables_wrapper .dataTables_scroll {
            overflow: auto;
            white-space: nowrap;
        }

        .select2-container--open {
            z-index: 9999 !important;
        }
    </style>
</head>

<body>
    <div id="app">
        @include('components.sidebar')

        <div id="main" class='layout-navbar navbar-fixed'>
            @include('components.header')

            <div id="main-content">
                <div class="page-heading">
                    <div class="page-title mb-4">
                        <div class="row">
                            <div class="col-12 col-md-6 order-md-1 order-last">
                                <h3>@yield('title')</h3>
                            </div>
                            <div class="col-12 col-md-6 order-md-2 order-first">
                                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                                    <ol class="breadcrumb">
                                        @foreach($breadcrumbs as $breadcrumb)
                                            @if($breadcrumb['url'])
                                                <li class="breadcrumb-item"><a href="{{ $breadcrumb['url'] }}">{{ $breadcrumb['label'] }}</a></li>
                                            @else
                                                <li class="breadcrumb-item active">{{ $breadcrumb['label'] }}</li>
                                            @endif
                                        @endforeach
                                    </ol>
                                </nav>
                            </div>
                        </div>
                    </div>

                    <section class="section">
                        <div class="card">
                            @yield('content')
                        </div>
                    </section>
                </div>
            </div>

            @include('components.footer')
        </div>
    </div>

    <script src="{{ asset('assets/static/js/initTheme.js') }}"></script>
    <script src="{{ asset('assets/static/js/components/dark.js') }}"></script>
    <script src="{{ asset('assets/extensions/perfect-scrollbar/perfect-scrollbar.min.js') }}"></script>
    <script src="{{ asset('assets/compiled/js/app.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script>
        $(document).ready(() => {
            let table = new DataTable("#table-data", {
                scrollX: true,
                autoWidth: true,
                language: {
                    emptyTable: "Tidak ada data yang tersedia."
                }
            });

            setTimeout(() => {
                table.columns.adjust();
                table.responsive.recalc();
            }, 500);

            $(window).on("resize", () => {
                table.columns.adjust();
                table.responsive.recalc();
            });

            window.notyf = new Notyf({
                ripple: true,
                duration: 2000,
                dismissible: true,
                position: {
                    x: 'center',
                    y: 'top'
                },
            });

            $(document).on("click", "#logout-button", () => {
                $("#logout-form").submit();
            });

            $("#currentYear").html(new Date().getFullYear());
        });
    </script>

    @yield('script')
</body>

</html>

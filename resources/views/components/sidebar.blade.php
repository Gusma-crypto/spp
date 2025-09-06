@php
    if (Auth::check()) {
        $role = App\Models\Role::find(Auth::user()->role_id);
    }
@endphp

<div id="sidebar">
    <div class="sidebar-wrapper active">
        <div class="sidebar-header position-relative" style="padding: 1rem 1rem 0 1rem !important; margin-bottom: 0px !important;">
            <div class="d-flex justify-content-between align-items-center">
                <div class="logo">
                    <a href="{{ route('dashboard') }}">
                        <!-- <img src="{{ asset('assets/static/images/simple-logo.png') }}" alt="Logo" style="width: 90%; height: 10%" /> -->
                        <img src="{{ asset('assets/logo-new.png') }}" alt="Logo" style="width: 75%; height: 10%" />
                    </a>
                </div>

                <div class="theme-toggle d-flex gap-2 align-items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" aria-hidden="true" role="img" class="iconify iconify--system-uicons" width="20" height="20" preserveAspectRatio="xMidYMid meet" viewBox="0 0 21 21">
                        <g fill="none" fill-rule="evenodd" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                            <path
                                d="M10.5 14.5c2.219 0 4-1.763 4-3.982a4.003 4.003 0 0 0-4-4.018c-2.219 0-4 1.781-4 4c0 2.219 1.781 4 4 4zM4.136 4.136L5.55 5.55m9.9 9.9l1.414 1.414M1.5 10.5h2m14 0h2M4.135 16.863L5.55 15.45m9.899-9.9l1.414-1.415M10.5 19.5v-2m0-14v-2"
                                opacity=".3"></path>
                            <g transform="translate(-210 -1)">
                                <path d="M220.5 2.5v2m6.5.5l-1.5 1.5"></path>
                                <circle cx="220.5" cy="11.5" r="4"></circle>
                                <path d="m214 5l1.5 1.5m5 14v-2m6.5-.5l-1.5-1.5M214 18l1.5-1.5m-4-5h2m14 0h2">
                                </path>
                            </g>
                        </g>
                    </svg>

                    <div class="form-check form-switch fs-6">
                        <input class="form-check-input  me-0" type="checkbox" id="toggle-dark" style="cursor: pointer">
                        <label class="form-check-label"></label>
                    </div>

                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" aria-hidden="true" role="img" class="iconify iconify--mdi" width="20" height="20" preserveAspectRatio="xMidYMid meet" viewBox="0 0 24 24">
                        <path fill="currentColor"
                            d="m17.75 4.09l-2.53 1.94l.91 3.06l-2.63-1.81l-2.63 1.81l.91-3.06l-2.53-1.94L12.44 4l1.06-3l1.06 3l3.19.09m3.5 6.91l-1.64 1.25l.59 1.98l-1.7-1.17l-1.7 1.17l.59-1.98L15.75 11l2.06-.05L18.5 9l.69 1.95l2.06.05m-2.28 4.95c.83-.08 1.72 1.1 1.19 1.85c-.32.45-.66.87-1.08 1.27C15.17 23 8.84 23 4.94 19.07c-3.91-3.9-3.91-10.24 0-14.14c.4-.4.82-.76 1.27-1.08c.75-.53 1.93.36 1.85 1.19c-.27 2.86.69 5.83 2.89 8.02a9.96 9.96 0 0 0 8.02 2.89m-1.64 2.02a12.08 12.08 0 0 1-7.8-3.47c-2.17-2.19-3.33-5-3.49-7.82c-2.81 3.14-2.7 7.96.31 10.98c3.02 3.01 7.84 3.12 10.98.31Z">
                        </path>
                    </svg>
                </div>

                <div class="sidebar-toggler">
                    <a href="#" class="sidebar-hide d-xl-none d-block"><i class="bi bi-x bi-middle"></i></a>
                </div>
            </div>
        </div>

        <div class="sidebar-menu" style="margin-top: 0px !important;">
            <ul class="menu" style="margin-top: 0 !important;">
                <li class="sidebar-title">Menu</li>

                <li class="sidebar-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <a href="{{ route('dashboard') }}" class='sidebar-link'>
                        <i class="bi bi-grid-fill"></i>
                        <span>Dashboard</span>
                    </a>
                </li>

                @if ($role->name === "Super Admin" || $role->name === "Bendahara")
                    <li class="sidebar-item has-sub {{ request()->routeIs('master.*') ? 'active' : '' }}">
                        <a href="#" class='sidebar-link'>
                            <i class="bi bi-database"></i>
                            <span>Kelola Data</span>
                        </a>

                        <ul class="submenu">
                            <li class="submenu-item {{ request()->routeIs('master.class.*') ? 'active' : '' }}">
                                <a href="{{ route('master.class.index') }}" class="submenu-link">Data Kelas</a>
                            </li>

                            <li class="submenu-item {{ request()->routeIs('master.year.*') ? 'active' : '' }}">
                                <a href="{{ route('master.year.index') }}" class="submenu-link">Data Tahun Ajaran</a>
                            </li>

                            @if ($role->name === "Super Admin")
                                <li class="submenu-item {{ request()->routeIs('master.user.*') ? 'active' : '' }}">
                                    <a href="{{ route('master.user.index') }}" class="submenu-link">Data Pengguna Aplikasi</a>
                                </li>
                            @endif

                            <li class="submenu-item {{ request()->routeIs('master.student.*') ? 'active' : '' }}">
                                <a href="{{ route('master.student.index') }}" class="submenu-link">Data Siswa</a>
                            </li>

                            <!-- <li class="submenu-item {{ request()->routeIs('master.income.*') ? 'active' : '' }}">
                                <a href="{{ route('master.income.index') }}" class="submenu-link">Data Pemasukan</a>
                            </li> -->

                            <li class="submenu-item {{ request()->routeIs('master.expense.*') ? 'active' : '' }}">
                                <a href="{{ route('master.expense.index') }}" class="submenu-link">Data Pengeluaran</a>
                            </li>
                        </ul>
                    </li>
                @endif

                @if ($role->name === "Super Admin" || $role->name === "Bendahara" || $role->name === "Siswa")
                    <li class="sidebar-item has-sub {{ request()->routeIs('*.transaction.*') ? 'active' : '' }}">
                        <a href="#" class='sidebar-link'>
                            <i class="bi bi-cash"></i>
                            <span>Transaksi</span>
                        </a>

                        <ul class="submenu">
                            <li class="submenu-item {{ request()->routeIs('spp.transaction.*') ? 'active' : '' }}">
                                <a href="{{ route('spp.transaction.index') }}" class="submenu-link">SPP</a>
                            </li>
                        </ul>
                    </li>
                @endif

                @if ($role->name === "Super Admin" || $role->name === "Kepala Sekolah" || $role->name === "Bendahara")
                    <!-- <li class="sidebar-item has-sub {{ request()->routeIs('report.*') ? 'active' : '' }}">
                        <a href="#" class='sidebar-link'>
                            <i class="bi bi-graph-up"></i>
                            <span>Laporan</span>
                        </a>

                        <ul class="submenu">
                            <li class="submenu-item {{ request()->routeIs('report.spp.*') ? 'active' : '' }}">
                                <a href="{{ route('report.spp.index') }}" class="submenu-link">Transaksi SPP</a>
                            </li>

                            <li class="submenu-item {{ request()->routeIs('report.income.*') ? 'active' : '' }}">
                                <a href="{{ route('report.income.index') }}" class="submenu-link">Pemasukan</a>
                            </li>

                            <li class="submenu-item {{ request()->routeIs('report.expense.*') ? 'active' : '' }}">
                                <a href="{{ route('report.expense.index') }}" class="submenu-link">Pengeluaran</a>
                            </li>
                        </ul>
                    </li> -->

                    <!-- <li class="sidebar-item {{ request()->routeIs('report.*') ? 'active' : '' }}">
                        <a href="{{ route('report.spp.index') }}" class='sidebar-link'>
                            <i class="bi bi-graph-up"></i>
                            <span>Laporan Transaksi SPP</span>
                        </a>
                    </li> -->
                    @if ($role->name === "Kepala Sekolah")
                    <li class="sidebar-item">
                        <a href="{{ route('master.expense.index') }}" class='sidebar-link'>
                            <i class="bi bi-check-circle"></i>
                            <span>Approval Pengeluaran</span>
                        </a>
                    </li> 
                    @endif
                    <li class="sidebar-item has-sub {{ request()->routeIs('*.transaction.*') ? 'active' : '' }}">
                        <a href="#" class='sidebar-link'>
                            <i class="bi bi-graph-up"></i>
                            <span>Laporan</span>
                        </a>

                        <ul class="submenu">
                            <li class="submenu-item {{ request()->routeIs('spp.transaction.*') ? 'active' : '' }}">
                                <a href="{{ route('report.spp.index') }}" class="submenu-link">Transaksi SPP</a>
                            </li>
                            <li class="submenu-item {{ request()->routeIs('master.income.*') ? 'active' : '' }}">
                                <a href="{{ route('report.income.index') }}" class="submenu-link">Data Pemasukan</a>
                            </li>
                            <li class="submenu-item {{ request()->routeIs('master.income.*') ? 'active' : '' }}">
                                <a href="{{ route('report.unpaid.index') }}" class="submenu-link">Data Belum Lunas</a>
                            </li>
                        </ul>
                    </li>
                @endif
            </ul>
        </div>
    </div>
</div>

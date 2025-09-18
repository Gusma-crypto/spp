<header>
    <nav class="navbar navbar-expand navbar-light navbar-top py-1 bg-white shadow-sm mb-1">
        <div class="container-fluid">
            <!-- Burger Button -->
            <a href="#" class="burger-btn d-block">
                <i class="bi bi-justify fs-3"></i>
            </a>

            <!-- Navbar Toggler (Mobile) -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                    data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                    aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- Navbar Content -->
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <div class="ms-auto d-flex align-items-center">
                    <div class="dropdown">
                        <a href="#" data-bs-toggle="dropdown" aria-expanded="false">
                            <div class="user-menu d-flex align-items-center">
                                <div class="user-name text-end me-3">
                                    <h6 class="mb-0 text-gray-600">
                                        {{ Auth::user()->first_name.' '.Auth::user()->last_name }}
                                    </h6>
                                    @php
                                        $role = App\Models\Role::find(Auth::user()->role_id);
                                    @endphp
                                    <p class="mb-0 text-sm text-gray-600">
                                        {{ $role->name }}
                                    </p>
                                </div>
                                <div class="user-img d-flex align-items-center">
                                    <div class="avatar avatar-md">
                                        <img src="{{ asset('assets/compiled/jpg/1.jpg') }}" alt="User Avatar">
                                    </div>
                                </div>
                            </div>
                        </a>

                        <!-- Dropdown Menu -->
                        <ul class="dropdown-menu dropdown-menu-end mt-2" style="min-width: 11rem;">
                            <li>
                                <a class="dropdown-item" href="#">
                                    <i class="icon-mid bi bi-person me-2"></i> My Profile
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                    @csrf
                                </form>
                                <a class="dropdown-item" href="#"
                                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    <i class="icon-mid bi bi-box-arrow-left me-2"></i> Logout
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </nav>
</header>

<aside class="sidenav bg-white navbar navbar-vertical navbar-expand-xs border-0 border-radius-xl my-3 fixed-start ms-4 "
    id="sidenav-main">
    <div class="sidenav-header">
        <i class="fas fa-times p-3 cursor-pointer text-secondary opacity-5 position-absolute end-0 top-0 d-none d-xl-none"
            aria-hidden="true" id="iconSidenav"></i>
        <a class="navbar-brand m-0" href="{{ route('home') }}" target="_blank">
            <img src="{{ asset('./img/alcon-logo.png') }}" class="navbar-brand-img h-100" alt="main_logo">
            <span class="ms-1 font-weight-bold">ALCON FINANCE APP</span>
        </a>
    </div>
    <hr class="horizontal dark mt-0">
    <div class="collapse navbar-collapse  w-auto " id="sidenav-collapse-main">
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link {{ Route::currentRouteName() == 'home' ? 'active' : '' }}"
                    href="{{ route('home') }}">
                    <div
                        class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="ni ni-tv-2 text-dark text-sm opacity-10"></i>
                    </div>
                    <span class="nav-link-text ms-1">Dashboard</span>
                </a>
            </li>
            <li class="nav-item mt-3">
                <h6 class="ps-4 ms-2 text-uppercase text-xs font-weight-bolder opacity-6">Administration pages</h6>
            </li>
            <li class="nav-item">
                <a class="nav-link dropdown-toggle {{ in_array(Route::currentRouteName(), ['permissions.index', 'roles.index', 'user-management.index']) ? 'active' : '' }}"
                    href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <div
                        class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="ni ni-bullet-list-67 text-dark text-sm opacity-10"></i>
                    </div>
                    <span class="nav-link-text ms-1">User Management</span>
                </a>
                <ul class="dropdown-menu ps-4 mt-0" aria-labelledby="navbarDropdown">
                    <li>
                        <a class="dropdown-item {{ Route::currentRouteName() == 'permissions.index' ? 'active' : '' }}"
                            href="{{ route('permissions.index') }}">
                            Permission
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item {{ Route::currentRouteName() == 'role.index' ? 'active' : '' }}"
                            href="{{ route('roles.index') }}">
                            Role
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item {{ Route::currentRouteName() == 'user.index' ? 'active' : '' }}"
                            href="{{ route('user.index') }}">
                            Users
                        </a>
                    </li>
                </ul>
            </li>

            <li class="nav-item mt-3">
                <h6 class="ps-4 ms-2 text-uppercase text-xs font-weight-bolder opacity-6">Prensensi</h6>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ Route::currentRouteName() == 'presence.index' ? 'active' : '' }}"
                    href="{{ route('presence.index') }}">
                    <div
                        class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="ni ni-time-alarm text-dark text-sm opacity-10"></i>
                    </div>
                    <span class="nav-link-text ms-1">Absensi</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ Route::currentRouteName() == 'shift.index' ? 'active' : '' }}"
                    href="{{ route('shift.index') }}">
                    <div
                        class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="ni ni-settings-gear-65 text-dark text-sm opacity-10"></i>
                    </div>
                    <span class="nav-link-text ms-1">Setting Shift</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ Route::currentRouteName() == 'setattendance.index' ? 'active' : '' }}"
                    href="{{ route('setattendance.index') }}">
                    <div
                        class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="ni ni-settings-gear-65 text-dark text-sm opacity-10"></i>
                    </div>
                    <span class="nav-link-text ms-1">Setting Attendance</span>
                </a>
            </li>
            <li class="nav-item mt-3">
                <h6 class="ps-4 ms-2 text-uppercase text-xs font-weight-bolder opacity-6">Payroll</h6>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#" onclick="togglePanel(event)">
                    <div
                        class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="ni ni-chart-bar-32 text-dark text-sm opacity-10"></i>
                    </div>
                    <span class="nav-link-text ms-1">Pra Payroll</span>
                    <span class="expand-icon">▼</span>
                </a>
                <div class="panel" style="display: none; padding-left: 20px;">
                    <a class="nav-link" href="{{ route('prapayroll.index') }}">Master</a>
                    <a class="nav-link" href="{{ route('prapayroll.index-detail') }}">Detail</a>
                </div>
            </li>


            <li class="nav-item">
                <a class="nav-link {{ Route::currentRouteName() == 'runpayroll.index' ? 'active' : '' }}"
                    href="{{ route('runpayroll.index') }}">
                    <div
                        class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="ni ni-calendar-grid-58 text-dark text-sm opacity-10"></i>
                    </div>
                    <span class="nav-link-text ms-1">Run Payroll</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ Route::currentRouteName() == 'historypayroll.index' ? 'active' : '' }}"
                    href="{{ route('historypayroll.index') }}">
                    <div
                        class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="ni ni-book-bookmark text-dark text-sm opacity-10"></i>
                    </div>
                    <span class="nav-link-text ms-1">Payroll History</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ Route::currentRouteName() == 'reportpayroll.index' ? 'active' : '' }}"
                    href="{{ route('reportpayroll.index') }}">
                    <div
                        class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="ni ni-money-coins text-dark text-sm opacity-10"></i>
                    </div>
                    <span class="nav-link-text ms-1">Report Payroll</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ Route::currentRouteName() == 'componen.index' ? 'active' : '' }}"
                    href="{{ route('componen.index') }}">
                    <div
                        class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="ni ni-tag text-dark text-sm opacity-10"></i>
                    </div>
                    <span class="nav-link-text ms-1">Component</span>
                </a>
            </li>
            <li class="nav-item mt-3">
                <h6 class="ps-4 ms-2 text-uppercase text-xs font-weight-bolder opacity-6">Management Employee</h6>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ Route::currentRouteName() == 'employee.index' ? 'active' : '' }}"
                    href="{{ route('employee.index') }}">
                    <div
                        class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="ni ni-building text-dark text-sm opacity-10"></i>
                    </div>
                    <span class="nav-link-text ms-1">Employee</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ Route::currentRouteName() == 'position.index' ? 'active' : '' }}"
                    href="{{ route('position.index') }}">
                    <div
                        class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="ni ni-circle-08 text-dark text-sm opacity-10"></i>
                    </div>
                    <span class="nav-link-text ms-1">Position</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ Route::currentRouteName() == 'paramposition.index' ? 'active' : '' }}"
                    href="{{ route('paramposition.index') }}">
                    <div
                        class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="ni ni-ungroup text-dark text-sm opacity-10"></i>
                    </div>
                    <span class="nav-link-text ms-1">Setting Position</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ Route::currentRouteName() == 'project.index' ? 'active' : '' }}"
                    href="{{ route('project.index') }}">
                    <div
                        class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="ni ni-books text-dark text-sm opacity-10"></i>
                    </div>
                    <span class="nav-link-text ms-1">Project</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ Route::currentRouteName() == 'group.index' ? 'active' : '' }}"
                    href="{{ route('group.index') }}">
                    <div
                        class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="ni ni-briefcase-24 text-dark text-sm opacity-10"></i>
                    </div>
                    <span class="nav-link-text ms-1">Group</span>
                </a>
            </li>
        </ul>
    </div>
    <script>
        function togglePanel(event) {
            event.preventDefault();
            const panel = event.target.closest('li').querySelector('.panel');
            panel.style.display = panel.style.display === 'none' ? 'block' : 'none';
        }
    </script>
</aside>

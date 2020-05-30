<div id="layoutSidenav_nav">
    <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
        <div class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
            <a class="navbar-brand" href="{{ url('/home') }}">
                <img src="{{asset('images/logo-blue.svg')}}" alt="GiZmo" height="36px;" style="filter: grayscale(100%) brightness(150%);-webkit-filter: grayscale(100%) brightness(150%);">
            </a>
            <button class="sidebarToggle btn btn-link btn-sm order-1 order-lg-0" id="sidebarToggle" href="#">
                <i class="fas fa-bars"></i>
            </button>
        </div>
        <div class="sb-sidenav-menu">
            <div class="nav">
                {{-- <div class="sb-sidenav-menu-heading">Gizmo Admin</div> --}}
                {{-- <a class="nav-link" href="{{route('home')}}">
                    <div class="sb-nav-link-icon">
                        <i class="fas fa-tachometer-alt"></i>
                    </div>
                    Dashboard
                </a> --}}
                @if(auth()->user()->isQuestionsEditor() || auth()->user()->isAdmin())
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseContentManagement"
                   aria-expanded="false" aria-controls="collapseContentManagement">
                    <div class="sb-nav-link-icon">
                        <i class="fas fa-columns"></i>
                    </div>
                    Content Management
                    <div class="sb-sidenav-collapse-arrow">
                        <i class="fas fa-angle-down"></i>
                    </div>
                </a>
                <div class="collapse {{ Route::is('questions.*') || Route::is('lessons.*') || Route::is('topics.*') || Route::is('units.*') || Route::is('levels.*') || Route::is('placements.*') || Route::is('error_report.*') ? 'show' : '' }}" id="collapseContentManagement" aria-labelledby="headingOne" data-parent="#sidenavAccordion">
                    <nav class="sb-sidenav-menu-nested nav">
                        <a class="nav-link" href="{{ Route('questions.create') }}">
                            Create Question
                        </a>
                        <a class="nav-link" href="{{ url('/questions') }}">
                            Manage Question
                        </a>
                        @if(auth()->user()->isAdmin())
                            <a class="nav-link" href="{{ url('/lessons') }}">
                                Manage Lessons
                            </a>
                            <a class="nav-link" href="{{ url('/topics') }}">
                                Manage Topics
                            </a>
                            <a class="nav-link" href="{{ url('/units') }}">
                                Manage Units
                            </a>
                            <a class="nav-link" href="{{ url('/levels') }}">
                                Manage Levels
                            </a>
                            <a class="nav-link" href="{{ url('/placements') }}">
                                Manage Placements
                            </a>
                            <a class="nav-link" href="{{ route('error_report.index', 'new') }}">
                                Error Report
                            </a>
                        @endif
                    </nav>
                </div>
                @endif
                @if(auth()->user()->isAdmin())
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseClassManagement"
                   aria-expanded="false" aria-controls="collapseClassManagement">
                    <div class="sb-nav-link-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    Class Management
                    <div class="sb-sidenav-collapse-arrow">
                        <i class="fas fa-angle-down"></i>
                    </div>
                </a>
                <div class="collapse {{ Route::is('students.*') || Route::is('applications.*') ? 'show' : '' }}" id="collapseClassManagement" aria-labelledby="headingTwo" data-parent="#sidenavAccordion">
                    <nav class="sb-sidenav-menu-nested nav">
                        <a class="nav-link" href="{{ route('students.index') }}">
                            Participants
                        </a>
                        <a class="nav-link" href="{{ url('/applications') }}">
                            Manage Assignments
                        </a>
                    </nav>
                </div>
                @endif
                @if(auth()->user()->isSuperAdmin())
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseSettings"
                   aria-expanded="false" aria-controls="collapseSettings">
                    <div class="sb-nav-link-icon">
                        <i class="fas fa-cog"></i>
                    </div>
                    Settings
                    <div class="sb-sidenav-collapse-arrow">
                        <i class="fas fa-angle-down"></i>
                    </div>
                </a>
                <div class="collapse {{ Route::is('settings.*') || Route::is('users.*') ? 'show' : '' }}" id="collapseSettings" aria-labelledby="headingThree" data-parent="#sidenavAccordion">
                    <nav class="sb-sidenav-menu-nested nav">
                        <a class="nav-link" href="{{ route('settings.index') }}">
                            Settings
                        </a>
                        <a class="nav-link" href="{{ route('users.index') }}">
                            Administrators
                        </a>
                    </nav>
                </div>
                @endif
                {{-- <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapsePages" aria-expanded="false" aria-controls="collapsePages">
                    <div class="sb-nav-link-icon">
                        <i class="fas fa-book-open"></i>
                    </div>
                    Pages
                    <div class="sb-sidenav-collapse-arrow">
                        <i class="fas fa-angle-down"></i>
                    </div>
                </a>
                <div class="collapse" id="collapsePages" aria-labelledby="headingTwo" data-parent="#sidenavAccordion">
                    <nav class="sb-sidenav-menu-nested nav accordion" id="sidenavAccordionPages">
                        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#pagesCollapseError" aria-expanded="false" aria-controls="pagesCollapseError">
                            Error
                            <div class="sb-sidenav-collapse-arrow">
                                <i class="fas fa-angle-down"></i>
                            </div>
                        </a>
                        <div class="collapse" id="pagesCollapseError" aria-labelledby="headingOne" data-parent="#sidenavAccordionPages">
                            <nav class="sb-sidenav-menu-nested nav">
                                <a class="nav-link" href="401.html">
                                    401 Page
                                </a>
                                <a class="nav-link" href="404.html">
                                    404 Page
                                </a>
                                <a class="nav-link" href="500.html">
                                    500 Page
                                </a>
                            </nav>
                        </div>
                    </nav>
                </div> --}}
            </div>
        </div>
        <div class="sb-sidenav-footer">
            <a class="pull-right text-decoration-none text-white" href="{{ url('/logout') }}">
                <div class="small" style="color: rgba(255, 255, 255, 0.5);">Logged in as:</div>
                {{ Auth::user()->name }}
                <i class="fas fa-sign-out-alt"></i>
            </a>
        </div>
    </nav>
</div>

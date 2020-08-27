<nav class="navbar navbar-expand-lg navbar-light bg-light">
  <a class="navbar-brand" href="#">IPACS</a>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent"
    aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>

  <div id="navbarContent" class="collapse navbar-collapse">
    <ul class="navbar-nav mr-auto">
      <!-- Level one dropdown -->
      @auth

      <li class="nav-item dropdown">
        <a id="dropdownMenu1" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
          class="nav-link dropdown-toggle">Subject Management</a>
        <ul aria-labelledby="dropdownMenu1" class="dropdown-menu border-0 shadow">
          <li><a href="#" class="dropdown-item">Generate Subject IDs</a></li>
          <li><a href="#" class="dropdown-item">Some other action</a></li>
          <li class="dropdown-divider"></li>

          <!-- Level two dropdown-->
          <li class="dropdown-submenu">
            <a id="dropdownMenu2" href="#" role="button" data-toggle="dropdown" aria-haspopup="true"
              aria-expanded="false" class="dropdown-item dropdown-toggle">Follow-up Schedule</a>
            <ul aria-labelledby="dropdownMenu2" class="dropdown-menu border-0 shadow">
              <li>
                <a tabindex="-1" href="#" class="dropdown-item">This Week's Schedule</a>
                <a tabindex="-1" href="#" class="dropdown-item">Next Week's Schedule</a>
              </li>

              <!-- Level three dropdown-->
              <li class="dropdown-submenu">
                <a id="dropdownMenu3" href="#" role="button" data-toggle="dropdown" aria-haspopup="true"
                  aria-expanded="false" class="dropdown-item dropdown-toggle">level 2</a>
                <ul aria-labelledby="dropdownMenu3" class="dropdown-menu border-0 shadow">
                  <li><a href="#" class="dropdown-item">3rd level</a></li>
                  <li><a href="#" class="dropdown-item">3rd level</a></li>
                </ul>
              </li>
              <!-- End Level three -->

              <li><a href="#" class="dropdown-item">level 2</a></li>
              <li><a href="#" class="dropdown-item">level 2</a></li>
            </ul>

            <!-- End Level two -->
          </li>
          <li><a href="#" class="dropdown-item">Print Labels</a></li>
          <li><a href="#" class="dropdown-item">Manage Label Print Queue</a></li>
          <li><a href="#" class="dropdown-item">Switch Arm</a></li>
          <li><a href="#" class="dropdown-item">Drop Subject</a></li>
        </ul>
      </li>
      <li class="nav-item dropdown">
        <a id="dropdownMenu1" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
          class="nav-link dropdown-toggle">Administration</a>
        <ul aria-labelledby="dropdownMenu1" class="dropdown-menu border-0 shadow">
          <li><a href="#" class="dropdown-item">Users</a></li>
          <li><a href="projects" class="dropdown-item">Projects</a></li>
        </ul>
      </li>
      <!-- End Level one -->
      @endauth
      <!-- Level one dropdown -->
      <li class="nav-item dropdown">
        <a id="dropdownMenu1" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
          class="nav-link dropdown-toggle">Help</a>
        <ul aria-labelledby="dropdownMenu1" class="dropdown-menu border-0 shadow">
          <li><a href="#" class="dropdown-item">Manual</a></li>
          <li class="dropdown-divider"></li>
          <li><a href="#" class="dropdown-item">Home Page</a></li>
          <li><a href="#" class="dropdown-item">About IPACS</a></li>
        </ul>
      </li>
      <!-- End Level one -->

      <li class="nav-item"><a href="#" class="nav-link">About</a></li>
      <li class="nav-item"><a href="#" class="nav-link">Services</a></li>
      <li class="nav-item"><a href="#" class="nav-link">Contact</a></li>
    </ul>

    <!-- Right Side Of Navbar -->
    <ul class="navbar-nav ml-auto">
      <!-- Authentication Links -->
      @guest
      <li class="nav-item">
        <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
      </li>
      @if (Route::has('register'))
      <li class="nav-item">
        <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
      </li>
      @endif
      @else
      <li class="nav-item"><a href="laratrust" class="nav-link">Laratrust</a></li>
      <li class="nav-item dropdown">
        <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown"
          aria-haspopup="true" aria-expanded="false" v-pre>
          {{ Auth::user()->name }} <span class="caret"></span>
        </a>

        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
          <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault();
            document.getElementById('logout-form').submit();">
            {{ __('Logout') }}
          </a>

          <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
            @csrf
          </form>
        </div>
      </li>
      @endguest
    </ul>

  </div>



</nav>
{{-- <li class="nav-item">
        <a class="nav-link" href="{{ route('trusty.index') }}">{{ __('User Management') }}</a>
</li> --}}
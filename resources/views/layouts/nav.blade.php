<x-nav.navbar>

  <nav class="flex justify-between">

    <!-- Left Side Of Navbar -->
    <div class="flex space-x-5">
      <x-nav.link href="/">{{ config('app.name') }}</x-nav.link>
      {{-- <x-nav.link href="/selectproject">My Projects</x-nav.link> --}}
      @auth
      <x-nav.link href="/project/select">My Projects</x-nav.link>
      @endauth
    </div>

    <!-- Main Nav Section -->
    @auth
    <div class="flex space-x-5">
      @if (session()->has('currentProject'))
      <x-nav.dropdown>
        <x-slot name="nav_item">
          <div>Subject Management</div>
        </x-slot>
        <x-nav.dropdown-link href="/subjects">Manage Subjects</x-nav.dropdown-link>
        <x-nav.dropdown-link href="/subjects/create">Generate Subject IDs</x-nav.dropdown-link>
        {{-- <x-nav.dropdown-link href="#">Enrol Subject</x-nav.dropdown-link> --}}
        <x-nav.dropdown-submenu>
          <x-slot name="nav_item">
            <div>Follow-up Schedule</div>
          </x-slot>
          <x-nav.dropdown-link href="/schedule/thisweek">This Week's Schedule</x-nav.dropdown-link>
          <x-nav.dropdown-link href="/schedule/nextweek">Next Week's Schedule</x-nav.dropdown-link>
        </x-nav.dropdown-submenu>
        <x-nav.dropdown-link href="/event_subject">Log Event Status</x-nav.dropdown-link>
        <x-nav.dropdown-link href="/labels/queue">Add Pending Labels to Queue</x-nav.dropdown-link>
        <x-nav.dropdown-link href="/labels">Manage Label Print Queue</x-nav.dropdown-link>
        <x-nav.dropdown-link href="/labels/print">Print Labels</x-nav.dropdown-link>
        {{-- <x-nav.dropdown-link href="#">Switch Arm</x-nav.dropdown-link> --}}
        {{-- <x-nav.dropdown-link href="#">Update Switch Arm Date</x-nav.dropdown-link> --}}
        {{-- <x-nav.dropdown-link href="#">Revert Arm Switch</x-nav.dropdown-link> --}}
      </x-nav.dropdown>

      <x-nav.dropdown>
        <x-slot name="nav_item">
          <div>Sample Logging</div>
        </x-slot>
        <x-nav.dropdown-link href="/primary">Register Primary Samples</x-nav.dropdown-link>
        <x-nav.dropdown-link href="/primary.log">Log Primary Samples</x-nav.dropdown-link>
        <x-nav.dropdown-submenu>
          <x-slot name="nav_item">
            <div>Log Derivative Samples</div>
          </x-slot>
          <x-nav.dropdown-link href="/derivative/parent">Log By Parent Sample</x-nav.dropdown-link>
          <x-nav.dropdown-link href="/derivative/pse">Log By Event</x-nav.dropdown-link>
        </x-nav.dropdown-submenu>
        <x-nav.dropdown-link href="/samples">Sample Details</x-nav.dropdown-link>
      </x-nav.dropdown>

      <x-nav.dropdown>
        <x-slot name="nav_item">
          <div>Sample Management</div>
        </x-slot>
        {{-- <x-nav.dropdown-link href="#">Sample Lookup</x-nav.dropdown-link> --}}
        <x-nav.dropdown-link href="/samples/search">Retrieve Sample Details</x-nav.dropdown-link>
        <hr />
        <x-nav.dropdown-link href="/samplestore">Allocate Storage</x-nav.dropdown-link>
        <x-nav.dropdown-link href="/samplestore/report">Storage Reports</x-nav.dropdown-link>
        <hr />
        <x-nav.dropdown-link href="/sample/logout">Log Sample out of Storage</x-nav.dropdown-link>
        <x-nav.dropdown-link href="/sample/logreturn">Log Sample Return to Storage</x-nav.dropdown-link>
        <hr />
        <x-nav.dropdown-link href="/sample/logused">Log Sample as Used</x-nav.dropdown-link>
        <x-nav.dropdown-link href="/sample/loglost">Log Sample as Lost</x-nav.dropdown-link>
        <hr />
        <x-nav.dropdown-link href="/manifest/samplelist">Select Samples for Shipping</x-nav.dropdown-link>
        <x-nav.dropdown-link href="/manifest">Shipping Manifests</x-nav.dropdown-link>
        <x-nav.dropdown-link href="/manifest/receive">Receive Manifest Shipment</x-nav.dropdown-link>
        {{-- <x-nav.dropdown-link href="#">Receive External Shipment</x-nav.dropdown-link> --}}
      </x-nav.dropdown>

      <x-nav.dropdown>
        <x-slot name="nav_item">
          <x-nav.link href="/datafiles">Data Files</x-nav.link>
        </x-slot>
      </x-nav.dropdown>

      <x-nav.dropdown>
        <x-slot name="nav_item">
          <div>Project Administration</div>
        </x-slot>
        <x-nav.dropdown-link href="/team">Team Members</x-nav.dropdown-link>
        <x-nav.dropdown-link href="/sites">Sites</x-nav.dropdown-link>
        <x-nav.dropdown-link href="/arms">Arms</x-nav.dropdown-link>
        <x-nav.dropdown-link href="/events">Events</x-nav.dropdown-link>
        <x-nav.dropdown-link href="/sampletypes">Sample Types</x-nav.dropdown-link>
        {{-- <x-nav.dropdown-link href="#">Schedule Labels</x-nav.dropdown-link> --}}
        <x-nav.dropdown-link href="/substitute">Colleague Substitution</x-nav.dropdown-link>
        <x-nav.dropdown-link href="/progress">Progress Report</x-nav.dropdown-link>
        <x-nav.dropdown-link href="#">Sample Storage Status</x-nav.dropdown-link>
      </x-nav.dropdown>

      @endif
    </div>
    @endauth

    <!-- Right Side Of Navbar -->
    <div class="flex space-x-5">
      @auth

      <x-nav.dropdown>
        <x-slot name="nav_item">
          <div>Administration</div>
        </x-slot>
        <x-nav.dropdown-submenu alignment="left">
          <x-slot name="nav_item">
            <div>Access Control</div>
          </x-slot>
          <x-nav.dropdown-link href="/user">Users</x-nav.dropdown-link>
          <x-nav.dropdown-link href="/role">Roles</x-nav.dropdown-link>
          <x-nav.dropdown-link href="/permission">Permissions</x-nav.dropdown-link>
          {{-- <x-nav.dropdown-link href="/laratrust">Access Control</x-nav.dropdown-link> --}}
        </x-nav.dropdown-submenu>
        <x-nav.dropdown-link href="/project">Projects</x-nav.dropdown-link>
        <x-nav.dropdown-link href="#">Freezer Layout</x-nav.dropdown-link>
        <x-nav.dropdown-submenu alignment="left">
          <x-slot name="nav_item">
            <div>Freezer Management</div>
          </x-slot>
          <x-nav.dropdown-link href="/unitDefinition">Physical Storage</x-nav.dropdown-link>
          <x-nav.dropdown-link href="/physicalUnit">Virtual Storage</x-nav.dropdown-link>
        </x-nav.dropdown-submenu>
      </x-nav.dropdown>

      <x-nav.link href="#">Manual</x-nav.link>
      <x-nav.dropdown alignment="right">
        <x-slot name="nav_item">
          @if( Session::has('original_user') )
            <div class="bg-red-600 px-2 rounded-md">{{Auth::user()->firstname}}</div>
          @else
            <div>{{Auth::user()->firstname}}</div>
          @endif
        </x-slot>
        <x-nav.dropdown-link href="/changePassword">Change Password</x-nav.dropdown-link>
        <x-nav.dropdown-link href="/{{ route('logout') }}" onclick="event.preventDefault();
        document.getElementById('logout-form').submit();">
          Log out
        </x-nav.dropdown-link>
        <x-nav.dropdown-link href="/substitute/{{Auth::user()->id}}">My Substitutes</x-nav.dropdown-link>
        @if (Auth::user()->hasRole('sysadmin'))
        <x-nav.dropdown-link href="/user/impersonate">Impersonation</x-nav.dropdown-link>
        @endif
        @if( Session::has('original_user') )
        <x-nav.dropdown-link href="/user/impersonate/stop">Revert to Me</x-nav.dropdown-link>
        @endif

        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
          @csrf
        </form>
      </x-nav.dropdown>
      @else

      {{-- @if (Route::has('login'))
      <a href="/{{ route('login') }}"
      class="font-medium text-indigo-200 hover:text-indigo-300 focus:outline-none focus:underline transition ease-in-out
      duration-150">
      Log in
      </a>
      @endif

      @if (Route::has('register'))
      <a href="/{{ route('register') }}"
        class="font-medium text-indigo-200 hover:text-indigo-300 focus:outline-none focus:underline transition ease-in-out duration-150">Register</a>
      @endif --}}

      @endauth
    </div>

  </nav>

</x-nav.navbar>

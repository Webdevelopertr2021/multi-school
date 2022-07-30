@php
    $applicationCount = 0;
    if(auth("supervisor")->user()->teacher_application_permission == 1)
    {
      $data = \App\Models\LeaveRequestApproval::where("supervisor_id",auth("supervisor")->user()->id)->where("status","pending")->get();
      $applicationCount = count($data);
    }
@endphp
<div class="main-sidebar sidebar-style-2">
    <aside id="sidebar-wrapper">
      <div class="sidebar-brand">
        <a href="index.html"> <img alt="image" src="{{ asset("backend/img/logo.png") }}" class="header-logo" /> <span
            class="logo-name">{{ auth("supervisor")->user()->role }}</span>
        </a>
      </div>
      <ul class="sidebar-menu">
        <li class="menu-header">Main</li>
        <li class="dropdown">
          <router-link :to="{name: 'superv.dashboard'}" class="nav-link"><i data-feather="monitor"></i><span>Dashboard</span></router-link>
        </li>
        <li class="dropdown">
          <router-link :to="{name: 'superv.teacher-list'}" class="nav-link"><i data-feather="users"></i><span>My Teachers</span></router-link>
        </li>

        @if (auth("supervisor")->user()->teacher_application_permission == 1)
        <li class="dropdown">
          <router-link :to="{name: 'superv.leave-request'}" class="nav-link"><i data-feather="minus-square"></i>
            <span>Leave Requests @if($applicationCount > 0) <span class="text-danger">({{ $applicationCount }})</span> @endif</span></router-link>
        </li>    
        @endif
        

        {{-- <li class="dropdown">
          <a href="#" class="menu-toggle nav-link has-dropdown"><i
              data-feather="users"></i><span>Teachers</span></a>
          <ul class="dropdown-menu">
            <li><a class="nav-link" href="widget-chart.html">My Teachers</a></li>
            <li><a class="nav-link" href="widget-data.html">Data Widgets</a></li>
          </ul>
        </li> --}}

      </ul>
    </aside>
  </div>
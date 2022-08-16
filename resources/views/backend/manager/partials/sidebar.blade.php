@php
    $applicationCount = 0;
    if(auth("manager")->user()->teacher_application_permission == 1)
    {
      $data = \App\Models\LeaveRequestApproval::where("supervisor_id",auth("manager")->user()->id)->where("status","pending")->get();
      $applicationCount = count($data);
    }
@endphp
<div class="main-sidebar sidebar-style-2">
    <aside id="sidebar-wrapper">
      <div class="sidebar-brand">
        <a href="index.html"> <img alt="image" src="{{ asset("backend/img/logo.png") }}" class="header-logo" /> <span
            class="logo-name">{{ auth("manager")->user()->role }}</span>
        </a>
      </div>
      <ul class="sidebar-menu">
        <li class="menu-header">Main</li>
        <li class="dropdown">
          <router-link :to="{name: 'manager.dashboard'}" class="nav-link"><i data-feather="monitor"></i><span>Dashboard</span></router-link>
        </li>

        <li class="dropdown">
          <router-link :to="{name: 'manager.supervisors'}" class="nav-link"><i data-feather="users"></i><span>Supervisors</span></router-link>
        </li>

        <li class="dropdown">
          <router-link :to="{name: 'manager.student-list'}" class="nav-link"><i data-feather="users"></i><span>Student List</span></router-link>
        </li>

        <li class="dropdown">
          <router-link :to="{name: 'manager.teacher-list'}" class="nav-link"><i data-feather="users"></i><span>My Teachers</span></router-link>
        </li>

        @if (auth("manager")->user()->teacher_application_permission == 1)
        <li class="dropdown">
          <router-link :to="{name: 'manager.leave-request'}" class="nav-link"><i data-feather="minus-square"></i>
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
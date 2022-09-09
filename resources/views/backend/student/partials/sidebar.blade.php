<div class="main-sidebar sidebar-style-2">
    <aside id="sidebar-wrapper">
      <div class="sidebar-brand">
        <a href="index.html"> <img alt="image" src="{{ asset("backend/img/logo.png") }}" class="header-logo" />
        </a>
      </div>
      <ul class="sidebar-menu">
        <li class="menu-header">Main</li>
        <li class="dropdown">
          <router-link :to="{name: 'student.dashboard'}" class="nav-link"><i data-feather="monitor"></i><span>Dashboard</span></router-link>
        </li>

        <li class="dropdown">
          <a href="#" class="menu-toggle nav-link has-dropdown"><i
              data-feather="users"></i><span>Exams</span></a>
          <ul class="dropdown-menu">
            <li><router-link :to="{name: 'student.upcoming-exam'}" class="nav-link">Upcoming Exams</router-link></li>
            <li><a class="nav-link" href="widget-data.html">Exam Reports</a></li>
          </ul>
        </li>
        

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
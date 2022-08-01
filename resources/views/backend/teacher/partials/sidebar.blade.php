<div class="main-sidebar sidebar-style-2">
    <aside id="sidebar-wrapper">
      <div class="sidebar-brand">
        <a href="#"> <img alt="image" src="{{ asset("backend/img/logo.png") }}" class="header-logo" /> <span
          class="logo-name">{{ auth("teacher")->user()->role }}</span>
        </a>
      </div>
      <ul class="sidebar-menu">
        <li class="menu-header">Main</li>
        <li class="dropdown">
          <a href="index.html" class="nav-link"><i data-feather="monitor"></i><span>Dashboard</span></a>
        </li>
        <li class="dropdown">
          <router-link :to="{name: 'teacher.leave'}" class="nav-link"><i data-feather="minus-square"></i><span>Leave Request</span></router-link>
        </li>

        <li class="dropdown">
          <router-link :to="{name: 'teacher.my-students'}" class="nav-link"><i data-feather="users"></i><span>My Students</span></router-link>
        </li>

        <li class="dropdown">
          <a href="#" class="menu-toggle nav-link has-dropdown"><i
              data-feather="briefcase"></i><span>Widgets</span></a>
          <ul class="dropdown-menu">
            <li><a class="nav-link" href="widget-chart.html">Chart Widgets</a></li>
            <li><a class="nav-link" href="widget-data.html">Data Widgets</a></li>
          </ul>
        </li>

      </ul>
    </aside>
  </div>
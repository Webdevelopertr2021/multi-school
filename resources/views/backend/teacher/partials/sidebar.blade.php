<div class="main-sidebar sidebar-style-2">
    <aside id="sidebar-wrapper">
      <div class="sidebar-brand">
        <router-link :to="{name: 'teacher.home'}"> <img alt="image" src="{{ asset("backend/img/logo.png") }}" class="header-logo" /> <span
          class="logo-name">{{ auth("teacher")->user()->role }}</span>
        </router-link>
      </div>
      <ul class="sidebar-menu">
        <li class="menu-header">Main</li>
        <li class="dropdown">
          <router-link :to="{name: 'teacher.home'}" class="nav-link"><i data-feather="monitor"></i><span>Dashboard</span></router-link>
        </li>
        <li class="dropdown">
          <router-link :to="{name: 'teacher.leave'}" class="nav-link"><i data-feather="minus-square"></i><span>Leave Request</span></router-link>
        </li>

        <li class="dropdown">
          <router-link :to="{name: 'teacher.my-students'}" class="nav-link"><i data-feather="users"></i><span>My Students</span></router-link>
        </li>

        <li class="dropdown">
          <router-link :to="{name: 'teacher.my-payments'}" class="nav-link"><i data-feather="dollar-sign"></i><span>My Payments</span></router-link>
        </li>


      </ul>
    </aside>
  </div>
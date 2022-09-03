<div class="main-sidebar sidebar-style-2">
    <aside id="sidebar-wrapper">
      <div class="sidebar-brand">
        <router-link :to="{name: 'admin.dashboard'}" href="index.html"> <img alt="image" src="{{ asset("backend/img/logo.png") }}" class="header-logo" /> <span
            class="logo-name">{{ auth()->user()->role }} {{ auth()->user()->role=='super'?'ADMIN':'' }}</span>
        </router-link>
      </div>
      <ul class="sidebar-menu">
        <li class="menu-header">Main</li>
        <li class="dropdown">
          <router-link :to="{name: 'admin.dashboard'}" class="nav-link"><i data-feather="monitor"></i><span>Dashboard</span></router-link>
        </li>
        <li class="dropdown">
          <a href="#" class="menu-toggle nav-link has-dropdown"><i
              data-feather="layers"></i><span>Schools</span></a>
          <ul class="dropdown-menu">
            <li><router-link :to="{name: 'admin.add-school'}" class="nav-link">Add new school</router-link></li>
            <li><router-link :to="{name : 'admin.school-list'}" class="nav-link">All Schools</router-link></li>
          </ul>
        </li>

        <li class="dropdown">
          <router-link :to="{name: 'admin.add-supervisor'}" class="nav-link"><i data-feather="users"></i><span>Add Supervisor</span></router-link>
        </li>
        
        <li class="dropdown">
          <router-link :to="{name: 'admin.supervisor-list'}" class="nav-link"><i data-feather="users"></i><span>All Supervisors</span></router-link>
        </li>

            


        <li class="dropdown">
          <a href="#" class="menu-toggle nav-link has-dropdown"><i
              data-feather="layers"></i><span>Academic</span></a>
          <ul class="dropdown-menu">
            <li><router-link :to="{name: 'admin.create-class'}" class="nav-link">All Classes</router-link></li>
            <li><router-link :to="{name : 'admin.create-section'}" class="nav-link">All Section</router-link></li>
          </ul>
        </li>

        <li class="dropdown">
          <a href="#" class="menu-toggle nav-link has-dropdown"><i
              data-feather="align-justify"></i><span>Students</span></a>
          <ul class="dropdown-menu">
            <li><router-link :to="{name: 'admin.add-student'}" class="nav-link">Add Students</router-link></li>
            <li><router-link :to="{name : 'admin.student-list'}" class="nav-link">All Students</router-link></li>
          </ul>
        </li>
        <li class="dropdown">
          <router-link :to="{name: 'admin.all-teacher'}" class="nav-link"><i data-feather="users"></i><span>All Teachers</span></router-link>
        </li>

        <li class="dropdown">
          <router-link :to="{name: 'admin.leave-request'}" class="nav-link"><i data-feather="minus-square"></i>
            <span>Leave Requests</span></router-link>
        </li>

        <li class="dropdown">
          <router-link :to="{name: 'admin.payment-list'}" class="nav-link"><i data-feather="dollar-sign"></i>
            <span>Payments</span></router-link>
        </li>
        
        <li class="dropdown">
          <router-link :to="{name: 'admin.staff-view'}" class="nav-link"><i data-feather="dollar-sign"></i>
            <span>Staff Tree</span></router-link>
        </li>

        <li class="dropdown">
          <router-link :to="{name: 'admin.settings'}" class="nav-link"><i data-feather="settings"></i>
            <span>Settings</span></router-link>
        </li>

        <li class="dropdown">
          <a href="#" class="menu-toggle nav-link has-dropdown"><i
              data-feather="layers"></i><span>Question Bank</span></a>
          <ul class="dropdown-menu">
            <li><router-link :to="{name: 'admin.add-exam'}" class="nav-link">Add Exam</router-link></li>
            <li><router-link :to="{name: 'admin.exam-list'}" class="nav-link">Exam List</router-link></li>
            <li><router-link :to="{name : 'admin.exam-category'}" class="nav-link">Exam Category</router-link></li>
          </ul>
        </li>

      </ul>
    </aside>
  </div>
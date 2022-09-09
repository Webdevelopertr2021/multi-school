<!DOCTYPE html>
<html lang="en">


<!-- index.html  21 Nov 2019 03:44:50 GMT -->
<head>
  <meta charset="UTF-8">
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
  <title>@yield("title")</title>
  <!-- General CSS Files -->
  <link rel="stylesheet" href="{{ asset("backend/css/app.min.css") }}">
  <!-- Template CSS -->
  <link rel="stylesheet" href="{{ asset("backend/css/style.css") }}">
  <link rel="stylesheet" href="{{ asset("backend/css/components.css") }}">
  <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs4/jszip-2.5.0/dt-1.12.1/b-2.2.3/b-colvis-2.2.3/b-html5-2.2.3/b-print-2.2.3/datatables.min.css"/>
  <!-- Custom style CSS -->
  <link rel="stylesheet" href="{{ asset("backend/css/custom.css") }}">
  <link rel='shortcut icon' type='image/x-icon' href='{{ asset("backend/img/favicon.ico") }}' />
</head>

<body>
  <div class="loader"></div>
  <div id="{{ auth()->user()->role??"student" }}_app">
    <vue-progress-bar></vue-progress-bar>
    <div class="main-wrapper main-wrapper-1">

      <div class="navbar-bg"></div>

      {{-- Header --}}
      @yield('header')
      {{-- End --}}

      {{-- Sidebar --}}
      @yield('sidebar')
      {{-- End --}}

      <!-- Main Content -->
      <div class="main-content">
        <section class="section">
          @yield("content")
        </section>
      </div>
      <footer class="main-footer">
        <div class="footer-left">
          <a href="#">Developed by, <b>Tekno</b></a>
        </div>
        <div class="footer-right">
        </div>
      </footer>
    </div>
  </div>
  <!-- General JS Scripts -->
  
  <script src="{{ asset("backend/js/app.min.js") }}"></script>
  
 
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/v/bs4/jszip-2.5.0/dt-1.12.1/b-2.2.3/b-colvis-2.2.3/b-html5-2.2.3/b-print-2.2.3/datatables.min.js"></script>

  <script src="{{ asset("js/app.js") }}"></script>
  <!-- Page Specific JS File -->
  <script src="{{ asset("backend/js/page/index.js") }}"></script>
  <!-- Template JS File -->
  <script src="{{ asset("backend/js/scripts.js") }}"></script>
  <!-- Custom JS File -->
  <script src="{{ asset("backend/js/custom.js") }}"></script>
</body>


<!-- index.html  21 Nov 2019 03:47:04 GMT -->
</html>
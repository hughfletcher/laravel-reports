<!DOCTYPE html>
<html>
    <head>
      <meta charset="utf-8">
      <meta http-equiv="X-UA-Compatible" content="IE=edge">
      <title>Laravel Reports</title>
      <!-- Tell the browser to be responsive to screen width -->
      <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
      <link href="{{ asset('vendor/reports/css/bootstrap.min.css') }}" rel="stylesheet">
      <link href="{{ asset('vendor/reports/css/AdminLTE.min.css') }}" rel="stylesheet">
      <link href="{{ asset('vendor/reports/css/skin-blue-light.min.css') }}" rel="stylesheet">
      <link href="{{ asset('vendor/reports/css/app.css') }}" rel="stylesheet">
      @stack('css')
      <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
    </head>
<body class="hold-transition skin-blue-light sidebar-mini fixed">
<div class="wrapper">

  <header class="main-header sticky-table-header-offset">
    <!-- Logo -->
    <a href="../../index2.html" class="logo">
      <!-- mini logo for sidebar mini 50x50 pixels -->
      <span class="logo-mini">LR</i></span>
      <!-- logo for regular state and mobile devices -->
      <span class="logo-lg">Laravel<strong>Reports</strong></span>
    </a>
    <!-- Header Navbar: style can be found in header.less -->
    <nav class="navbar navbar-static-top">
      <!-- Sidebar toggle button-->
      <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
        <span class="sr-only">Toggle navigation</span>
        <i class="fas fa-bars"></i>
      </a>
    </nav>
  </header>
  <!-- Left side column. contains the logo and sidebar -->
  <aside class="main-sidebar">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
      <!-- Sidebar user panel -->

      <!-- /.search form -->
      <!-- sidebar menu: : style can be found in sidebar.less -->
      <ul class="sidebar-menu" data-widget="tree">
        <li class="header">MAIN NAVIGATION</li>
        @foreach(Reports::all() as $report_item)
            @if($report_item instanceof Reports\Directory)
          <li>
              <a href="{{ route('reports.list', ['dir' => $report_item->id])}}">
                <i class="fa"></i> <span>{{ $report_item->name }}</span>
                <span class="pull-right-container">
                </span>
              </a>
          </li>
      @endif
        @endforeach
      </ul>
    </section>
    <!-- /.sidebar -->
  </aside>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper" id="{{ str_replace('.', '-', Route::currentRouteName()) }}">
      @hasSection('content-title')
      <section class="content-header">
        @yield('content-title')
      </section>
        @endif

    <!-- Main content -->
    <section class="content">
        @if($errors->isNotEmpty())
        <div class="callout callout-danger">
          <h4>There are issues with your form...</h4>
          @foreach ($errors->all() as $error)
              <p>{{ $error }}</p>
          @endforeach
        </div>
        @endif
        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>

        @endif
        @yield('content')

    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
  <footer class="main-footer">
    <div class="pull-right hidden-xs">
      <b>Mark</b> {{ App::environment('local') ? 'dev' : basename(base_path()) }}
    </div>
    <strong>Copyright &copy; 2017-2018.</strong> All rights reserved.
  </footer>


</div>
<!-- ./wrapper -->

<script src="{{ asset('vendor/reports/js/jquery.min.js') }}"></script>
<script src="{{ asset('vendor/reports/js/bootstrap.min.js') }}"></script>
<script src="{{ asset('vendor/reports/js/adminlte.min.js') }}"></script>
@stack('js')
</body>
</html>

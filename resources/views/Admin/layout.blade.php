<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta http-equiv="x-ua-compatible" content="ie=edge">
  <meta name="csrf-token" content="{{csrf_token()}}">

  <title>@setting('sitename') | @yield('page')</title>

  <!-- Font Awesome Icons -->
  <link rel="stylesheet" href="{{asset('plugins/font-awesome/css/font-awesome.min.css')}}">
  <!-- Theme style -->
  <link rel="stylesheet" href="{{asset('dist/css/adminlte.min.css')}}">
  <!-- Google Font: Source Sans Pro -->
  <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
  <link rel="stylesheet" type="text/css" href="{{asset('css/dataTables.bootstrap4.css')}}">
  <link rel="stylesheet" type="text/css" href="{{asset('css/custom.css')}}">
  <script src="{{asset('ckeditor/ckeditor.js')}}"></script>
  
</head>
<body class="hold-transition sidebar-mini">
  <div class="wrapper" id="app">
    <!-- Navbar -->
    <nav class="main-header navbar navbar-expand bg-white navbar-light border-bottom">
      <!-- Left navbar links -->
      <ul class="navbar-nav">
        <li class="nav-item">
          <a class="nav-link" data-widget="pushmenu" href="#"><i class="fa fa-bars"></i></a>
        </li>
        <li class="nav-item d-none d-sm-inline-block">
          <a href="/al-admin" class="nav-link">Home</a>
        </li>
        <li class="nav-item d-none d-sm-inline-block">
          <a href="#" class="nav-link">Contact</a>
        </li>
      </ul>

      <!-- SEARCH FORM -->
      <form class="form-inline ml-3">
        <div class="input-group input-group-sm">
          <input class="form-control form-control-navbar" type="search" placeholder="Search" aria-label="Search">
          <div class="input-group-append">
            <button class="btn btn-navbar" type="submit">
              <i class="fa fa-search"></i>
            </button>
          </div>
        </div>
      </form>

      <!-- Right navbar links -->
      <ul class="navbar-nav ml-auto">
      {{-- <li class="nav-item">
        <a class="nav-link" data-widget="control-sidebar" data-slide="true" href="#"><i
            class="fa fa-th-large"></i></a>
          </li> --}}
        </ul>
      </nav>
      <!-- /.navbar -->

      <!-- Main Sidebar Container -->
      <aside class="main-sidebar sidebar-dark-primary elevation-4">
        <!-- Brand Logo -->
        <a href="/al-admin" class="brand-link">
          <img src="/dist/img/AdminLTELogo.png" alt="AdminLTE Logo" class="brand-image img-circle elevation-3"
          style="opacity: .8">
          <span class="brand-text font-weight-light">@setting('sitename')</span>
        </a>

        <!-- Sidebar -->
        <div class="sidebar">
          <!-- Sidebar user panel (optional) -->
          <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image">
              <img src="{{(!empty(Auth::user()->photo) ? asset(Auth::user()->photo) : asset('images/avatar.jpg'))}}" class="img-circle elevation-2" alt="{{Auth::user()->firstname." ".Auth::user()->lastname}}" title="{{Auth::user()->firstname." ".Auth::user()->lastname}}">
            </div>
            <div class="info">
              <a href="/al-admin/user/{{Auth::user()->id}}/edit" class="d-block">{{Auth::user()->firstname." ".Auth::user()->lastname}}</a>
            </div>
          </div>

          <!-- Sidebar Menu -->
          <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
          <!-- Add icons to the links using the .nav-icon class
           with font-awesome or any other icon font library -->
           
           <li class="nav-item">
            <a href="/al-admin" class="nav-link">
              <i class="nav-icon fa fa-dashboard"></i>
              <p>
                Dashboard
              </p>
            </a>
          </li>
          
          <li class="nav-item has-treeview">
            <a href="/#" class="nav-link">
              <i class="nav-icon fa fa-pencil"></i>
              <p>
                Post
                <i class="right fa fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              @can('create articles')
              <li class="nav-item">
                <a href="/al-admin/post/create" class="nav-link">
                  <i class="fa fa-plus nav-icon"></i>
                  <p>New Post</p>
                </a>
              </li>
              @endcan
              <li class="nav-item">
                <a href="/al-admin/post" class="nav-link">
                  <i class="fa fa-list nav-icon"></i>
                  <p>All Posts</p>
                </a>
              </li>
            </ul>
          </li>
          
          @role('super-admin')
          <li class="nav-item">
            <a href="/al-admin/settings" class="nav-link">
              <i class="nav-icon fa fa-cog"></i>
              <p>
                Settings
              </p>
            </a>
          </li>
          @endrole
          @hasanyrole('super-admin|moderator')
          <li class="nav-item">
            <a href="/al-admin/user" class="nav-link">
              <i class="nav-icon fa fa-user"></i>
              <p>
               Users
             </p>
           </a>
         </li>
         @endhasanyrole
         <li class="nav-item">
          <logout></logout>
        </li>
      </ul>
    </nav>
    <!-- /.sidebar-menu -->
  </div>
  <!-- /.sidebar -->
</aside>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0 text-dark">@yield('page')</h1>
        </div><!-- /.col -->
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item active">Dashboard</li>
          </ol>
        </div><!-- /.col -->
      </div><!-- /.row -->
    </div><!-- /.container-fluid -->
  </div>
  <!-- /.content-header -->

  <!-- Main content -->
  <section class="content">
    <div class="container-fluid">

      @yield('content')
      
    </div><!--/. container-fluid -->
  </section>
  <!-- /.content -->
</div>
<!-- /.content-wrapper -->

<!-- Control Sidebar -->
<aside class="control-sidebar control-sidebar-dark">
  <!-- Control sidebar content goes here -->
</aside>
<!-- /.control-sidebar -->

<!-- Main Footer -->
<footer class="main-footer">
  <!-- To the right -->
  <div class="float-right d-sm-none d-md-block">
    Anything you want
  </div>
  <!-- Default to the left -->
  <strong>Copyright &copy; 2014-2018 <a href="https://altechtic.com.ng">@setting('sitename') </a>.</strong> All rights reserved.
</footer>
</div>
<!-- ./wrapper -->

<!-- REQUIRED SCRIPTS -->
<!-- jQuery -->
<script src="{{asset('js/jquery.min.js')}}"></script>
<script>
  $(document).ready(function () {
// highlight submenu item
var a = $('a[href="' + this.location.pathname + '"]').addClass('active');
// Open
$('a[href="' + this.location.pathname + '"]').parents('li').addClass('menu-open');
// Highlight parent menu item.
$('a[href="' + this.location.pathname + '"]').parents('li').children('a').addClass('active');
});
</script>
<!-- Bootstrap -->
<script src="{{asset('plugins/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
<script src="{{asset('js/app.js')}}"></script>
<!-- AdminLTE App -->
<script src="{{asset('dist/js/adminlte.js')}}"></script>

<!-- OPTIONAL SCRIPTS -->
{{-- 
  <script src="{{asset('vendor/laravel-filemanager/js/lfm.js')}}"></script> --}}
  <script src="{{ asset('js/datatables/datatables.min.js') }}"></script>
  <script src="{{ asset('js/datatables/dataTables.bootstrap4.js') }}"></script>
  <script>
  //$('#lfm').filemanager('image');
</script>
<script>
  {!! \File::get(base_path('vendor/unisharp/laravel-filemanager/public/js/stand-alone-button.js')) !!}
</script>
<script>
  $('#lfm').filemanager('image');
</script>
{{-- <script src="{{ asset('js/datatables/datatables-init.js') }}"></script> --}}

<!-- PAGE PLUGINS -->
{{-- <script src="{{asset('plugins/sparkline/jquery.sparkline.min.js')}}"></script>

<script src="{{asset('plugins/jvectormap/jquery-jvectormap-1.2.2.min.js')}}"></script>
<script src="{{asset('plugins/jvectormap/jquery-jvectormap-world-mill-en.js')}}"></script>

<script src="{{asset('plugins/slimScroll/jquery.slimscroll.min.js')}}"></script>

<script src="{{asset('plugins/chartjs-old/Chart.min.js')}}"></script>


<script src="{{asset('dist/js/pages/dashboard2.js')}}"></script> --}}
</body>
</html>

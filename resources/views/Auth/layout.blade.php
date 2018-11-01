<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
	<meta charset="utf-8">
	<meta name="author" content="Kodinger">
	<meta name="viewport" content="width=device-width,initial-scale=1">
	<meta name="csrf-token" content="{{csrf_token()}}">
	<title>@yield('title')</title>
	<link rel="stylesheet" type="text/css" href="{{asset('css/app.css')}}">
	<link rel="stylesheet" type="text/css" href="{{asset('css/my-login.css')}}">
	<link rel="stylesheet" href="{{asset('vendor/font-awesome/css/font-awesome.min.css')}}">
	<link rel="stylesheet" type="text/css" href="{{asset('css/custom.css')}}">
</head>
<body class="my-login-page">
	<section class="h-100">
		<div class="container h-100">
			<div class="row justify-content-md-center h-100">
				<div class="card-wrapper">
					<div class="brand">
						<img src="{{asset('img/logo.jpg')}}">
					</div>
					<div class="card fat">
						<div class="card-body">
							<h4 class="card-title">@yield('ct')</h4>
							<div id="app">
								{{-- @if(session('success'))
								{!! session('success') !!}
								@endif --}}
								@yield('content')
							</div>
						</div>
					</div>
					<div class="footer">
						Copyright &copy; 2017 &mdash; {{env('APP_NAME')}} 
					</div>
				</div>
			</div>
		</div>
	</section>

	{{-- <script src="{{asset('js/jquery.min.js')}}"></script> --}}
	<script src="{{asset('js/app.js')}}"></script>
	<script src="{{asset('js/my-login.js')}}"></script>
	 
</body>
</html>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Reliance - HRIS</title>	
	<link rel="stylesheet" href="{{ elixir('css/app.css') }}">
	{!! HTML::style('plugins/font-awesome/css/font-awesome.min.css') !!}
	<link href='https://fonts.googleapis.com/css?family=Lato:300,400,700' rel='stylesheet' type='text/css'>
	<link rel="shortcut icon" href="{{ url('fav_reliance.ico') }} "/>
</head>
<body style="background:#f5f5f5">
	<div class="container">
		<div class="row">
			<div class="col-xs-12 col-sm-12 col-md-12 text-center">
				{!! HTML::image('logo_reliance.png', 'Logo Reliance', ['class' => 'logo-login'], ['secure' => 'yes']) !!}
			</div>
		</div>
		<div class="row">
			<div class="col-md-6 col-md-offset-3" style="">
				@yield('area', '[area]')
			</div>
		</div>
	</div>	
	{!! HTML::script('plugins/jquery/jquery-2.1.4.min.js') !!}
	@include('plugins.select2')
</body>
</html>
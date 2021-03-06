@extends('widget_templates.'. (isset($widget_template) ? $widget_template : 'plain_no_title'))

@if ((isset($widget_errors) && !$widget_errors->count() || !isset($widget_errors)))

	@section('widget_title')
	@overwrite

	@section('widget_body')
		<div class="navbar-header">
			<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
				<span class="sr-only">Toggle navigation</span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</button>
			<span class="breadcrumb">
				<a class="first" href="{{route('hr.organisations.index')}}">
					{!! HTML::image('logo_reliance.png', 'Logo Reliance', ['class' => 'logo-breadcrumb'], []) !!}
				</a>
				<div class="hidden-sm hidden-md hidden-lg"><br></div>
				@forelse($breadcrumb as $key => $value)
					<i class="fa fa-angle-double-right"></i><a class="" href="{{$value['route']}}">{{$value['name']}}</a> 
				@empty 
				@endforelse 
			</span>
		</div>
		 
		<ul class="nav navbar-top-links navbar-right">
			@if(isset($filter))
				<li>
					<a href="javascript:;" class="open-filter"><i class="fa fa-search"></i></a>
				</li>
			@endif
			<li class="dropdown">
				<a class="dropdown-toggle" data-toggle="dropdown" href="#">
					@if ((File::exists(Session::get('user.avatar')))&&(!Session::get('user.avatar')))
						{!! HTML::image(Session::get('user.avatar'), '', array( 'width' => 32, 'height' => 32, 'class' => 'img-rounded' )) !!} 
					@else
					 	{!! HTML::image('/tmp_avatar.png', '', array( 'width' => 32, 'height' => 32, 'class' => 'img-rounded' )) !!} 
					@endif &nbsp; {{Session::get('user.name')}} &nbsp;&nbsp; <i class="fa fa-caret-down"></i>
				</a>
				<ul class="dropdown-menu dropdown-user">	
					<li><a href="{{ route('hr.persons.show', [Session::get('loggedUser'), 'org_id' => Session::get('user.organisationid')]) }}"><i class="fa fa-dashboard fa-fw"></i> Dashboard</a></li>
					@if (Session::get('user.menuid')<=3)
						<li><a href="{{ route('hr.queue.index') }}"><i class="fa fa-bell fa-fw"></i> Notifikasi</a></li>
					@endif
					<li><a href="javascript:;" data-toggle="modal" data-target="#add_widget" data-org="{{ isset($data['id']) ? $data['id'] : Session::get('user.organisation_id') }}">
						<i class="fa fa-plus fa-fw"></i> Tambah Widget</a>
					</li>
					<li><a href="{{route('hr.password.get')}}"><i class="fa fa-gear fa-fw"></i> Ubah Password</a></li>
					<li><a href="{{route('hr.logout.get')}}"><i class="fa fa-sign-out fa-fw"></i> Logout</a></li>
				</ul>	            
			</li>	        
		</ul>		 
	@overwrite
@else
	@section('widget_title')
	@overwrite
	@section('widget_body')
	@overwrite
@endif
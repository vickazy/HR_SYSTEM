@if (isset($widget_errors) && $widget_errors->count())
	<div class="alert alert-danger">
		<h4>{{$widget_data['widget_name']}}</h4>
		@foreach ($widget_errors->all('<li>:message</li>') as $message)
			{!! $message !!}
		@endforeach
	</div>
@else		
	@yield('widget_body', '[widget_body]')
@endif
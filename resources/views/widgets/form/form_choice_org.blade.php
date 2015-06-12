@extends('widget_templates.'.($widget_template ? $widget_template : 'plain'))

@section('widget_title')	
	
@overwrite

@section('widget_body')
	{!! Form::open(['url' => $widget_data['form_url'], 'method' => 'post']) !!}	
		<div class="form-group">
			<div class="col-md-2">
				<label class="control-label">TEST</label>
			</div>	
			<div class="col-md-10">
				<select name="TEST" id="org" class="select2-skin" style="width:100%">
					@if(isset($widget_data['data']))
					@foreach($widget_data['data'] as $key => $value)
						<option value="">{{$value['name']}}</option>
					@endforeach
					@endif
				</select>
			</div>
		</div>
		<div class="form-group">
			<div class="col-md-12 text-right">
				<input type="submit" class="btn btn-primary" value="Pilih">
			</div>
		</div>
	{!! Form::close() !!}
@overwrite	
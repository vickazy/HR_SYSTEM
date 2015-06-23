@extends('widget_templates.'.($widget_template ? $widget_template : 'plain'))

@if (!$widget_error_count)
	@section('widget_title')
	<h1> {!! $widget_title  or 'Kalender' !!} </h1>
	<small>Total data {{$CalendarComposer['widget_data']['calendarlist']['calendar-pagination']->total()}}</small>
	@overwrite

	@section('widget_body')
		<a href="{{ $CalendarComposer['widget_data']['calendarlist']['route_create'] }}" class="btn btn-primary">Tambah</a>
		@if(isset($CalendarComposer['widget_data']['calendarlist']['calendar']))
			<div class="clearfix">&nbsp;</div>
			<table class="table">
				<thead>
					<tr class="row">
						<th class="col-sm-3">Nama Kalender</th>
						<th class="col-sm-4">Hari Kerja</th>
						<th class="col-sm-2">Jam Kerja</th>
						<th class="col-sm-3">&nbsp;</th>
					</tr>
				</thead>
				@foreach($CalendarComposer['widget_data']['calendarlist']['calendar'] as $key => $value)
					<tbody>
						<tr class="row">
							<td>
								{{$value['name']}}
							</td>
							<td>
								{{str_replace(',', ', ', $value['workdays'])}}
							</td>
							<td>
								@time_indo($value['start']) - 
								@time_indo($value['end'])
							</td>
							<td class="text-right">
								<a href="javascript:;" class="btn btn-default" data-toggle="modal" data-target="#delete" data-delete-action="{{ route('hr.calendars.delete', [$value['id'], 'org_id' => $data['id']]) }}"><i class="fa fa-trash"></i></a>
								<a href="{{route('hr.calendars.edit', [$value['id'], 'org_id' => $data['id']])}}" class="btn btn-default"><i class="fa fa-pencil"></i></a>
								<a href="{{route('hr.calendars.show', [$value['id'], 'org_id' => $data['id']])}}" class="btn btn-default"><i class="fa fa-eye"></i></a>
							</td>
						</tr>
					</tbody>
				@endforeach
			</table>

			<div class="row">
				<div class="col-sm-12 text-center">
					<p>Menampilkan {!!$CalendarComposer['widget_data']['calendarlist']['calendar-display']['from']!!} - {!!$CalendarComposer['widget_data']['calendarlist']['calendar-display']['to']!!}</p>
					{!!$CalendarComposer['widget_data']['calendarlist']['calendar-pagination']->appends(Input::all())->render()!!}
				</div>
			</div>

			<div class="clearfix">&nbsp;</div>
		@endif
	@overwrite	
@else
	@section('widget_title')
	@overwrite

	@section('widget_body')
	@overwrite
@endif
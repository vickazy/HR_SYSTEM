@extends('widget_templates.'.($widget_template ? $widget_template : 'plain'))

@if (!$widget_error_count)
	<?php
		$PolicyComposer['widget_data']['idlelist']['idle-pagination']->setPath(route('hr.idles.index'));
	?>

	@section('widget_title')
		<h1> {!! $widget_title or 'Idle' !!} </h1>
		<small>Total data {{$PolicyComposer['widget_data']['idlelist']['idle-pagination']->total()}}</small>
		@if(isset($PolicyComposer['widget_data']['idlelist']['active_filter']) && !is_null($PolicyComposer['widget_data']['idlelist']['active_filter']))
			 <div class="clearfix">&nbsp;</div>
			@foreach($PolicyComposer['widget_data']['idlelist']['active_filter'] as $key => $value)
				<span class="active-filter">{{$value}}</span>
			@endforeach
		@endif
	@overwrite

	@section('widget_body')
		@if((int)Session::get('user.menuid')<=3)
			<a href="{{ $PolicyComposer['widget_data']['idlelist']['route_create'] }}" class="btn btn-primary">Tambah</a>
		@endif
		@if(isset($PolicyComposer['widget_data']['idlelist']['idle']))
			<div class="clearfix">&nbsp;</div>			
			<table class="table table-hover table-affix">
				<thead>
					<tr>
						<th>No</th>
						<th>Sejak</th>
						<th>Diubah Oleh</th>
						<th>Idle Pertama</th>
						<th>Idle Kedua</th>
						<th>Idle Ketiga</th>
						<th>&nbsp;</th>
					</tr>
				</thead>
				<tbody>
					<?php $i = $PolicyComposer['widget_data']['idlelist']['idle-display']['from'];?>
					@forelse($PolicyComposer['widget_data']['idlelist']['idle'] as $key => $value)
						<tr>
							<td>
								{{$i}}
							</td>
							<td>
								{{ date('d-m-Y', strtotime($value['start'])) }}
							</td>
							<td>
								{{$value['createdby']['name']}}
							</td>
							<td>
								{{gmdate('H:i:s', $value['margin_bottom_idle'])}} - {{gmdate('H:i:s', $value['idle_1'])}}
							</td>
							<td>
								{{gmdate('H:i:s', ($value['idle_1'] + 1))}} - {{gmdate('H:i:s', $value['idle_2'])}}
							</td>
							<td>
								{{gmdate('H:i:s', ($value['idle_2'] + 1))}} - Infinite
							</td>
							<td class="text-right">
								@if((int)Session::get('user.menuid')<=2)
									<a href="javascript:;" class="btn btn-default" data-toggle="modal" data-target="#delete" data-delete-action="{{ route('hr.policies.delete', [$value['id'], 'org_id' => $data['id'] ]) }}"><i class="fa fa-trash"></i></a>
								@endif
								@if((int)Session::get('user.menuid')<=3)
									<a href="{{route('hr.policies.edit', [$value['id'], 'org_id' => $data['id']])}}" class="btn btn-default"><i class="fa fa-pencil"></i></a>
								@endif
							</td>
						</tr>
						<?php $i++;?>
					@empty 
						<tr>
							<td class="text-center" colspan="7">Tidak ada data</td>
						</tr>
					@endforelse
				</tbody>
			</table>

			<div class="row">
				<div class="col-sm-12 text-center">
					<p>Menampilkan {!!$PolicyComposer['widget_data']['idlelist']['idle-display']['from']!!} - {!!$PolicyComposer['widget_data']['idlelist']['idle-display']['to']!!}</p>
					{!!$PolicyComposer['widget_data']['idlelist']['idle-pagination']->appends(Input::all())->render()!!}
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
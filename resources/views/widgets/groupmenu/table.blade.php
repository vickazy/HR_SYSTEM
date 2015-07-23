@extends('widget_templates.'.($widget_template ? $widget_template : 'plain'))

@if (!$widget_error_count)
	<?php $MenuComposer['widget_data']['menu']['menu-pagination']->setPath(route('hr.authgroups.show', $id)); ?>

	@section('widget_title')
		<h1> {!! $widget_title  or 'Otentikasi Group' !!} </h1>
		<small>Total data {{$MenuComposer['widget_data']['menu']['menu-pagination']->total()}}</small>
	@overwrite

	@section('widget_body')

		@if(isset($MenuComposer['widget_data']['menu']['menu']))
			<div class="clearfix">&nbsp;</div>
			<table class="table table-hover table-affix">
				<thead>
					<tr>
						<th class="">Aplikasi</th>			
						<th>Menu</th>
						<th class="text-center">Aktif</th>
					</tr>
				</thead>
				<tbody>
					<?php $prev = null; $count = 0;?>
					<?php $i = $MenuComposer['widget_data']['menu']['menu-display']['from'];?>
					<form class="check" action="" method="post">
						@forelse($MenuComposer['widget_data']['menu']['menu'] as $key => $value)
							<tr>
								<td> 
									{{ $value['application']['name'] }}
								</td>
								<td>{{ $value['name'] }}</td>
								<td class="text-center">
									@foreach($data['menus'] as $key2 => $value2)
										@if($value2['id'] == $value['id'])
											<input type="checkbox" name="active" checked>
											<?php $flag = true;?>
										@endif
									@endforeach
									@if(!isset($flag))
										<input type="checkbox" name="active">
									@endif
								</td>
							</tr>
							<?php $prev = $value['application']['id'];?>
						@empty 
							<tr>
								<td class="text-center" colspan="3">Tidak ada data</td>
							</tr>
						@endforelse
					</form>
				</tbody>
			</table>
		
			<div class="row">
				<div class="col-sm-12 text-center">					
					<p>Menampilkan {!!$MenuComposer['widget_data']['menu']['menu-display']['from']!!} - {!!$MenuComposer['widget_data']['menu']['menu-display']['to']!!}</p>
					{!!$MenuComposer['widget_data']['menu']['menu-pagination']->appends(Input::all())->render()!!}					
				</div>
			</div>

			<div class="clearfix">&nbsp;</div>
		@endif
		</div>
		</div>
	@overwrite
@else
	@section('widget_title')
	@overwrite

	@section('widget_body')
	@overwrite
@endif
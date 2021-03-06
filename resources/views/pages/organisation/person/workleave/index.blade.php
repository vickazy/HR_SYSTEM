@section('nav_topbar')
	@include('widgets.common.nav_topbar', 
	['breadcrumb' => [
						['name' => $data['name'], 'route' => route('hr.organisations.show', [$data['id'], 'org_id' => $data['id']]) ], 
						['name' => 'Data Karyawan', 'route' => route('hr.persons.index', ['org_id' => $data['id']]) ],
						['name' => $person['name'], 'route' => route('hr.persons.show', ['id' => $person['id'], 'person_id' => $person['id'],'org_id' => $data['id'] ])], 
						['name' => 'Pengaturan Cuti', 'route' => route('hr.person.workleaves.index', ['id' => $person['id'], 'person_id' => $person['id'],'org_id' => $data['id'] ])], 
					]
	])
@stop

@section('nav_sidebar')
	@include('widgets.common.nav_sidebar', [
		'widget_template'		=> 'plain_no_title',
		'widget_title'			=> 'Structure',		
		'widget_title_class'	=> 'text-uppercase ml-10 mt-20',
		'widget_body_class'		=> '',
		'widget_options'		=> 	[
										'sidebar'						=> 
										[
											'search'					=> [],
											'sort'						=> [],
											'page'						=> 1,
											'per_page'					=> 100,
											'active_workleave_person'	=> 'yes'
										]
									]
	])
@overwrite

@section('content_filter')
	@include('widgets.common.filter', [
		'widget_template'		=> 'plain_no_title',
		'widget_options'		=> [
									'form_url'	=> route('hr.person.workleaves.index', ['org_id' => $data['id'], 'person_id' => $person['id'], 'page' => (Input::has('page') ? Input::get('page') : 1)])
									],
	])
@overwrite

@section('content_body')
	<div class="row">
		<div class="col-sm-4">
			@include('widgets.organisation.person.workleave.followworkleave', [
					'widget_template'		=> 'plain',
					'widget_title'			=> '<h5 class="mt-10">Kuota Cuti "'.$person['name'].'"</h5>',
					'widget_title_class'	=> 'text-uppercase ml-10 mt-20',
					'widget_body_class'		=> '',
					'widget_options'		=> 	[
													'workleavelist'			=>
													[
														'organisation_id'	=> $data['id'],
														'search'			=> ['status' => 'CN', 'chartid' => $wleave['chart_id'], 'withattributes' => ['workleave']],
														'sort'				=> ['chart_id' => 'asc'],
														'page'				=> 1,
														'per_page'			=> 100
													],
												],
					'wleave'				=> $wleave

				])
		</div>
		<div class="col-sm-4">
			@include('widgets.organisation.person.workleave.left_quota', [
					'widget_template'		=> 'plain',
					'widget_title'			=> '<h5 class="mt-30">Sisa Cuti "'.$person['name'].'"</h5>',
					'widget_title_class'	=> 'text-uppercase ml-10 mt-20',
					'widget_body_class'		=> '',
					'widget_options'		=> 	[
													'personlist'			=>
													[
														'organisation_id'	=> $data['id'],
														'search'			=> ['id' => $person['id'], 'globalworkleave' => ['organisationid' => $data['id'], 'on' => date('Y-m-d')]],
														'sort'				=> ['persons.name' => 'asc'],
														'page'				=> 1,
														'per_page'			=> 1,
													]
												]
				])
		</div>
		<div class="col-sm-4">
			@include('widgets.organisation.person.workleave.quota_workleave_year', [
					'widget_template'		=> 'plain',
					'widget_title'			=> '<h5 class="mt-30">Cuti yang Timbul "'.$person['name'].'"</h5>',
					'widget_title_class'	=> 'text-uppercase ml-10 mt-20',
					'widget_body_class'		=> '',
					'widget_options'		=> 	[
													'workleavelist'			=>
													[
														'organisation_id'	=> $data['id'],
														'search'			=> ['personid' => $person['id'], 'ondate' => [date('Y-m-d', strtotime('first day of this year')), date('Y-m-d', strtotime('last day of this year'))], 'quota' => true, 'sumquota' => true],
														'sort'				=> [],
														'page'				=> 1,
														'per_page'			=> 1,
													]
												]
				])
		</div>
	</div>

	@include('widgets.organisation.person.workleave.table', [
		'widget_template'		=> 'panel',
		'widget_title'			=> 'Pengaturan Cuti "'.$person['name'].'"'.((Input::has('page') && (int)Input::get('page') > 1) ? '<small class="font-16"> Halaman '.Input::get('page').'</small>' : null),
		'widget_title_class'	=> 'text-uppercase ml-10 mt-20',
		'widget_body_class'		=> '',
		'widget_options'		=> 	[
										'workleavelist'			=>
										[
											'organisation_id'	=> $data['id'],
											'search'			=> array_merge(['personid' => $person['id'], 'withattributes' => ['createdby', 'parent']], (isset($filtered['search']) ? $filtered['search'] : [])),
											'sort'				=> (isset($filtered['sort']) ? $filtered['sort'] : ['start' => 'asc']),
											'active_filter'		=> (isset($filtered['active']) ? $filtered['active'] : null),
											'page'				=> (Input::has('page') ? Input::get('page') : 1),
											'per_page'			=> 12,
											'route_create'		=> route('hr.person.workleaves.create', ['org_id' => $data['id'], 'person_id' => $person['id']])
										]
									]
	])
	
	{!! Form::open(['url' => 'javascript:;','method' => 'POST', 'files' => true]) !!}
		@include('widgets.modal.import_csv', [
			'widget_template'		=> 'plain_no_title',
			'class_id'				=> 'import_csv_person_workleave'
		])
	{!! Form::close() !!}

	{!! Form::open(array('route' => array('hr.person.workleaves.delete', 0),'method' => 'DELETE')) !!}
		@include('widgets.modal.delete', [
			'widget_template'		=> 'plain_no_title'
		])
	{!! Form::close() !!}
@overwrite

@section('content_footer')
@overwrite
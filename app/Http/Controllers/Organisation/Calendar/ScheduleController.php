<?php namespace App\Http\Controllers\Organisation\Calendar;
use Input, Session, App, Paginator, Redirect, DB, Config, Response, DateTime, DateInterval, DatePeriod;
use App\Http\Controllers\BaseController;
use Illuminate\Support\MessageBag;
use App\Console\Commands\Saving;
use App\Console\Commands\Getting;
use App\Models\Calendar;
use App\Models\Schedule;

class ScheduleController extends BaseController
{
	protected $controller_name = 'jadwal';

	public function index($page = 1)
	{
		if(Input::has('org_id'))
		{
			$org_id 							= Input::get('org_id');
		}
		else
		{
			$org_id 							= Session::get('user.organisation');
		}

		if(Input::has('cal_id'))
		{
			$cal_id 							= Input::get('cal_id');
		}
		else
		{
			App::abort(404);
		}

		if(Input::has('start'))
		{
			$start 								= Input::get('start');
		}
		else
		{
			$start 								= date('Y-m-d', strtotime('First Day of this month'));
		}

		if(Input::has('end'))
		{
			$end 								= Input::get('end');
		}
		else
		{
			$end 								= date('Y-m-d', strtotime('First Day of next month'));
		}

		if(!in_array($org_id, Config::get('user.orgids')))
		{
			App::abort(404);
		}

		$search['id'] 							= $cal_id;
		$search['organisationid'] 				= $org_id;
		$sort 									= ['name' => 'asc'];
		$results 								= $this->dispatch(new Getting(new Calendar, $search, $sort , 1, 1));
		$contents 								= json_decode($results);

		if(!$contents->meta->success)
		{
			return Response::json(['message' => 'Not Found'], 404);
		}
		$calendar 								= json_decode(json_encode($contents->data), true);

		unset($search);
		unset($sort);

		$search['calendarid'] 					= $cal_id;
		$search['ondate'] 						= [$start, $end];
		$sort 									= ['name' => 'asc'];
		$results 								= $this->dispatch(new Getting(new Schedule, $search, $sort , 1, 100));
		$contents 								= json_decode($results);

		if(!$contents->meta->success)
		{
			return Response::json(['message' => 'Not Found'], 404);
		}

		$schedules 								= json_decode(json_encode($contents->data), true);

		$begin 									= new DateTime( $start );
		$ended 									= new DateTime( $end  );

		$interval 								= DateInterval::createFromDateString('1 day');
		$periods 								= new DatePeriod($begin, $interval, $ended);
		
		$workdays 								= [];
		$wd										= ['senin' => 'monday', 'selasa' => 'tuesday', 'rabu' => 'wednesday', 'kamis' => 'thursday', 'jumat' => 'friday', 'sabtu' => 'saturday', 'minggu' => 'sunday', 'monday' => 'monday', 'tuesday' => 'tuesday', 'wednesday' => 'wednesday', 'thursday' => 'thursday', 'friday' => 'friday', 'saturday' => 'saturday', 'sunday' => 'sunday'];
		
		$workday 								= explode(',', $calendar['workdays']);

		foreach ($workday as $key => $value) 
		{
			if($value!='')
			{
				$value 							= str_replace(' ', '', $value);
				$workdays[]						= $wd[strtolower($value)];
			}
		}

		$schedule 								= [];
		$date 									= [];
		$k 										= 0;
		$j 										= 0;
		foreach ( $periods as $period )
		{
			$j++;
			foreach($schedules as $i => $sh)	
			{
				if($period->format('Y-m-d') == date('Y-m-d', strtotime($sh['on'])))
				{
					$schedule[$k]['data_target']= '#modal_schedule_branch';
					$schedule[$k]['id']			= $sh['id'];
					$schedule[$k]['title'] 		= $sh['name'];
					$schedule[$k]['start']		= $sh['on'].'T'.$sh['start'];
					$schedule[$k]['end']		= $sh['on'].'T'.$sh['end'];
					$schedule[$k]['status']		= $sh['status'];					
					$schedule[$k]['ed_action']	= route('hr.calendar.schedules.store', ['id' => $sh['id'], 'org_id' => $org_id, 'cal_id' => $cal_id]);
					$schedule[$k]['del_action']	= route('hr.calendar.schedules.delete', ['id' => $sh['id'], 'org_id' => $org_id, 'cal_id' => $cal_id]);

					switch (strtolower($sh['status'])) 
					{
						case 'presence_indoor':
							$schedule[$k]['backgroundColor']= '#31708f';
							$schedule[$k]['color']			= '#31708f';
							break;
						case 'presence_outdoor':
							$schedule[$k]['backgroundColor']= '#ag4442';
							$schedule[$k]['color']			= '#ag4442';
							break;
						case 'absence_workleave':
							$schedule[$k]['backgroundColor']= '#00B10F';
							$schedule[$k]['color']			= '#00B10F';
						break;
						case 'absence_not_workleave':
							$schedule[$k]['backgroundColor']= '#3C763D';
							$schedule[$k]['color']			= '#3C763D';
							break;
						default:
							$schedule[$k]['backgroundColor']= '#8abd3b';
							$schedule[$k]['color']			= '#8abd3b';
							break;
					}					

					$date[]							= $period->format('Y-m-d');
					$k++;
				}
			}

			if(!in_array($period->format('Y-m-d'), $date))
			{
				if(in_array(strtolower($period->format('l')), $workdays))
				{
					$schedule[$k]['id']				= $sh['id'];
					$schedule[$k]['title'] 			= 'Masuk Kerja';
					$schedule[$k]['start']			= $period->format('Y-m-d').'T'.$calendar['start'];
					$schedule[$k]['end']			= $period->format('Y-m-d').'T'.$calendar['end'];
					$schedule[$k]['status']			= 'presence_indoor';
					$schedule[$k]['backgroundColor']= '#31708F';
					$schedule[$k]['color']			= '#31708F';

					$date[]							= $period->format('Y-m-d');
					$k++;
				}
				else
				{
					$schedule[$k]['id']				= $period->format('Ymd');
					$schedule[$k]['title'] 			= 'Libur';
					$schedule[$k]['start']			= $period->format('Y-m-d').'T'.'00:00:00';
					$schedule[$k]['end']			= $period->format('Y-m-d').'T'.'00:00:00';
					$schedule[$k]['status']			= 'absence_not_workleave';
					$schedule[$k]['backgroundColor']= '#D78409';
					$schedule[$k]['color']			= '#D78409';

					$date[]							= $period->format('Y-m-d');
					$k++;
				}
			}
		}

		return Response::json($schedule);		
	}
	
	public function create($id = null)
	{
		if(Input::has('org_id'))
		{
			$org_id 							= Input::get('org_id');
		}
		else
		{
			$org_id 							= Session::get('user.organisation');
		}

		if(Input::has('cal_id'))
		{
			$cal_id 							= Input::get('cal_id');
		}
		else
		{
			App::abort(404);
		}

		if(!in_array($org_id, Config::get('user.orgids')))
		{
			App::abort(404);
		}

		$search['id'] 							= $cal_id;
		$search['organisationid'] 				= $org_id;
		$search['withattributes'] 				= ['organisation'];
		$sort 									= ['name' => 'asc'];
		$results 								= $this->dispatch(new Getting(new Calendar, $search, $sort , 1, 1));
		$contents 								= json_decode($results);

		if(!$contents->meta->success)
		{
			App::abort(404);
		}

		$calendar 								= json_decode(json_encode($contents->data), true);
		$data 									= $calendar['organisation'];

		// ---------------------- GENERATE CONTENT ----------------------
		$this->layout->pages 					= view('pages.schedule.create', compact('id', 'data', 'calendar'));

		return $this->layout;
	}
	
	public function store($id = null)
	{
		if(Input::has('id'))
		{
			$id 								= Input::get('id');
		}

		if(Input::has('org_id'))
		{
			$org_id 							= Input::get('org_id');
		}
		else
		{
			$org_id 							= Session::get('user.organisation');
		}

		if(Input::has('cal_id'))
		{
			$cal_id 							= Input::get('cal_id');
		}
		else
		{
			App::abort(404);
		}

		$search['id'] 							= $cal_id;
		$search['organisationid'] 				= $org_id;
		$sort 									= ['name' => 'asc'];
		$results 								= $this->dispatch(new Getting(new Calendar, $search, $sort , 1, 1));
		$contents 								= json_decode($results);

		if(!$contents->meta->success)
		{
			App::abort(404);
		}

		$attributes 							= Input::only('name', 'status', 'on', 'start', 'end');
		$attributes['on'] 						= date('Y-m-d', strtotime($attributes['on']));

		$errors 								= new MessageBag();

		DB::beginTransaction();

		$content 								= $this->dispatch(new Saving(new Schedule, $attributes, $id, new Calendar, $cal_id));
		$is_success 							= json_decode($content);
		
		if(!$is_success->meta->success)
		{
			foreach ($is_success->meta->errors as $key => $value) 
			{
				if(is_array($value))
				{
					foreach ($value as $key2 => $value2) 
					{
						$errors->add('Calendar', $value2);
					}
				}
				else
				{
					$errors->add('Calendar', $value);
				}
			}
		}

		if(!$errors->count())
		{
			DB::commit();
			return Redirect::route('hr.calendars.show', [$cal_id, 'org_id' => $org_id])->with('alert_success', 'Jadwal kalender "' . $contents->data->name. '" sudah disimpan');
		}

		DB::rollback();
		return Redirect::back()->withErrors($errors)->withInput();
	}


	public function edit($id)
	{
		return $this->create($id);
	}
}
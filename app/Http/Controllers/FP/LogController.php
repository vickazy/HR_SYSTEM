<?php namespace App\Http\Controllers\FP;

use Input, Session, App, Paginator, Redirect, DB, Config, Validator, Image, Response;
use App\Http\Controllers\BaseController;
use Illuminate\Support\MessageBag;
use App\Console\Commands\Saving;
use App\Console\Commands\Getting;
use App\Console\Commands\Deleting;
use App\Console\Commands\Checking;
use App\Models\Organisation;
use App\Models\Log;
use App\Models\Person;
use App\Models\ErrorLog;
use App\Models\Branch;
use App\Models\Api;

class LogController extends BaseController 
{

	protected $controller_name 					= 'log';

	public function store()
	{		
		$attributes 							= Input::only('application', 'log');

		//cek apa ada aplication
		if(!$attributes['application'])
		{
			return Response::json('101', 200);
		}

		if(!isset($attributes['application']['api']['client']) || !isset($attributes['application']['api']['secret']) || !isset($attributes['application']['api']['station_id']))
		{
			return Response::json('102', 200);
		}	

		if(!$attributes['application']['api']['station_name'])
		{
			return Response::json('105', 200);
		}

		//cek API key & secret
		$results 								= $this->dispatch(new Getting(new API, ['client' => $attributes['application']['api']['client'], 'secret' => $attributes['application']['api']['secret'], 'workstationaddress' => $attributes['application']['api']['station_id'], 'withattributes' => ['branch']], [], 1, 1));
		
		$content 								= json_decode($results);
		if(!$content->meta->success)
		{
			$filename                       	= storage_path().'/logs/appid.log';
			$fh                             	= fopen($filename, 'a+'); 
			$template 							= date('Y-m-d H:i:s : Log : ').json_encode($attributes['application']['api'])."\n";
	        fwrite($fh, $template); 
	        fclose($fh);

			return Response::json('402', 200);
		}

		if(isset($attributes['application']['api']['version']) && strtolower($attributes['application']['api']['version'])!=strtolower($content->data->tr_version))
		{
			$apiattributes 						= json_decode(json_encode($content->data), true);
			$apiattributes['tr_version']		= strtolower($attributes['application']['api']['version']);

			$content_2 							= $this->dispatch(new Saving(new API, $apiattributes, $apiattributes['id'], new Branch, $apiattributes['branch_id']));
			$is_success 						= json_decode($content_2);
			
			if(!$is_success->meta->success)
			{
				return Response::json('301', 200);
			}
		}

		$organisationid 						= $content->data->branch->organisation_id;

		//cek apa ada data log
		if(!$attributes['log'])
		{
			return Response::json('103', 200);
		}

		//cek apa data bisa disimpan
		DB::beginTransaction();
		if(isset($attributes['log']))
		{
			$attributes['log']					= (array)$attributes['log'];
			foreach ($attributes['log'] as $key => $value) 
			{
				$log['name']					= strtolower('fingerprint');
				$log['on']						= date("Y-m-d H:i:s", strtotime($value[1]));
				$log['pc']						= $attributes['application']['api']['station_name'];
				$log['ip'] 						= $_SERVER['REMOTE_ADDR'];
				$data 							= $this->dispatch(new Getting(new Person, ['username' => $value[0]], [] , 1, 1), false);
				$person 						= json_decode($data);

				if(!$person->meta->success)
				{
					$log['email']				= $value[0];
					$log['message']				= json_encode($person->meta->errors);
					$saved_error_log 			= $this->dispatch(new Saving(new ErrorLog, $log, null, new Organisation, $organisationid));
				}
				else
				{
					$saved_log 					= $this->dispatch(new Saving(new Log, $log, null, new Person, $person->data->id));
					$is_success_2 				= json_decode($saved_log);
					if(!$is_success_2->meta->success)
					{
						$log['email']			= $value[0];
						$log['message']			= $is_success_2->meta->errors;
						$saved_error_log 		= $this->dispatch(new Saving(new ErrorLog, $log, null, new Organisation, $organisationid));
					}
				}
			}
		}
		DB::commit();
		return Response::json('Sukses', 200);
	}
}

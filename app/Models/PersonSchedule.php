<?php namespace App\Models;

/* ----------------------------------------------------------------------
 * Document Model:
 * 	ID 								: Auto Increment, Integer, PK
 * 	person_id 						: Foreign Key From Person, Integer, Required
 * 	created_by 						: Foreign Key From Person, Integer, Required
 * 	name 		 					: Required max 255
 * 	on 		 						: Required, Date
 * 	start 	 						: Required, Time
 * 	end		 						: Required, Time
 * 	status		 					: Required, enum DN, SS, SL, CN, CB, CI, UL, HB, L
 * 	break_idle		 				: Numeric
 *	created_at						: Timestamp
 * 	updated_at						: Timestamp
 * 	deleted_at						: Timestamp
 * 
/* ----------------------------------------------------------------------
 * Document Relationship :
 * 	//this package
 	1 Relationship belongsTo 
	{
		Person
	}

 * ---------------------------------------------------------------------- */

use Illuminate\Database\Eloquent\SoftDeletes;
use Str, Validator, DateTime, Exception;

class PersonSchedule extends BaseModel {

	use SoftDeletes;
	use \App\Models\Traits\BelongsTo\HasPersonTrait;
	use \App\Models\Traits\MorphTo\HasQueueMorphTrait;

	public 		$timestamps 		= 	true;

	protected 	$table 				= 	'person_schedules';

	protected 	$fillable			= 	[
											'created_by' 				,
											'name' 						,
											'on' 						,
											'start' 					,
											'end' 						,
											'status' 					,
											'break_idle' 				,
										];

	protected 	$rules				= 	[
											'created_by'				=> 'required|exists:persons,id',
											'name'						=> 'required|max:255',
											'on'						=> 'required|date_format:"Y-m-d"',
											'start'						=> 'required|date_format:"H:i:s"',
											'end'						=> 'required|date_format:"H:i:s"',
											'status'					=> 'required|in:DN,SS,SL,CN,CB,UL,HB,L',
											'break_idle'				=> 'numeric',
										];

	public $searchable 				= 	[
											'id' 						=> 'ID', 
											'personid' 					=> 'PersonID', 
											'name' 						=> 'Name', 
											'status' 					=> 'Status', 
											'ondate' 					=> 'OnDate', 
											'withattributes' 			=> 'WithAttributes',
											'withtrashed' 				=> 'WithTrashed',
										];

	public $searchableScope 		= 	[
											'id' 						=> 'Could be array or integer', 
											'personid' 					=> 'Could be array or integer', 
											'name' 						=> 'Must be string', 
											'status' 					=> 'Must be string', 
											'ondate' 					=> 'Could be array or string (date)', 
											'withattributes' 			=> 'Must be array of relationship',
											'withtrashed' 				=> 'Must be true',
										];

	public $sortable 				= 	['created_at', 'on'];

	/* ---------------------------------------------------------------------------- CONSTRUCT ----------------------------------------------------------------------------*/
	/**
	 * boot
	 *
	 * @return void
	 * @author 
	 **/
	static function boot()
	{
		parent::boot();

		Static::saving(function($data)
		{
			$validator = Validator::make($data->toArray(), $data->rules);

			if ($validator->passes())
			{
				return true;
			}
			else
			{
				$data->errors = $validator->errors();
				return false;
			}
		});
	}

	/* ---------------------------------------------------------------------------- QUERY BUILDER ---------------------------------------------------------------------------*/
	
	/* ---------------------------------------------------------------------------- MUTATOR ---------------------------------------------------------------------------------*/
	
	/* ---------------------------------------------------------------------------- ACCESSOR --------------------------------------------------------------------------------*/
	
	/* ---------------------------------------------------------------------------- FUNCTIONS -------------------------------------------------------------------------------*/
	
	/* ---------------------------------------------------------------------------- SCOPE -------------------------------------------------------------------------------*/

	public function scopeID($query, $variable)
	{
		if(is_array($variable))
		{
			return $query->whereIn('person_schedules.id', $variable);
		}
		return $query->where('person_schedules.id', $variable);
	}
	
	public function scopeName($query, $variable)
	{
		return $query->where('name', 'like', '%'.$variable.'%');
	}

	public function scopeStatus($query, $variable)
	{
		return $query->where('status', $variable);
	}

	public function scopeOnDate($query, $variable)
	{
		if(is_array($variable))
		{
			if(!is_null($variable[1]))
			{
				return $query->where('on', '<=', date('Y-m-d', strtotime($variable[1])))
							 ->where('on', '>=', date('Y-m-d', strtotime($variable[0])));
			}
			elseif(!is_null($variable[0]))
			{
				return $query->where('on', 'like', date('Y-m', strtotime($variable[0])).'%');
			}
			else
			{
				return $query->where('on', 'like', date('Y-m').'%');
			}
		}
		return $query->where('on', 'like', date('Y-m', strtotime($variable)).'%');
	}
	
	public function scopeAffectSalary($query, $variable)
	{
		if($variable)
		{
			return $query->wherein('status', ['UL', 'CN', 'CI', 'CB']);
		}
		return $query->wherein('status', ['SS', 'SL', 'L']);
	}
}

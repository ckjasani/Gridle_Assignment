<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class QuestionModel extends Model
{

	protected $fillable = array('question');
		
  	public function user()
	{
		return $this->belongsTo('App\UserModel');
	}  
	
	public function insertque($user_id,$question)
	{
		$user = UserModel::find($user_id);
		$que = $user->QuestionModel()->create(['que' => $question]);
		print_r($que);exit;
		return 1;
	}
	
	public function displayall()
	{
		QuestionModel::all();
	}
	
	public function displaybyuser($user_id)
	{
		$data = QuestionModel::where('user_id',$user_id)->get();
		return $data;
	}
}
?>

<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserModel extends Model
{
	protected $table = 'user';
	protected $primaryKey = 'user_id';
	protected $fillable = array('email','pwd');
	
    public function question()
	{
		return $this->hasMany('App\QuestionModel');
	}
	
	public function displayall()
	{
		UserModel::all();
	}
	
	public function displaybyuser($user_id)
	{
		UserModel::find($user_id);
	}
	
	public function checkuser($email,$pwd)
	{
		$data = UserModel::where('email',$email)->where('pwd',$pwd)->get();
		return $data;
	}
}
?>
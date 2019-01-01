<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Session;
use Validator;
use App\Http\Controllers\Response;
use App\UserModel;

use App\QuestionModel;


class UserController extends Controller
{
	public function showlogin()
	{
		return view('login1');
	}
	
	public function register(Request $request)
	{

		$validator = Validator::make($request->all(), [
			'name' => 'required',
            'email' => 'required|email|unique:user,email|max:255',
            'pwd' => 'required|max:255'
        ]);
		if ($validator->fails()) 
		{		
    	    $message = $validator->errors()->all();

			foreach($message as $msg)
			{
				echo $msg."<br>";
			}			
		}
		else
		{
			$insetid = DB::table('user')->insertGetId(['name' => $request->name, 'pwd' => $request->pwd, 'email' => $request->email ]);
			if($insetid > 0)
			{
				return "User added successfully";
			}
			else
			{
				return "Something went wrong";
			}
		}
	}

	/*Check user is valid or not*/
    public function checkuser(Request $request)
	{
	    $validator = Validator::make($request->all(), [
            'email' => 'required|email|max:255',
            'pwd' => 'required|max:255',
        ]);

        if ($validator->fails()) 
		{
		
    	    $message = $validator->errors();
		
			echo "Email or Password is incorrect";
		}		
		else
		{
			$user = new UserModel();
			$data = $user->checkuser($request->email,$request->pwd);
			//$data = DB::table('user')->select('user_id')->where('email',$request->email)->where('pwd',$request->pwd)->get();
			if(count($data) > 0)
			{
				Session::put('user',$data[0]->user_id);
				echo "Authorized User";			
			}
			else
			{
				echo "Not authorized user";
			}
		}
	}
	
	/*Add Question of logged in user*/
	public function addquestion(Request $request)
	{
		if($request->session()->has('user'))
		{
			$validator = Validator::make($request->all(), [
            'question' => 'required'
        ]);

			if($validator->fails())
			{
				echo "Question is required";
			}
			else
			{							
				/*$questionmodel = new QuestionModel();
				
				$quedata = $questionmodel->insertque(Session::get('user'),$request->question);
				exit;*/		
				$id = DB::table('question')->insertGetId(['user_id' => Session::get('user'),'que' => $request->question]);
				if($id > 0)
				{
					echo "Question is added";
				}
				else
				{
					echo "something went wrong";
				}
			}
		}
		else
		{
			echo "Please login first";
		}
	}
	
	/*Display all question*/
	public function dispallque()
	{
		if(Session::has('user'))
		{
			$data = DB::table('question')->select('que','que_id')->get();
			if(count($data) > 0)
			{
				echo "<table border='1'>";
				foreach($data as $key => $datas)
				{
					echo "<tr><td>".($key+1)."</td><td><a href='getanswer/".$datas->que_id."'>".$datas->que."</a></td></tr>";
				}
				echo "</table>";
			}
		}
		else
		{
			echo "Please login first";
		}
	}
	
	/*Display question of specific user*/
	public function dispque()
	{
		if(Session::has('user'))
		{
			$data = DB::table('question')->select('que')->where('user_id',Session::get('user'))->get();
			if(count($data) > 0)
			{
				echo "<table border='1'>";
				foreach($data as $key => $datas)
				{
					echo "<tr><td>".($key+1)."</td><td>".$datas->que."</td></tr>";
				}
				echo "</table>";
			}
			else
			{
				echo "No records found";
			}
		}
		else
		{
			echo "Please login first";
		}
	}
	
	/*save comment of given ans*/
	public function savecomment(Request $request)
	{
		if(Session::has('user'))
		{
			$validator = Validator::make($request->all(), [
            'comment' => 'required'
        	]);

			if($validator->fails())
			{
				return "Comment is required";
			}
			else
			{
				$insertdata = DB::table('comment')->insertGetId(['user_id' => Session::get('user'), 'comment' => $request->comment, 'ans_id' => $request->ans_id]);
				if($insertdata > 0)
				{
					return "Comment added successfully";
				}
				else
				{
					return "Something went wrong";
				}
			}			
		}
		else
		{
			return "Please login first";
		}
	}
	
	/*Display question basis of passed question id*/
	public function saveans(Request $request)
	{

		if(Session::has('user'))
		{
			$data = DB::table('question')->leftjoin('answer','question.que_id','answer.que_id')->select('que','ans','upvote','downvote')->where('question.que_id',$request->que_id)->get();
			if(count($data) > 0)
			{
				echo "Question is:".$data[0]->que."<br>";
				echo "<br>Answer are as follow <br>";
				foreach($data as $data)
				{
					echo "<br>Up Vote => ".($data->upvote == "" ? "0" : $data->upvote)."<br>";
					echo "Down Vote => ".($data->downvote == "" ? "0" : $data->downvote)."<br>";
					echo ($data->ans == "" ? "No answer yet" : $data->ans)."<br>";
				}
			}
			else
			{
				echo "No records found";
			}
		}
		else
		{
			echo "Please login first";
		}
	}
	
	/*Save Answer of given question*/
	public function saveansdata(Request $request)
	{
		if(Session::has('user'))
		{		
			$validator = Validator::make($request->all(), [
            'ans' => 'required'
        	]);

			if($validator->fails())
			{
				echo "Answer is required";
			}
			else
			{
				$get_id = DB::table('answer')->insertGetId(['que_id' => $request->que_id, 'upvote' => 0, 'downvote' => 0, 'ans' => $request->ans, 'user_id' => Session::get('user')]);
				if($get_id > 0)
				{
					echo "Answer added successfully";
				}
				else
				{
					echo "Something went wrong";
				}
			}	
		}
		else
		{
			echo "Please login first";
		}
	}
	
	/*To save vote on basis of requested answer*/
	public function savevote(Request $request)
	{
		if(Session::has('user'))
		{
			if(isset($request->upvote))
			{
				$upvotedata = DB::table('answer')->where('ans_id',$request->ans_id)->value('upvote');
				$upvote = $upvotedata + 1;
				$updatedata = DB::table('answer')->where('ans_id',$request->ans_id)->update(['upvote' => $upvote]);
				if($updatedata > 0)
					return "Voted Successfully";
			}
			else if(isset($request->downvote))
			{
				$downvotedata = DB::table('answer')->where('ans_id',$request->ans_id)->value('downvote');
				$downvote = $downvotedata + 1;
				$updatedata = DB::table('answer')->where('ans_id',$request->ans_id)->update(['downvote' => $downvote]);
				if($updatedata > 0)
					return "Voted Successfully";
			}
			else
			{
				return "Something went wrong";
			}
			return "Something went wrong";
		}
		else
		{
			echo "Please login first";
		}
	}
	
	/*Search by question text*/
	public function searchbyquestiontext(Request $request)
	{		
			if(isset($request->que))
			{
//				$data = DB::table('question')->leftjoin('answer','question.que_id','answer.que_id')->leftjoin('comment','answer.ans_id','comment.ans_id')->select('que','ans','upvote','downvote','comment')->where('question.que','like','%'.$request->que.'%')->get();
				$data = DB::table('question')->leftjoin('answer','question.que_id','answer.que_id')->select('que','ans','ans_id','upvote','downvote')->where('question.que','like','%'.$request->que.'%')->get();
				if(count($data) > 0)
				{				
//					dd($data);
					foreach($data as $key => $data)
					{
						echo "<br><br>".($key+1).") Question is:".$data->que;
						echo "<br>Up Vote => ".($data->upvote > 0 ? $data->upvote : 0)."<br>";
						echo "Down Vote => ".($data->downvote > 0 ? $data->downvote : 0)."<br>";
						echo "Answer is: ".($data->ans != null ? $data->ans : "No answer yet")."<br>";
						echo "All Comments<br>";
						$comment = DB::table('comment')->where('ans_id',$data->ans_id)->select('comment')->get();
						foreach($comment as $keydata => $commentdata)
						{
							echo ($keydata+1).") ".$commentdata->comment."<br>";
						}
						//echo ($data->comment == "" ? "First to give comment" : $data->comment)."<br>";
					}
				}
				else
				{
					echo "No records found";
				}
			}
			else
			{
				return "Something went wrong";
			}				
	}
	
	public function logout()
	{
		Session::forget('user');
		return "Successfully log out";
	}
}

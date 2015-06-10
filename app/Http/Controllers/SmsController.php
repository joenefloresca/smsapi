<?php
namespace App\Http\Controllers;

use Validator;
use Input;
use Redirect;
use Session;
use Request;
use Auth;
use Borla\Chikka\Chikka;
use Illuminate\Routing\Controller as BaseController;
use \App\Http\Models\SmsLog;

class SmsController extends BaseController
{
	public function __construct()
	{
		$this->middleware('auth');
	}

    public function create()
    {
    	return view('sms.create');
    }

    public function store()
    {
    	$rules = array(
            'number'     => 'required',
            'message'  => 'required',
        );

        $validator = Validator::make(Input::all(), $rules);

        if ($validator->fails()) 
        {
            return Redirect::to('sms/create')->withErrors($validator);
        }
        else
        {
        	
            // Mobile number of receiver and message to send
			$mobile 	= Input::get('number');
			$message 	= Input::get('message');
			$credentials = array(
			  'client_id' => '30c78bec559d12c8e83b37ebaebf1c2b50b0be997d24a76e11317a8f01e2c0a0',
			  'secret_key'=> '1b2238693e1ff6b4afd35ad6dfa2976ee79704878172a524d6625f83f03d8b97',
			  'shortcode' => '2929001511'
			);

			// Send SMS
			$chikka = new Chikka($credentials);
			$result = $chikka->send($mobile, $message);

			/* For Logging SMS*/
			$User_id = Auth::user()->id;
			$SmsLog = new SmsLog;
            $SmsLog->User_id 	= $User_id;
            $SmsLog->Number 	= $mobile;
            $SmsLog->Message 	= $message;
            $SmsLog->Source 	= 'Web Request';
      
            $SmsLog->save();
			

			if($result->status == 200)
			{
				Session::flash('alert-success', 'SMS Sent.');
			}
			else
			{
				Session::flash('alert-danger', 'SMS not Sent. There are problems with your Input');
			}

            return Redirect::to('sms/create');
        }
    }
}

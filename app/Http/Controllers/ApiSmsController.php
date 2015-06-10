<?php
namespace App\Http\Controllers;

use Borla\Chikka\Chikka;
use Illuminate\Routing\Controller as BaseController;
use Input;
use Auth;
use \App\Http\Models\SmsLog;

class ApiSmsController extends BaseController
{
	public function __construct()
	{
		$this->middleware('auth');
	}

    public function send()
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

		$User_id = Auth::user()->id;
		$SmsLog = new SmsLog;
        $SmsLog->User_id 	= $User_id;
        $SmsLog->Number 	= $mobile;
        $SmsLog->Message 	= $message;
        $SmsLog->Source 	= 'API Request';
  
        $SmsLog->save();

		return $response = array('message' => $result->message, 'status' => $result->status);;

    }
}

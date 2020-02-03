<?php
App::uses('Component', 'Controller');
class PusherComponent extends Component {
	public function notification($device_type=null,$token_id=null,$message=null,$notitype=null,$game_id=null,$badge =null){
		//echo "hello".$device_type;echo "<br>";echo $token_id;echo "<br>";echo $message;echo "<br>";echo $notitype;echo "<br>";echo $game_id;
		$url 				= "https://fcm.googleapis.com/fcm/send";
		$server_key 		="AAAA4-wwmbs:APA91bE5MYbT5oZZv3u8ikwqbkV5HuBJFYSAdK1fkHnKsOd_zqFA1PCy5SPPud6f6VFjMDRbmxdWPCFJ46ntkywLDlipk6N4eFyq1RX73EpRDKKQcqlOV0Tv8a1LJDPQtJHN8VSqC97d";
		if($device_type == 1){//android
			$AndroidService[] 	= 	$token_id;
			$resgistrationIDs 	= 	$AndroidService;
			$fields 			= 	array('registration_ids'=>$resgistrationIDs,'data'=>array('alert'=>$message,'type'=>$notitype,'badge'=>$badge,'game_id'=>$game_id),);
		}else{//IOS
			$target 			=	$token_id;
			$payload['aps'] 	= 	['alert' => $message,'badge' =>$badge,'type'=>$notitype,'game_id'=>$game_id];
			$data 				= 	$payload;
			$fields = array(
				"to" =>$target,
				"notification"=> array(
				  "data" 	=> 	$data['aps'],
				  "badge" 	=> 	$data['aps']['badge'],
				  "body" 	=> 	$data['aps']['alert'],
				  "type"	=>	$data['aps']['type'],
				  "game_id"	=>	$data['aps']['game_id']
				),
				"priority" => 'high'
			  );  
		}
		$headers = array(
			'Content-Type:application/json',
			'Authorization:key='.$server_key
		);
	    //CURL request to route notification to FCM connection server (provided by Google)			
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
		$result = curl_exec($ch);
		
		if($result == FALSE){
			return curl_error($ch);
			 //die('Oops! FCM Send Error: ' . curl_error($ch));
		}else{
			return true;
		} 
		
		curl_close($ch);
		
	}
	
	
}



?>
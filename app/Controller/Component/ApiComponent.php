<?php

App::uses('Component', 'Controller');

class ApiComponent extends Component {
    public function checkEmailId($emailId = null) {
        $UserModel = ClassRegistry::init('User');
		$data = $UserModel->find('count',array("conditions"=>array("email"=>$emailId)));
		if($data > 0){
			return 1;
		}else{
			return 0;
		}
    }
	
	public function checkMobileNo($mobile = null) {
        $UserModel = ClassRegistry::init('User');
		$data = $UserModel->find('count',array("conditions"=>array("mobile"=>$mobile)));
		if($data > 0){
			return 1;
		}else{
			return 0;
		}
    }
	
	
	public function checkAccessKey($access_key = null) {
        $UserModel = ClassRegistry::init('User');
		$data = $UserModel->find('first',array("conditions"=>array("access_key"=>$access_key),"fields"=>array('id','full_name','lang_type','city_id','user_type','user_image','gender')));
		if($data){
			return $data;
		}else{
			return "";
		}
    }
	
	function uploadFile($path, $fileData) {
		/*find file extention*/
		$extArr = explode('.', $fileData['name']);
		$ext = end($extArr);

		//$filename = $this->random_string(5).time() . '.' . $ext;
		$filename = time() . '.' . $ext;
		$url = $path . $filename;

		if (move_uploaded_file($fileData['tmp_name'], $url))
			return $filename;
		else
			return '';
	}
	
	public function getUserById($user_id=null){
		$UserModel 	= ClassRegistry::init('User');
		$data 		= $UserModel->find('first',array("conditions"=>array("id"=>$user_id)));
		if($data){
			return $data;
		}else{
			return "";
		}
	}
	
	
	
	public function getLanguageType($user_id=null){
		$UserModel 	= ClassRegistry::init('User');
		$data 		= $UserModel->find('first',array("conditions"=>array("id"=>$user_id),"fields"=>array('lang_type')));
		if($data){
			return $data['User']['lang_type'];
		}else{
			return 1;
		}
	}
	
	public function getGameReviewsById($game_id=null){
		$UserModel 	= ClassRegistry::init('GameReview');
		$UserModel->bindModel(
				array(
					'belongsTo'=>array(
						'User'=>array(
						  'className' => 'User',
						  'foreignKey' => 'user_id',
						), 
					)
				)
		); 
		$data 		= $UserModel->find('all',array("conditions"=>array("game_id"=>$game_id),"fields"=>array('GameReview.*','User.full_name','User.user_image','User.social_id')));
		if($data){
			return $data;
		}else{
			return "";
		}
	}
	
	public function getCityById($city_id=null){
		$UserModel 	= ClassRegistry::init('City');
		$data 		= $UserModel->find('first',array("conditions"=>array("id"=>$city_id)));
		if($data){
			return $data;
		}else{
			return "";
		}
	}
	
	public function getTeamByGameId($game_id=null){
		$UserModel 	= ClassRegistry::init('Team');
		$UserModel->bindModel(
				array(
					'belongsTo'=>array(
						'User'=>array(
						  'className' => 'User',
						  'foreignKey' => 'user_id',
						), 
					)
				)
		); 
		$data 		= $UserModel->find('all',array("conditions"=>array("game_id"=>$game_id),"fields"=>array('Team.*','User.full_name','User.user_image','User.social_id')));
		if($data){
			return $data;
		}else{
			return "";
		}
	}
	
	public function getVenueImagebyVenueId($venue_id = null){
		$UserModel 	= ClassRegistry::init('VenueImage');
		$data 		= $UserModel->find('first',array("conditions"=>array("venue_id"=>$venue_id)));
		if($data){
			return $data;
		}else{
			return "";
		}
	}
	
	public function getPlayerAvailabilityByGameId($game_id=null){
		$UserModel 	= ClassRegistry::init('PlayerAvailability');
		$UserModel->bindModel(
				array(
					'belongsTo'=>array(
						'User'=>array(
						  'className' => 'User',
						  'foreignKey' => 'player_id',
						), 
					)
				)
		); 
		$data 		= $UserModel->find('all',array("conditions"=>array("game_id"=>$game_id,'PlayerAvailability.status'=>1),"fields"=>array('PlayerAvailability.*','User.full_name','User.id','User.lang_type','User.device_id','User.device_type','User.badge')));
		if($data){
			return $data;
		}else{
			return "";
		}
	}
	
	
	
}



?>
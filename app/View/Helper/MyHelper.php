<?php
class MyHelper extends AppHelper {

	var $helpers = array('Html', 'Form', 'Ajax', 'Js', 'Javascript', 'Session');


	function getuserbyId($user_id){
		$this->User = ClassRegistry::init('User');
        $conditions=array('User.id'=>$user_id);
        $fields =array('id','full_name','status');
       
        $userdata = $this->User->find('first',array(
                                    'conditions'=>$conditions,
                                    'fields' => $fields
                                    ));
        
        return $userdata['User'];

    }
	
	function getCityById($city_id){
		$this->City = ClassRegistry::init('City');
        $conditions=array('City.id'=>$city_id,'status'=>1);
        $userdata = $this->City->find('first',array(
                                    'conditions'=>$conditions,
                                    ));
        return $userdata;
	}
	
	function getSportById($sport_id){
		$this->Sport = ClassRegistry::init('Sport');
        $conditions=array('Sport.id'=>$sport_id,'status'=>1);
        $userdata = $this->Sport->find('first',array(
                                    'conditions'=>$conditions,
                                    ));
        return $userdata;
	}
	
	function getVenueById($venue_id){
		$this->Venue = ClassRegistry::init('Venue');
        $conditions=array('Venue.id'=>$venue_id);
        $userdata = $this->Venue->find('first',array(
                                    'conditions'=>$conditions,
                                    ));
        return $userdata;
	}

	function getVenueImageById($venue_id){
		$this->Venue = ClassRegistry::init('VenueImage');
        $conditions=array('VenueImage.venue_id'=>$venue_id);
        $userdata = $this->Venue->find('all',array(
                                    'conditions'=>$conditions,
                                    ));
        return $userdata;
	}
   
	function getGameById($game_id){
		$this->Game = ClassRegistry::init('Game');
        $conditions=array('Game.id'=>$game_id);
        $userdata = $this->Game->find('first',array(
                                    'conditions'=>$conditions,
                                    ));
        return $userdata;
	}

	function getPaymentStatus($status=null){
		switch ($status) {
			case "0":
				return "Refund";
				break;
			case "1":
				return "Success";
				break;
			case "2":
				return "TransferSuccess";
				break;
			case "3":
				return "TransferFailure";
				break;
			default:
				return "OtherProblem";
		}
	}

	function getLanguage($language = null){
		switch ($language) {
			case "1":
				return "English";
				break;
			case "2":
				return "Spanish";
				break;
			case "3":
				return "Itallian";
				break;
			case "4":
				return "German";
				break;
			default:
				return "French";
		}
	}

	function getVenueByUserId($user_id){
		$this->Venue = ClassRegistry::init('Venue');
        $conditions=array('Venue.user_id'=>$user_id);
        $userdata = $this->Venue->find('all',array(
                                    'conditions'=>$conditions,'order'=>array('name')
                                    ));
        return $userdata;
	}




}

?>
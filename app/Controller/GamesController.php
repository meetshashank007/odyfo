<?php
App::import('Vendor','message'); 
class GamesController extends AppController {
	public $name = 'Games';
    public $helpers = array('Html', 'Form', 'Session', 'Text','My');
    public $components = array('Email','Auth','Session', 'Cookie','Api');

/**
 * Displays a view
 *
 * @return void
 * @throws NotFoundException When the view file could not be found
 *	or MissingViewException in debug mode.
 */
	
	function beforeFilter(){
		parent::beforeFilter();
		$this->Auth->userModel 		= 'Games';
		if(!$this->Session->check('Auth.Admin')){           
          $this->redirect(SITE_PATH.'admin/admins/login/');
		} 
	}
	
	public function admin_manage_games(){
		$this->layout = 'Admin/default';
		$getGames = $this->Game->find('all',array('order'=>array('id desc')));
		$this->set('result',$getGames);
	}
	
	public function admin_updateStatus(){
		$data['Game']['status']  	= $_REQUEST['status'];
		$data['Game']['id']			= $_REQUEST['game_id'];
		$datas 	= $this->Game->save($data,false);
		echo $datas['Game']['status'];
		die;
	}
	
	public function admin_view_game($game_id=null){
		$this->layout = 'Admin/default';
		$this->loadModel('Venue');
		$this->loadModel('Sport');
		$this->loadModel('City');
		if($this->request->is('post')){
			$data['name']					=	$this->request->data['name'];
			$data['date_time']				=	($this->request->data['date']." ".$this->request->data['time']);
			$data['price']					=	$this->request->data['price'];
			$data['payment_mode']			=	$this->request->data['payment_mode'];
			$data['sports_id']				=	$this->request->data['sport_id'];
			
			$data['venue_id']				=	$this->request->data['venue_id'];
			$data['city_id']				=	$this->request->data['city_id'];
			
			$data['gender']					=	$this->request->data['gender'];
			$data['no_of_player']			=	$this->request->data['no_of_player'];
			$data['min_player']				=	$this->request->data['min_player'];
			$data['already_player']			=	$this->request->data['already_player'];
			$data['no_of_already_player']	=	$this->request->data['no_of_already_player'];
			
			$data['status']					=	$this->request->data['status'];
			$data['description']			=	$this->request->data['description'];
			$data['id']						=	$game_id;
			if (isset($_FILES['image']['name']) &&  !empty($_FILES['image']['name'])) {
				if($_FILES['image']['size'] !=0){
					$fileName = $this->Api->uploadFile('../webroot/img/game/', $_FILES['image']);
					$imgpath = "../webroot/img/game/".$this->request->data['game_image'];
					if(file_exists($imgpath)){
						unlink($imgpath);
					}
					$data['image'] = $fileName;
				}
			}
			
			$this->Session->setFlash(__(_ADMIN2, true), 'message', array('class' => 'alert-success msg'));
			$this->Game->save($data,false);
			$this->redirect(SITE_PATH.'admin/Games/manage_games/');
		}if(!empty($game_id)){
			$result		=	$this->Game->find('first',array('conditions'=>array('id'=>$game_id)));
			$this->set(compact('result'));
		}
		$getVenues	=	$this->Venue->find('all',array("conditions"=>array('user_id'=>0),"fields"=>array("id","name"),'order'=>array('name')));
		$getSports	=	$this->Sport->find('all',array("fields"=>array("id","name"),'order'=>array('name')));
		$getCity	=	$this->City->find('all',array("fields"=>array("id","name"),'order'=>array('name')));
		$this->set(compact('getVenues','getSports','getCity'));
	}
	
	public function admin_delete($game_id=null){
		$this->loadModel('Game');
		$this->loadModel('Team');
		$chkTeam =	$this->Team->find('count',array('conditions'=>array('game_id'=>$game_id)));
		if($chkTeam == 0){
			$datas 	= $this->Game->delete( $game_id,false);
			if($datas == 1){
				$this->Session->setFlash(__(_ADMIN3, true), 'message', array('class' => 'alert-success msg'));
			}else{
				$this->Session->setFlash(__(_ADMIN4, true), 'message', array('class' => 'alert-error msg'));
			}
		}else{
			$this->Session->setFlash(__(_ADMIN5, true), 'message', array('class' => 'alert-error msg'));
		}
		$this->redirect(SITE_PATH.'admin/Games/manage_games/');die;
	}
	
	public function admin_getSportVenue(){
		$this->loadModel('Venue');
		$sports_id  = $_REQUEST['sport_id'];
		$datas 		= $this->Venue->find("first",array("conditions"=>array('sports_id'=>$sports_id),"fields"=>array("id","name")));
		if($datas){
			echo json_encode(array('venue_id'=>$datas['Venue']['id'],'venue_name'=>$datas['Venue']['name']));
		}else{
			echo json_encode(array('venue_id'=>'','venue_name'=>''));
		}
		
		die;
	}
	
	public function admin_getSportByVenueId(){
		$this->loadModel('Venue');
		$venue_id  = $_REQUEST['venue_id'];
		
		$this->Venue->bindModel(
							array(
								'belongsTo'=>array(
								 'Sport'=>array(
								  'className' => 'Sport',
								  'foreignKey' => 'sports_id',
									)        
								)
							)
						);
		$datas 	=	$this->Venue->find("all",array('conditions'=>array('Venue.user_id'=>0,'Venue.id'=>$venue_id),"fields"=>array("Sport.name","Sport.id"),'order'=>array('Sport.name')));
		/* echo "<pre>";print_r($datas);
		$log = $this->Venue->getDataSource()->getLog(false, false);
							debug($log);die; */
		if($datas){
			$res		=	array();
			foreach($datas as $data){
				$res[]	=	$data['Sport'];
			}
			echo json_encode(array('sport_id'=>$res[0]['id'],'sport_name'=>$res[0]['name']));
		}else{
			echo json_encode(array('sport_id'=>'','sport_name'=>''));
		}
		
		die;
	}
	
	public function admin_game_team($game_id = null){
		$this->loadModel('Team');
		$this->Team->bindModel(
				array(
					'belongsTo'=>array(
						'User'=>array(
						  'className' => 'User',
						  'foreignKey' => 'user_id',
						)
					)
				)
			);
		$getTeamDetails 	=	$this->Team->find("all",array('conditions'=>array('Team.game_id'=>$game_id),"fields"=>array("Team.*","User.full_name","User.user_image","User.social_id")));
		$this->set('result',$getTeamDetails);
	}
	
	
	
	
	
}

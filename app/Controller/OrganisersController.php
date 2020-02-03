<?php
App::import('Vendor','message'); 
class OrganisersController extends AppController {
	public $name = 'Organisers';
    public $helpers = array('Html', 'Form', 'Session', 'Text');
    public $components = array('Email','Auth','Session', 'Cookie','Pusher');

/**
 * Displays a view
 *
 * @return void
 * @throws NotFoundException When the view file could not be found
 *	or MissingViewException in debug mode.
 */
	
	function beforeFilter(){
		parent::beforeFilter();
		$this->Auth->userModel 		= 'User';
		if(!$this->Session->check('Auth.Admin')){           
          $this->redirect(SITE_PATH.'admin/admins/login/');
		} 
	}
	
	public function admin_manage_organisers(){
		$this->layout = 'Admin/default';
		$this->loadModel('User');
		$getUsers = $this->User->find('all',array('conditions'=>array('user_type'=>2),'order'=>array('id desc')));
		$this->set('result',$getUsers);
	}
	
	public function admin_updateStatus(){
		$this->loadModel('User');
		$data['User']['status']  	= $_REQUEST['status'];
		$data['User']['id']			= $_REQUEST['user_id'];
		$datas 	= $this->User->save($data,false);
		echo $data['User']['status'];
		die;
	}
	
	public function admin_view_organiser($user_id=null){
		$this->loadModel('User');
		$this->layout = 'Admin/default';
		$this->loadModel('UserRating');
		$this->loadModel('Game');
		$this->loadModel('Venue');
		if($this->request->is('post')){
			$data['full_name'] 		= 	$this->data['full_name'];
			$data['gender']  		= 	$this->data['gender'];
			$data['mobile']			= 	$this->data['mobile'];
			$data['status']			= 	$this->data['status'];
			$data['admin_status']	= 	$this->data['admin_status'];
			$data['id']				=	$user_id;
			$this->Session->setFlash(__(_ADMIN2, true), 'message', array('class' => 'alert-success msg'));
			$this->User->save($data,false);
			$this->redirect(SITE_PATH.'admin/Organisers/manage_organisers/');
		}if(!empty($user_id)){
			$result 	= 	$this->User->find('first',array('conditions'=>array('id'=>$user_id)));
			
			$getRating	=	$this->UserRating->find('first',array('conditions'=>array('player_id'=>$user_id),"fields"=>array('avg(rating) as rating')));
			$rating		=	((!empty($getRating))? round($getRating[0]['rating'],1):'0');
			
			$getMyRanks	=	$this->UserRating->find('all',array("conditions"=>array("UserRating.user_type"=>2),"fields"=>array('player_id','avg(rating) as rating'),"group"=>array('player_id'),"order"=>array('rating desc')));
			if($getMyRanks){
				foreach($getMyRanks  as $getMyRank){
					$playerRank[]	=	$getMyRank['UserRating']['player_id'];
				}
			}
			$rank 		= 	array_search($user_id,$playerRank);
			$ranking	=	(!empty($rank)?	$rank:"0");
			
			
			$totalGame	= 	$this->Game->find("count",array('conditions'=>array('user_id'=>$user_id,'game_status'=>2,'status'=>1)));//count organiser complete game
					
			$totalVenue	= 	$this->Venue->find("count",array('conditions'=>array('user_id'=>$user_id)));
			
			$totalTournament = 0;
			
			
			$this->set(compact('result','rating','ranking','totalGame','totalVenue','totalTournament'));
		}
	}
	
	public function admin_verifyStatus(){
		$this->loadModel('User');
		$data['User']['admin_status']  	= $_REQUEST['admin_status'];
		$data['User']['id']				= $_REQUEST['user_id'];
		$datas 	= $this->User->save($data,false);
		if($_REQUEST['admin_status'] == 1){
			$getUserName = $this->User->find("first",array("conditions"=>array('id'=>$data['User']['id']),"fields"=>array("full_name","email","device_id","device_type","badge","lang_type")));
			$mail_slug 	= 	"account_activate";
			$from		=	"odyfosports@gmail.com";
			$to 		=	$getUserName['User']['email'];
			$replace_var = array("name"=>$getUserName['User']['full_name']);
			$sendMail  = $this->sendEmail($mail_slug,$replace_var,$from,$to);
			
			$badge 	= 	($getUserName['User']['badge'] + 1);
			$this->User->updateAll(
				array('badge'=>$badge),
				array('id'=>$getUserName['User']['id'])
			);
			//$message = "Congratulations! Your registered account has been activated successfully !!";
			$message = (($getUserName['User']['lang_type']==2)?"¡Felicidades! Tu cuenta registrada ha sido activada exitosamente!":"Congratulations! Your registered account has been activated successfully !!");
			if(!empty($getUserName['User']['device_id'])){
				$this->Pusher->notification($getUserName['User']['device_type'],$getUserName['User']['device_id'],$message,'ActivateAccount',0,$badge);
			}
			
		}
		echo $data['User']['admin_status'];
		die;
	}
	
	
	
	public function admin_delete($user_id=null){
		$this->loadModel('User');
		$this->loadModel('AdminNotification');
		$this->loadModel('BankDetail');
		$this->loadModel('CardDetail');
		$this->loadModel('Game');
		
		$this->loadModel('GameReview');
		$this->loadModel('News');
		$this->loadModel('Notification');
		$this->loadModel('Payment');
		
		$this->loadModel('PlayerAvailability');
		$this->loadModel('PlayerCancelGame');
		
		$this->loadModel('PurchaseGame');
		$this->loadModel('Team');
		$this->loadModel('UserRating');
		$this->loadModel('Venue');
		
		$this->loadModel('VenueImage');
		$this->loadModel('ViewNews');
		
		$this->BankDetail->deleteAll(array('user_id' =>$user_id));
		
		$this->CardDetail->deleteAll(array('user_id' =>$user_id));
		$this->Game->deleteAll(array('user_id' =>$user_id));
		
		$this->GameReview->deleteAll(array('user_id' =>$user_id));
		$this->News->deleteAll(array('user_id' =>$user_id));
		
		$this->Notification->deleteAll(array("OR"=>array(array('sender_id'=>$user_id),array('receiver_id'=>$user_id))));
		$this->Payment->deleteAll(array('user_id' =>$user_id));
		
		$this->PlayerAvailability->deleteAll(array('player_id' =>$user_id));
		$this->PlayerCancelGame->deleteAll(array('player_id' =>$user_id));
		
		$this->PurchaseGame->deleteAll(array('user_id' =>$user_id));
		$this->Team->deleteAll(array('user_id' =>$user_id));
		
		$this->UserRating->deleteAll(array("OR"=>array(array('user_id'=>$user_id),array('player_id'=>$user_id))));
		$this->Venue->deleteAll(array('user_id' =>$user_id));
		
		$this->VenueImage->deleteAll(array('user_id' =>$user_id));
		$this->ViewNews->deleteAll(array('user_id' =>$user_id));
		
		$this->User->query("DELETE FROM users WHERE id='$user_id'");
		
		$this->Session->setFlash(__(_ADMIN3, true), 'message', array('class' => 'alert-success msg'));
		$this->redirect(SITE_PATH.'admin/Organisers/manage_organisers');die;
	}
	
	public function admin_view_ratings($player_id = null){
		$this->layout = 'Admin/default';
		$this->loadModel('UserRating');
		$this->UserRating->bindModel(
				array(
					'belongsTo'=>array(
						'User'=>array(
						  'className' => 'User',
						  'foreignKey' => 'user_id',
						),
						'Game'=>array(
						  'className' => 'Game',
						  'foreignKey' => 'game_id',
						)
					)
				)
		); 
		$getRatings	=	$this->UserRating->find('all',array('conditions'=>array('player_id'=>$player_id),"fields"=>array('UserRating.*','User.full_name','Game.name'),"order"=>array('UserRating.id desc')));
		$this->set('result',$getRatings);
	}
	
	
	
	
}

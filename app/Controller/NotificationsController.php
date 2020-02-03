<?php
App::import('Vendor','message'); 
class NotificationsController extends AppController {
	public $name = 'AdminNotifications';
    public $helpers = array('Html', 'Form', 'Session', 'Text');
    public $components = array('Email','Auth','Session', 'Cookie','Api','Pusher');

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
	
	public function admin_send_notifications(){
		$this->layout = 'Admin/default';
		$this->loadModel('AdminNotification');
		$this->loadModel('User');
		if(isset($this->request->data) && !empty($this->request->data)){
			$data['AdminNotification']['message'] = $this->request->data['message'];
			if (isset($_FILES['image']['name']) &&  !empty($_FILES['image']['name'])) {
				if($_FILES['image']['size'] !=0){
					$fileName = $this->Api->uploadFile('../webroot/img/notification/', $_FILES['image']);
					$imgpath = "../webroot/img/notification/".$fileName;
					if ($fileName != '')
						$data['AdminNotification']['image']  = SITE_PATH."img/notification/".$fileName;
				}
			}
			$data['AdminNotification']['user_type'] = $this->request->data['user_type'];
			$gettokens = $this->User->find("all",array("conditions"=>array('User.status'=>1,'User.user_type'=>$data['AdminNotification']['user_type']),"fields"=>array("User.id","User.device_id","device_type","full_name","lang_type","badge")));
			//echo "<pre>";print_r($gettokens);
			/* $gettoken['User']['device_id'] = "ftF_6ykoK8w:APA91bHEw7qmAhPExecCYxq9gQbUvYcEhM0C1Uq2v1zFBIvJhUdIyNehQ2Ss4SmxkfNaDCgKURYmuNYy_OOsX9lPOeKJdy9hV_kfDEdKX8yNmtr8oW-LxpWHhoV_5-FRJb1KUzfM9z8C";
                    $gettoken['User']['device_type'] = 1;
                    $gettoken['User']['username'] = 'thewinteriscoming';
						$noti_msg	  =		$data['AdminNotification']['message'];
						$total_badge  =  	2;
						$this->Pusher->notification($gettoken['User']['device_type'],$gettoken['User']['device_id'],$noti_msg,$gettoken['User']['username'],'admin',$total_badge,$image);
						die; */
			if($gettokens){
				foreach($gettokens as $gettoken){
					if($gettoken['User']['device_id']){
						$noti_msg	  =		$data['AdminNotification']['message'];
						$total_badge  =  	($gettoken['User']['device_id']+1);
						
						if(!empty($data['AdminNotification']['image'])){
							$image = $data['AdminNotification']['image'];
						}else{
							$image = '';
						}
						
						
						$this->Pusher->notification($gettoken['User']['device_type'],$gettoken['User']['device_id'],$noti_msg,'admin',$total_badge,$image);
						
					}
					$data['AdminNotification']['description'] 	= 	 $this->request->data['description'];
					$data['AdminNotification']['receiver_id'] 	= 	 $gettoken['User']['id'];
					$this->AdminNotification->create();
					$this->AdminNotification->save($data);
				}$this->redirect(array('controller'=>'notifications','action' => 'manage_notifications'));
			}
			$this->Session->setFlash(__(_ADMIN6, true), 'message', array('class' => 'alert-success msg'));
			$this->redirect(SITE_PATH.'admin/Notifications/manage_notifications/');
		}
	}
	
	public function admin_manage_notifications(){
		$this->layout = 'Admin/default';
		$this->loadModel('AdminNotification');
		$getUsers = $this->AdminNotification->find('all',array("order"=>array('id desc')));
		$this->set('result',$getUsers);
	}
	
	public function admin_delete($notiId=null){
		$this->loadModel('AdminNotification');
		$getData = $this->AdminNotification->find("first",array("conditions"=>array('id'=>$notiId),"fields"=>array('image')));
		$images = explode('/',$getData['AdminNotification']['image']);
		$Image = "../webroot/img/notification/".$images[6];
		if (file_exists($Image)){
			unlink($Image);
		}
		$datas 	= $this->AdminNotification->delete( $notiId,false);
		if($datas == 1){
			$this->Session->setFlash(__(_ADMIN3, true), 'message', array('class' => 'alert-success msg'));
		}else{
			$this->Session->setFlash(__(_ADMIN4, true), 'message', array('class' => 'alert-error msg'));
		}$this->redirect(SITE_PATH.'admin/Notifications/manage_notifications');die;
	}
	
	
	
	
}

<?php 
App::import('Vendor', 'message');
class AdminsController extends AppController {
	public $name = 'Admins';
    public $helpers = array('Html', 'Form', 'Session', 'Text');
    public $components = array('Email','Auth','Session', 'Cookie');
	/*  public $components = array('Auth', 'Session', 'Email',
			'Auth'=>array(
				'authenticate'=>array(
					'Admin'=>array('userModel'=>'Admin')
				),'Form' => array(
                'fields' => array(
                    'username' => 'username',
                    'password' => 'password'
                ),
                'passwordHasher' => 'Blowfish'
            )
			)
			
			
			);  */
	
	function beforeFilter(){
		parent::beforeFilter();
		$this->Auth->userModel = 'Admin';
		$this->Auth->fields = array('username'=>'username', 'password'=>'password');

		if(!empty($this->Auth))
			$this->Auth->allowedActions = array('admin_login', 'admin_forgot_password');
	}

	public function admin_login() { 
		$this->layout = 'Admin/login';
		if(!empty($this->data)){
			$this->Admin->recursive = 2;
			$adminArr = $this->Admin->find('first', array('conditions'=>array('username'=>trim($this->request->data['username']), 'password'=>md5($this->request->data['password']),'Admin.status'=>1)));	
			if(!empty($adminArr)){
				if($this->Auth->login($adminArr['Admin'])){			
					$this->redirect('/admin/admins/dashboard/');
				}else{
					$this->Session->setFlash(_ADMIN1,'default',array('class'=>'alert alert-danger'),'LoginError');
				}
			}else{
				$this->Session->setFlash(_ADMIN1,'default',array('class'=>'alert alert-danger'),'LoginError');
			}
		}
	}

   
	
	function admin_dashboard() { 
		$this->layout = 'Admin/default';
		$this->loadModel('Game');
		$this->loadModel('User');
		$this->loadModel('News');
		$totalGames 		= $this->Game->find('count');
		$totalPlayers 		= $this->User->find('count',array('conditions'=>array('user_type'=>1)));
		$totalOrganisers 	= $this->User->find('count',array('conditions'=>array('user_type'=>2)));
		$totalNews 			= $this->News->find('count');
		$this->set(compact('totalGames','totalPlayers','totalOrganisers','totalNews'));
	}
	
	public function admin_logout() {
        if ($this->Session->check('Auth.Admin')==1) { 
            if ($this->Session->delete('Auth.Admin')){
                $this->redirect(SITE_PATH.'admin/admins/login');
            }   
        }else{
            $this->redirect(SITE_PATH.'/admin/admins/login');
        }
    }

	public function admin_forgot_password() {
 		$this->layout = 'Admin/sign_in';
 		if($this->request->is('post')){
 			$email 		= trim($this->request->data['Admin']['email']);
 			$getdata 	= $this->Admin->find("first",array("conditions"=>array("Admin.email"=>$email)));
 			if($getdata){
 				$password 					= $this->Custom->createTempPassword(10);
 				$data['Admin']['id'] 		= $getdata['Admin']['id'];
 				$data['Admin']['password'] 	= $this->Auth->password($password);
 				if($this->Admin->save($data)){
 					$email_var = ['name'=>ucwords($getdata['Admin']['first_name']." ".$getdata['Admin']['last_name']),'email'=> $getdata['Admin']['email'], 'password'=> $password];
                  $this->send_mail('admin_forgot_password',$email_var,$from = ADMIN_EMAIL,$to = $getdata['Admin']['email']);
 				}
			$this->Session->setFlash(__(_MSG10022, true), 'message', array('class' => 'alert_before_login alert-success'));
              $this->redirect('/admin/admins/sign_in/');
 			}else{
 				$this->Session->setFlash(__(_MSG1008, true), 'message', array('class' => 'alert-error'));         
 			}
		}
	}

	function admin_change_password() { 
        $this->layout = 'Admin/default'; 
        if($this->request->is('post')){
        	$data['Admin']['password']  =   $this->Auth->password($this->request->data['new_password']);
            $admin_id                   =   $this->Session->read('Auth.id');
            $data['Admin']['id']        =   $admin_id ;
            if($this->Admin->save($data)){
              	$this->Session->setFlash(__(_MSG10015, true), 'message', array('class' => 'alert-success msg'));
            	$this->redirect('/admin/admins/change_password/');
            }else{
                $this->Session->setFlash(__(_MSG1006, true), 'message', array('class' => 'alert-error msg'));
            }
		}
	}

	function admin_change_profile() { 
        $this->layout = 'Admin/default'; 
        $admin_id     =  $this->Session->read('Auth.id');
        if($this->request->data){
        	if($this->Admin->save($this->request->data)){
              	$this->Session->setFlash(__(_MSG1005, true), 'message', array('class' => 'alert-success msg'));
            	$this->redirect('/admin/admins/change_profile/');
            }else{
                $this->Session->setFlash(__(_MSG1006, true), 'message', array('class' => 'alert-error msg'));
            }
		}else{
			$getdata = $this->Admin->find("first",array("conditions"=>array("Admin.id"=>$admin_id)));
			$this->set(compact('getdata'));
		}
	}

    function admin_verifyold_password(){
   		$password = $this->Auth->password($_REQUEST['old_password']);
   		$id 	  = $this->Session->read('Auth.id');
   		$getData  = $this->Admin->find("first",array("conditions"=>array("Admin.id"=>$id),"fields"=>array("Admin.password")));
   		if($getData['Admin']['password']==$password){
   			echo 0;
   		}else{
   			echo 1;
   		}die;
    }

    function admin_verifyemail(){
   		$email 	  = $this->request->data['email'];
   		$getData  = $this->Admin->find("first",array("conditions"=>array("Admin.email"=>$email),"fields"=>array("Admin.email","Admin.id")));
   		if($getData){
   			echo 1;
   		}else{
   			echo 0;
   		}die;
    }












	

}

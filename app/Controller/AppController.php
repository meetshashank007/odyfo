<?php ob_start();
/**
 * Application level Controller
 *
 * This file is application-wide controller file. You can put all
 * application-wide controller-related methods here.
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Controller
 * @since         CakePHP(tm) v 0.2.9
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

App::uses('Controller', 'Controller');

/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @package		app.Controller
 * @link		http://book.cakephp.org/2.0/en/controllers.html#the-app-controller
 */
class AppController extends Controller {
	public $components = array('Flash', 'Auth', 'DebugKit.Toolbar');
	public $helpers 	= array('Html','Form','Session','Text');
	//public $components  = array('Auth','Session','Cookie','Email');
	/**********comment on 30May19**********************/
	/* public $components = array('Auth', 'Session', 'Email',
			'Auth'=>array(
				'authenticate'=>array(
					'Admin'=>array('userModel'=>'Admin')
				)
			));  */
	
	function beforeFilter(){
		if(isset($this->params['prefix']) && $this->params['prefix'] == 'admin'){
		   //print_r($this->params['prefix']);die;          
			$this->Auth->loginAction = array('controller'=>'admins', 'action'=>'login');
			AuthComponent::$sessionKey = 'Auth.Admin';
			$this->layout = 'Admin/default'; 
			
		}
	}  
	
	public function sendEmail($mail_slug=null,$replace_var=[],$from=null,$to=null){
		App::uses('CakeEmail', 'Network/Email');
		$this->loadModel('EmailTemplate');
		$mail_temp 	= $this->EmailTemplate->find("first",array("conditions"=>array('slug'=>$mail_slug),"fields"=>array("subject","description")));
		$subject 	= utf8_encode($mail_temp['EmailTemplate']['subject']);
		$mail_temp 	= utf8_encode($mail_temp['EmailTemplate']['description']);
		foreach($replace_var as $key=>$val){
			$mail_temp = str_replace('{'.$key.'}',$val,$mail_temp);
		}
		$Email 		= new CakeEmail('smtp');
		$Email->from($from);
		$Email->to($to);
		$Email->subject($subject);
		$Email->emailFormat('html');
		/* echo "<br>";echo $from;
		echo "<pre>";print_r($mail_temp);die; */
		if($Email->send($mail_temp)){
			return true;
		}else{
			return false;
		}
		
	}
}

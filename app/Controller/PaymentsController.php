<?php
App::import('Vendor','message'); 
class PaymentsController extends AppController {
	public $name = 'Payments';
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
		$this->Auth->userModel 		= 'Payments';
		if(!$this->Session->check('Auth.Admin')){           
          $this->redirect(SITE_PATH.'admin/admins/login/');
		} 
	}
	
	public function admin_manage_payments(){
		$this->layout = 'Admin/default';
		$getPayments  = $this->Payment->find('all',array('order'=>array('id desc')));
		$this->set('result',$getPayments);
	}
	
	public function admin_view_payment($payment_id=null){
		$this->layout = 'Admin/default';
		$this->loadModel('Payment');
		$result		  =	$this->Payment->find('first',array('conditions'=>array('id'=>$payment_id)));
		$this->set(compact('result'));
	}
	
	public function admin_delete($payment_id=null){
		$this->loadModel('Payment');
		$datas 	= $this->Payment->delete( $payment_id,false);
		if($datas == 1){
			$this->Session->setFlash(__(_ADMIN3, true), 'message', array('class' => 'alert-success msg'));
		}else{
			$this->Session->setFlash(__(_ADMIN4, true), 'message', array('class' => 'alert-error msg'));
		}$this->redirect(SITE_PATH.'admin/Payments/manage_payments/');die;
	}
	
}

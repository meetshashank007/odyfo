<?php
App::import('Vendor','message'); 
class StaticPagesController extends AppController {
	public $name = 'StaticPages';
    public $helpers = array('Html', 'Form', 'Session', 'Text','My');
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
		$this->Auth->userModel 		= 'StaticPages';
		if(!$this->Session->check('Auth.Admin')){           
          $this->redirect(SITE_PATH.'admin/admins/login/');
		} 
	}
	
	public function admin_manage_pages(){
		$this->layout = 'Admin/default';
		$getPages = $this->StaticPage->find('all',array('order'=>array('id desc')));
		$this->set('result',$getPages);
	}
	
	public function admin_updateStatus(){
		$this->loadModel('StaticPage');
		$data['StaticPage']['status']  	= $_REQUEST['status'];
		$data['StaticPage']['id']		= $_REQUEST['page_id'];
		$datas 	= $this->StaticPage->save($data,false);
		echo $datas['StaticPage']['status'];
		die;
	}
	
	public function admin_add_pages($pages_id=null){
		$this->layout = 'Admin/default';
		if($this->request->is('post')){
			$data['title']			=	$this->request->data['title'];
			$data['language']		=	$this->request->data['language'];
			$data['description']	=	$this->request->data['description'];
			$data['status']			=	$this->request->data['status'];
			
			$this->Session->setFlash(__(_ADMIN2, true), 'message', array('class' => 'alert-success msg'));
			if(!empty($pages_id)){$data['id'] = $pages_id;}
			$this->StaticPage->save($data,false);
			$this->redirect(SITE_PATH.'admin/StaticPages/manage_pages/');
		}if(!empty($pages_id)){
			$getPages = $this->StaticPage->find('first',array('conditions'=>array('id'=>$pages_id)));
			$this->set('result',$getPages);
		}
	}
	
	public function admin_delete($page_id=null){
		$this->loadModel('StaticPage');
		$datas 	= $this->StaticPage->delete( $page_id,false);
		if($datas == 1){
			$this->Session->setFlash(__(_ADMIN3, true), 'message', array('class' => 'alert-success msg'));
		}else{
			$this->Session->setFlash(__(_ADMIN4, true), 'message', array('class' => 'alert-error msg'));
		}$this->redirect(SITE_PATH.'admin/StaticPages/manage_pages/');die;
	}
	
	
	
	
	
	
	
}

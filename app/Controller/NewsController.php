<?php
App::import('Vendor','message'); 
class NewsController extends AppController {
	public $name = 'News';
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
		$this->Auth->userModel 		= 'News';
		if(!$this->Session->check('Auth.Admin')){           
          $this->redirect(SITE_PATH.'admin/admins/login/');
		} 
	}
	
	public function admin_manage_news(){
		$this->layout = 'Admin/default';
		$getNews = $this->News->find('all',array('order'=>array('id desc')));
		$this->set('result',$getNews);
	}
	
	public function admin_updateStatus(){
		$this->loadModel('User');
		$data['News']['status']  	= $_REQUEST['status'];
		$data['News']['id']			= $_REQUEST['news_id'];
		$datas 	= $this->News->save($data,false);
		
		if($_REQUEST['status'] == 1){
			$this->News->bindModel(
				array(
					'belongsTo'=>array(
						'User'=>array(
							'className' => 'User',
							 'foreignKey' => false,
							'conditions' => 'News.user_id = User.id',
						)        
					)
				)
			);
			$getUserName		=	$this->News->find("first",array('conditions'=>array('News.id'=>$data['News']['id']),"fields"=>array("User.full_name","User.email","User.device_id","User.device_type","User.badge","User.id")));
			
			$badge 	= 	($getUserName['User']['badge'] + 1);
			$this->User->updateAll(
				array('badge'=>$badge),
				array('id'=>$getUserName['User']['id'])
			);
			$message = "Your feed has been published successfully.";
			$this->Pusher->notification($getUserName['User']['device_type'],$getUserName['User']['device_id'],$message,'PublishFeed',0,$badge);
		}
		
		
		echo $datas['News']['status'];
		
		
		
		die;
	}
	
	public function admin_view_news($news_id=null){
		$this->layout = 'Admin/default';
		if($this->request->is('post')){
			$data['name']			=	$this->request->data['name'];
			$data['description']	=	$this->request->data['description'];
			$data['id']				=	$news_id;
			$data['status']			=	$this->request->data['status'];
			if (isset($_FILES['image']['name']) &&  !empty($_FILES['image']['name'])) {
				if($_FILES['image']['size'] !=0){
					$fileName = $this->Api->uploadFile('../webroot/img/news/', $_FILES['image']);
					
					$imgpath = "../webroot/img/news/".$this->request->data['news_image'];
					if(file_exists($imgpath)){
						unlink($imgpath);
					}
					
					$data['image'] = $fileName;
				}
			}
			
			$this->Session->setFlash(__(_ADMIN2, true), 'message', array('class' => 'alert-success msg'));
			$this->News->save($data,false);
			$this->redirect(SITE_PATH.'admin/News/manage_news/');
		}if(!empty($news_id)){
			$getNews = $this->News->find('first',array('conditions'=>array('id'=>$news_id)));
			$this->set('result',$getNews);
		}
	}
	
	public function admin_delete($news_id=null){
		$this->loadModel('News');
		$datas 	= $this->News->delete( $news_id,false);
		if($datas == 1){
			$this->Session->setFlash(__(_ADMIN3, true), 'message', array('class' => 'alert-success msg'));
		}else{
			$this->Session->setFlash(__(_ADMIN4, true), 'message', array('class' => 'alert-error msg'));
		}$this->redirect(SITE_PATH.'admin/News/manage_news/');die;
	}
	
	
	
	
	
	
	
}

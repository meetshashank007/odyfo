<?php
class WebsController extends AppController {

/**
 * Displays a view
 *
 * @return void
 * @throws NotFoundException When the view file could not be found
 *	or MissingViewException in debug mode.
 */
	
	function beforeFilter(){
		parent::beforeFilter();
	}
	
	public function index(){
		$this->layout='';
	}
	
	public function contact(){
		$this->layout='';
		$this->loadModel('WebContact');
		if($this->request->is('post')){
			//echo "<pre>";print_r($this->data);die;
			$data['name'] 		= 	$this->data['contactname'];
			$data['email']  	= 	$this->data['contactemail'];
			$data['contact']	= 	$this->data['contactmobile'];
			$data['message']	= 	$this->data['message'];
			$mail_slug			=	"contact";
			$replace_var 		= 	array("name"=>$data['name'],"message"=>$data['message'],"email"=>$data['email']);
			$from				=	"deepak.bansal@techugo.com";
			$to					=	"deepak.bansal@techugo.com";
			//$sendMail  			= 	$this->sendEmail($mail_slug,$replace_var,$from,$to);
			$this->Session->write('contactMessage', 'Thanks for email us..will get back ASAP..!');
			$this->WebContact->save($data,false);
			$this->redirect(SITE_PATH."contact");
		}
	}
	
	public function booking(){
		$this->layout='';
		$this->loadModel('WebBooking');
		if($this->request->is('post')){
			$data['name'] 		= 	$this->data['contactname'];
			$data['email']  	= 	$this->data['contactemail'];
			$data['contact']	= 	$this->data['contactmobile'];
			$data['message']	= 	$this->data['message'];
			$data['city']		= 	$this->data['city'];
			$data['game']		= 	$this->data['game'];
			$mail_slug			=	"booking";
			$replace_var 		= 	array("name"=>$data['name'],"game"=>$data['game'],"city"=>$data['city']);
			$from				=	"deepak.bansal@techugo.com";
			$to					=	"deepak.bansal@techugo.com";
			//$sendMail  			= 	$this->sendEmail($mail_slug,$replace_var,$from,$to);
			$this->Session->write('bookingMessage', 'Your booking has been saved successfully.');
			$this->WebBooking->save($data,false);
			$this->redirect(SITE_PATH."booking");
		}
	}
	
	
	
	
	
	
}

<?php
/**
 * Static content controller.
 *
 * This file will render views from views/pages/
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

App::uses('AppController', 'Controller');
App::import('Vendor','message'); 


App::import('Vendor', 'twilio-php-master', array('file' => 'twilio-php-master/Twilio/autoload.php'));
App::import('Vendor', 'stripe', array('file' => 'stripe/init.php'));
use Twilio\Rest\Client;
/**
 * Static content controller
 *
 * Override this controller by placing a copy in controllers directory of an application
 *
 * @package       app.Controller
 * @link http://book.cakephp.org/2.0/en/controllers/pages-controller.html
 */
class ApisController extends AppController {

/**
 * This controller does not use a model
 *
 * @var array
 */
	public $uses = array();
	public $components = array('Api','Auth','Session','Cookie','Email','Pusher');

/**
 * Displays a view
 *
 * @return void
 * @throws NotFoundException When the view file could not be found
 *	or MissingViewException in debug mode.
 */
	
	function beforeFilter(){
		parent::beforeFilter();
		if(!empty($this->Auth))
			$this->Auth->allowedActions = array('register','login','socialLogin','checkSocialId','otpVerification','resendOTP','forgot_password','reset_password','getCityList','addGame','game_review','gameDetailsById','home','getSportList','addVenue','myVenues','addNews','myNews','getAllNews','getNewsDetailByID','saveUserCityId','addTeam','userProfile','rateOrganiserPlayer','logout','payAsYouGo','deleteGame','myGames','deleteNews','getRatingDetails','addCardDetails','updateProfileImage','addBankDetails','deleteVenue','stripeCharge','cancelTransaction','getBankDetails','getCardDetails','playerAvailability','gameConductStatus','organiserPayment','playerCashPayment','resetBadge','sendPlayerAvaialabilityNoti','getNotificationHistory','sendOTP','getPages','updateLanguage','removePlayerTeam','sendOTP1','getPagesTerms','testsendOTP','testMobileNum');
		header('Content-type:application/json');
	}
	
	
	public function register(){
		$this->layout="None";
		$this->loadModel("User");
		if(isset($this->data) && !empty($this->data)){
			if(empty($this->data['fullname'])){
				$error	= 	"Please enter full name";
			}elseif(empty($this->data['email'])){
				$error	= 	"Please enter email";
			}elseif(empty($this->data['password'])){
				$error	= 	"Please enter password";
			}elseif(empty($this->data['mobile'])){
				$error	= 	"Please enter mobile";
			}elseif(empty($this->data['gender'])){//1=male,2=female
				$error	= 	"Please enter gender";
			}elseif(empty($this->data['device_type'])){//1=android,2=ios
				$error	= 	"Please enter device type";
			}elseif(empty($this->data['user_type'])){//1=player,2=organiser
				$error	= 	"Please enter user type";
			}elseif(empty($this->data['lang_type'])){//1=eng,2=spanish
				$error	= 	"Please enter lang type";
			}elseif(empty($this->data['device_id'])){
				$error	= 	"Please enter device id";
			}else{
				$chkemail = $this->Api->checkEmailId($this->data['email']);
				
				if($chkemail == 1){
					$message = ($this->data['lang_type'] == 2?"ID de correo electrónico ya existe ID":"Email id already exists");
					$response_arry = array('status'=> 400, 'message'=>$message);
				}else{
					$chkMobileNo = $this->User->find("count",array("conditions"=>array("mobile"=>$this->data['mobile'])));
					if($chkMobileNo > 0){
						$message = ($this->data['lang_type'] == 2?"El número del telefono móvil ya existe":"Mobile number already exists");
						$response_arry = array('status'=> 400, 'message'=>$message);
						echo json_encode($response_arry);exit;
					}
					$res['User']['email'] 			= 	$this->data['email'];
					$res['User']['password'] 		= 	$this->Auth->password($this->data['password']);
					$res['User']['full_name'] 		= 	$this->data['fullname'];
					$res['User']['gender']  		= 	$this->data['gender'];
					$res['User']['mobile']			= 	$this->data['mobile'];
					$res['User']['otp'] 			= 	mt_rand(100000,1000000);//create random OTP
					//$res['User']['otp'] 			= 	'123456';
					$sendSMS 						= 	$this->sendOTP($res['User']['mobile'],$res['User']['otp']);
					
					$currdate 						= 	date('Y-m-d H:i:s');
					$res['User']['otp_valid_time']	=	date('Y-m-d H:i:s', strtotime('+10 minutes', strtotime($currdate)));
					$res['User']['device_type']		= 	$this->data['device_type'];
					$res['User']['user_type']		= 	$this->data['user_type'];
					$res['User']['lang_type']		= 	$this->data['lang_type'];
					$res['User']['device_id']		= 	$this->data['device_id'];
					
					
					$allowed_types = unserialize(ALLOWED_IMAGE_TYPES);
					if (isset($_FILES['image']['name']) &&  !empty($_FILES['image']['name'])) {
						if($_FILES['image']['size'] !=0){
							if (in_array($_FILES['image']['type'], $allowed_types)) {
								$fileName = $this->Api->uploadFile('../webroot/img/user/', $_FILES['image']);
								$imgpath = "../webroot/img/user/".$fileName;
								if ($fileName != '')
									$res['User']['user_image'] = $fileName;
								}else {
									$error = 'Please Upload only *.gif, *.jpg, *.png image only!';
									$response_arry = array('error'=> '400', 'status'=> '400', 'message'=> $error);
									echo json_encode($response_arry);exit;

								}
						}
					}
					$res['User']['access_key'] 	=   md5(date("Y-m-d H:i:s"));
					if($this->data['user_type'] == 1){
						$res['User']['admin_status'] 	=	1;
					}
					if($this->User->save($res)){
						$userId 	= 	$this->User->id;
						$getResult 	= 	$this->User->find("first",array("conditions"=>array("id"=>$userId)));
						$getResult['User']['user_image']	=	SITE_PATH."img/user/".$getResult['User']['user_image'];
						$data  		= 	$getResult['User'];
						$message 	= 	($this->data['lang_type'] == 2?"El usuario ha sido registrado correctamente":"User has been registerd successfully");
						$response_arry = array( 'status'=> 200, 'message'=>$message,"result"=>str_replace("null","''",$data));
						//$response_arry = array( 'status'=> 200, 'message'=>"User has been registerd successfully","result"=>str_replace("null","''",$data));
					}else{
						$response_arry = array( 'status'=> 400, 'message'=>"error");
					}
				}
			}if(!empty($error)){
          		$response_arry = array('status'=> 400, 'message'=> $error);
        	}
		}else{
			$response_arry = array( 'status'=> 400, 'message'=>"nodata");
		}
		echo json_encode($response_arry);exit;
	}
	
	public function socialLogin(){
		$this->layout="None";
		$this->loadModel("User");
		$this->loadModel("CardDetail");
		$this->loadModel("BankDetail");
		$this->data = json_decode(file_get_contents('php://input'),true);
		if(isset($this->data) && !empty($this->data)){
			if(empty($this->data['type'])){//1=fbId,gplusId
				$error	= 	"Please enter type";
			}elseif(empty($this->data['social_id'])){
				$error	= 	"Please enter social id";
			}elseif(empty($this->data['device_id'])){
				$error	= 	"Please enter device id";
			}elseif(empty($this->data['device_type'])){
				$error	= 	"Please enter device type";
			}elseif(empty($this->data['lang_type'])){//1=eng,2=spanish
				$error	= 	"Please enter lang type";
			}elseif(empty($this->data['user_type'])){//1=player,2=organiser
				$error	= 	"Please enter user type";
			}elseif(empty($this->data['full_name'])){
				$error	= 	"Please enter full name";
			}elseif(empty($this->data['type'])){//1=fb_id,2=gplus_id
				$error	= 	"Please enter type";
			}else{
				$currdate 						= 	date('Y-m-d H:i:s');
				$chkSocialId = $this->User->find("first",array("conditions"=>array("social_id"=>$this->data['social_id'],'type'=>$this->data['type']),"fields"=>array('id','status','user_type','admin_status')));
				if($chkSocialId){
					if($chkSocialId['User']['status']==0){
						$status 			= 	401;
						//$message 			= 	MSG1;
						$message 			=  ($this->data['lang_type'] == 2?"No estás autorizado. Por favor verifica tu OTP":"You are not authorized.Please verify your OTP");
						
						$result				=	(object)[];
					}else{
						//if($chkSocialId['User']['user_type'] == 2 && $chkSocialId['User']['admin_status'] == 0){
						if($chkSocialId['User']['admin_status'] == 0){
							$status 		= 	402;
							$msg1 			=	($this->data['lang_type'] == 2?"Su cuenta ha sido desactivada por Admin. Póngase en contacto con admin":"Your account has been de-activated by Admin.Please contact to admin.");
							
							$msg2			=	($this->data['lang_type'] == 2?"Tu cuenta no está activada por admin":"Your account is not activated by admin.");
							
							$message 		= 	(($chkSocialId['User']['user_type']==1)?$msg1:$msg2);
							$result			=	(object)[];
						}else{
							if(isset($this->data['email']) && !empty($this->data['email'])){
								$chkEmail = $this->Api->checkEmailId($this->data['email']);
								if($chkEmail == 1){//email already exist
									/* $status 						= 	400;
									$message 						= 	MSG6; */
									$message 		= ($this->data['lang_type'] == 2?"ID de correo electrónico ya existe ID":"Email id already exists");
									$response_arry 	= array('status'=> 400, 'message'=>$message);
									//$response_arry = array('status'=> 400, 'message'=>MSG6);
									echo json_encode($response_arry);exit;
								}else{
									$data['User']['email']	=	$this->data['email'];
									$status 				= 	200;
									//$message 				= 	MSG2;
									$message 				= ($this->data['lang_type'] == 2?"Sesión iniciada con éxito":"Login Successfully");
									
								}
							}if(isset($this->data['mobile']) && !empty($this->data['mobile'])){
								$chkMobile = $this->Api->checkMobileNo($this->data['mobile']);
								if($chkMobile == 1){//Mobile already exist
									/* $status 						= 	400;
									$message 						= 	MSG9; */
									$message = ($this->data['lang_type'] == 2?"El número del telefono móvil ya existe":"Mobile number already exists");
									$response_arry = array('status'=> 400, 'message'=>$message);
									echo json_encode($response_arry);exit;
								}else{
									$data['User']['mobile'] 		= 	$this->data['mobile'];
									$data['User']['otp'] 			= 	mt_rand(100000,1000000);//create random OTP
									//$data['User']['otp'] 			= 	'123456';
									$sendSMS 						= 	$this->sendOTP($this->data['mobile'],$data['User']['otp']);
									
									$data['User']['otp_valid_time']	=	date('Y-m-d H:i:s', strtotime('+5 minutes', strtotime($currdate)));
									$status 						= 	200;
									//$message 						= 	MSG2;
									$message 						= ($this->data['lang_type'] == 2?"Sesión iniciada con éxito":"Login Successfully");
								}
							}
							$data['User']['access_key'] 			= 	md5(date("Y-m-d H:i:s"));
							$data['User']['social_id']				=	$this->data['social_id'];
							$data['User']['id']						=	$chkSocialId['User']['id'];
						}
					}
				}else{
					if(isset($this->data['email']) && !empty($this->data['email'])){
						$chkEmail = $this->Api->checkEmailId($this->data['email']);
						if($chkEmail == 1){//email already exist
							/* $status 						= 	400;
							$message 						= 	MSG6; */
							$message 		= ($this->data['lang_type'] == 2?"ID de correo electrónico ya existe ID":"Email id already exists");
							$response_arry 	= array('status'=> 400, 'message'=>$message);
							echo json_encode($response_arry);exit;
						}else{
							$data['User']['email']	=	$this->data['email'];
							$status 				= 	200;
							$message 				= ($this->data['lang_type'] == 2?"Sesión iniciada con éxito":"Login Successfully");
						}
					}if(isset($this->data['mobile']) && !empty($this->data['mobile'])){
						$chkMobile = $this->Api->checkMobileNo($this->data['mobile']);
						if($chkMobile == 1){//Mobile already exist
							/* $status 						= 	400;
							$message 						= 	MSG9; */
							$message 	= ($this->data['lang_type'] == 2?"El número del telefono móvil ya existe":"Mobile number already exists");
							$response_arry = array('status'=> 400, 'message'=>$message);
							echo json_encode($response_arry);exit;
						}else{
							$data['User']['mobile'] 		= 	$this->data['mobile'];
							$data['User']['otp'] 			= 	mt_rand(100000,1000000);//create random OTP
							//$data['User']['otp'] 			= 	'123456';
							$sendSMS 						= 	$this->sendOTP($this->data['mobile'],$data['User']['otp']);
							
							$data['User']['otp_valid_time']	=	date('Y-m-d H:i:s', strtotime('+5 minutes', strtotime($currdate)));
							$status 						= 	200;
							$message 				= ($this->data['lang_type'] == 2?"Sesión iniciada con éxito":"Login Successfully");
						}
					}
					//reg
					$data['User']['access_key'] 			= 	md5(date("Y-m-d H:i:s"));
					$data['User']['social_id']				=	$this->data['social_id'];
					$status 								= 	$status;
					$message 								= 	$message;
				}
				$data['User']['device_id']					=	$this->data['device_id'];
				$data['User']['device_type']				=	$this->data['device_type'];
				$data['User']['type']						=	$this->data['type'];
				$data['User']['lang_type']					=	$this->data['lang_type'];
				$data['User']['user_type']					=	$this->data['user_type'];
				$data['User']['full_name']					=	$this->data['full_name'];
				if(isset($this->data['gender']) && !empty($this->data['gender'])){
					$data['User']['gender']					=	$this->data['gender'];
				}
				if (isset($this->data['image']) &&  !empty($this->data['image'])) {
					$data['User']['user_image'] 			= 	$this->data['image'];
				}
				if($this->data['user_type'] == 1){
					$data['User']['admin_status'] 	=	1;
				}
				$this->User->save($data,false);
				$getUserDetail 	= 	$this->User->find("first",array("conditions"=>array("social_id"=>$this->data['social_id'])));
				$cardStatus 	=	$this->CardDetail->find('count',array("conditions"=>array('user_id'=>$getUserDetail['User']['id'])));
				$data['User']['card_status']	= (($cardStatus > 0)?1:0);
				
				$bankStatus 	=	$this->BankDetail->find('count',array("conditions"=>array('user_id'=>$getUserDetail['User']['id'])));
				$data['User']['bank_status']	= (($bankStatus > 0)?1:0);
				if($getUserDetail){
					$getUserDetail['User']['card_status']	=	$data['User']['card_status'];
					$getUserDetail['User']['bank_status']	=	$data['User']['bank_status'];
					$getCityName 							= 	$this->Api->getCityById($getUserDetail['User']['city_id']);
					$getUserDetail['User']['city_name']		=	$getCityName['City']['name'];
					$result 								= 	str_replace("null","''",$getUserDetail['User']);
				}else{
					$data['User']['city_name']				=	"";
					$result 								=	str_replace("null","''",$data['User']);
				}
				$response_arry = array('status'=> $status, 'message'=>$message,"result"=>$result);
			}if(!empty($error)){
          		$response_arry = array('status'=> 400, 'message'=> $error);
        	}
		}else{
			$response_arry = array('status'=> 400, 'message'=>_MSGNODATA);
		}
		echo json_encode($response_arry);exit;
	}
	
	public function checkSocialId(){
		$this->layout="None";
		$this->loadModel("User");
		$this->loadModel("CardDetail");
		$this->loadModel("BankDetail");
		$this->data = json_decode(file_get_contents('php://input'),true);
		if(isset($this->data) && !empty($this->data)){
			if(empty($this->data['social_id'])){
				$error	= 	"Please enter social id";
			}elseif(empty($this->data['user_type'])){
				$error	= 	"Please enter user type";
			}else{
				$chkSocialId = $this->User->find("first",array("conditions"=>array("social_id"=>$this->data['social_id'])));
				if($chkSocialId){
					if($this->data['user_type'] == $chkSocialId['User']['user_type']){
						$getCityName 						= 	$this->Api->getCityById($chkSocialId['User']['city_id']);
						$chkSocialId['User']['city_name']	=	$getCityName['City']['name'];
						$data['id']							=	$chkSocialId['User']['id'];
						$data['access_key'] 				= 	md5(date("Y-m-d H:i:s"));
						$this->User->save($data,false);
						$chkSocialId['User']['access_key']	=	$data['access_key'];
						
						$cardStatus 	=	$this->CardDetail->find('count',array("conditions"=>array('user_id'=>$chkSocialId['User']['id'])));
						$chkSocialId['User']['card_status']	= (($cardStatus > 0)?1:0);
						
						$bankStatus 	=	$this->BankDetail->find('count',array("conditions"=>array('user_id'=>$chkSocialId['User']['id'])));
						$chkSocialId['User']['bank_status']	= (($bankStatus > 0)?1:0);
						
						$message 		= 	($chkSocialId['User']['lang_type']==2?'Éxito':'Success');
						
						$response_arry 	= 	array('status'=> 200, 'message'=>$message,"result"=>str_replace("null","''",$chkSocialId['User']));
					}else{
						$org		=	($chkSocialId['User']['lang_type'] == 2?"Organizador":"Organiser");
						$player		=	($chkSocialId['User']['lang_type'] == 2?"Jugador ":"Player");
						$msg 		= 	($getDetail['User']['user_type'] == 1)? $org:$player;
						$message 	= 	($chkSocialId['User']['lang_type'] == 2?"No estás autorizado como ":"You are not authorized as ");
						$response_arry 	= array('status'=> 400, 'message'=>$message." ".$msg,'result'=>(object)[]);
					}	
				}else{
					$response_arry = array('status'=>400, 'message'=>"Please login again");
				}
			}
			if(!empty($error)){
				$response_arry = array('status'=> 400, 'message'=> $error);
			}
		}else{
			$response_arry = array('status'=> 400, 'message'=>"nodata");
		}
		echo json_encode($response_arry);exit;
	}
	
	public function otpVerification(){
		$this->layout="None";
		$this->loadModel("User");
		$this->data = json_decode(file_get_contents('php://input'),true);
		if(isset($this->data) && !empty($this->data)){
			if(empty($this->data['mobile'])){
				$error	= 	"Please enter mobile number";
			}elseif(empty($this->data['otp'])){
				$error	= 	"Please enter otp";
			}elseif(empty($this->data['lang_type'])){//1=eng,2=spanish
				$error	= 	"Please enter language";
			}else{
				$chkUser = $this->Api->checkMobileNo($this->data['mobile']);
				if($chkUser){
					$chkOTP = $this->User->find("first",array("conditions"=>array("User.otp"=>$this->data['otp'],"User.mobile"=>$this->data['mobile']),"fields"=>array('id','status','otp_valid_time','lang_type')));
					if($chkOTP){
						$currDate 			= 	date('Y-m-d H:i:s');
						if(strtotime($currDate)	< strtotime($chkOTP['User']['otp_valid_time'])){
							$data['status']	=	1;
							$data['id']		=	$chkOTP['User']['id'];
							$this->User->save($data,false);
							$getUserData = $this->Api->getUserById($chkOTP['User']['id']);
							$result = array();
							if($getUserData){
								$result = str_replace("null","''",$getUserData['User']);
							}
							$message 		=	($this->data['lang_type'] == 2 ?'OTP verificado con éxito' :'OTP verified successfully');
							$response_arry  =   array('status'=> 200, 'message'=>$message,"result"=>$result);
						}else{
							$message 		=	($this->data['lang_type'] == 2 ?'OTP verificado el tiempo se excede' :'OTP verified time is exceed');
							$response_arry 	= 	array('status'=> 400, 'message'=>$message,'result'=>(object)[]);
						}
					}else{
						$getData 		= 	$this->User->find("first",array("conditions"=>array("User.mobile"=>$this->data['mobile']),"fields"=>array('lang_type')));
						$message 		=	($this->data['lang_type'] == 2 ?'OTP no es válido' :'OTP is invalid');
						$response_arry 	= 	array('status'=> 400, 'message'=>$message,'result'=>(object)[]);
					}
				}else{
					$message = ($this->data['lang_type'] == 2 ?'Por favor, introduzca el número de móvil registrado.' :'Please enter registered mobile number.');
					$response_arry = array('status'=> 400, 'message'=>$message,'result'=>(object)[]);
				}
			}if(!empty($error)){
          		$response_arry = array('status'=> 400, 'message'=> $error,'result'=>(object)[]);
        	}
		}else{
			$response_arry = array('status'=> 400, 'message'=>nodata,'result'=>(object)[]);
		}
		echo json_encode($response_arry);exit;
	}
	
	public function resendOTP(){
		$this->layout="None";
		$this->loadModel("User");
		$this->data = json_decode(file_get_contents('php://input'),true);
		if(isset($this->data) && !empty($this->data)){
			if(empty($this->data['mobile'])){
				$error	= 	"Please enter mobile number";
			}elseif(empty($this->data['lang_type'])){//1=eng,2=spanish
				$error	= 	"Please enter language";
			}else{
				$chkUser = $this->User->find("first",array("conditions"=>array("mobile"=>$this->data['mobile']),"fields"=>array('id','mobile','lang_type')));
				if($chkUser){
					$data['id']				=	$chkUser['User']['id'];
					$data['otp'] 			= 	mt_rand(100000,1000000);//create random OTP
					$sendSMS 				= 	$this->sendOTP($this->data['mobile'],$data['otp']);
					
					$currdate				=	date('Y-m-d H:i:s');
					$data['otp_valid_time']	=	date('Y-m-d H:i:s', strtotime('+10 minutes', strtotime($currdate)));
					
					$this->User->save($data,false);
					$getUserData = $this->Api->getUserById($chkUser['User']['id']);
					$result = array();
					if($getUserData){
						$result = str_replace("null","''",$getUserData['User']);
					}
					$message 		=	($this->data['lang_type'] == 2  ?'OTP enviado con éxito.' :'OTP sent successfully.');
					$response_arry 	= 	array('status'=> '200', 'message'=>$message,"result"=>$result);
				}else{
					$message = ($this->data['lang_type'] == 2 ?'Por favor, introduzca el número de móvil registrado.' :'Please enter registered mobile number.');
					$response_arry = array('status'=> '400', 'message'=>$message,'result'=>(object)[]);
				}
			}if(!empty($error)){
          		$response_arry = array('status'=> '400', 'message'=> $error,'result'=>(object)[]);
        	}
		}else{
			$response_arry = array('status'=> '400', 'message'=>_MSGNODATA,'result'=>(object)[]);
		}
		echo json_encode($response_arry);exit;
	}
	
	public function login(){
		$this->layout="None";
		$this->loadModel("User");
		$this->loadModel("CardDetail");
		$this->loadModel("BankDetail");
		$this->data = json_decode(file_get_contents('php://input'),true);
		if(isset($this->data) && !empty($this->data)){
			if(empty($this->data['email'])){
				$error	= 	"Please enter email";
			}elseif(empty($this->data['password'])){
				$error	= 	"Please enter password";
			}elseif(empty($this->data['device_id'])){
				$error	= 	"Please enter device id";
			}elseif(empty($this->data['lang_type'])){
				$error	= 	"Please enter lang type";
			}elseif(empty($this->data['user_type'])){//1=player,2=organiser
				$error	= 	"Please enter user type";
			}else{
				$getDetail = $this->User->find("first",array("conditions"=>array("OR"=>array(array("email"=>$this->data['email']),"mobile"=>$this->data['email']),'password'=>$this->Auth->password($this->data['password']))));
				if($getDetail){
					if($this->data['user_type'] == $getDetail['User']['user_type']){
						$cardStatus 	=	$this->CardDetail->find('count',array("conditions"=>array('user_id'=>$getDetail['User']['id'])));
						$getDetail['User']['card_status']	= (($cardStatus > 0)?1:0);
						
						$bankStatus 	=	$this->BankDetail->find('count',array("conditions"=>array('user_id'=>$getDetail['User']['id'])));
						$getDetail['User']['bank_status']	= (($bankStatus > 0)?1:0);
						
						if($getDetail['User']['status']==0){
							$message    = ($this->data['lang_type'] == 2?"No estás autorizado. Por favor verifica tu OTP":"You are not authorized.Please verify your OTP");
							$response_arry = array('status'=> 401, 'message'=>$message,'result'=>str_replace("null","''",$getDetail['User']));
						}else{
							if($getDetail['User']['admin_status'] == 0){
								
								$msg1 			=	($this->data['lang_type'] == 2?"Su cuenta ha sido desactivada por Admin. Póngase en contacto con admin":"Your account has been de-activated by Admin.Please contact to admin.");
							
								$msg2			=	($this->data['lang_type'] == 2?"Tu cuenta no está activada por admin":"Your account is not activated by admin.");
							
								$message 		= 	(($getDetail['User']['user_type']==1)?$msg1:$msg2);
								
								$response_arry = array('status'=> 402, 'message'=>$message,'result'=>str_replace("null","''",$getDetail['User']));
							}else{
								$data['id']					=	$getDetail['User']['id'];
								$data['device_id']			=	$this->data['device_id'];
								$data['lang_type']			=	$this->data['lang_type'];
								$data['access_key'] 		= 	md5(date("Y-m-d H:i:s"));
								$this->User->save($data,false);
								$getCityName 				= 	$this->Api->getCityById($getDetail['User']['city_id']);
								$getDetail['User']['city_name']		=	$getCityName['City']['name'];
								$getDetail['User']['access_key']	=	$data['access_key'];
								$getDetail['User']['device_id']		=	$data['device_id'];
								$getDetail['User']['lang_type']		=	$data['lang_type'];
								$message 							= 	($this->data['lang_type'] == 2?"Sesión iniciada con éxito":"Login Successfully");
								$response_arry  = array('status'=> 200, 'message'=>$message,"result"=>str_replace("null","''",$getDetail['User']));
							}
						}
					}else{
						$org		=	($this->data['lang_type'] == 2?"Organizador":"Organiser");
						$player		=	($this->data['lang_type'] == 2?"Jugador ":"Player");
						$msg 		= 	($getDetail['User']['user_type'] == 1)? $org:$player;
						
						
						$message 	= 	($this->data['lang_type'] == 2?"No estás autorizado como ":"You are not authorized as ");
						$response_arry = array('status'=> 400, 'message'=>$message." ".$msg,'result'=>(object)[]);
					}
				}else{
					$message 		= 	($this->data['lang_type'] == 2?"El correo electrónico o la contraseña no son válidos":"Either email or password is invalid");
					$response_arry = array('status'=> 400, 'message'=>$message,'result'=>(object)[]);
				}
				
			}if(!empty($error)){
          		$response_arry  = array('status'=> 400, 'message'=> $error);
        	}
		}else{
			$response_arry = array('status'=> 400, 'message'=>nodata,'result'=>(object)[]);
		}
		echo json_encode($response_arry);exit;
	}
	
	public function forgot_password(){
		$this->layout="None";
		$this->loadModel("User");
		$this->data = json_decode(file_get_contents('php://input'),true);
		if(isset($this->data) && !empty($this->data)){
			if(empty($this->data['mobile'])){
				$error	= 	"Please enter mobile";
			}elseif(empty($this->data['user_type'])){
				$error	= 	"Please enter user type";
			}elseif(empty($this->data['lang_type'])){//1=eng,2=spanish
				$error	= 	"Please enter language";
			}else{
				$mobile 	   	= 	$this->data['mobile'];
				$getId 	   		= 	$this->User->find("first",array("conditions"=>array("mobile"=>$mobile),"fields"=>array('id','full_name','mobile','user_type','social_id','lang_type')));
				if($getId){
					if(empty($getId['User']['social_id'])){
						if($this->data['user_type'] == $getId['User']['user_type']){
							$res['User']['id'] 				= 	$getId['User']['id'];
							$otp							=	mt_rand(100000,1000000);
							//$otp							=	"123456";
							$res['User']['otp'] 			= 	$otp;
							$currdate 						= 	date('Y-m-d H:i:s');
							$res['User']['otp_valid_time']	=	date('Y-m-d H:i:s', strtotime('+10 minutes', strtotime($currdate)));
							$res['User']['status']			=	0;
							$this->User->save($res,false);
							$sendSMS 						= 	$this->sendOTP($getId['User']['mobile'],$otp);
							
							$message 						= 	($this->data['lang_type'] == 2?"OTP ha sido enviado en su número de móvil.":"OTP has been sent on your mobile number.");
							$response_arry = array('status'=> 200, 'message'=>$message,'otp'=>"$otp",'mobile'=>$getId['User']['mobile']);
							
						}else{
							$org		=	($this->data['lang_type'] == 2?"Organizador":"Organiser");
							$player		=	($this->data['lang_type'] == 2?"Jugador ":"Player");
							$msg 		= 	($this->data['lang_type'] == 1)? $org:$player;
							
							
							$message 	= 	($this->data['lang_type'] == 2?"No estás autorizado como ":"You are not authorized as ");
							$response_arry = array('status'=> 400, 'message'=>$message." ".$msg,'result'=>(object)[]);
						}	
					}else{
						$message 	= 	($this->data['lang_type'] == 2?"Esta es una cuenta social. No se puede restablecer la contraseña.
":"This is social account.You can not reset password.");
						$response_arry = array('status'=> 400, 'message'=>$message,'result'=>(object)[]);
					}
				}else{
					$message = ($this->data['lang_type'] == 2 ?'Por favor, introduzca el número de móvil registrado.' :'Please enter registered mobile number.');
					$response_arry = array('status'=> 400, 'message'=>$message);
				}
			}if(!empty($error)){
          		$response_arry = array('status'=> 400, 'message'=> $error);
        	}
		}else{
			$response_arry = array('status'=> 400, 'message'=>"nodata");
		}
		echo json_encode($response_arry);exit;
	}
	
	public function reset_password(){
		$this->layout="None";
		$this->loadModel("User");
		$this->data = json_decode(file_get_contents('php://input'),true);
		if(isset($this->data) && !empty($this->data)){
			if(empty($this->data['mobile'])){
				$error	= 	"Please enter mobile number";
			}elseif(empty($this->data['password'])){
				$error	= 	"Please enter password";
			}elseif(empty($this->data['lang_type'])){//1=eng,2=spanish
				$error	= 	"Please enter language";
			}else{
				$mobile 	   		= 	$this->data['mobile'];
				$getId 	   			= 	$this->User->find("first",array("conditions"=>array("mobile"=>$mobile),"fields"=>array('id','full_name','lang_type')));
				if($getId){
					$res['User']['id'] 			= 	$getId['User']['id'];
					$res['User']['password'] 	= 	$this->Auth->password($this->data['password']);
					$this->User->save($res,false);
					$message 	= 	($this->data['lang_type'] == 2?"La contraseña se ha guardado correctamente":"Password has been saved succssfully");
					$response_arry = array('status'=> 200, 'message'=>$message);
				}else{
					$message = ($this->data['lang_type'] == 2 ?'Numero de celular invalido' :'Invalid Mobile number');
					$response_arry = array('status'=> 400, 'message'=>$message);
				}
			}if(!empty($error)){
          		$response_arry = array('status'=> 400, 'message'=> $error);
        	}
		}else{
			$response_arry = array('status'=> 400, 'message'=>"nodata");
		}
		echo json_encode($response_arry);exit;
	}
	
	public function getCityList(){
		$this->layout="None";
		$this->loadModel("City");
		$getCityDatas	=	$this->City->find('all',array("conditions"=>array('status'=>1),'limit' => 2));
		
		$res			=	array();
		if($getCityDatas){
			foreach($getCityDatas as $getCityData){
				$getCityData['City']['image'] = SITE_PATH."img/city/".$getCityData['City']['image'];
				$res[] 	=	 str_replace("null","''",$getCityData['City']);
			}
		}
		$response_arry = array('status'=> 200, 'message'=>'Success','result'=>$res);
		echo json_encode($response_arry);exit;
	}
	
	public function getSportList(){
		$this->layout="None";
		$this->loadModel("Sport");
		$this->data = json_decode(file_get_contents('php://input'),true);
		if(isset($this->data) && !empty($this->data)){
			if(empty($this->data['lang_type'])){//1=eng,2=spanish
				$error	= 	"Please enter language";
			}else{
				$getSportDatas	=	$this->Sport->find('all',array("conditions"=>array('status'=>1)));
				$res			=	array();
				if($getSportDatas){
					foreach($getSportDatas as $getSportData){
						$getSportData['Sport']['name']		=	($this->data['lang_type']==2?$getSportData['Sport']['spanish_name']:$getSportData['Sport']['name']);
						$res[] 	=	 str_replace("null","''",$getSportData['Sport']);
					}
				}
				$message = ($this->data['lang_type']==2?'Éxito':'Success');
				$response_arry = array('status'=> 200, 'message'=>$message,'result'=>$res);
			}if(!empty($error)){
          		$response_arry = array('status'=> 400, 'message'=> $error);
        	}
		}else{
			$response_arry = array('status'=> 400, 'message'=>"nodata");
		}
		echo json_encode($response_arry);exit;
	}
	
	public function addVenue(){
		$this->layout="None";
		$this->loadModel("Venue");
		$this->loadModel("VenueImage");
		if(isset($this->data) && !empty($this->data)){
			if(empty($this->data['access_key'])){
				$error	= 	"Please enter access key";
			}elseif(empty($this->data['address'])){
				$error	= 	"Please enter address";
			}elseif(empty($this->data['latitude'])){
				$error	= 	"Please enter latitude";
			}elseif(empty($this->data['longitude'])){
				$error	= 	"Please enter longitude";
			}elseif(empty($this->data['name'])){
				$error	= 	"Please enter name";
			}elseif(empty($this->data['sports_id'])){
				$error	= 	"Please enter sports_id";
			}else{
				$chkUser = $this->Api->checkAccessKey($this->data['access_key']);
				if($chkUser){
					$chkVenueName = $this->Venue->find('count',array('conditions'=>array("name"=>$this->data['name'],"latitude"=>$this->data['latitude'],"longitude"=>$this->data['longitude'])));
					if($chkVenueName == 0){
						$data['user_id']			=	$chkUser['User']['id'];
						$data['address'] 	   		= 	$this->data['address'];
						$data['latitude'] 	   		= 	$this->data['latitude'];
						$data['longitude'] 	   		= 	$this->data['longitude'];
						$data['name'] 	   			= 	$this->data['name'];
						$data['sports_id'] 	   		= 	$this->data['sports_id'];
						$allowed_types 					= 	unserialize(ALLOWED_IMAGE_TYPES);
						if (isset($_FILES['image']) &&  !empty($_FILES['image'])) {
							$total_image	=	 count($_FILES['image']['name']);
							for($i=0; $i< $total_image; $i++){
								if($_FILES['image']['size'][$i] !=0){
									if (in_array($_FILES['image']['type'][$i], $allowed_types)) {
										$path		=	'../webroot/img/venue/';
										$extArr 	= 	explode('.', $_FILES['image']['name'][$i]);
										$ext 		= 	end($extArr);
										$filename 	= 	time().$i . '.' . $ext;
										$url 		= 	$path . $filename;
										move_uploaded_file($_FILES['image']['tmp_name'][$i], $url);
										$image['image'][$i] = $filename;
									}else {
										$error = 'Please Upload only *.gif, *.jpg, *.png image only!';
										$response_arry = array('status'=> '400', 'message'=> $error);
										echo json_encode($response_arry);exit;
									}
								}
							}
						}
						$this->Venue->save($data,false);
						$datas['venue_id']	=	$this->Venue->id;
						$datas['user_id']	=	$chkUser['User']['id'];
						for($i=0;$i<count($image['image']) ;$i++){
							$datas['image']	=	$image['image'][$i];
							$this->VenueImage->create();
							$this->VenueImage->save($datas,false);
						}
						$message 		= ($chkUser['User']['id']==2?'Éxito':"Success");
						$response_arry 	= array('status'=> 200, 'message'=>$message);
					}else{
						$message 		= ($chkUser['User']['id']==2?'El nombre del lugar ya existe.':"Venue name already exist.");
						$response_arry 	=  array('status'=> 400, 'message'=>$message);
					}
				}else{
					$response_arry = array('status'=> 404, 'message'=>"Invalid access key.");
				}
			}if(!empty($error)){
          		$response_arry = array('status'=> 400, 'message'=> $error);
        	}
		}else{
			$response_arry = array('status'=> 400, 'message'=>"nodata");
		}
		echo json_encode($response_arry);exit;
	}
	
	
	public function myVenues(){
		$this->layout="None";
		$this->loadModel("Venue");
		$this->loadModel("VenueImage");
		$this->data = json_decode(file_get_contents('php://input'),true);
		if(isset($this->data) && !empty($this->data)){
			if(empty($this->data['access_key'])){
				$error	= 	"Please enter access key";
			}else{
				$chkUser = $this->Api->checkAccessKey($this->data['access_key']);
				if($chkUser){
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
					$getVenueDetails 	=	$this->Venue->find("all",array('conditions'=>array('Venue.user_id'=>$chkUser['User']['id']),"fields"=>array("Venue.*","Sport.name","Sport.spanish_name"),'order'=>array('id desc')));
					if($getVenueDetails){
						$res 		= 	array();
						
						foreach($getVenueDetails as $getVenueDetail){
							$getVenueImage							=	$this->VenueImage->find('all',array('conditions'=>array('venue_id'=>$getVenueDetail['Venue']['id'],'user_id'=>$getVenueDetail['Venue']['user_id']),'fields'=>array('image')));
							$getVenueDetail['Venue']['image']	=	array();
							foreach($getVenueImage as $getVenue_Image){
								$getVenueDetail['Venue']['image'][]	=	SITE_PATH."img/venue/".$getVenue_Image['VenueImage']['image'];
							}
							$getVenueDetail['Venue']['sports_name']	=	(($chkUser['User']['lang_type']==2)?$getVenueDetail['Sport']['spanish_name']:$getVenueDetail['Sport']['name']);	
							$res[] 									= 	str_replace("null","''",$getVenueDetail['Venue']);
						}
						$message 		= ($chkUser['User']['id']==2?'Éxito':"Success");
						$response_arry = array('status'=> 200, 'message'=>$message,'result'=>$res);
					}else{
						$message 		= ($chkUser['User']['id']==2?'Ningún lugar agregado por este usuario.':"No venue added by this user.");
						$response_arry = array('status'=> 400, 'message'=>$message);
					}
				}else{
					$response_arry = array('status'=> 404, 'message'=>"Invalid access key.");
				}
			}if(!empty($error)){
          		$response_arry = array('status'=> 400, 'message'=> $error);
        	}
		}else{
			$response_arry = array('status'=> 400, 'message'=>"nodata");
		}
		echo json_encode($response_arry);exit;
	}
	
	
	
	public function addGame(){
		$this->layout="None";
		$this->loadModel("Game");
		if(isset($this->data) && !empty($this->data)){
			if(empty($this->data['access_key'])){
				$error	= 	"Please enter access key";
			}elseif(empty($this->data['name'])){
				$error	= 	"Please enter game name";
			}elseif(empty($this->data['date_time'])){
				$error	= 	"Please enter date time";
			}elseif(empty($this->data['price'])){
				$error	= 	"Please enter price";
			}elseif(empty($this->data['venue_id'])){
				$error	= 	"Please enter venue_id";
			}elseif(empty($this->data['sports_id'])){
				$error	= 	"Please enter sports id";
			}elseif(empty($this->data['description'])){
				$error	= 	"Please enter description";
			}elseif(empty($this->data['gender'])){
				$error	= 	"Please enter gender";
			}elseif(empty($this->data['no_of_player'])){
				$error	= 	"Please enter no of players";
			}elseif(empty($this->data['min_player'])){
				$error	= 	"Please enter minimum no of players";
			}elseif((!isset($this->data['already_player']) && empty($this->data['already_player']))){//1=avaialable,0=not
				$error	= 	"Please enter already have players";
			}elseif(empty($this->data['payment_mode'])){//1=cash,2=card
				$error	= 	"Please enter payment mode";
			}else{
				$chkUser = $this->Api->checkAccessKey($this->data['access_key']);
				if($chkUser){
					$allowed_types 					= 	unserialize(ALLOWED_IMAGE_TYPES);
					if (isset($_FILES['image']['name']) &&  !empty($_FILES['image']['name'])) {
						if($_FILES['image']['size'] !=0){
							if (in_array($_FILES['image']['type'], $allowed_types)) {
								$fileName = $this->Api->uploadFile('../webroot/img/game/', $_FILES['image']);
								$imgpath = "../webroot/img/game/".$fileName;
								if ($fileName != '')
									$data['image'] = $fileName;
								}else {
									$error = 'Please Upload only *.gif, *.jpg, *.png image only!';
									$response_arry = array('status'=> '400', 'message'=> $error);
									echo json_encode($response_arry);exit;
								}
						}
					}
					if($this->data['already_player'] == 1){
						if(empty($this->data['no_of_already_player'])){
							$error	= 	"Please enter no of already players";
							$response_arry = array('status'=> 400, 'message'=> $error);
						}else{
							$data['no_of_already_player'] = $this->data['no_of_already_player'];
						}
					}
					
					
					$data['user_id']			=	$chkUser['User']['id'];
					$data['name']				=	$this->data['name'];
					
					$date_time					=	$this->data['date_time'];
					$data['date_time'] 			= 	date("Y-m-d H:i:s",strtotime($date_time));//convert time for spain
					$data['price']				=	$this->data['price'];
					$data['description']		=	$this->data['description'];
					$data['gender']				=	$this->data['gender'];
					$data['venue_id']			=	$this->data['venue_id'];
					$data['sports_id']			=	$this->data['sports_id'];
					$data['city_id']			=	$chkUser['User']['city_id'];
					$data['no_of_player']		=	$this->data['no_of_player'];
					$data['min_player']			=	$this->data['min_player'];
					$data['already_player']		=	$this->data['already_player'];
					$data['payment_mode']		=	$this->data['payment_mode'];
					if(isset($this->data['game_id'])){
						$data['id']				=	$this->data['game_id'];
					}
					$this->Game->save($data,false);
					$message 					= 	($chkUser['User']['id']==2?'El juego ha sido añadido con éxito.':"Game has been added successfully");
					$response_arry 				= 	array('status'=> 200, 'message'=>$message);
				}else{
					$response_arry = array('status'=> 404, 'message'=>"Invalid access key.");
				}
			}if(!empty($error)){
          		$response_arry = array('status'=> 400, 'message'=> $error);
        	}
		}else{
			$response_arry = array('status'=> 400, 'message'=>"nodata");
		}
		echo json_encode($response_arry);exit;
	}
	
	public function gameDetailsById_13Mar(){
		$this->layout="None";
		$this->loadModel("User");
		$this->loadModel("Game");
		$this->loadModel("GameReview");
		$this->data = json_decode(file_get_contents('php://input'),true);
		if(isset($this->data) && !empty($this->data)){
			if(empty($this->data['access_key'])){
				$error	= 	"Please enter access key";
			}elseif(empty($this->data['game_id'])){
				$error	= 	"Please enter game id";
			}elseif(empty($this->data['latitude'])){
				$error	= 	"Please enter latitude";
			}elseif(empty($this->data['longitude'])){
				$error	= 	"Please enter longitude";
			}else{
				$chkUser = $this->Api->checkAccessKey($this->data['access_key']);
				if($chkUser){
					$latitude 			= 	$this->data['latitude'];
					$longitude 			= 	$this->data['longitude'];
					$where				=	"where Game.id = ".$this->data['game_id'];
					$getDetails			=	$this->Game->query("SELECT Game.*,User.full_name,User.social_id,User.full_name,User.user_image,Venue.address as venue_address,Venue.name as venue_name,Venue.latitude,Venue.longitude,Sport.name as sport_name,City.name as city_name, ( 3961 * ACOS( COS( RADIANS($latitude) ) * COS( RADIANS( Latitude ) ) * COS( RADIANS( Longitude ) - RADIANS($longitude) ) + SIN( RADIANS($latitude) ) * SIN( RADIANS( Latitude ) ) ) ) AS  `distance`  FROM  
					`games` as Game 
					JOIN `venues` as Venue on Game.venue_id = Venue.id  
					JOIN `users` as User on Game.user_id = User.id 
					JOIN `sports` as Sport on Game.sports_id = Sport.id 
					JOIN `cities` as City on Game.city_id = City.id 
					" .$where. " order by distance asc");
					
					if($getDetails){
						foreach($getDetails as $getDetail){
							$getDetail['Game']['username']	=	$getDetail['User']['full_name'];
							if(!empty($getDetail['User']['social_id'])){
								$getDetail['Game']['user_image'] 	= 	$getDetail['User']['user_image'];
							}else{
								$getDetail['Game']['user_image'] 	= 	SITE_PATH."img/user/".$getDetail['User']['user_image'];
							}
							
							$getDetail['Game']['venue_address']		=	$getDetail['Venue']['venue_address'];
							$getDetail['Game']['venue_name']		=	$getDetail['Venue']['venue_name'];
							$getDetail['Game']['latitude']			=	$getDetail['Venue']['latitude'];
							$getDetail['Game']['longitude']			=	$getDetail['Venue']['longitude'];
							$getDetail['Game']['sport_name']		=	$getDetail['Sport']['sport_name'];
							$getDetail['Game']['image'] 			= 	SITE_PATH."img/game/".$getDetail['Game']['image'];
							$getDetail['Game']['city']				=	$getDetail['City']['city_name'];
							$distance								=	round($getDetail[0]['distance'],2);
							$getDetail['Game']['distance']			=	"$distance";
							
							$getGameReviews 						=	$this->Api->getGameReviewsById($getDetail['Game']['id']);
							$getDetail['Game']['Review']			=	array();
							if($getGameReviews){
								foreach($getGameReviews as $getGameReview){
									$getGameReview['GameReview']['username']		=	$getGameReview['User']['full_name'];
									if(!empty($getGameReview['User']['social_id'])){
										$getGameReview['GameReview']['user_image'] 	= 	$getGameReview['User']['user_image'];
									}else{
										$getGameReview['GameReview']['user_image'] 	= 	SITE_PATH."img/user/".$getGameReview['User']['user_image'];
									}
									$getDetail['Game']['Review'][]					=	$getGameReview['GameReview'];
								}
							}
							$getGameTeams 											=	$this->Api->getTeamByGameId($getDetail['Game']['id']);
							$getDetail['Game']['Team']								=	array();
							$getDetail['Game']['Team']['Blue']						=	array();
							$getDetail['Game']['Team']['Red']						=	array();
							if($getGameTeams){
								foreach($getGameTeams as $getGameTeam){
									$getGameTeam['Team']['username']				=	$getGameTeam['User']['full_name'];
									if(!empty($getGameTeam['User']['social_id'])){
										$getGameTeam['Team']['user_image'] 			= 	$getGameTeam['User']['user_image'];
									}else{
										$getGameTeam['Team']['user_image'] 			= 	SITE_PATH."img/user/".$getGameTeam['User']['user_image'];
									}
									if($getGameTeam['Team']['team_id'] == 1){
										$getDetail['Game']['Team']['Blue'][]		=	$getGameTeam['Team'];
									}else{
										$getDetail['Game']['Team']['Red'][]			=	$getGameTeam['Team'];
									}
								}
							}
						}
						$res		=	$getDetail['Game'];
						$response_arry = array('status'=> 200, 'message'=>'Success','result'=>str_replace("null","''",$getDetail['Game']));
					}else{
						$response_arry = array('status'=> 400, 'message'=>"Invalid game id");
					}
				}else{
					$response_arry = array('status'=> 404, 'message'=>"Invalid access key.");
				}
			}if(!empty($error)){
          		$response_arry = array('status'=> 400, 'message'=> $error);
        	}
		}else{
			$response_arry = array('status'=> 400, 'message'=>"nodata");
		}
		echo json_encode($response_arry);exit;
	}
	
	public function gameDetailsById(){
		$this->layout="None";
		$this->loadModel("User");
		$this->loadModel("Game");
		$this->loadModel("GameReview");
		$this->loadModel("VenueImage");
		$this->loadModel("Payment");
		$this->loadModel("Team");
		$this->loadModel('PlayerAvailability');
		$this->data = json_decode(file_get_contents('php://input'),true);
		if(isset($this->data) && !empty($this->data)){
			if(empty($this->data['access_key'])){
				$error	= 	"Please enter access key";
			}elseif(empty($this->data['game_id'])){
				$error	= 	"Please enter game id";
			}elseif(empty($this->data['latitude'])){
				$error	= 	"Please enter latitude";
			}elseif(empty($this->data['longitude'])){
				$error	= 	"Please enter longitude";
			}else{
				$chkUser = $this->Api->checkAccessKey($this->data['access_key']);
				if($chkUser){
					$latitude 			= 	$this->data['latitude'];
					$longitude 			= 	$this->data['longitude'];
					$where				=	"where Game.id = ".$this->data['game_id'];
					/* $getDetails			=	$this->Game->query("SELECT Game.*,User.full_name,User.social_id,User.full_name,User.user_image,Venue.address as venue_address,Venue.name as venue_name,Venue.latitude,Venue.longitude,Sport.name as sport_name,City.name as city_name, ( 3961 * ACOS( COS( RADIANS($latitude) ) * COS( RADIANS( Latitude ) ) * COS( RADIANS( Longitude ) - RADIANS($longitude) ) + SIN( RADIANS($latitude) ) * SIN( RADIANS( Latitude ) ) ) ) AS  `distance`  FROM  
					`games` as Game 
					JOIN `venues` as Venue on Game.venue_id = Venue.id  
					JOIN `users` as User on Game.user_id = User.id 
					JOIN `sports` as Sport on Game.sports_id = Sport.id 
					JOIN `cities` as City on Game.city_id = City.id 
					" .$where. " order by distance asc"); */
					$getDetails			=	$this->Game->query("SELECT Game.*,Venue.address as venue_address,Venue.name as venue_name,Venue.latitude,Venue.longitude,Sport.name as sport_name,City.name as city_name, ( 6371 * ACOS( COS( RADIANS($latitude) ) * COS( RADIANS( Latitude ) ) * COS( RADIANS( Longitude ) - RADIANS($longitude) ) + SIN( RADIANS($latitude) ) * SIN( RADIANS( Latitude ) ) ) ) AS  `distance`  FROM  
					`games` as Game 
					JOIN `venues` as Venue on Game.venue_id = Venue.id  
					JOIN `sports` as Sport on Game.sports_id = Sport.id 
					JOIN `cities` as City on Game.city_id = City.id 
					" .$where. " order by distance asc");
					if($getDetails){
						foreach($getDetails as $getDetail){
							if($getDetail['Game']['user_id'] !=0){
								$getUser	=	$this->Api->getUserById($getDetail['Game']['user_id']);
								$getDetail['Game']['username']	=	$getUser['User']['full_name'];
								if(!empty($getUser['User']['social_id'])){
									$getDetail['Game']['user_image'] 	= 	$getUser['User']['user_image'];
								}else{
									$getDetail['Game']['user_image'] 	= 	SITE_PATH."img/user/".$getUser['User']['user_image'];
								}
							}else{
								$getDetail['Game']['username']		=	"Odyfo";
								$getVenueImage						=	$this->Api->getVenueImagebyVenueId($getDetail['Game']['venue_id']);
								$getDetail['Game']['user_image'] 	= 	SITE_PATH."img/venue/".$getVenueImage['VenueImage']['image'];
							}
							
							
							$getDetail['Game']['venue_address']		=	$getDetail['Venue']['venue_address'];
							$getDetail['Game']['venue_name']		=	$getDetail['Venue']['venue_name'];
							$getDetail['Game']['latitude']			=	$getDetail['Venue']['latitude'];
							$getDetail['Game']['longitude']			=	$getDetail['Venue']['longitude'];
							$getDetail['Game']['sport_name']		=	$getDetail['Sport']['sport_name'];
							//$getDetail['Game']['image'] 			= 	SITE_PATH."img/game/".$getDetail['Game']['image'];
							$getDetail['Game']['city']				=	$getDetail['City']['city_name'];
							$distance								=	round($getDetail[0]['distance'],2);
							$getDetail['Game']['distance']			=	"$distance";
							
							$getGameReviews 						=	$this->Api->getGameReviewsById($getDetail['Game']['id']);
							$getDetail['Game']['Review']			=	array();
							$getDetail['Game']['Team']['Blue']		=	array();
							$getDetail['Game']['Team']['Red']		=	array();
							if($getGameReviews){
								foreach($getGameReviews as $getGameReview){
									$getGameReview['GameReview']['username']		=	$getGameReview['User']['full_name'];
									if(!empty($getGameReview['User']['social_id'])){
										$getGameReview['GameReview']['user_image'] 	= 	$getGameReview['User']['user_image'];
									}else{
										$getGameReview['GameReview']['user_image'] 	= 	SITE_PATH."img/user/".$getGameReview['User']['user_image'];
									}
									$getDetail['Game']['Review'][]					=	$getGameReview['GameReview'];
								}
							}
							$getGameTeams 											=	$this->Api->getTeamByGameId($getDetail['Game']['id']);
							$getDetail['Game']['Team']								=	array();
							$getDetail['Game']['Team']['Blue']						=	array();
							$getDetail['Game']['Team']['Red']						=	array();
							if($getGameTeams){
								foreach($getGameTeams as $getGameTeam){
									$getGameTeam['Team']['username']				=	$getGameTeam['User']['full_name'];
									if(!empty($getGameTeam['User']['social_id'])){
										$getGameTeam['Team']['user_image'] 			= 	$getGameTeam['User']['user_image'];
									}else{
										$getGameTeam['Team']['user_image'] 			= 	SITE_PATH."img/user/".$getGameTeam['User']['user_image'];
									}
									if($getGameTeam['Team']['team_id'] == 1){
										$getDetail['Game']['Team']['Blue'][]		=	$getGameTeam['Team'];
									}else{
										$getDetail['Game']['Team']['Red'][]			=	$getGameTeam['Team'];
									}
								}
							}
							$getVenueImageByIds		=	$this->VenueImage->find('all',array('conditions'=>array('venue_id'=>$getDetail['Game']['venue_id']),"fields"=>array('image')));
							$image			= array(SITE_PATH."img/game/".$getDetail['Game']['image']);
							if($getVenueImageByIds){
								foreach($getVenueImageByIds as $getVenueImageById){
									$image[]	=	SITE_PATH."img/venue/".$getVenueImageById['VenueImage']['image'];
								}
							}
							$getDetail['Game']['image']			=	$image;
							$this->loadModel('PurchaseGame');
							$getPurchaseStatus	=	$this->PurchaseGame->find('first',array('conditions'=>array('user_id'=>$chkUser['User']['id'],"game_id"=>$this->data['game_id'])));
							if($getPurchaseStatus){
								if($getPurchaseStatus['PurchaseGame']['pay_mode'] == 0){
									$getChargeId	=	$this->Payment->find('first',array('conditions'=>array('user_id'=>$chkUser['User']['id'],"game_id"=>$this->data['game_id'])));
									$getDetail['Game']['charge_id']		=	(!empty($getChargeId['Payment']['charge_id'])?$getChargeId['Payment']['charge_id']:'0');
								}else{
									$getDetail['Game']['charge_id']		=	'0';
								}
								$getDetail['Game']['pay_status']	=	$getPurchaseStatus['PurchaseGame']['pay_mode'];//1=cash,0=card
							}else{
								$getDetail['Game']['pay_status']	=	'2';//not paid
								$getDetail['Game']['charge_id']		=	'0';
							}
							
							
							
							$currentDateTime 						=	$this->data['date_time'];
							$gameTime 								= 	 date('Y-m-d H:i:s', strtotime('-1 day', strtotime($getDetail['Game']['date_time'])));
							if($gameTime >= $currentDateTime){
								$getDetail['Game']['cancel_status']	=	'1';//cancel
							}else{
								$getDetail['Game']['cancel_status']	=	'0';//not cancel
							}
							$chkTeam = $this->Team->find('first',array('conditions'=>array('user_id'=>$chkUser['User']['id'],'game_id'=>$this->data['game_id'],'status'=>1),'fields'=>array('team_id')));
							$getDetail['Game']['team_status']		=	(!empty($chkTeam)? $chkTeam['Team']['team_id']:'0');
							
							$gameStartTime 						= 	$getDetail['Game']['date_time'];
							$seconds 							= 	strtotime($gameStartTime) - strtotime($currentDateTime);
							$hours 								= 	$seconds / 60 /  60;
							$getDetail['Game']['book_status']   = 	($hours > 4)?'1':'0';//1=can book,0=not
							
							$chkAvailability					=	$this->PlayerAvailability->find("first",array('conditions'=>array('game_id'=>$this->data['game_id'],'player_id'=>$chkUser['User']['id'])));	
							$getDetail['Game']['confirm_status']= 	(!empty($chkAvailability)?$chkAvailability['PlayerAvailability']['status']:'0');//1=available,2=not,0=no status
						}
						$res		=	$getDetail['Game'];
						$message	=	($chkUser['User']['lang_type']==2?"Éxito":"Success");
						$response_arry = array('status'=> 200, 'message'=>$message,'result'=>str_replace("null","''",$getDetail['Game']));
					}else{
						$message	=	($chkUser['User']['lang_type']==2?"ID de juego no válido":"Invalid game id");
						$response_arry = array('status'=> 400, 'message'=>$message);
					}
				}else{
					$response_arry = array('status'=> 404, 'message'=>"Invalid access key.");
				}
			}if(!empty($error)){
          		$response_arry = array('status'=> 400, 'message'=> $error);
        	}
		}else{
			$response_arry = array('status'=> 400, 'message'=>"nodata");
		}
		echo json_encode($response_arry);exit;
	}
	
	public function gameDetailsById_live_4Mar19(){
		$this->layout="None";
		$this->loadModel("User");
		$this->loadModel("Game");
		$this->loadModel("GameReview");
		$this->data = json_decode(file_get_contents('php://input'),true);
		if(isset($this->data) && !empty($this->data)){
			if(empty($this->data['access_key'])){
				$error	= 	"Please enter access key";
			}elseif(empty($this->data['game_id'])){
				$error	= 	"Please enter game id";
			}else{
				$chkUser = $this->Api->checkAccessKey($this->data['access_key']);
				if($chkUser){
					$this->Game->bindModel(
							array(
								'belongsTo'=>array(
									'User'=>array(
									  'className' => 'User',
									  'foreignKey' => 'user_id',
									), 
									'Venue'=>array(
									  'className' => 'Venue',
									  'foreignKey' => 'venue_id',
									),
									'Sport'=>array(
									  'className' => 'Sport',
									  'foreignKey' => 'sports_id',
									),
									'City'=>array(
									  'className' => 'City',
									  'foreignKey' => 'city_id',
									),
								)
							)
					); 
					$getDetails 	=	$this->Game->find('first',array('conditions'=>array('Game.id'=>$this->data['game_id']),"fields"=>array('User.full_name','User.user_image','User.social_id','Game.*','Venue.address as venue_address','Venue.name as venue_name','Venue.latitude','Venue.longitude','Sport.name as sport_name','City.name as city_name')));
					if($getDetails){
						$getDetails['Game']['username']	=	$getDetails['User']['full_name'];
						if(!empty($getDetails['User']['social_id'])){
							$getDetails['Game']['user_image'] 	= 	$getDetails['User']['user_image'];
						}else{
							$getDetails['Game']['user_image'] 	= 	SITE_PATH."img/user/".$getDetails['User']['user_image'];
						}
						
						$getDetails['Game']['venue_address']	=	$getDetails['Venue']['venue_address'];
						$getDetails['Game']['venue_name']		=	$getDetails['Venue']['venue_name'];
						$getDetails['Game']['latitude']			=	$getDetails['Venue']['latitude'];
						$getDetails['Game']['longitude']		=	$getDetails['Venue']['longitude'];
						$getDetails['Game']['sport_name']		=	$getDetails['Sport']['sport_name'];
						$getDetails['Game']['image'] 			= 	SITE_PATH."img/game/".$getDetails['Game']['image'];
						$getDetails['Game']['city']				=	$getDetails['City']['city_name'];
						$getGameReviews 						=	$this->Api->getGameReviewsById($getDetails['Game']['id']);
						$getDetails['Game']['Review']			=	array();
						if($getGameReviews){
							foreach($getGameReviews as $getGameReview){
								$getGameReview['GameReview']['username']		=	$getGameReview['User']['full_name'];
								if(!empty($getGameReview['User']['social_id'])){
									$getGameReview['GameReview']['user_image'] 	= 	$getGameReview['User']['user_image'];
								}else{
									$getGameReview['GameReview']['user_image'] 	= 	SITE_PATH."img/user/".$getGameReview['User']['user_image'];
								}
								$getDetails['Game']['Review'][]					=	$getGameReview['GameReview'];
							}
						}
						$res		=	$getDetails['Game'];
						$response_arry = array('status'=> 200, 'message'=>'Success','result'=>str_replace("null","''",$getDetails['Game']));
					}else{
						$response_arry = array('status'=> 400, 'message'=>"Invalid game id");
					}
				}else{
					$response_arry = array('status'=> 404, 'message'=>"Invalid access key.");
				}
			}if(!empty($error)){
          		$response_arry = array('status'=> 400, 'message'=> $error);
        	}
		}else{
			$response_arry = array('status'=> 400, 'message'=>"nodata");
		}
		echo json_encode($response_arry);exit;
	}
	
	public function game_review(){
		$this->layout="None";
		$this->loadModel("GameReview");
		$this->loadModel("Game");
		$this->data = json_decode(file_get_contents('php://input'),true);
		if(isset($this->data) && !empty($this->data)){
			if(empty($this->data['access_key'])){
				$error	= 	"Please enter access key";
			}elseif(empty($this->data['game_id'])){
				$error	= 	"Please enter game id";
			}elseif(empty($this->data['rating'])){
				$error	= 	"Please enter rating";
			}else{
				$chkUser = $this->Api->checkAccessKey($this->data['access_key']);
				if($chkUser){
					$chkGames	=	$this->Game->find('count',array('conditions'=>array('id'=>$this->data['game_id'])));
					if($chkGames){
						if(isset($this->data['message']) && !empty($this->data['message'])){
							$data['message']		=	$this->data['message'];
						}
						$data['user_id']	=	$chkUser['User']['id'];
						$data['game_id']	=	$this->data['game_id'];
						$data['rating']		=	$this->data['rating'];
						$this->GameReview->save($data,false);
						$message			=	($chkUser['User']['lang_type']==2?"Revisión se ha añadido con éxitolid id del juego":"Review has been added successfully");
						$response_arry 		= 	array('status'=> 200, 'message'=>$message);
					}else{
						$message			=	($chkUser['User']['lang_type']==2?"ID de juego no válido":"Invalid game id");
						$response_arry = array('status'=> 400, 'message'=>$message);
					}
				}else{
					$response_arry = array('status'=> 404, 'message'=>"Invalid access key.");
				}
			}if(!empty($error)){
          		$response_arry = array('status'=> 400, 'message'=> $error);
        	}
		}else{
			$response_arry = array('status'=> 400, 'message'=>"nodata");
		}
		echo json_encode($response_arry);exit;
	}
	public function home(){
		$this->layout="None";
		$this->loadModel("User");
		$this->loadModel("Game");
		$this->loadModel("PurchaseGame");
		$this->data = json_decode(file_get_contents('php://input'),true);
		if(isset($this->data) && !empty($this->data)){
			if(empty($this->data['access_key'])){
				$error	= 	"Please enter access key";
			}elseif(empty($this->data['city_id'])){
				$error	= 	"Please enter city id";
			}elseif(empty($this->data['type']) && !isset($this->data['type'])){//0=all,1=football,2=basketball,3=tennis,4=volleyball
				$error	= 	"Please enter type";
			}elseif(empty($this->data['latitude'])){
				$error	= 	"Please enter latitude";
			}elseif(empty($this->data['longitude'])){
				$error	= 	"Please enter longitude";
			}else{
				$chkUser = $this->Api->checkAccessKey($this->data['access_key']);
				
				if($chkUser){
					$currDate	=	date("Y-m-d H:i:s");
					$getPurchaseGames	=	$this->PurchaseGame->find('all',array('conditions'=>array('user_id'=>$chkUser['User']['id']),'fields'=>array('game_id')));
					$game_id	=	array();
					if($getPurchaseGames){
						foreach($getPurchaseGames as $getPurchaseGame){
							$game_id[]	=	$getPurchaseGame['PurchaseGame']['game_id'];
						}
					}
					$gameIds = ((!empty($game_id))? implode(",",$game_id):0);
					
					if($this->data['type'] == 0){
						if($chkUser['User']['user_type']==2){
							$where 		= 	"where Game.status =1 and game_status = 0 and cancel_status = 0 and Game.city_id =".$this->data['city_id']." "."and Game.user_id =".$chkUser['User']['id']. " and Game.id NOT IN (".$gameIds.") and (date_time) > "."'".$currDate."'";
						}else{
							$where 		= 	"where Game.status =1 and Game.id NOT IN (".$gameIds.") and game_status = 0 and cancel_status = 0 and Game.city_id =".$this->data['city_id']. " and (date_time) > "."'".$currDate."'";
						}
					}else{
						if($chkUser['User']['user_type']==2){
							$where 		= 	"where Game.status =1 and cancel_status = 0 and Game.id NOT IN (".$gameIds.") and game_status = 0 and Game.city_id =".$this->data['city_id']. " and Game.sports_id =".$this->data['type']." "."and Game.user_id =".$chkUser['User']['id']. " and (date_time) > "."'".$currDate."'";
						}else{
							$where 		= 	"where Game.status =1 and game_status = 0 and cancel_status = 0 and Game.id NOT IN (".$gameIds.") and game_status = 0 and Game.city_id =".$this->data['city_id']. " and Game.sports_id =".$this->data['type']. " and (date_time) > "."'".$currDate."'";
						}
					}
					$latitude 			= 	$this->data['latitude'];
					$longitude 			= 	$this->data['longitude'];
					
					$chkGames			=	$this->Game->query("SELECT Game.*,Venue.address as venue_address,Venue.name as venue_name,Venue.latitude,Venue.longitude, ( 6371 * ACOS( COS( RADIANS($latitude) ) * COS( RADIANS( Latitude ) ) * COS( RADIANS( Longitude ) - RADIANS($longitude) ) + SIN( RADIANS($latitude) ) * SIN( RADIANS( Latitude ) ) ) ) AS  `distance`  FROM  `games` as Game JOIN `venues` as Venue on Game.venue_id = Venue.id " .$where. " order by distance asc");
					
					$res = array();
					if($chkGames){
						foreach($chkGames as $chkGame){
							if($chkGame['Game']['user_id'] !=0){
								$getUser	=	$this->Api->getUserById($chkGame['Game']['user_id']);
								$chkGame['Game']['username']	=	$getUser['User']['full_name'];
								if(!empty($getUser['User']['social_id'])){
									$chkGame['Game']['user_image'] 	= 	$getUser['User']['user_image'];
								}else{
									$chkGame['Game']['user_image'] 	= 	SITE_PATH."img/user/".$getUser['User']['user_image'];
								}
							}else{
								$chkGame['Game']['username']		=	"Odyfo";
								$getVenueImage						=	$this->Api->getVenueImagebyVenueId($chkGame['Game']['venue_id']);
								$chkGame['Game']['user_image'] 		= 	SITE_PATH."img/venue/".$getVenueImage['VenueImage']['image'];
							}
							$getCityDetail							=	$this->Api->getCityById($chkGame['Game']['city_id']);
							$chkGame['Game']['city']				=	$getCityDetail['City']['name'];
							$chkGame['Game']['city_image']			=	SITE_PATH."img/city/".$getCityDetail['City']['image'];
							$chkGame['Game']['image'] 				= 	SITE_PATH."img/game/".$chkGame['Game']['image'];
							$chkGame['Game']['venue_address']		=	$chkGame['Venue']['venue_address'];
							$chkGame['Game']['venue_name']			=	$chkGame['Venue']['venue_name'];
							$distance								=	round($chkGame[0]['distance'],2);
							$chkGame['Game']['distance']			=	"$distance";
							$res[]									=	str_replace("null","''",$chkGame['Game']);
						}
						
					}
					
					if($chkUser['User']['user_type']==2){
							$getGameIds  = $this->Game->query("SELECT games.id FROM `games` WHERE user_id=".$chkUser['User']['id']." and game_status=2 and id NOT IN(SELECT game_id  FROM `user_ratings` WHERE `user_id` =". $chkUser['User']['id']." group by game_id)");
							$game_id = array();
							$gameRating	=	array();
							if($getGameIds){
								foreach($getGameIds as $getGameId){
									$gameRating[]['game_id'] = $getGameId['games']['id'];
								}
							}
							$pendingRating 	= (string)count($getGameIds);
					}else{
						$pendingRating 	= "0";
						$gameRating =	array();
					}
					
					$message	=	($chkUser['User']['lang_type']==2?"Éxito":"Success");
					$response_arry 								= 	array('status'=> 200, 'message'=>$message,'pendingRating'=>$pendingRating,'gameRating'=>$gameRating,'result'=>$res);
				}else{
					$response_arry = array('status'=> 404, 'message'=>"Invalid access key.");
				}
			}if(!empty($error)){
          		$response_arry = array('status'=> 400, 'message'=> $error);
        	}
		}else{
			$response_arry = array('status'=> 400, 'message'=>"nodata");
		}
		echo json_encode($response_arry);exit;
	}
	
	public function home_13Mar19(){
		$this->layout="None";
		$this->loadModel("User");
		$this->loadModel("Game");
		$this->data = json_decode(file_get_contents('php://input'),true);
		if(isset($this->data) && !empty($this->data)){
			if(empty($this->data['access_key'])){
				$error	= 	"Please enter access key";
			}elseif(empty($this->data['city_id'])){
				$error	= 	"Please enter city id";
			}elseif(empty($this->data['type']) && !isset($this->data['type'])){//0=all,1=football,2=basketball
				$error	= 	"Please enter type";
			}elseif(empty($this->data['latitude'])){
				$error	= 	"Please enter latitude";
			}elseif(empty($this->data['longitude'])){
				$error	= 	"Please enter longitude";
			}else{
				$chkUser = $this->Api->checkAccessKey($this->data['access_key']);
				
				if($chkUser){
					if($this->data['type'] == 0){
						if($chkUser['User']['user_type']==2){
							$where 		= 	"where Game.status =1 and Game.city_id =".$this->data['city_id']." "."and Game.user_id =".$chkUser['User']['id'];
						}else{
							$where 		= 	"where Game.status =1 and Game.city_id =".$this->data['city_id'];
						}
					}else{
						if($chkUser['User']['user_type']==2){
							$where 		= 	"where Game.status =1 and Game.city_id =".$this->data['city_id']. " and Game.sports_id =".$this->data['type']." "."and Game.user_id =".$chkUser['User']['id'];
						}else{
							$where 		= 	"where Game.status =1 and Game.city_id =".$this->data['city_id']. " and Game.sports_id =".$this->data['type'];
						}
					}
					$latitude 			= 	$this->data['latitude'];
					$longitude 			= 	$this->data['longitude'];
					
					$chkGames			=	$this->Game->query("SELECT Game.*,User.full_name,User.social_id,User.user_image,Venue.address as venue_address,Venue.name as venue_name,Venue.latitude,Venue.longitude, ( 6371 * ACOS( COS( RADIANS($latitude) ) * COS( RADIANS( Latitude ) ) * COS( RADIANS( Longitude ) - RADIANS($longitude) ) + SIN( RADIANS($latitude) ) * SIN( RADIANS( Latitude ) ) ) ) AS  `distance`  FROM  `games` as Game JOIN `venues` as Venue on Game.venue_id = Venue.id  join `users` as User on Game.user_id = User.id " .$where. " order by distance asc");
					$res = array();
					if($chkGames){
						foreach($chkGames as $chkGame){
							$chkGame['Game']['username']	=	$chkGame['User']['full_name'];
							if(!empty($chkGame['User']['social_id'])){
								$chkGame['Game']['user_image']	=	$chkGame['User']['user_image'];
							}else{
								$chkGame['Game']['user_image']	=	SITE_PATH."img/user/".$chkGame['User']['user_image'];
							}
							$getCityDetail						=	$this->Api->getCityById($chkGame['Game']['city_id']);
							$chkGame['Game']['city']			=	$getCityDetail['City']['name'];
							$chkGame['Game']['city_image']		=	SITE_PATH."img/city/".$getCityDetail['City']['image'];
							$chkGame['Game']['image'] 			= 	SITE_PATH."img/game/".$chkGame['Game']['image'];
							$chkGame['Game']['venue_address']	=	$chkGame['Venue']['venue_address'];
							$chkGame['Game']['venue_name']		=	$chkGame['Venue']['venue_name'];
							$distance							=	round($chkGame[0]['distance'],2);
							$chkGame['Game']['distance']		=	"$distance";
							$res[]								=	str_replace("null","''",$chkGame['Game']);
						}
					}$response_arry 				= 	array('status'=> 200, 'message'=>'Success','result'=>$res);
				}else{
					$response_arry = array('status'=> 404, 'message'=>"Invalid access key.");
				}
			}if(!empty($error)){
          		$response_arry = array('status'=> 400, 'message'=> $error);
        	}
		}else{
			$response_arry = array('status'=> 400, 'message'=>"nodata");
		}
		echo json_encode($response_arry);exit;
	}
	
	
	public function home_live_4Mar19(){
		$this->layout="None";
		$this->loadModel("User");
		$this->loadModel("Game");
		$this->data = json_decode(file_get_contents('php://input'),true);
		if(isset($this->data) && !empty($this->data)){
			if(empty($this->data['access_key'])){
				$error	= 	"Please enter access key";
			}elseif(empty($this->data['city_id'])){
				$error	= 	"Please enter city id";
			}elseif(empty($this->data['type']) && !isset($this->data['type'])){//0=all,1=football,2=basketball
				$error	= 	"Please enter type";
			}else{
				$chkUser = $this->Api->checkAccessKey($this->data['access_key']);
				if($chkUser){
					if($this->data['type'] == 0){
						$condition = array('Game.status'=>1,'Game.city_id'=>$this->data['city_id']);
					}else{
						$condition = array('Game.status'=>1,'Game.city_id'=>$this->data['city_id'],'Game.sports_id'=>$this->data['type']);
					}
					$this->Game->bindModel(
							array(
								'belongsTo'=>array(
									'User'=>array(
									  'className' => 'User',
									  'foreignKey' => 'user_id',
									), 
									'Venue'=>array(
									  'className' => 'Venue',
									  'foreignKey' => 'venue_id',
									), 
								)
							)
					); 
					$limit		=	10;
					$offset 	= 	(isset($this->data['pageno']))?(($this->data['pageno']-1)*$limit):0;
					$chkGames	=	$this->Game->find('all',array('conditions'=>$condition,"fields"=>array('Game.*','User.full_name','User.social_id','User.user_image','Venue.address as venue_address','Venue.name as venue_name','Venue.latitude','Venue.longitude'),"offset"=>$offset,"limit"=>$limit,"order"=>array('Game.id desc')));
					$res = array();
					if($chkGames){
						foreach($chkGames as $chkGame){
							$chkGame['Game']['username']	=	$chkGame['User']['full_name'];
							if(!empty($chkGame['User']['social_id'])){
								$chkGame['Game']['user_image']	=	$chkGame['User']['user_image'];
							}else{
								$chkGame['Game']['user_image']	=	SITE_PATH."img/user/".$chkGame['User']['user_image'];
							}
							$getCityDetail						=	$this->Api->getCityById($chkGame['Game']['city_id']);
							$chkGame['Game']['city']			=	$getCityDetail['City']['name'];
							$chkGame['Game']['city_image']		=	SITE_PATH."img/city/".$getCityDetail['City']['image'];
							$chkGame['Game']['image'] 			= 	SITE_PATH."img/game/".$chkGame['Game']['image'];
							$chkGame['Game']['venue_address']	=	$chkGame['Venue']['venue_address'];
							$chkGame['Game']['venue_name']		=	$chkGame['Venue']['venue_name'];
							$res[]	=	$chkGame['Game'];
						}
					}$response_arry 				= 	array('status'=> 200, 'message'=>'Success','result'=>$res);
				}else{
					$response_arry = array('status'=> 404, 'message'=>"Invalid access key.");
				}
			}if(!empty($error)){
          		$response_arry = array('status'=> 400, 'message'=> $error);
        	}
		}else{
			$response_arry = array('status'=> 400, 'message'=>"nodata");
		}
		echo json_encode($response_arry);exit;
	}
	
	public function addNews(){
		$this->layout="None";
		$this->loadModel("News");
		if(isset($this->data) && !empty($this->data)){
			if(empty($this->data['access_key'])){
				$error	= 	"Please enter access key";
			}elseif(empty($this->data['name'])){
				$error	= 	"Please enter game name";
			}elseif(empty($this->data['description'])){
				$error	= 	"Please enter description";
			}else{
				$chkUser = $this->Api->checkAccessKey($this->data['access_key']);
				if($chkUser){
					$allowed_types 					= 	unserialize(ALLOWED_IMAGE_TYPES);
					if (isset($_FILES['image']['name']) &&  !empty($_FILES['image']['name'])) {
						if($_FILES['image']['size'] !=0){
							if (in_array($_FILES['image']['type'], $allowed_types)) {
								$fileName = $this->Api->uploadFile('../webroot/img/news/', $_FILES['image']);
								$imgpath = "../webroot/img/news/".$fileName;
								if ($fileName != '')
									$data['image'] = $fileName;
								}else {
									$error = 'Please Upload only *.gif, *.jpg, *.png image only!';
									$response_arry = array('status'=> '400', 'message'=> $error);
									echo json_encode($response_arry);exit;
								}
						}
					}
					$data['user_id']		=	$chkUser['User']['id'];
					$data['name']			=	$this->data['name'];
					$data['description']	=	$this->data['description'];
					if(isset($this->data['news_id'])){
						$data['id']			=	$this->data['news_id'];
					}
					$this->News->save($data);
					$message				=	($chkUser['User']['lang_type']==2?"Las noticias se han añadido con éxito.":"News has been added successfully");
					$response_arry 			= 	array('status'=> 200, 'message'=>'News has been added successfully');
					
				}else{
					$response_arry = array('status'=> 404, 'message'=>"Invalid access key.");
				}
			}if(!empty($error)){
          		$response_arry = array('status'=> 400, 'message'=> $error);
        	}
		}else{
			$response_arry = array('status'=> 400, 'message'=>"nodata");
		}
		echo json_encode($response_arry);exit;
	}
	
	public function myNews(){
		$this->layout="None";
		$this->loadModel("News");
		$this->data = json_decode(file_get_contents('php://input'),true);
		if(isset($this->data) && !empty($this->data)){
			if(empty($this->data['access_key'])){
				$error	= 	"Please enter access key";
			}else{
				$chkUser = $this->Api->checkAccessKey($this->data['access_key']);
				if($chkUser){
					$getNewsDetails			=	$this->News->find('all',array('conditions'=>array('News.user_id'=>$chkUser['User']['id'],'News.status'=>1),'order'=>array('id desc')));
					$data					=	array();
					if($getNewsDetails){
						foreach($getNewsDetails as $getNewsDetail){
							$getNewsDetail['News']['image']		=	SITE_PATH."img/news/".$getNewsDetail['News']['image'];
							$data[]								=	str_replace("null","''",$getNewsDetail['News']);
						}
					}
					$message				=	($chkUser['User']['lang_type']==2?"Éxito":"Success");
					$response_arry 			= 	array('status'=> 200, 'message'=>$message,'result'=>$data);
					
				}else{
					$response_arry = array('status'=> 404, 'message'=>"Invalid access key.");
				}
			}if(!empty($error)){
          		$response_arry = array('status'=> 400, 'message'=> $error);
        	}
		}else{
			$response_arry = array('status'=> 400, 'message'=>"nodata");
		}
		echo json_encode($response_arry);exit;
	}
	
	public function getAllNews(){
		$this->layout="None";
		$this->loadModel("News");
		$this->data = json_decode(file_get_contents('php://input'),true);
		if(isset($this->data) && !empty($this->data)){
			if(empty($this->data['access_key'])){
				$error	= 	"Please enter access key";
			}else{
				$chkUser = $this->Api->checkAccessKey($this->data['access_key']);
				if($chkUser){
					$this->News->bindModel(
							array(
								'belongsTo'=>array(
								 'User'=>array(
								  'className' => 'User',
								  'foreignKey' => 'user_id',
									)        
								)
							)
					); 
					$getNewsDetails			=	$this->News->find('all',array('conditions'=>array('News.status'=>1),"fields"=>array("News.*","User.full_name"),"order"=>array('id desc')));
					$data					=	array();
					if($getNewsDetails){
						foreach($getNewsDetails as $getNewsDetail){
							$getNewsDetail['News']['image']		=	SITE_PATH."img/news/".$getNewsDetail['News']['image'];
							$getNewsDetail['News']['user_name']	=	$getNewsDetail['User']['full_name'];
							$data[]								=	str_replace("null","''",$getNewsDetail['News']);
						}
					}
					$message				=	($chkUser['User']['lang_type']==2?"Éxito":"Success");
					$response_arry 			= 	array('status'=> 200, 'message'=>$message,'result'=>$data);
					
				}else{
					$response_arry = array('status'=> 404, 'message'=>"Invalid access key.");
				}
			}if(!empty($error)){
          		$response_arry = array('status'=> 400, 'message'=> $error);
        	}
		}else{
			$response_arry = array('status'=> 400, 'message'=>"nodata");
		}
		echo json_encode($response_arry);exit;
	}
	
	public function getNewsDetailByID(){
		$this->layout="None";
		$this->loadModel("News");
		$this->loadModel("ViewNews");
		$this->data = json_decode(file_get_contents('php://input'),true);
		
		if(isset($this->data) && !empty($this->data)){
			if(empty($this->data['access_key']) && !isset($this->data['access_key'])){
				$error	= 	"Please enter access key";
			}elseif(empty($this->data['news_id'])){
				$error	= 	"Please enter news id";
			}else{
				$chkUser = $this->Api->checkAccessKey($this->data['access_key']);
				if($chkUser){
					$this->News->bindModel(
							array(
								'belongsTo'=>array(
								 'User'=>array(
								  'className' => 'User',
								  'foreignKey' => 'user_id',
									)        
								)
							)
					); 
					$getNews			=	$this->News->find('first',array('conditions'=>array('News.id'=>$this->data['news_id'],'News.status'=>1),"fields"=>array("News.*","User.full_name")));
					
					if($getNews){
						$data['user_id']	=	$chkUser['User']['id'];
						$getViewNews		=	$this->ViewNews->find('count',array('conditions'=>array('news_id'=>$this->data['news_id'],'user_id'=>$chkUser['User']['id'])));
						if($getViewNews == 0){
							$data['news_id']	=	$this->data['news_id'];
							$this->ViewNews->save($data,false);
							
							$res['id'] 			= 	$getNews['News']['id'];
							$res['view_count']	=	($getNews['News']['view_count'] + 1);
							$this->News->save($res,false);
						}
						$getNews['News']['image']		=	SITE_PATH."img/news/".$getNews['News']['image'];
						$getNews['News']['user_name']	=	$getNews['User']['full_name'];
						$datas							=	str_replace("null","''",$getNews['News']);
						$message						=	($chkUser['User']['lang_type']==2?"Éxito":"Success");
						$response_arry 					= 	array('status'=> 200, 'message'=>$message,'result'=>$datas);
					}else{
						$message						=	($chkUser['User']['lang_type']==2?"ID de noticias no válida.":"Invalid news id.");
						$response_arry 					=	array('status'=> 400, 'message'=>$message);
					}
				}else{
					$response_arry = array('status'=> 404, 'message'=>"Invalid access key.");
				}
			}if(!empty($error)){
          		$response_arry = array('status'=> 400, 'message'=> $error);
        	}
		}else{
			$response_arry = array('status'=> 400, 'message'=>"nodata");
		}
		echo json_encode($response_arry);exit;
	}
	
	public function saveUserCityId(){
		$this->layout="None";
		$this->loadModel("User");
		$this->loadModel("City");
		$this->data = json_decode(file_get_contents('php://input'),true);
		if(isset($this->data) && !empty($this->data)){
			if(empty($this->data['access_key'])){
				$error	= 	"Please enter access key";
			}elseif(empty($this->data['city_id'])){
				$error	= 	"Please enter city id";
			}else{
				$chkUser = $this->Api->checkAccessKey($this->data['access_key']);
				if($chkUser){
					$chkCityId = $this->City->find('first',array('conditions'=>array('id'=>$this->data['city_id']),'fields'=>array('id','name')));
					if($chkCityId){
						$data['id']	=	$chkUser['User']['id'];
						$data['city_id']	=	$this->data['city_id'];
						$this->User->save($data,false);
						$message			=	($chkUser['User']['lang_type']==2?"La ciudad se ha guardado con éxito.":"City has been saved successfully");
						$response_arry 		= 	array('status'=> 200, 'message'=>$message,'city_id'=>$this->data['city_id'],'city_name'=>$chkCityId['City']['name']);
					}else{
						$message			=	($chkUser['User']['lang_type']==2?"Ciudad inválida.":"Invalid city.");
						$response_arry = array('status'=> 400, 'message'=>"Invalid city.");
					}
				}else{
					$response_arry = array('status'=> 404, 'message'=>"Invalid access key.");
				}
			}if(!empty($error)){
          		$response_arry = array('status'=> 400, 'message'=> $error);
        	}
		}else{
			$response_arry = array('status'=> 400, 'message'=>"nodata");
		}
		echo json_encode($response_arry);exit;
	}
	
	public function addTeam(){
		$this->layout="None";
		$this->loadModel("Team");
		$this->loadModel("Game");
		$this->loadModel("PlayerAvailability");
		$this->data = json_decode(file_get_contents('php://input'),true);
		if(isset($this->data) && !empty($this->data)){
			if(empty($this->data['access_key'])){
				$error	= 	"Please enter access key";
			}elseif(empty($this->data['game_id'])){
				$error	= 	"Please enter game id";
			}elseif(empty($this->data['team_id'])){//1=blue,2=red
				$error	= 	"Please enter team id";
			}elseif(empty($this->data['date_time'])){
				$error	= 	"Please enter date time";
			}else{
				$chkUser = $this->Api->checkAccessKey($this->data['access_key']);
				if($chkUser){
					$chkGameId = $this->Game->find('first',array('conditions'=>array('id'=>$this->data['game_id']),'fields'=>array('id','gender','date_time','no_of_player')));
					if($chkGameId){
						$chkTotalTeam 	=	$this->Team->find("count",array('conditions'=>array('team_id'=>$this->data['team_id'],'game_id'=>$this->data['game_id'],'user_id !='=>$chkUser['User']['id'])));
						if($chkTotalTeam < $chkGameId['Game']['no_of_player']){
							if(($chkUser['User']['gender'] == $chkGameId['Game']['gender']) || ($chkGameId['Game']['gender']==3)){
								$checkAlreadyAdd = $this->Team->find('first',array('conditions'=>array('user_id'=>$chkUser['User']['id'],'game_id'=>$this->data['game_id'])));
								$data['user_id']		=	$chkUser['User']['id'];
								$data['game_id']		=	$this->data['game_id'];
								$data['team_id']		=	$this->data['team_id'];
								if(!empty($checkAlreadyAdd)){
									$data['id']	=	$checkAlreadyAdd['Team']['id'];
								}
								$this->Team->save($data,false);
								
								$currentDateTime 		=	$this->data['date_time'];
								$gameTime				=	$chkGameId['Game']['date_time'];
								$seconds 				= 	strtotime($gameTime) - strtotime($currentDateTime);
								$hours 					= 	$seconds / 60 /  60;
								if($hours < 20 && $hours > 4){
									$datas['user_id']	=	$chkUser['User']['id'];
									$datas['game_id']	=	$this->data['game_id'];
									$datas['player_id']	=	$chkUser['User']['id'];
									$datas['status']	=	1;
									$this->PlayerAvailability->save($datas);
									$message			=	($chkUser['User']['lang_type']==2?"De acuerdo con nuestra política, no hay reembolso otorgado. Como te unirás al juego en 24 horas de tiempo de juego":"As per our policy, No refund awarded. As you are joining game with in 24 hrs of game timings.");
									$response_arry 		= 	array('status'=> 200, 'message'=>$message);
								}else{
									$message			=	($chkUser['User']['lang_type']==2?"Has sido agregado en este equipo.":"You have been added in this team");
									$response_arry 		= 	array('status'=> 200, 'message'=>$message);
								}
								
							}else{
								if($chkUser['User']['lang_type']==2){
									$gender =($chkGameId['Game']['gender']==1)?'Masculino':'Hembra';
								}else{
									$gender =($chkGameId['Game']['gender']==1)?'Male':'Female';
								}
								
								
								$message		=	($chkUser['User']['lang_type']==2?"Este juego es para ". $gender . "jugadores por favor intenta elegir cualquier otro juego":"This game is for ".$gender." players. please try to choose any other game");
								$response_arry 	= 	array('status'=> 400, 'message'=>$message);
							}
						}else{
							$message			=	($chkUser['User']['lang_type']==2?"Este equipo tiene un número máximo de jugadores, elija otro equipo.":"This team has a maximum number of players, please choose another team.");
							$response_arry = array('status'=> 400, 'message'=>$message);
						}
					}else{
						$message				=	($chkUser['User']['lang_type']==2?"Juego no válido.":"Invalid Game.");
						$response_arry = array('status'=> 400, 'message'=>$message);
					}
				}else{
					$response_arry = array('status'=> 404, 'message'=>"Invalid access key.");
				}
			}if(!empty($error)){
          		$response_arry = array('status'=> 400, 'message'=> $error);
        	}
		}else{
			$response_arry = array('status'=> 400, 'message'=>"nodata");
		}
		echo json_encode($response_arry);exit;
	}
	
	public function userProfile(){
		$this->layout="None";
		$this->loadModel("User");
		$this->loadModel("UserRating");
		$this->loadModel("Game");
		$this->loadModel("Venue");
		$this->loadModel("Payment");
		$this->loadModel("Sport");
		$this->data = json_decode(file_get_contents('php://input'),true);
		if(isset($this->data) && !empty($this->data)){
			if(empty($this->data['access_key'])){
				$error	= 	"Please enter access key";
			}else{
				$chkUser = $this->Api->checkAccessKey($this->data['access_key']);
				if($chkUser){
					$this->User->bindModel(
							array(
								'belongsTo'=>array(
								 'City'=>array(
								  'className' => 'City',
								  'foreignKey' => 'city_id',
									)        
								)
							)
					); 
					$getUser 						= 	$this->User->find('first',array('conditions'=>array('User.id'=>$chkUser['User']['id']),'fields'=>array('id','social_id','full_name','email','mobile','user_image','city_id','City.name as city_name')));
					
					$getUser['User']['user_image']	=	(!empty($getUser['User']['social_id'])?$getUser['User']['user_image']:SITE_PATH."img/user/".$getUser['User']['user_image']);
					
					$getUser['User']['city'] 		=	$getUser['City']['city_name'];
					
					$getRating						=	$this->UserRating->find('first',array('conditions'=>array('player_id'=>$chkUser['User']['id']),"fields"=>array('avg(rating) as rating')));
					$getUser['User']['rating']		=	((!empty($getRating))? round($getRating[0]['rating'],1):'0');
					
					$getUser['User']['totalGame']	= 	$this->Game->find("count",array('conditions'=>array('user_id'=>$chkUser['User']['id'],'game_status'=>2,'status'=>1)));//count organiser complete game
					
					$getUser['User']['totalVenue']	= 	$this->Venue->find("count",array('conditions'=>array('user_id'=>$chkUser['User']['id'])));
					
					$getUser['User']['totalTournament']	= 0;
					
					
					if($chkUser['User']['user_type'] == 1){
						$condition	=	array("UserRating.user_type"=>1);
					}else{
						$condition	=	array("UserRating.user_type"=>2);
					}
					
					$getMyRanks						=	$this->UserRating->find('all',array("conditions"=>$condition,"fields"=>array('player_id','avg(rating) as rating'),"group"=>array('player_id'),"order"=>array('rating desc')));
					$playerRank						=	array();
					if($getMyRanks){
						foreach($getMyRanks  as $getMyRank){
							$playerRank[]			=	$getMyRank['UserRating']['player_id'];
						}
					}
					$rank 							= 	array_search($chkUser['User']['id'],$playerRank);
					$getUser['User']['rank']		=	(!empty($rank)?	$rank:"0");
					
					$this->UserRating->bindModel(
							array(
								'belongsTo'=>array(
								 'User'=>array(
								  'className' => 'User',
								  'foreignKey' => 'player_id',
									)        
								)
							)
					); 
					$getTopPlayerByRatings					=	$this->UserRating->find('all',array("conditions"=>$condition,"fields"=>array('User.full_name','User.user_image','User.social_id','player_id','avg(rating) as rating'),"group"=>array('player_id'),"limit"=>5,"order"=>array('rating desc')));
					
					$getUser['User']['topPlayer']			=	array();
					if($getTopPlayerByRatings){
						foreach($getTopPlayerByRatings  as $getTopPlayerByRating){
							$getPlayer['username']			=	$getTopPlayerByRating['User']['full_name'];
							
							$getPlayer['user_image'] 		=	(!empty($getTopPlayerByRating['User']['social_id'])?$getTopPlayerByRating['User']['user_image']:(!empty($getTopPlayerByRating['User']['user_image'])?SITE_PATH."img/user/".$getTopPlayerByRating['User']['user_image']:''));
							
							
							$getPlayer['rating']			=	(string)(round($getTopPlayerByRating[0]['rating'],1));
							$getUser['User']['topPlayer'][]	=	str_replace(null,'',$getPlayer);
						}
					}
					
					$this->Payment->bindModel(
							array(
								'belongsTo'=>array(
								 'Game'=>array(
								  'className' => 'Game',
								  'foreignKey' => 'game_id',
									)
								)
							)
					); 
					$getDatas = $this->Payment->find("all",array('conditions'=>array('Payment.user_id'=>$chkUser['User']['id'],"Payment.status"=>array("0",'1','2'),'pay_mode'=>0),'fields'=>array('Payment.*','Game.name','Game.sports_id','Game.venue_id'),"order"=>array("id desc")));
					
					$getUser['User']['transaction']	=	array();
					if($getDatas){
						foreach($getDatas as $getData){
							$getSportName = $this->Sport->find('first',array('conditions'=>array('id'=>$getData['Game']['sports_id']),'fields'=>array('name')));
							$getData['Payment']['sport_name']		=	$getSportName['Sport']['name'];	
							
							$getVenueName	=	$this->Venue->find('first',array('conditions'=>array('id'=>$getData['Game']['venue_id']),'fields'=>array('name')));
							$getData['Payment']['venue']			=	$getVenueName['Venue']['name'];	
							
							$getData['Payment']['game_name']		=	$getData['Game']['name'];
							$getData['Payment']['sport_id']			=	$getData['Game']['sports_id'];
							$getData['Payment']['type']				=	(($getData['Payment']['status'] == 1)?"Debit":"Credit");
							$getData['Payment']['amount']			=	($getData['Payment']['amount']/100);
							$getUser['User']['transaction'][]		=	str_replace("null","''",$getData['Payment']);
						}
					}
					$message								=	($chkUser['User']['lang_type']==2?"Éxito":"Success");
					$response_arry 							= 	array('status'=> 200, 'message'=>$message,'result'=>str_replace("null","''",$getUser['User']));
				}else{
					$response_arry = array('status'=> 404, 'message'=>"Invalid access key.");
				}
			}if(!empty($error)){
          		$response_arry = array('status'=> 400, 'message'=> $error);
        	}
		}else{
			$response_arry = array('status'=> 400, 'message'=>"nodata");
		}
		echo json_encode($response_arry);exit;
	}
	
	public function payAsYouGo(){
		$this->layout="None";
		$this->loadModel("Game");
		$this->data = json_decode(file_get_contents('php://input'),true);
		if(isset($this->data) && !empty($this->data)){
			if(empty($this->data['access_key'])){
				$error	= 	"Please enter access key";
			}elseif(empty($this->data['game_id'])){
				$error	= 	"Please enter game id";
			}else{
				$chkUser = $this->Api->checkAccessKey($this->data['access_key']);
				if($chkUser){
					$chkGameId = $this->Game->find('first',array('conditions'=>array('id'=>$this->data['game_id']),'fields'=>array('id')));
					if($chkGameId){
						$data['id']				=	$chkGameId['Game']['id'];	
						$data['game_status']	=	1;
						$this->Game->save($data,false);
						$response_arry 							= 	array('status'=> 200, 'message'=>'Success');
					}else{
						$response_arry = array('status'=> 400, 'message'=>"Invalid Game.");
					}
				}else{
					$response_arry = array('status'=> 404, 'message'=>"Invalid access key.");
				}
			}if(!empty($error)){
          		$response_arry = array('status'=> 400, 'message'=> $error);
        	}
		}else{
			$response_arry = array('status'=> 400, 'message'=>"nodata");
		}
		echo json_encode($response_arry);exit;
	}
	
	public function deleteGame(){
		$this->layout="None";
		$this->loadModel("Game");
		$this->loadModel("Team");
		$this->loadModel("Payment");
		$this->loadModel("CardDetail");
		$this->loadModel("User");
		$this->loadModel("Notification");
		$this->data = json_decode(file_get_contents('php://input'),true);
		if(isset($this->data) && !empty($this->data)){
			if(empty($this->data['access_key'])){
				$error	= 	"Please enter access key";
			}elseif(empty($this->data['game_id'])){
				$error	= 	"Please enter game id";
			}else{
				$chkUser = $this->Api->checkAccessKey($this->data['access_key']);
				if($chkUser){
					$chkGameId = $this->Game->find('first',array('conditions'=>array('id'=>$this->data['game_id']),'fields'=>array('id','game_status','image','min_player','price','name')));
					if($chkGameId){
						$chkTeam 	=	$this->Team->find('all',array('conditions'=>array('Team.game_id'=>$this->data['game_id'])));
						if(count($chkTeam) >= $chkGameId['Game']['min_player']){//refund + 20%(trasfer)charge
							$getCardDetail = $this->CardDetail->find('first',array('conditions'=>array('user_id'=>$chkUser['User']['id'])));
								$exp_date = explode('-',$getCardDetail['CardDetail']['exp_date']);
								\Stripe\Stripe::setApiKey(STRIPE_SECRET_KEY);

								$token = \Stripe\Token::create([
								  'card' => [
									'number' 	=> $getCardDetail['CardDetail']['card_no'],
									'exp_month' => $exp_date[0],
									'exp_year' 	=> $exp_date[1],
									'cvc' 		=> $getCardDetail['CardDetail']['cvv_code']
								  ]
								]);
								$totalprice 	= 	(((count($chkTeam) * $chkGameId['Game']['price'])*100)*0.20);
								$token_id		=	$token->id;
								$description	=	"During Game delete";
								\Stripe\Stripe::setApiKey(STRIPE_SECRET_KEY);
								try{
									$charge = \Stripe\Charge::create(array(
									  "amount" 		=> $totalprice, // amount in cents, again
									  "currency" 	=> "EUR",
									  "source" 		=> $token_id,
									  "description" => $description)
									);
									$success = 1;
								}catch(Stripe_CardError $e) {
									$success	=	0;
									$error = $e->getMessage();
								} catch (Stripe_InvalidRequestError $e) {
								  // Invalid parameters were supplied to Stripe's API
								  $success	=	0;
								  $error = $e->getMessage();
								} catch (Stripe_AuthenticationError $e) {
								  // Authentication with Stripe's API failed
								  $success	=	0;
								  $error = $e->getMessage();
								} catch (Stripe_ApiConnectionError $e) {
								  // Network communication with Stripe failed
								  $success	=	0;
								  $error = $e->getMessage();
								} catch (Stripe_Error $e) {
								  // Display a very generic error to the user, and maybe send
								  // yourself an email
								  $success	=	0;
								  $error = $e->getMessage();
								}catch (Exception $e) {
								  // Something else happened, completely unrelated to Stripe
								  $success	=	0;
								  $error = $e->getMessage();
								}
								$status = (($success!=1)?5:1);
								$data['user_id']		=	$chkUser['User']['id'];
								$data['game_id']		=	$this->data['game_id'];
								$data['amount']			=	$totalprice;
								$data['token']			=	$token_id;
								$data['description']	=	$description;
								$data['charge_id']		=	$charge->id;
								$data['transaction_id']	=	$charge->balance_transaction;
								$data['status']			=	$status;
								$this->Payment->save($data);
						}
						$user_id = array();
						if($chkTeam){
							$message = $chkGameId['Game']['name']." "."has been cancelled by Organiser";
							foreach($chkTeam as $team){
								$getUserDetail = $this->User->find("first",array('conditions'=>array('id'=>$team['Team']['user_id']),"fields"=>array("id","device_id","device_type","badge","lang_type")));
								if(!empty($getUserDetail['User']['device_id'])){
									$badge 	= 	($getUserDetail['User']['badge'] + 1);
									$this->User->updateAll(
										array('badge'=>$badge),
										array('id'=>$getUserDetail['User']['id'])
									);
									$noti_msg = (($getUserDetail['User']['lang_type']==2)?$chkGameId['Game']['name']." "."ha sido cancelado por el organizador":$chkGameId['Game']['name']." "."has been cancelled by Organiser");
									
									$this->Pusher->notification($getUserDetail['User']['device_type'],$getUserDetail['User']['device_id'],$noti_msg,'CancelGameByOrganiser',$this->data['game_id'],$badge);
								}
								$data1['Notification']['type']			=	"CancelGameByOrganiser";
								$data1['Notification']['message'] 		= 	$message;
								$data1['Notification']['sender_id']		=	0;
								$data1['Notification']['receiver_id']	=	$team['Team']['user_id'];
								$data1['Notification']['game_id']		=	$this->data['game_id'];
								$this->Notification->create();
								$this->Notification->save($data1,false);
								
								$user_id[] = $team['Team']['user_id'];
							}
						}
						$getChargeIds = $this->Payment->find('all',array('conditions'=>array('user_id'=>$user_id,"game_id"=>$this->data['game_id'])));
						if($getChargeIds){
							foreach($getChargeIds as $getChargeId){
								if(!empty($getChargeId['Payment']['charge_id'])){
									$refundStatus = $this->refundStripe($getChargeId['Payment']['charge_id']);
									$status = (($refundStatus == 0)?4:0);
									$data['user_id']		=	$getChargeId['Payment']['user_id'];
									$data['game_id']		=	$this->data['game_id'];
									$data['amount']			=	$getChargeId['Payment']['amount'];
									$data['token']			=	$getChargeId['Payment']['token'];
									$data['description']	=	$getChargeId['Payment']['description'];
									$data['charge_id']		=	$getChargeId['Payment']['charge_id'];
									$data['transaction_id']	=	$getChargeId['Payment']['transaction_id'];
									$data['status']			=	$status;
									$this->Payment->create();
									$this->Payment->save($data);
									$this->Team->deleteAll(array('user_id'=>$getChargeId['Payment']['user_id'],'game_id'=>$this->data['game_id']));
								}
							}
						}
						$data['id']				=	$chkGameId['Game']['id'];	
						$data['cancel_status']	=	1;
						$data['cancel_date']	=	date("Y-m-d H:i:s");
						$this->Game->save($data,false);
						$message	   =	($chkUser['User']['lang_type']==2?"Éxito":"Success");
						$response_arry = array('status'=> 200, 'message'=>$message);
					}else{
						$message	   =	($chkUser['User']['lang_type']==2?"Juego no válido.":"Invalid Game.");
						$response_arry = array('status'=> 400, 'message'=>$message);
					}
				}else{
					$response_arry = array('status'=> 404, 'message'=>"Invalid access key.");
				}
			}if(!empty($error)){
          		$response_arry = array('status'=> 400, 'message'=> $error);
        	}
		}else{
			$response_arry = array('status'=> 400, 'message'=>"nodata");
		}
		echo json_encode($response_arry);exit;
	}
	
	public function deleteNews(){
		$this->layout="None";
		$this->loadModel("News");
		$this->data = json_decode(file_get_contents('php://input'),true);
		if(isset($this->data) && !empty($this->data)){
			if(empty($this->data['access_key'])){
				$error	= 	"Please enter access key";
			}elseif(empty($this->data['news_id'])){
				$error	= 	"Please enter news id";
			}else{
				$chkUser = $this->Api->checkAccessKey($this->data['access_key']);
				if($chkUser){
					$chkNewsId = $this->News->find('first',array('conditions'=>array('id'=>$this->data['news_id']),'fields'=>array('id','image')));
					if($chkNewsId){
							$data['id']		=	$chkNewsId['News']['id'];
							$path 			= 	"../webroot/img/news/".$chkNewsId['News']['image'];
							if (file_exists($path)){
								unlink($path);
							}
							$this->News->delete($data['id']);
							$message		=	($chkUser['User']['lang_type']==2?"Las noticias se han eliminado con éxito":"News has been deleted successfully");
							$response_arry 	= 	array('status'=> 200, 'message'=>$message);
					}else{
						$message		=	($chkUser['User']['lang_type']==2?"ID de noticias no válida.":"Invalid News id.");
						$response_arry = array('status'=> 400, 'message'=>"Invalid News id.");
					}
				}else{
					$response_arry = array('status'=> 404, 'message'=>"Invalid access key.");
				}
			}if(!empty($error)){
          		$response_arry = array('status'=> 400, 'message'=> $error);
        	}
		}else{
			$response_arry = array('status'=> 400, 'message'=>"nodata");
		}
		echo json_encode($response_arry);exit;
	}
	
	public function myGames(){
		$this->layout="None";
		$this->loadModel("Game");
		$this->loadModel("Team");
		$this->loadModel("Venue");
		$this->loadModel("Sport");
		$this->loadModel("PurchaseGame");
		$this->loadModel("PlayerCancelGame");
		$this->data = json_decode(file_get_contents('php://input'),true);
		if(isset($this->data) && !empty($this->data)){
			if(empty($this->data['access_key'])){
				$error	= 	"Please enter access key";
			}elseif(empty($this->data['user_type'])){
				$error	= 	"Please enter user type";
			}else{
				$chkUser = $this->Api->checkAccessKey($this->data['access_key']);
				if($chkUser){
					if($chkUser['User']['user_type'] == $this->data['user_type']){
						if($this->data['user_type'] == 1){
							$currDate	=	date("Y-m-d H:i:s");
							$this->Team->bindModel(
								array(
									'belongsTo'=>array(
										'PurchaseGame'=>array(
											'className' => 'PurchaseGame',
											'foreignKey' => false,
											'conditions' => 'PurchaseGame.game_id = Team.game_id'
										),
										'Game'=>array(
											'className' => 'Game',
											'foreignKey' => 'game_id',
										)
									 
									)
								)
							); 
							//$condition		=	array('Team.user_id'=>$chkUser['User']['id'],'game_status !=' => 0);
							$condition		=	array('Team.user_id'=>$chkUser['User']['id'],'PurchaseGame.user_id'=>$chkUser['User']['id'],"(Game.date_time) >"=>$currDate,"game_status !="=>2);
							$getGameDetails	=	$this->Team->find("all",array('conditions'=>$condition));
							
							
							
						}else{
							$condition  	=	array('user_id'=>$chkUser['User']['id'],'game_status'=> 2);
							$getGameDetails	=	$this->Game->find("all",array('conditions'=>$condition));
							$getOrgCancelGames	=	$this->Game->find("all",array('conditions'=>array('user_id'=>$chkUser['User']['id'],'cancel_status'=> 1)));
						}
						
						$result['Upcoming'] =	array();
						$result['Completed']=	array();
						if($getGameDetails){
							foreach($getGameDetails as $getGameDetail){
								$joinedPlayer = $this->Team->find('count',array('conditions'=>array('game_id'=>$getGameDetail['Game']['id'])));
								$getGameDetail['Game']['join_player']	=	(string)$joinedPlayer;
								$getGameDetail['Game']['image'] = SITE_PATH."img/game/".$getGameDetail['Game']['image'];
								if($this->data['user_type'] == 1){
									$getGameDetail['Game']['team_id']	=	$getGameDetail['Team']['team_id'];
								}else{
									$getTeamtype = $this->Team->find('first',array('conditions'=>array('game_id'=>$getGameDetail['Game']['id']),"fields"=>array('team_id')));
									$getGameDetail['Game']['team_id']	=	(!empty($getTeamtype)?$getTeamtype['Team']['team_id']:"");
								}
								$getVenueAddress = $this->Venue->find('first',array('conditions'=>array('id'=>$getGameDetail['Game']['venue_id']),'fields'=>array('address')));
								$getGameDetail['Game']['address']	=	(!empty($getVenueAddress)?$getVenueAddress['Venue']['address']:"");
								$getSportName = $this->Sport->find('first',array('conditions'=>array('id'=>$getGameDetail['Game']['sports_id']),'fields'=>array('name','spanish_name')));
								$getGameDetail['Game']['sport_name']	=	(($chkUser['User']['lang_type'] == 2) ?$getSportName['Sport']['spanish_name']:$getSportName['Sport']['name']);
								//$getGameDetail['Game']['sport_name']	=	$getSportName['Sport']['name'];
								$result['Upcoming'][] 	=	$getGameDetail['Game'];
							}
						}
						if($this->data['user_type'] == 1){
							$this->Team->bindModel(
								array(
									'belongsTo'=>array(
										'PurchaseGame'=>array(
											'className' => 'PurchaseGame',
											'foreignKey' => false,
											'conditions' => 'PurchaseGame.game_id = Team.game_id'
										),
										'Game'=>array(
											'className' => 'Game',
											'foreignKey' => 'game_id',
										)
									 
									)
								)
							); 
							$getPlayerCompleteGames = $this->Team->find("all",array('conditions'=>array('Team.user_id'=>$chkUser['User']['id'],"Game.game_status"=>2)));
							if($getPlayerCompleteGames){
								foreach($getPlayerCompleteGames as $getGameDetail){
									$joinedPlayer = $this->Team->find('count',array('conditions'=>array('game_id'=>$getGameDetail['Game']['id'])));
									$getGameDetail['Game']['join_player']	=	(string)$joinedPlayer;
									$getGameDetail['Game']['image'] = SITE_PATH."img/game/".$getGameDetail['Game']['image'];
									if($this->data['user_type'] == 1){
										$getGameDetail['Game']['team_id']	=	$getGameDetail['Team']['team_id'];
									}else{
										$getTeamtype = $this->Team->find('first',array('conditions'=>array('game_id'=>$getGameDetail['Game']['id']),"fields"=>array('team_id')));
										$getGameDetail['Game']['team_id']	=	(!empty($getTeamtype)?$getTeamtype['Team']['team_id']:"");
									}
									$getVenueAddress = $this->Venue->find('first',array('conditions'=>array('id'=>$getGameDetail['Game']['venue_id']),'fields'=>array('address')));
									$getGameDetail['Game']['address']	=	(!empty($getVenueAddress)?$getVenueAddress['Venue']['address']:"");
									$getSportName = $this->Sport->find('first',array('conditions'=>array('id'=>$getGameDetail['Game']['sports_id']),'fields'=>array('name','spanish_name')));
									$getGameDetail['Game']['sport_name']	=	(($chkUser['User']['lang_type'] == 2) ?$getSportName['Sport']['spanish_name']:$getSportName['Sport']['name']);
									$result['Completed'][] 	=	$getGameDetail['Game'];
								}
							}
						
							$this->PlayerCancelGame->bindModel(
								array(
									'belongsTo'=>array(
										'Game'=>array(
										  'className' => 'Game',
										  'foreignKey' => 'game_id',
										)
									)
								)
							); 
							$getPlayerCancelGames = $this->PlayerCancelGame->find('all',array('conditions'=>array('player_id'=>$chkUser['User']['id'])));
							$result['Cancel'] =	array();
							if($getPlayerCancelGames){
								foreach($getPlayerCancelGames as $getPlayerCancelGame){
									$getVenueAddress = $this->Venue->find('first',array('conditions'=>array('id'=>$getPlayerCancelGame['Game']['venue_id']),'fields'=>array('address')));
									$getPlayerCancelGame['Game']['address']	=	(!empty($getVenueAddress)?$getVenueAddress['Venue']['address']:"");
									
									$getSportName = $this->Sport->find('first',array('conditions'=>array('id'=>$getPlayerCancelGame['Game']['sports_id']),'fields'=>array('name','spanish_name')));
									
									/* $getPlayerCancelGame['Game']['sport_name']	=	(!empty($getSportName)?$getSportName['Sport']['name']:''); */
									$getPlayerCancelGame['Game']['sport_name']	=	(!empty($getSportName)?(($chkUser['User']['lang_type'] == 2) ?$getSportName['Sport']['spanish_name']:$getSportName['Sport']['name']):'');
									$getTeam		=	$this->Team->find('first',array('conditions'=>array('user_id'=>$chkUser['User']['id'],"game_id"=>$getPlayerCancelGame['PlayerCancelGame']['game_id']),"fields"=>array('team_id')));
									
									$getPlayerCancelGame['Game']['team_id'] = (!empty($getTeam)?$getTeam['Team']['team_id']:'');
									$getPlayerCancelGame['Game']['cancel_date']	=	$getPlayerCancelGame['PlayerCancelGame']['created'];
									$result['Cancel'][] 	=	$getPlayerCancelGame['Game'];
								}
							}
						}else{
							$result['Cancel']	=	array();
							if($getOrgCancelGames){
								foreach($getOrgCancelGames as $getOrgCancelGame){
									
									$getVenueAddress = $this->Venue->find('first',array('conditions'=>array('id'=>$getOrgCancelGame['Game']['venue_id']),'fields'=>array('address')));
									$getOrgCancelGame['Game']['address']		=	(!empty($getVenueAddress)?$getVenueAddress['Venue']['address']:"");
									
									$getSportName = $this->Sport->find('first',array('conditions'=>array('id'=>$getOrgCancelGame['Game']['sports_id']),'fields'=>array('name','spanish_name')));
									$getOrgCancelGame['Game']['sport_name']	=	(!empty($getSportName)?(($chkUser['User']['lang_type'] == 2) ?$getSportName['Sport']['spanish_name']:$getSportName['Sport']['name']):'');
									
									$getOrgCancelGame['Game']['team_id']		=	"";
									$getOrgCancelGame['Game']['cancel_date']	=	$getOrgCancelGame['Game']['cancel_date'];
									$result['Cancel'][] 	=	$getOrgCancelGame['Game'];
								}
							}
							
						}
						$message			=	($chkUser['User']['lang_type']==2?"Éxito":"Success");
						$response_arry 		= 	array('status'=> 200, 'message'=>$message,'result'=>$result);
					}else{
						/* $message 			= 	($chkUser['User']['user_type'] == 1)? 'Player':'Organiser';
						$response_arry 		= 	array('status'=> 400, 'message'=>"You are not authorized as ".$message,'result'=>(object)[]); */
						$org		=	($chkUser['User']['lang_type'] == 2?"Organizador":"Organiser");
						$player		=	($chkUser['User']['lang_type'] == 2?"Jugador ":"Player");
						$msg 		= 	($chkUser['User']['user_type'] == 1)? $org:$player;
						$message 	= 	($chkUser['User']['lang_type'] == 2?"No estás autorizado como ":"You are not authorized as ");
						$response_arry 	= 	array('status'=> 400, 'message'=>$message." ".$msg,'result'=>(object)[]); 
					}
				}else{
					$response_arry = array('status'=> 404, 'message'=>"Invalid access key.");
				}
			}if(!empty($error)){
          		$response_arry = array('status'=> 400, 'message'=> $error);
        	}
		}else{
			$response_arry = array('status'=> 400, 'message'=>"nodata");
		}
		echo json_encode($response_arry);exit;
	}
	
	public function myGames_27Mar19(){
		$this->layout="None";
		$this->loadModel("Game");
		$this->loadModel("Team");
		$this->data = json_decode(file_get_contents('php://input'),true);
		if(isset($this->data) && !empty($this->data)){
			if(empty($this->data['access_key'])){
				$error	= 	"Please enter access key";
			}elseif(empty($this->data['user_type'])){
				$error	= 	"Please enter user type";
			}else{
				$chkUser = $this->Api->checkAccessKey($this->data['access_key']);
				if($chkUser){
					if($chkUser['User']['user_type'] == $this->data['user_type']){
						if($this->data['user_type'] == 1){
							
							$this->Team->bindModel(
								array(
									'belongsTo'=>array(
									 'Game'=>array(
									  'className' => 'Game',
									  'foreignKey' => 'game_id',
										)        
									)
								)
							); 
							$condition		=	array('Team.user_id'=>$chkUser['User']['id'],'game_status !=' => 0);
							$getGameDetails	=	$this->Team->find("all",array('conditions'=>$condition));
							
						}else{
							$condition  	=	array('user_id'=>$chkUser['User']['id'],'game_status'=> 2);
							$getGameDetails	=	$this->Game->find("all",array('conditions'=>$condition));
						}
						$result['Upcoming'] =	array();
						$result['Completed']=	array();
						if($getGameDetails){
							foreach($getGameDetails as $getGameDetail){
								if($getGameDetail['Game']['game_status'] == 1){
									$result['Upcoming'][] 	=	$getGameDetail['Game'];
								}else{
									$result['Completed'][] 	=	$getGameDetail['Game'];
								}
							}
						}
						$response_arry 		= 	array('status'=> 200, 'message'=>'Success','result'=>$result);
					}else{
						$message 			= 	($chkUser['User']['user_type'] == 1)? 'Player':'Organiser';
						$response_arry 		= 	array('status'=> 400, 'message'=>"You are not authorized as ".$message,'result'=>(object)[]);
					}
				}else{
					$response_arry = array('status'=> 404, 'message'=>"Invalid access key.");
				}
			}if(!empty($error)){
          		$response_arry = array('status'=> 400, 'message'=> $error);
        	}
		}else{
			$response_arry = array('status'=> 400, 'message'=>"nodata");
		}
		echo json_encode($response_arry);exit;
	}
	
	public function getRatingDetails(){
		$this->layout="None";
		$this->loadModel("Game");
		$this->loadModel("Team");
		$this->data = json_decode(file_get_contents('php://input'),true);
		if(isset($this->data) && !empty($this->data)){
			if(empty($this->data['access_key'])){
				$error	= 	"Please enter access key";
			}elseif(empty($this->data['user_type'])){//1=player,2=organiser
				$error	= 	"Please enter user type";
			}elseif(empty($this->data['game_id'])){
				$error	= 	"Please enter game id";
			}else{
				$chkUser = $this->Api->checkAccessKey($this->data['access_key']);
				if($chkUser){
					$chkGameId = $this->Game->find('first',array('conditions'=>array('id'=>$this->data['game_id']),'fields'=>array('id')));
					if($chkGameId){
						$data['id']				=	$this->data['game_id'];
						$data['game_status']	=	2;
						$this->Game->Save($data,false);//update game status as complete
						
						
						$this->Game->bindModel(
							array(
								'belongsTo'=>array(
									'Venue'=>array(
									  'className' => 'Venue',
									  'foreignKey' => 'venue_id',
									)
								)
							)
						); 
						$getVenueDetail		=	$this->Game->find('first',array("conditions"=>array('Game.id'=>$this->data['game_id']),"fields"=>array('Game.date_time','Game.image','Venue.name','Venue.address')));
						$res	=	array();	
						if($this->data['user_type'] == 2){//organiser
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
							$getPlayerLists = $this->Team->find('all',array("conditions"=>array('game_id'=>$this->data['game_id']),"fields"=>array('User.id','User.full_name','User.user_image','Team.team_id')));
							if($getPlayerLists){
								foreach($getPlayerLists as $getPlayerList){
									$getPlayerList['User']['user_image'] = SITE_PATH."img/user/".$getPlayerList['User']['user_image'];
									$getPlayerList['User']['team_id']	 =	$getPlayerList['Team']['team_id'];
									$res[]	=	$getPlayerList['User'];
								}
							}
						}
						$message		=	($chkUser['User']['lang_type']==2?"Éxito":"Success");
						$response_arry 	= 	array('status'=> 200, 'message'=>$message,'venue_name'=>$getVenueDetail['Venue']['name'],'venue_address'=>$getVenueDetail['Venue']['address'],'game_image'=>SITE_PATH."img/game/".$getVenueDetail['Game']['image'],'game_date'=>$getVenueDetail['Game']['date_time'],'result'=>$res);
					}else{
						$message		=	($chkUser['User']['lang_type']==2?"Juego no válido.":"Invalid Game.");
						$response_arry 	= array('status'=> 400, 'message'=>$message);
					}
				}else{
					$response_arry 		= array('status'=> 404, 'message'=>"Invalid access key.");
				}
			}if(!empty($error)){
          		$response_arry = array('status'=> 400, 'message'=> $error);
        	}
		}else{
			$response_arry = array('status'=> 400, 'message'=>"nodata");
		}
		echo json_encode($response_arry);exit;
	}
	
	public function rateOrganiserPlayer(){
		$this->layout="None";
		$this->loadModel("User");
		$this->loadModel("Game");
		$this->loadModel("UserRating");
		$this->data = json_decode(file_get_contents('php://input'),true);
		if(isset($this->data) && !empty($this->data)){
			if(empty($this->data['access_key'])){
				$error	= 	"Please enter access key";
			}elseif(empty($this->data['user_type'])){//1=player,2=organiser
				$error	= 	"Please enter user type";
			}elseif(empty($this->data['game_id'])){
				$error	= 	"Please enter game id";
			}elseif(empty($this->data['rating'])){
				$error	= 	"Please enter rating";
			}else{
				$chkUser = $this->Api->checkAccessKey($this->data['access_key']);
				
				if($chkUser){
					if($chkUser['User']['user_type'] == $this->data['user_type']){
						$chkGameId = $this->Game->find('first',array('conditions'=>array('id'=>$this->data['game_id']),'fields'=>array('id')));
						if($chkGameId){
							$data['user_id']		=	$chkUser['User']['id'];	
							$data['game_id']		=	$this->data['game_id'];
							$data['user_type']		=	$this->data['user_type'];
							foreach($this->data['rating'] as $rate){
								$data['player_id']	=	$rate['user_id'];
								$data['rating']		=	$rate['rating'];
								$this->UserRating->create();
								$this->UserRating->save($data,false);
							}
							$message				=	($chkUser['User']['lang_type']==2?"Éxito":"Success");
							$response_arry 			= 	array('status'=> 200, 'message'=>$message);
						}else{
							$message				=	($chkUser['User']['lang_type']==2?"Juego no válido.":"Invalid Game.");
							$response_arry 			= 	array('status'=> 400, 'message'=>$message);
						}
					}else{
						/* $message 			= 	($chkUser['User']['user_type'] == 1)? 'Player':'Organiser';
						$response_arry 		= 	array('status'=> 400, 'message'=>"You are not authorized as ".$message,'result'=>(object)[]); */
						
						$org			=	($chkUser['User']['lang_type'] == 2?"Organizador":"Organiser");
						$player			=	($chkUser['User']['lang_type'] == 2?"Jugador ":"Player");
						$msg 			= 	($chkUser['User']['user_type'] == 1)? $org:$player;
						$message 		= 	($chkUser['User']['lang_type'] == 2?"No estás autorizado como ":"You are not authorized as ");
						$response_arry 	= 	array('status'=> 400, 'message'=>$message." ".$msg,'result'=>(object)[]);
					}
				}else{
					$response_arry = array('status'=> 404, 'message'=>"Invalid access key.");
				}
			}if(!empty($error)){
          		$response_arry = array('status'=> 400, 'message'=> $error);
        	}
		}else{
			$response_arry = array('status'=> 400, 'message'=>"nodata");
		}
		echo json_encode($response_arry);exit;
	}
	
	public function rateOrganiserPlayer_30Mar19(){
		$this->layout="None";
		$this->loadModel("User");
		$this->loadModel("Game");
		$this->loadModel("UserRating");
		$this->data = json_decode(file_get_contents('php://input'),true);
		if(isset($this->data) && !empty($this->data)){
			if(empty($this->data['access_key'])){
				$error	= 	"Please enter access key";
			}elseif(empty($this->data['user_type'])){//1=player,2=organiser
				$error	= 	"Please enter user_type";
			}elseif(empty($this->data['game_id'])){
				$error	= 	"Please enter game id";
			}elseif(empty($this->data['rate_user'])){
				$error	= 	"Please enter rate user";
			}else{
				$chkUser = $this->Api->checkAccessKey($this->data['access_key']);
				if($chkUser){
					$chkGameId = $this->Game->find('first',array('conditions'=>array('id'=>$this->data['game_id']),'fields'=>array('id')));
					if($chkGameId){
						$data['user_id']		=	$chkUser['User']['id'];	
						$data['game_id']		=	$this->data['game_id'];
						foreach($this->data['rate_user'] as $rate){
							$data['player_id']		=	$rate['user_id'];
							$data['rating']			=	$rate['rating'];
							$this->UserRating->create();
							$this->UserRating->save($data,false);
						}
						$response_arry 							= 	array('status'=> 200, 'message'=>'Success');
					}else{
						$response_arry = array('status'=> 400, 'message'=>"Invalid Game.");
					}
				}else{
					$response_arry = array('status'=> 404, 'message'=>"Invalid access key.");
				}
			}if(!empty($error)){
          		$response_arry = array('status'=> 400, 'message'=> $error);
        	}
		}else{
			$response_arry = array('status'=> 400, 'message'=>"nodata");
		}
		echo json_encode($response_arry);exit;
	}
	
	public function addCardDetails(){
		$this->layout="None";
		$this->loadModel("CardDetail");
		$this->data = json_decode(file_get_contents('php://input'),true);
		if(isset($this->data) && !empty($this->data)){
			if(empty($this->data['access_key'])){
				$error	= 	"Please enter access key";
			}elseif(empty($this->data['card_holder_name'])){
				$error	= 	"Please enter card holder name";
			}elseif(empty($this->data['card_no'])){
				$error	= 	"Please enter card number";
			}elseif(empty($this->data['cvv_code'])){
				$error	= 	"Please enter cvv code";
			}elseif(empty($this->data['card_type'])){
				$error	= 	"Please enter card type";
			}elseif(empty($this->data['exp_date'])){
				$error	= 	"Please enter expiry date";
			}else{
				$chkUser = $this->Api->checkAccessKey($this->data['access_key']);
				if($chkUser){
					$data['user_id']			=	$chkUser['User']['id'];
					$data['card_holder_name']	=	$this->data['card_holder_name'];
					$data['card_no']			=	$this->data['card_no'];
					$data['cvv_code']			=	$this->data['cvv_code'];
					$data['card_type']			=	$this->data['card_type'];
					$data['exp_date']			=	$this->data['exp_date'];
					$this->CardDetail->save($data);
					$card_id 	= $this->CardDetail->id;
					$message	=	($chkUser['User']['lang_type']==2?"Los detalles de la tarjeta se han guardado correctamente.":"Card details has been saved successfully");
					$response_arry = array('status'=> 200, 'message'=>$message,"card_id"=>$card_id);
				}else{
					$response_arry = array('status'=> 404, 'message'=>"Invalid access key.");
				}
			}if(!empty($error)){
          		$response_arry = array('status'=> 400, 'message'=> $error);
        	}
		}else{
			$response_arry = array('status'=> 400, 'message'=>"nodata");
		}
		echo json_encode($response_arry);exit;
	}
	
	public function addBankDetails(){
		$this->layout="None";
		$this->loadModel("BankDetail");
		$this->data = json_decode(file_get_contents('php://input'),true);
		if(isset($this->data) && !empty($this->data)){
			if(empty($this->data['access_key'])){
				$error	= 	"Please enter access key";
			}elseif(empty($this->data['bank_name'])){
				$error	= 	"Please enter bank name";
			}/*elseif(empty($this->data['account_no'])){
				$error	= 	"Please enter account number";
			}*/elseif(empty($this->data['swift_code'])){
				$error	= 	"Please enter swift code";
			}elseif(empty($this->data['country'])){
				$error	= 	"Please enter country";
			}elseif(empty($this->data['account_holder_name'])){
				$error	= 	"Please enter account holder name";
			}else{
				$chkUser = $this->Api->checkAccessKey($this->data['access_key']);
				if($chkUser){
					$data['user_id']			=	$chkUser['User']['id'];
					$data['bank_name']			=	$this->data['bank_name'];
					if(isset($this->data['account_no']) && !empty($this->data['account_no'])){
						$data['account_no']			=	$this->data['account_no'];
					}else{
						$data['account_no']			=  "";
					}
					
					$data['iban']				=	$this->data['swift_code'];
					$data['country']			=	$this->data['country'];
					$data['account_holder_name']=	$this->data['account_holder_name'];
					if(isset($this->data['bank_id']) && !empty($this->data['bank_id'])){
						$data['id'] = $this->data['bank_id'];
					}
					$this->BankDetail->save($data,false);
					$message		=	($chkUser['User']['lang_type']==2?"Los datos bancarios se han guardado con éxito":"Bank details has been saved successfully");
					$response_arry  = array('status'=> 200, 'message'=>$message);
				}else{
					$response_arry = array('status'=> 404, 'message'=>"Invalid access key.");
				}
			}if(!empty($error)){
          		$response_arry = array('status'=> 400, 'message'=> $error);
        	}
		}else{
			$response_arry = array('status'=> 400, 'message'=>"nodata");
		}
		echo json_encode($response_arry);exit;
	}
	
	public function getBankDetails(){
		$this->layout="None";
		$this->loadModel("BankDetail");
		$this->data = json_decode(file_get_contents('php://input'),true);
		if(isset($this->data) && !empty($this->data)){
			if(empty($this->data['access_key'])){
				$error	= 	"Please enter access key";
			}else{
				$chkUser = $this->Api->checkAccessKey($this->data['access_key']);
				if($chkUser){
					$getBankDetails	=	$this->BankDetail->find('all',array('conditions'=>array('user_id'=>$chkUser['User']['id'])));
					$res	=	array();
					if($getBankDetails){
						foreach($getBankDetails as $getBankDetail){
							$res[] = $getBankDetail['BankDetail'];
						}
						$message		=	($chkUser['User']['lang_type']==2?"Éxito":"Success");
						$response_arry 	= 	array('status'=> 200, 'message'=>$message,"result"=>$res);
					}else{
						$message		=	($chkUser['User']['lang_type']==2?"No se encontraron detalles":"No details found");
						$response_arry 	= 	array('status'=> 400, 'message'=>$message);
					}
					
				}else{
					$response_arry = array('status'=> 404, 'message'=>"Invalid access key.");
				}
			}if(!empty($error)){
          		$response_arry = array('status'=> 400, 'message'=> $error);
        	}
		}else{
			$response_arry = array('status'=> 400, 'message'=>"nodata");
		}
		echo json_encode($response_arry);exit;
	}
	
	public function getCardDetails(){
		$this->layout="None";
		$this->loadModel("CardDetail");
		$this->data = json_decode(file_get_contents('php://input'),true);
		if(isset($this->data) && !empty($this->data)){
			if(empty($this->data['access_key'])){
				$error	= 	"Please enter access key";
			}else{
				$chkUser = $this->Api->checkAccessKey($this->data['access_key']);
				if($chkUser){
					$getCardDetails	=	$this->CardDetail->find('all',array('conditions'=>array('user_id'=>$chkUser['User']['id'])));
					$res	=	array();
					if($getCardDetails){
						foreach($getCardDetails as $getCardDetail){
							$res[] = $getCardDetail['CardDetail'];
						}
						$message		=	($chkUser['User']['lang_type']==2?"Éxito":"Success");
						$response_arry 	= 	array('status'=> 200, 'message'=>$message,"result"=>$res);
					}else{
						$message		=	($chkUser['User']['lang_type']==2?"No se encontraron detalles":"No details found");
						$response_arry 	= 	array('status'=> 400, 'message'=>$message);
					}
					
				}else{
					$response_arry = array('status'=> 404, 'message'=>"Invalid access key.");
				}
			}if(!empty($error)){
          		$response_arry = array('status'=> 400, 'message'=> $error);
        	}
		}else{
			$response_arry = array('status'=> 400, 'message'=>"nodata");
		}
		echo json_encode($response_arry);exit;
	}
	
	public function updateProfileImage(){
		$this->layout="None";
		$this->loadModel("User");
		if(isset($this->data) && !empty($this->data)){
			if(empty($this->data['access_key'])){
				$error	= 	"Please enter access key";
			}else{
				$chkUser = $this->Api->checkAccessKey($this->data['access_key']);
				if($chkUser){
					$allowed_types 					= 	unserialize(ALLOWED_IMAGE_TYPES);
					if (isset($_FILES['image']['name']) &&  !empty($_FILES['image']['name'])) {
						if($_FILES['image']['size'] !=0){
							//if (in_array($_FILES['image']['type'], $allowed_types)) {echo "if";
								$fileName = $this->Api->uploadFile('../webroot/img/user/', $_FILES['image']);
								$imgpath = "../webroot/img/user/".$fileName;
								if ($fileName != '')
									$res['User']['user_image'] = $fileName;
								/*} else {echo "else";
									$error = 'Please Upload only *.gif, *.jpg, *.png image only!';
									$response_arry = array('status'=> 400, 'message'=> $error);
									echo json_encode($response_arry);exit;
								} */
						}
						$path = "../webroot/img/user/".$chkUser['User']['user_image'];
						if (file_exists($path)){
							unlink($path);
						}
						$res['User']['id']	=	$chkUser['User']['id'];
						$this->User->save($res,false);
						$message		=	($chkUser['User']['lang_type']==2?"La imagen se ha actualizado con éxito.":"Image has been updated successfully.");
						$response_arry  = array('status'=> 200, 'message'=>$message);
					}
					
				}else{
					$response_arry = array('status'=> 404, 'message'=>"Invalid access key.");
				}
			}if(!empty($error)){
          		$response_arry = array('status'=> 400, 'message'=> $error);
        	}
		}else{
			$response_arry = array('status'=> 400, 'message'=>"nodata");
		}
		echo json_encode($response_arry);exit;
	}
	public function deleteVenue(){
		$this->layout="None";
		$this->loadModel("Venue");
		$this->loadModel("VenueImage");
		$this->loadModel("Game");
		$this->data = json_decode(file_get_contents('php://input'),true);
		if(isset($this->data) && !empty($this->data)){
			if(empty($this->data['access_key'])){
				$error	= 	"Please enter access key";
			}elseif(empty($this->data['venue_id'])){
				$error	= 	"Please enter venue id";
			}else{
				$chkUser = $this->Api->checkAccessKey($this->data['access_key']);
				if($chkUser){
					$chkVenueId = $this->Venue->find('first',array('conditions'=>array('id'=>$this->data['venue_id']),'fields'=>array('id')));
					if($chkVenueId){
						$chkGame = $this->Game->find('count',array('conditions'=>array('venue_id'=>$this->data['venue_id'])));
						if($chkGame == 0){
							$chkVenueImages = $this->VenueImage->find('all',array('conditions'=>array('venue_id'=>$this->data['venue_id']),"fields"=>array('id','image')));
								if($chkVenueImages){
									foreach($chkVenueImages as $chkVenueImage){
										$path 			= 	"../webroot/img/venue/".$chkVenueImage['VenueImage']['image'];
										if (file_exists($path)){
											unlink($path);
										} 
										
										$this->VenueImage->delete($chkVenueImage['VenueImage']['id']);
									}
								}
								$this->Venue->delete($chkVenueId['Venue']['id']);
								$message		=	($chkUser['User']['lang_type']==2?"El lugar se ha eliminado con éxito":"Venue has been deleted successfully");
								$response_arry 	= 	array('status'=> 200, 'message'=>$message);
								
						}else{
							$message	=	($chkUser['User']['lang_type']==2?"El lugar no se puede eliminar. Ya está en uso.":"Venue can not be deleted.Its already in use");
							$response_arry 		= 	array('status'=> 400, 'message'=>$message);
						}
					}else{
						$message	=	($chkUser['User']['lang_type']==2?"ID de lugar no válido.":"Invalid Venue id.");
						$response_arry = array('status'=> 400, 'message'=>"Invalid Venue id.");
					}
				}else{
					$response_arry = array('status'=> 404, 'message'=>"Invalid access key.");
				}
			}if(!empty($error)){
          		$response_arry = array('status'=> 400, 'message'=> $error);
        	}
		}else{
			$response_arry = array('status'=> 400, 'message'=>"nodata");
		}
		echo json_encode($response_arry);exit;
	}
	
	public function getNotificationHistory(){
		$this->layout="None";
		$this->loadModel('Notification');
		$this->data = json_decode(file_get_contents('php://input'),true);
		if(isset($this->data) && !empty($this->data)){
			if(empty($this->data['access_key'])){
				$error	= 	"Please enter access key";
			}else{
				$chkUser = $this->Api->checkAccessKey($this->data['access_key']);
				if($chkUser){
					$getDatas = $this->Notification->find("all",array('conditions'=>array('receiver_id'=>$chkUser['User']['id']),'order'=>array('id'=>'desc')));
					$res	=	array();
					if($getDatas){
						foreach($getDatas as $getData){
							if($getData['Notification']['sender_id'] !=0){
								$getSenderName = $this->Api->getUserById($getData['Notification']['sender_id']);
								$getData['Notification']['sender_name']	=	$getSenderName['User']['full_name'];
							}else{
								$getData['Notification']['sender_name']	= 	"Odyfo";
							}
							
							$res[]	=	$getData['Notification'];
						}
					}
					$message	=	($chkUser['User']['lang_type']==2?"Éxito":"Success");
					$response_arry = array('status'=> 200, 'message'=>$message,"result"=>str_replace("null","''",$res));
				}else{
					$response_arry = array('status'=> 404, 'message'=>"Invalid access key.");
				}
			}if(!empty($error)){
          		$response_arry = array('status'=> 400, 'message'=> $error);
        	}
		}else{
			$response_arry = array('status'=> 400, 'message'=>"nodata");
		}
		echo json_encode($response_arry);exit;
	}
	
	public function stripeCharge(){
		$this->layout="None";
		$this->loadModel('Payment');
		$this->loadModel('Game');
		$this->data = json_decode(file_get_contents('php://input'),true);
		if(isset($this->data) && !empty($this->data)){
			if(empty($this->data['access_key'])){
				$error	= 	"Please enter access key";
			}elseif(empty($this->data['amount'])){
				$error	= 	"Please enter amount";
			}elseif(empty($this->data['game_id'])){
				$error	= 	"Please enter game id";
			}elseif(empty($this->data['token'])){
				$error	= 	"Please enter token";
			}elseif(empty($this->data['description'])){
				$error	= 	"Please enter description";
			}elseif(empty($this->data['user_type'])){
				$error	= 	"Please enter user_type";
			}else{
				$chkUser = $this->Api->checkAccessKey($this->data['access_key']);
				if($chkUser){
					if($this->data['user_type'] == $chkUser['User']['user_type']){
						$chkGameId = $this->Game->find('first',array('conditions'=>array('id'=>$this->data['game_id']),'fields'=>array('id')));
						if($chkGameId){
							\Stripe\Stripe::setApiKey(STRIPE_SECRET_KEY);
							try{
								/* $refund = \Stripe\Refund::create([
								  "charge" => "ch_1ELmF8B6XWFbG6qYjZs2qgXi"
								]); */
								$charge = \Stripe\Charge::create(array(
								  "amount" 		=> $this->data['amount'], // amount in cents, again
								  "currency" 	=> "EUR",
								  "source" 		=> $this->data['token'],
								  "description" => $this->data['description'])
								);
								$success = 1;
							}catch(Stripe_CardError $e) {
								$success	=	0;
								$error = $e->getMessage();
							} catch (Stripe_InvalidRequestError $e) {
							  // Invalid parameters were supplied to Stripe's API
							  $success	=	0;
							  $error = $e->getMessage();
							} catch (Stripe_AuthenticationError $e) {
							  // Authentication with Stripe's API failed
							  $success	=	0;
							  $error = $e->getMessage();
							} catch (Stripe_ApiConnectionError $e) {
							  // Network communication with Stripe failed
							  $success	=	0;
							  $error = $e->getMessage();
							} catch (Stripe_Error $e) {
							  // Display a very generic error to the user, and maybe send
							  // yourself an email
							  $success	=	0;
							  $error = $e->getMessage();
							}catch (Exception $e) {
							  // Something else happened, completely unrelated to Stripe
							  $success	=	0;
							  $error = $e->getMessage();
							}
							if ($success!=1){
								$response_arry = array('status'=> 400, 'message'=> $error);
							}else{
								$data['user_id']		=	$chkUser['User']['id'];
								$data['game_id']		=	$this->data['game_id'];
								$data['amount']			=	$this->data['amount'];
								$data['token']			=	$this->data['token'];
								$data['description']	=	$this->data['description'];
								$data['charge_id']		=	$charge->id;
								$data['transaction_id']	=	$charge->balance_transaction;
								$data['status']			=	1;
								$this->Payment->save($data);
								
								$this->loadModel('PurchaseGame');
								$datas['game_id']		=	$this->data['game_id'];	
								$datas['user_id']		=	$chkUser['User']['id'];
								$this->PurchaseGame->save($datas,false);
								$message		=	($chkUser['User']['lang_type']==2?"Éxito":"Success");
								$response_arry 	= 	array('status'=> 200, 'message'=> $message,'result'=>array('charge_id'=>$data['charge_id'],'transaction_id'=>$data['transaction_id']));
							}
						}else{
							$message		=	($chkUser['User']['lang_type']==2?"ID de juego no válido":"Invalid game id");
							$response_arry  =   array('status'=> 400, 'message'=>$message);
						}
					}else{
						/* $message = ($chkUser['User']['user_type'] == 1)? 'Player':'Organiser';
						$response_arry = array('status'=> 400, 'message'=>"You are not authorized as ".$message,'result'=>(object)[]); */
						
						$org			=	($chkUser['User']['lang_type'] == 2?"Organizador":"Organiser");
						$player			=	($chkUser['User']['lang_type'] == 2?"Jugador ":"Player");
						$msg 			= 	($chkUser['User']['user_type'] == 1)? $org:$player;
						$message 		= 	($chkUser['User']['lang_type'] == 2?"No estás autorizado como ":"You are not authorized as ");
						$response_arry 	= 	array('status'=> 400, 'message'=>$message." ".$msg,'result'=>(object)[]);
					}
				}else{
					$response_arry = array('status'=> 404, 'message'=>"Invalid access key.");
				}
			}if(!empty($error)){
          		$response_arry = array('status'=> 400, 'message'=> $error);
        	}
		}else{
			$response_arry = array('status'=> 400, 'message'=>"nodata");
		}
		echo json_encode($response_arry);exit;
	}
	
	public function cancelTransaction(){
		$this->layout="None";
		$this->loadModel('Payment');
		$this->loadModel('Game');
		$this->loadModel('Team');
		$this->loadModel('PurchaseGame');
		$this->data = json_decode(file_get_contents('php://input'),true);
		if(isset($this->data) && !empty($this->data)){
			if(empty($this->data['access_key'])){
				$error	= 	"Please enter access key";
			}elseif(empty($this->data['game_id'])){
				$error	= 	"Please enter game id";
			}/* elseif(empty($this->data['charge_id'])){
				$error	= 	"Please enter charge id";
			} */elseif(empty($this->data['user_type'])){
				$error	= 	"Please enter user_type";
			}else{
				$chkUser = $this->Api->checkAccessKey($this->data['access_key']);
				if($chkUser){
					if($this->data['user_type'] == $chkUser['User']['user_type']){
						$chkGameId = $this->Game->find('first',array('conditions'=>array('id'=>$this->data['game_id']),'fields'=>array('id','date_time')));
						if($chkGameId){
							/* $chkPaymentMode 	= 	$this->Payment->find('first',array('conditions'=>array('user_id'=>$chkUser['User']['id'],'game_id'=>$this->data['game_id'],'charge_id !='=>'')));
							if($chkPaymentMode){ */
								$currentDateTime 	=	(date('Y-m-d H:i:s'));
								$gameTime 			= 	 date('Y-m-d H:i:s', strtotime('-1 day', strtotime($chkGameId['Game']['date_time'])));
								
								$payment_mode = $this->PurchaseGame->find('first',array('conditions'=>array('user_id'=>$chkUser['User']['id'],'game_id'=>$this->data['game_id'])));
								
								if($payment_mode['PurchaseGame']['pay_mode'] == 0){//card
								
									$chkPaymentMode 	= 	$this->Payment->find('first',array('conditions'=>array('user_id'=>$chkUser['User']['id'],'game_id'=>$this->data['game_id']),"order"=>array("id desc")));
									if($chkPaymentMode){
										if($gameTime >= $currentDateTime){
											/* $chkChargeId 	= 	$this->Payment->find('first',array('conditions'=>array('user_id'=>$chkUser['User']['id'],'charge_id'=>$this->data['charge_id'])));
											if($chkChargeId){ */
												
												\Stripe\Stripe::setApiKey(STRIPE_SECRET_KEY);
												try{
													/* $refund = \Stripe\Refund::create([
													  "charge" => $this->data['charge_id']
													]); */
													 $refund = \Stripe\Refund::create([
													  "charge" => $chkPaymentMode['Payment']['charge_id']
													]);
													$success = 1;
												}catch(Stripe_CardError $e) {
													$success	=	0;
													$error = $e->getMessage();
												} catch (Stripe_InvalidRequestError $e) {
												  // Invalid parameters were supplied to Stripe's API
												  $success	=	0;
												  $error = $e->getMessage();
												} catch (Stripe_AuthenticationError $e) {
												  // Authentication with Stripe's API failed
												  $success	=	0;
												  $error = $e->getMessage();
												} catch (Stripe_ApiConnectionError $e) {
												  // Network communication with Stripe failed
												  $success	=	0;
												  $error = $e->getMessage();
												} catch (Stripe_Error $e) {
												  // Display a very generic error to the user, and maybe send
												  // yourself an email
												  $success	=	0;
												  $error = $e->getMessage();
												}catch (Exception $e) {
												  // Something else happened, completely unrelated to Stripe
												  $success	=	0;
												  $error = $e->getMessage();
												}
												if ($success!=1){
													$response_arry = array('status'=> 400, 'message'=> $error);
												}else{
													$data['user_id']		=	$chkUser['User']['id'];
													$data['game_id']		=	$this->data['game_id'];
													$data['amount']			=	$chkPaymentMode['Payment']['amount'];
													$data['token']			=	$chkPaymentMode['Payment']['token'];
													$data['description']	=	$chkPaymentMode['Payment']['description'];
													$data['charge_id']		=	$this->data['charge_id'];
													$data['transaction_id']	=	$chkPaymentMode['Payment']['transaction_id'];
													$data['status']			=	0;
													$this->Payment->save($data);
													$this->Team->deleteAll(array('user_id'=>$chkUser['User']['id'],'game_id'=>$this->data['game_id']));
													
													$this->loadModel('PlayerCancelGame');
													$re['game_id']		=	$this->data['game_id'];
													$re['player_id']	=	$chkUser['User']['id'];
													$this->PlayerCancelGame->save($re);
													
													$this->PurchaseGame->deleteAll(array('user_id'=>$chkUser['User']['id'],'game_id'=>$this->data['game_id']));
													$message	   =	($chkUser['User']['lang_type']==2?"Éxito":"Success");
													$response_arry = array('status'=> 200, 'message'=> $message);
												}
										}else{
											$this->Team->deleteAll(array('user_id'=>$chkUser['User']['id'],'game_id'=>$this->data['game_id']));
											$message	   =	($chkUser['User']['lang_type']==2?"Éxito":"Success");
											$response_arry = array('status'=> 200, 'message'=> $message);
											/* $response_arry = array('status'=> 400, 'message'=>"You can not cancel this game because time is exceed"); */
										}
									}else{
										$message	   =	($chkUser['User']['lang_type']==2?"Pago inválido":"Invalid payment");
										$response_arry = array('status'=> 400, 'message'=>$message);
									}
								}else{
									$this->loadModel('PlayerCancelGame');
									$re['game_id']		=	$this->data['game_id'];
									$re['player_id']	=	$chkUser['User']['id'];
									$this->PlayerCancelGame->save($re);
									
									$this->PurchaseGame->deleteAll(array('user_id'=>$chkUser['User']['id'],'game_id'=>$this->data['game_id']));
									$message	   =	($chkUser['User']['lang_type']==2?"Éxito":"Success");
									$this->Team->deleteAll(array('user_id'=>$chkUser['User']['id'],'game_id'=>$this->data['game_id']));
									$response_arry = array('status'=> 200, 'message'=> $message);
									/* if($gameTime >= $currentDateTime){
										$this->Team->deleteAll(array('user_id'=>$chkUser['User']['id'],'game_id'=>$this->data['game_id']));
										$response_arry = array('status'=> 200, 'message'=> 'success');
									}else{
										$this->Team->deleteAll(array('user_id'=>$chkUser['User']['id'],'game_id'=>$this->data['game_id']));
										$response_arry = array('status'=> 400, 'message'=>"You can not cancel this game because time is exceed");
									} */
								}
							/* }else{
									$response_arry = array('status'=> 400, 'message'=>"Invalid payment");
							} */
						}else{
							$message	   =	($chkUser['User']['lang_type']==2?"ID de juego no válido":"Invalid game id");
							$response_arry = array('status'=> 400, 'message'=>$message);
						}
					}else{
						/* $message = ($chkUser['User']['user_type'] == 1)? 'Player':'Organiser';
						$response_arry = array('status'=> 400, 'message'=>"You are not authorized as ".$message,'result'=>(object)[]); */
						
						$org			=	($chkUser['User']['lang_type'] == 2?"Organizador":"Organiser");
						$player			=	($chkUser['User']['lang_type'] == 2?"Jugador ":"Player");
						$msg 			= 	($chkUser['User']['user_type'] == 1)? $org:$player;
						$message 		= 	($chkUser['User']['lang_type'] == 2?"No estás autorizado como ":"You are not authorized as ");
						$response_arry 	= 	array('status'=> 400, 'message'=>$message." ".$msg,'result'=>(object)[]);
					}
				}else{
					$response_arry = array('status'=> 404, 'message'=>"Invalid access key.");
				}
			}if(!empty($error)){
          		$response_arry = array('status'=> 400, 'message'=> $error);
        	}
		}else{
			$response_arry = array('status'=> 400, 'message'=>"nodata");
		}
		echo json_encode($response_arry);exit;
	}
	
	public function playerAvailability(){
		$this->layout="None";
		$this->loadModel("PlayerAvailability");
		$this->loadModel("Game");
		$this->loadModel("User");
		$this->data = json_decode(file_get_contents('php://input'),true);
		if(isset($this->data) && !empty($this->data)){
			if(empty($this->data['access_key'])){
				$error	= 	"Please enter access key";
			}elseif(empty($this->data['game_id'])){
				$error	= 	"Please enter game id";
			}elseif(empty($this->data['player_id'])){
				$error	= 	"Please enter player id";
			}elseif(empty($this->data['status'])){//1=available,2=not
				$error	= 	"Please enter status";
			}else{
				$chkUser = $this->Api->checkAccessKey($this->data['access_key']);
				if($chkUser){
					$chkPlayerId = $this->User->find('first',array('conditions'=>array('id'=>$this->data['player_id']),'fields'=>array('id')));
					if($chkPlayerId){
						$chkGame = $this->Game->find('count',array('conditions'=>array('id'=>$this->data['game_id'])));
						if($chkGame > 0){
							$data['user_id']	=	$chkUser['User']['id'];
							$data['game_id']	=	$this->data['game_id'];
							$data['player_id']	=	$this->data['player_id'];
							$data['status']		=	$this->data['status'];
							$this->PlayerAvailability->save($data);
							$message	=	($chkUser['User']['lang_type']==2?"Los datos se han guardado con éxito.":"Data has been saved successfully");

							$response_arry 		= 	array('status'=> 200, 'message'=>$message);
						}else{
							$message			=	($chkUser['User']['lang_type']==2?"ID de juego no válido":"Invalid game id");
							$response_arry 		= 	array('status'=> 400, 'message'=>$message);
						}
					}else{
						$message			=	($chkUser['User']['lang_type']==2?"ID de jugador no válida.":"Invalid game id");
						$response_arry = array('status'=> 400, 'message'=>$message);
					}
				}else{
					$response_arry = array('status'=> 404, 'message'=>"Invalid access key.");
				}
			}if(!empty($error)){
          		$response_arry = array('status'=> 400, 'message'=> $error);
        	}
		}else{
			$response_arry = array('status'=> 400, 'message'=>"nodata");
		}
		echo json_encode($response_arry);exit;
	}
	
	public function gameConductStatus(){//wheather org want to play game or not
		$this->layout="None";
		$this->loadModel("Game");
		$this->data = json_decode(file_get_contents('php://input'),true);
		if(isset($this->data) && !empty($this->data)){
			if(empty($this->data['access_key'])){
				$error	= 	"Please enter access key";
			}elseif(empty($this->data['game_id'])){
				$error	= 	"Please enter game id";
			}elseif(empty($this->data['status'])){//1=start,2=stop
				$error	= 	"Please enter status";
			}else{
				$chkUser = $this->Api->checkAccessKey($this->data['access_key']);
				if($chkUser){
					$chkGame = $this->Game->find('first',array('conditions'=>array('id'=>$this->data['game_id'],'user_id'=>$chkUser['User']['id']),'fields'=>array('id')));
					if($chkGame){
						$data['id']					=	$chkGame['Game']['id'];
						$data['conduct_status']		=	$this->data['status'];
						$this->Game->save($data,false);
						$message			=	($chkUser['User']['lang_type']==2?"Los datos se han guardado con éxito.":"Data has been saved successfully");
						$response_arry 		= 	array('status'=> 200, 'message'=>$message);
					}else{
						$message			=	($chkUser['User']['lang_type']==2?"Detalle de juego inválido":"Invalid game detail");
						$response_arry 		= 	array('status'=> 400, 'message'=>$message);
					}
				}else{
					$response_arry = array('status'=> 404, 'message'=>"Invalid access key.");
				}
			}if(!empty($error)){
          		$response_arry = array('status'=> 400, 'message'=> $error);
        	}
		}else{
			$response_arry = array('status'=> 400, 'message'=>"nodata");
		}
		echo json_encode($response_arry);exit;
	}
	
	
	
	public function playerCashPayment(){
		$this->layout="None";
		$this->loadModel("Game");
		$this->loadModel("Payment");
		$this->loadModel("PurchaseGame");
		$this->data = json_decode(file_get_contents('php://input'),true);
		if(isset($this->data) && !empty($this->data)){
			if(empty($this->data['access_key'])){
				$error	= 	"Please enter access key";
			}elseif(empty($this->data['game_id'])){
				$error	= 	"Please enter game id";
			}elseif(empty($this->data['amount'])){
				$error	= 	"Please enter amount";
			}else{
				$chkUser = $this->Api->checkAccessKey($this->data['access_key']);
				if($chkUser){
					$chkGame = $this->Game->find('first',array('conditions'=>array('id'=>$this->data['game_id']),'fields'=>array('id')));
					if($chkGame){
						$data['user_id']	=	$chkUser['User']['id'];
						$data['game_id']	=	$this->data['game_id'];
						$data['amount']		=	$this->data['amount'];
						$data['pay_mode']	=	1;
						$data['status']		=	1;
						$this->Payment->save($data,false);
						
						$datas['user_id']	=	$chkUser['User']['id'];
						$datas['game_id']	=	$this->data['game_id'];
						$datas['pay_mode']	=	1;
						$this->PurchaseGame->save($datas,false);
						$message	=	($chkUser['User']['lang_type']==2?"La reserva ha sido completada":"Booking has been completed");
						$response_arry 		= 	array('status'=> 200, 'message'=>$message);
					}else{
						$message	=	($chkUser['User']['lang_type']==2?"ID de juego no válido":"Invalid game id");
						$response_arry 		= 	array('status'=> 400, 'message'=>$message);
					}
				}else{
					$response_arry = array('status'=> 404, 'message'=>"Invalid access key.");
				}
			}if(!empty($error)){
          		$response_arry = array('status'=> 400, 'message'=> $error);
        	}
		}else{
			$response_arry = array('status'=> 400, 'message'=>"nodata");
		}
		echo json_encode($response_arry);exit;
	}
	
	
	public function resetBadge(){
		$this->layout="None";
		$this->loadModel("User");
		$this->data = json_decode(file_get_contents('php://input'),true);
		if(isset($this->data) && !empty($this->data)){
			if(empty($this->data['access_key'])){
				$error	= 	"Please enter access key";
			}else{
				$chkUser = $this->Api->checkAccessKey($this->data['access_key']);
				if($chkUser){
					$data['id']		=	$chkUser['User']['id'];
					$data['badge']	=	0;
					$this->User->save($data,false);
					$message	=	($chkUser['User']['lang_type']==2?"Éxito":"Success");
					$response_arry 		= 	array('status'=> 200, 'message'=>$message);
				}else{
					$response_arry = array('status'=> 404, 'message'=>"Invalid access key.");
				}
			}if(!empty($error)){
          		$response_arry = array('status'=> 400, 'message'=> $error);
        	}
		}else{
			$response_arry = array('status'=> 400, 'message'=>"nodata");
		}
		echo json_encode($response_arry);exit;
	}
	
	public function getPages(){
		$this->layout="None";
		$this->loadModel("StaticPage");
		$this->data = json_decode(file_get_contents('php://input'),true);
		if(isset($this->data) && !empty($this->data)){
			if(empty($this->data['access_key'])){
				$error	= 	"Please enter access key";
			}elseif(empty($this->data['title'])){
				$error	= 	"Please enter title";
			}if(empty($this->data['language'])){//1=english,2=spanish,3=itallian,4=german,5=french
				$error	= 	"Please enter language";
			}else{
				$chkUser = $this->Api->checkAccessKey($this->data['access_key']);
				if($chkUser){
					$getResult = $this->StaticPage->find("first",array("conditions"=>array('title'=>$this->data['title'],'language'=>$this->data['language']))); 
					$res = array();
					if($getResult){
						$res = $getResult['StaticPage'];
					}
					$message			=	($chkUser['User']['lang_type']==2?"Éxito":"Success");
					$response_arry 		= 	array('status'=> 200, 'message'=>$message,'result'=>$res);
				}else{
					$response_arry = array('status'=> 404, 'message'=>"Invalid access key.");
				}
			}if(!empty($error)){
          		$response_arry = array('status'=> 400, 'message'=> $error);
        	}
		}else{
			$response_arry = array('status'=> 400, 'message'=>"nodata");
		}
		echo json_encode($response_arry);exit;
	}
	
	
	public function getPagesTerms(){
		$this->layout="None";
		$this->loadModel("StaticPage");
		$this->data = json_decode(file_get_contents('php://input'),true);
		
		if(isset($this->data) && !empty($this->data)){
			if(empty($this->data['title'])){
				$error	= 	"Please enter title";
			}if(empty($this->data['language'])){//1=english,2=spanish,3=itallian,4=german,5=french
				$error	= 	"Please enter language";
			}else{
				
				
					$getResult = $this->StaticPage->find("first",array("conditions"=>array('title'=>$this->data['title'],'language'=>$this->data['language']))); 
					$res = array();
					if($getResult){
						$res = $getResult['StaticPage'];
					}
					$message			=	($chkUser['User']['lang_type']==2?"Éxito":"Success");
					$response_arry 		= 	array('status'=> 200, 'message'=>$message,'result'=>$res);
				
			}if(!empty($error)){
          		$response_arry = array('status'=> 400, 'message'=> $error);
        	}
		}else{
			$response_arry = array('status'=> 400, 'message'=>"nodata");
		}
		echo json_encode($response_arry);exit;
		
	}
	
	
	public function updateLanguage(){
		$this->layout="None";
		$this->loadModel("User");
		$this->data = json_decode(file_get_contents('php://input'),true);
		if(isset($this->data) && !empty($this->data)){
			if(empty($this->data['access_key'])){
				$error	= 	"Please enter access key";
			}elseif(empty($this->data['lang_type'])){
				$error	= 	"Please enter lang type";
			}else{
				$chkUser = $this->Api->checkAccessKey($this->data['access_key']);
				if($chkUser){
					$data['User']['id']			=	$chkUser['User']['id'];
					$data['User']['lang_type']	=	$this->data['lang_type'];
					$this->User->save($data,false);
					$message			=	($this->data['lang_type']==2?"Éxito":"Success");
					$response_arry 		= 	array('status'=> 200, 'message'=>$message,'lang_type'=>$this->data['lang_type']);
				}else{
					$response_arry = array('status'=> 404, 'message'=>"Invalid access key.");
				}
			}if(!empty($error)){
          		$response_arry = array('status'=> 400, 'message'=> $error);
        	}
		}else{
			$response_arry = array('status'=> 400, 'message'=>"nodata");
		}
		echo json_encode($response_arry);exit;
	}
	
	public function removePlayerTeam(){
		$this->layout="None";
		$this->loadModel("Team");
		$this->data = json_decode(file_get_contents('php://input'),true);
		if(isset($this->data) && !empty($this->data)){
			if(empty($this->data['access_key'])){
				$error	= 	"Please enter access key";
			}elseif(empty($this->data['game_id'])){
				$error	= 	"Please enter game id";
			}else{
				$chkUser = $this->Api->checkAccessKey($this->data['access_key']);
				if($chkUser){
					$chkTeam = $this->Team->find("first",array("conditions"=>array("game_id"=>$this->data['game_id'],"user_id"=>$chkUser['User']['id'])));
					if($chkTeam){
						$this->Team->delete($chkTeam['Team']['id']);
						$message			=	($chkUser['User']['lang_type']==2?"Éxito":"Success");
						$response_arry 		= 	array('status'=> 200, 'message'=>$message);
					}else{
						$message			=	($chkUser['User']['lang_type']==2?"Equipo no encontrado":"Team not found");
						$response_arry 		= 	array('status'=> 400, 'message'=>$message);
					}
				}else{
					$response_arry = array('status'=> 404, 'message'=>"Invalid access key.");
				}
			}if(!empty($error)){
          		$response_arry = array('status'=> 400, 'message'=> $error);
        	}
		}else{
			$response_arry = array('status'=> 400, 'message'=>"nodata");
		}
		echo json_encode($response_arry);exit;
	}
	
	
	public function organiserPayment(){//11:30pm start crone
		$this->layout="None";
		$this->loadModel("PlayerAvailability");
		$this->loadModel("Game");
		
		/* $this->loadModel("EmailTemplate");
		$from 	   		= 	ADMIN_EMAIL;
		$to 	   		= 	"deepak.bansal@techugo.com";
		$mail_slug 		= 	"registration";
		$replace_var 	= 	array("name"=>'OrganiserPayment');
		$sendMail  		=	$this->sendEmail($mail_slug,$replace_var,$from,$to);
		 */
		
		
		
		$currDate 	= 	date('Y-m-d');
		$this->Game->bindModel(
				array(
					'belongsTo'=>array(
						'BankDetail'=>array(
						  'className' => 'BankDetail',
						  'foreignKey' => false,
						  'conditions' => 'Game.user_id = BankDetail.user_id',
						),	
						'CardDetail'=>array(
						  'className' => 'CardDetail',
						  'foreignKey' => false,
						  'conditions' => 'Game.user_id = CardDetail.user_id',
						)
					)
				)
		); 
		$getGameDetails = $this->Game->find("all",array('conditions'=>array('date(date_time)'=>$currDate,'status'=>1,'game_status'=>2,'conduct_status'=>1,'noti_status'=>0),'fields'=>array('Game.user_id','Game.id','Game.price','Game.card_id','BankDetail.*','CardDetail.*')));
		if($getGameDetails){
			foreach($getGameDetails as $getGameDetail){
				$totalPlayer 		= 	json_decode($this->countGamePlayer($getGameDetail['Game']['id']));
				$cardPlayer			=	$totalPlayer[0]->total;
				$Cardamount			=	(($getGameDetail['Game']['price'] * $cardPlayer)*100);
				$orgAmount			=	($Cardamount * 0.80);//80% to organiser
				$odyfoAmount		=	($Cardamount * 0.20);//20% to odyfo
				
				\Stripe\Stripe::setApiKey(STRIPE_SECRET_KEY);
				$account 	= 	\Stripe\Account::create([
				  "type" 	=> 	"custom",
				  "country" =>  $getGameDetail['BankDetail']['country'],
				]);	
				$bank_details = \Stripe\Token::create([
				  "bank_account" => [
						"country" 				=> 	$getGameDetail['BankDetail']['country'],
						"currency" 				=> 	"EUR",
						"account_holder_name" 	=> 	$getGameDetail['BankDetail']['account_holder_name'],
						"account_holder_type" 	=> 	"individual",
						//"account_number" 		=> 	$getGameDetail['BankDetail']['account_no']
						"account_number" 		=> 	(!empty($getGameDetail['BankDetail']['account_no'])?$getGameDetail['BankDetail']['account_no']:$getGameDetail['BankDetail']['iban'])
					]
				]);
				//echo "<pre>";print_r($bank_details);die;
				/* $bank_details = \Stripe\Token::create([
				  "bank_account" => [
						"country" => "ES",
						"currency" => "EUR",
						"account_holder_name" => "deepak",
						"account_holder_type" => "individual",
						"account_number" => "ES89370400440532013000"
					]
				]);
				echo "<pre>";print_r($bank_details); */
				
				$accounts 			= 	\Stripe\Account::retrieve($account->id);
				$ids 				= 	$accounts->external_accounts->create(["external_account" => $bank_details->id]);
				
				$OrgtransferStatus  = 	$this->transferStripe($orgAmount,$ids->account,$getGameDetail['Game']['user_id'],$getGameDetail['Game']['id']); 
				
				/* $stripeAccount		=	'acct_1EPqnACwQ3DExNaj';
				$OdyfotransferStatus = $this->transferStripe($odyfoAmount,$stripeAccount,$getGameDetail['Game']['user_id'],$getGameDetail['Game']['id']); */ 
				
				//odyfo amount transfer to odyfo account
				//need to update noti status also
				if(isset($totalPlayer[1])&& !empty($totalPlayer[1])){
					$cashPlayer			=	$totalPlayer[1]->total;
					$Cashamount		 	=	(($getGameDetail['Game']['price'] * $cashPlayer)*100);
					$cashOdyfoAmount	=	($Cashamount * 0.20);//20% to odyfo
					
					$exp_date 		= 	explode('-',$getGameDetail['CardDetail']['exp_date']);
					$token = \Stripe\Token::create([
					  'card' => [
						'number' 	=> $getGameDetail['CardDetail']['card_no'],
						'exp_month' => $exp_date[0],
						'exp_year' 	=> $exp_date[1],
						'cvc' 		=> $getGameDetail['CardDetail']['cvv_code']
					  ]
					]);
					$token_id		=	$token->id;
					$description	=	"During Cash transfer";
					
					try{
						$charge = \Stripe\Charge::create(array(
						  "amount" 		=> $cashOdyfoAmount, // amount in cents, again
						  "currency" 	=> "EUR",
						  "source" 		=> $token_id,
						  "description" => $description)
						);
						$success = 1;
					}catch(Stripe_CardError $e) {
						$success	=	0;
						$error = $e->getMessage();
					} catch (Stripe_InvalidRequestError $e) {
					  // Invalid parameters were supplied to Stripe's API
					  $success	=	0;
					  $error = $e->getMessage();
					} catch (Stripe_AuthenticationError $e) {
					  // Authentication with Stripe's API failed
					  $success	=	0;
					  $error = $e->getMessage();
					} catch (Stripe_ApiConnectionError $e) {
					  // Network communication with Stripe failed
					  $success	=	0;
					  $error = $e->getMessage();
					} catch (Stripe_Error $e) {
					  // Display a very generic error to the user, and maybe send
					  // yourself an email
					  $success	=	0;
					  $error = $e->getMessage();
					}catch (Exception $e) {
					  // Something else happened, completely unrelated to Stripe
					  $success	=	0;
					  $error = $e->getMessage();
					}
					$status = (($success!=1)?5:1);
					$data['user_id']		=	$getGameDetail['Game']['user_id'];
					$data['game_id']		=	$getGameDetail['Game']['id'];
					$data['amount']			=	$cashOdyfoAmount;
					$data['token']			=	$token_id;
					$data['description']	=	$description;
					$data['charge_id']		=	$charge->id;
					$data['transaction_id']	=	$charge->balance_transaction;
					$data['status']			=	$status;
					$this->Payment->create();
					$this->Payment->save($data);
				}
				
			}echo json_encode(array('status'=>200,'message'=>"data found"));
		}else{
			echo json_encode(array('status'=>400,'message'=>"no data"));
		}die;
	}
	
	public function countGamePlayer($game_id = null){
		$this->layout="None";
		$this->loadModel('PlayerAvailability');
		
		$this->PlayerAvailability->bindModel(
			array(
				'belongsTo'=>array(
				 'PurchaseGame'=>array(
				  'className' => 'PurchaseGame',
				  'foreignKey' => false,
				   'conditions' => 'PlayerAvailability.game_id = PurchaseGame.game_id',
					)        
				)
			)
		); 
		$totalPlayers = $this->PlayerAvailability->find('all',array('conditions'=>array('PlayerAvailability.game_id'=>$game_id,'PlayerAvailability.status'=>1),'fields'=>array('count(*)as total','PurchaseGame.pay_mode'),'group'=>array('pay_mode')));
		
		$data = array();
		if($totalPlayers){
			foreach($totalPlayers as $totalPlayer){
				$res['total']		=	$totalPlayer[0]['total'];
				$res['pay_mode']	=	$totalPlayer['PurchaseGame']['pay_mode'];//0=card,1=cash
				$data[] = $res;
			}
		}
		return json_encode($data);
	}
	
	
	public function refundStripe($charge_id = null){
		\Stripe\Stripe::setApiKey(STRIPE_SECRET_KEY);
		try{
			$refund = \Stripe\Refund::create([
			  "charge" => $charge_id
			]);
			$success = 1;
		}catch(Stripe_CardError $e) {
			$success	=	0;
			$error = $e->getMessage();
		} catch (Stripe_InvalidRequestError $e) {
		  // Invalid parameters were supplied to Stripe's API
		  $success	=	0;
		  $error = $e->getMessage();
		} catch (Stripe_AuthenticationError $e) {
		  // Authentication with Stripe's API failed
		  $success	=	0;
		  $error = $e->getMessage();
		} catch (Stripe_ApiConnectionError $e) {
		  // Network communication with Stripe failed
		  $success	=	0;
		  $error = $e->getMessage();
		} catch (Stripe_Error $e) {
		  // Display a very generic error to the user, and maybe send
		  // yourself an email
		  $success	=	0;
		  $error = $e->getMessage();
		}catch (Exception $e) {
		  // Something else happened, completely unrelated to Stripe
		  $success	=	0;
		  $error = $e->getMessage();
		}
		if ($success!=1){
			return 0;
		}else{
			return 1;
		}
	}
	
	
	
	public function transferStripe($amount=null,$account_no = null,$user_id=null,$game_id = null){
		$this->layout="None";
		$this->loadModel('Payment');
		\Stripe\Stripe::setApiKey(STRIPE_SECRET_KEY);
		try{
			$transfer =	\Stripe\Transfer::create([
						  "amount" => $amount,
						  "currency" => "eur",
						  "destination" => $account_no,
						  "transfer_group" => "ORDER_".$user_id
						]);
			$success = 1;
		}catch(Stripe_CardError $e) {
			$success	=	0;
			$error = $e->getMessage();
		} catch (Stripe_InvalidRequestError $e) {
		  $success	=	0;
		  $error = $e->getMessage();
		} catch (Stripe_AuthenticationError $e) {
		  $success	=	0;
		  $error = $e->getMessage();
		} catch (Stripe_ApiConnectionError $e) {
		  $success	=	0;
		  $error = $e->getMessage();
		} catch (Stripe_Error $e) {
			$success	=	0;
			$error = $e->getMessage();
		}catch (Exception $e) {
			$success	=	0;
			$error = $e->getMessage();
		}//echo $success;echo "<br>";
		
		if ($success!=1){
			$data['status']			=	3;//transfer failed
		}else{
			$data['status']			=	2;//transfer success
		}
		$data['user_id']		=	$user_id;
		$data['game_id']		=	$game_id;
		$data['amount']			=	$amount;
		$data['account_no']		=	$account_no;
		$data['transfer_id']	=	(!empty($transfer)?$transfer->id:0);
		$data['transaction_id']	=	(!empty($transfer)?$transfer->balance_transaction:0);
		$this->Payment->save($data);
		return 1;
	}
	
	public function sendPlayerAvaialabilityNoti(){//every 15 min
		$this->layout="None";
		$this->loadModel("Game");
		$this->loadModel("Team");
		$this->loadModel("Notification");
		$this->loadModel("User");
		
		/* $this->loadModel("EmailTemplate");
		$from 	   		= 	ADMIN_EMAIL;
		$to 	   		= 	"deepak.bansal@techugo.com";
		$mail_slug 		= 	"registration";
		$replace_var 	= 	array("name"=>'playerEvery15min');
		$sendMail  		=	$this->sendEmail($mail_slug,$replace_var,$from,$to); */
			
		$currDate		=	date("Y-m-d H:i:s");
		$currGreatDate 	= 	date("Y-m-d H:i:s",strtotime(date("Y-m-d H:i:s")." +1455 minutes"));//15 min more to 24HR
		$currLeastDate	= 	date("Y-m-d H:i:s",strtotime(date("Y-m-d H:i:s")." +1425 minutes"));//15 min less to 24HR
	
		/* echo "<br>";echo $currGreatDate 	= 	"2019-04-20 11:16:28";
		echo "<br>";echo $currLeastDate		=	"2019-04-20 10:46:28"; */
	
		$getGames 	= 	$this->Game->find("all",array('conditions'=>array('Game.date_time >='=>$currLeastDate,'Game.date_time <'=>$currGreatDate)));
		$gameId		=	array();
		if($getGames){
			foreach($getGames as $getGame){
				$gameId[] = $getGame['Game']['id'];
			}
			
			$this->Team->bindModel(
				array(
					'belongsTo'=>array(
						'User'=>array(
						  'className'  => 'User',
						  'foreignKey' => false,
						  'conditions' => 'Team.user_id = User.id',
						)        
					)
				)
			); 
			
			$getDatas = $this->Team->find('all',array('conditions'=>array('Team.game_id'=>$gameId)));
			if($getDatas){
				foreach($getDatas as $getData){
					if(!empty($getData['User']['device_id'])){
						
						$badge 	= 	($getData['User']['badge'] + 1);
						$this->User->updateAll(
							array('badge'=>$badge),
							array('id'=>$getData['User']['id'])
						);
						
						$message 	=	"Please confirm your availability for ". $getGame['Game']['name']." "."game";
						
						$msg 		= 	"Por favor confirme su disponibilidad para ". $getGame['Game']['name']." "."juego";
						$noti_msg	=	($getData['User']['lang_type']==2?$msg:$message);
						//send push to player for availability
						$this->Pusher->notification($getData['User']['device_type'],$getData['User']['device_id'],$noti_msg,"PlayerAvailability",$getData['Team']['game_id'],$badge);
					}
					$data1['Notification']['type']			=	"PlayerAvailability";
					$data1['Notification']['message'] 		= 	$message;
					$data1['Notification']['sender_id']		=	0;
					$data1['Notification']['receiver_id']	=	$getData['User']['id'];
					$data1['Notification']['game_id']		=	$getData['Team']['game_id'];
					$this->Notification->create();
					$this->Notification->save($data1,false);
				}
			}		
		}
		//Before 20 hr game start
		//send push to organiser for total player
		$this->Game->bindModel(
			array(
				'belongsTo'=>array(
					'User'=>array(
					  'className'  => 'User',
					  'foreignKey' => false,
					  'conditions' => 'Game.user_id = User.id',
					)        
				)
			)
		); 
		
		$currGreatDate1 	= 	date("Y-m-d H:i:s",strtotime(date("Y-m-d H:i:s")." +1215 minutes"));//15 min more to 20HR
		$currLeastDate1		= 	date("Y-m-d H:i:s",strtotime(date("Y-m-d H:i:s")." +1185 minutes"));//15 min less to 20HR
		
		/* echo "<br>";echo $currGreatDate1		=	"2019-04-23 02:19:05";
		
		echo "<br>";echo $currLeastDate1		=	"2019-04-23 01:49:05"; */
		
		$get_Games 	= 	$this->Game->find("all",array('conditions'=>array('Game.date_time >='=>$currLeastDate1,'Game.date_time <'=>$currGreatDate1),"fields"=>array('Game.*','User.device_id','User.device_type','User.lang_type','User.id','User.badge')));
		/*  $log = $this->Game->getDataSource()->getLog(false, false);
							debug($log);  */
	
		$gameId		=	array();
		if($get_Games){
			foreach($get_Games as $get_Game){//echo "<pre>";print_r($getGame);die;
				$gameId 	= $get_Game['Game']['id'];
				$getDatas 	= $this->Team->find('all',array('conditions'=>array('Team.game_id'=>$gameId),'fields'=>array('count(*)as total','Team.team_id','game_id'),'group'=>array('team_id')));
				if($getDatas){
					$blue = 0;$red = 0;
					foreach($getDatas as $getData){
						if($getData['Team']['team_id'] == 1){
							$blue = $getData[0]['total'];
						}if($getData['Team']['team_id'] == 2){
							$red = $getData[0]['total'];
						}
						$message = "Team in blue is ".$blue." "."and team in red is ".$red." "."in game ".$get_Game['Game']['name'];
					}
					
					if(!empty($get_Game['User']['device_id'])){
						$badge 	= 	($get_Game['User']['badge'] + 1);
						$this->User->updateAll(
							array('badge'=>$badge),
							array('id'=>$get_Game['User']['id'])
						);
						
						$msg 		= 	"Equipo azul es ". $blue." "."y equipo rojo es ".$red." "."en juego ".$get_Game['Game']['name'];
						$noti_msg	=	($get_Game['User']['lang_type']==2?$msg:$message);
						
						$this->Pusher->notification($get_Game['User']['device_type'],$get_Game['User']['device_id'],$noti_msg,"AvailableTeam",$get_Game['Game']['id'],$badge);
					}
					$data1['Notification']['type']			=	"GamePlayer";
					$data1['Notification']['message'] 		= 	$message;
					$data1['Notification']['sender_id']		=	0;
					$data1['Notification']['receiver_id']	=	$get_Game['User']['id'];
					$data1['Notification']['game_id']		=	$get_Game['Game']['id'];
					$this->Notification->create();
					$this->Notification->save($data1,false);
				}
			}
		}
		/*************************************************************************/
		
		//Before 4 hr game start
		//send push to organiser for total player
		/****************************************************************/
		$this->Game->bindModel(
			array(
				'belongsTo'=>array(
					'User'=>array(
					  'className'  => 'User',
					  'foreignKey' => false,
					  'conditions' => 'Game.user_id = User.id',
					)        
				)
			)
		); 
		$currGreatDate1 	= 	date("Y-m-d H:i:s",strtotime(date("Y-m-d H:i:s")." +255 minutes"));//15 min more to 4HR
		$currLeastDate1		= 	date("Y-m-d H:i:s",strtotime(date("Y-m-d H:i:s")." +225 minutes"));//15 min less to 4HR
		$get_Games 			= 	$this->Game->find("all",array('conditions'=>array('Game.date_time >='=>$currLeastDate1,'Game.date_time <'=>$currGreatDate1),"fields"=>array('Game.*','User.device_id','User.device_type','User.lang_type','User.id','User.badge')));
		$gameId				=	array();
		if($get_Games){
			foreach($get_Games as $get_Game){//echo "<pre>";print_r($getGame);die;
				$gameId 	= $get_Game['Game']['id'];
				$getDatas 	= $this->Team->find('all',array('conditions'=>array('Team.game_id'=>$gameId),'fields'=>array('count(*)as total','Team.team_id','game_id'),'group'=>array('team_id')));
				if($getDatas){
					$blue = 0;$red = 0;
					foreach($getDatas as $getData){
						if($getData['Team']['team_id'] == 1){
							$blue = $getData[0]['total'];
						}if($getData['Team']['team_id'] == 2){
							$red = $getData[0]['total'];
						}
						$message 	= 	"Team in blue is ".$blue." "."and team in red is ".$red." "."in game ".$getData['Game']['name'];
						
						$msg 		= 	"Equipo azul es ". $blue." "."y equipo rojo es ".$red." "."en juego ".$getData['Game']['name'];
						$noti_msg	=	($get_Game['User']['lang_type']==2?$msg:$message);
					}
				}else{
					$message 	= 	"Team in blue is 0 and team in red is also 0 in game ".$getData['Game']['name'];
					$msg 		= 	"Equipo azul es 0 y equipo rojo es 0 en juego ".$getData['Game']['name'];
					$noti_msg	=	($get_Game['User']['lang_type']==2?$msg:$message);
				}
				if(!empty($get_Game['User']['device_id'])){
						
					$badge 	= 	($get_Game['User']['badge'] + 1);
					$this->User->updateAll(
						array('badge'=>$badge),
						array('id'=>$get_Game['User']['id'])
					);
					
					
					$this->Pusher->notification($get_Game['User']['device_type'],$get_Game['User']['device_id'],$noti_msg,"TotalTeam",$get_Game['Game']['id'],$badge);
				}
				$data1['Notification']['type']			=	"TotalTeam";
				$data1['Notification']['message'] 		= 	$message;
				$data1['Notification']['sender_id']		=	0;
				$data1['Notification']['receiver_id']	=	$get_Game['User']['id'];
				$data1['Notification']['game_id']		=	$get_Game['Game']['id'];
				$this->Notification->create();
				$this->Notification->save($data1,false);
			}
		}
		/******************************END****************************************/
		
		
		/**********************Send push on game complete*************************/
		$gameGreatDate 	= 	date("Y-m-d H:i:s",strtotime(date("Y-m-d H:i:s")." +105 minutes"));//01:45 hr more to current time
		$gameLeastDate	= 	date("Y-m-d H:i:s",strtotime(date("Y-m-d H:i:s")." +75 minutes"));//01:15 hr less to current time
		
		/* echo $gameGreatDate		=	"2019-04-23 02:19:05";
		echo "<br>";echo $gameLeastDate		=	"2019-04-23 01:49:05";  */
		$this->Game->bindModel(
			array(
				'belongsTo'=>array(
					'User'=>array(
					  'className'  => 'User',
					  'foreignKey' => false,
					  'conditions' => 'Game.user_id = User.id',
					)        
				)
			)
		); 
		$getDetails     =	$this->Game->find("all",array('conditions'=>array('date_time >='=>$gameLeastDate,'date_time <='=>$gameGreatDate,'conduct_status'=>1,'game_status !='=>2,'Game.status'=>1,'Game.cancel_status'=>0),"fields"=>array('Game.id','User.id','User.full_name','User.lang_type','User.device_id','User.device_type','User.badge')));
		//echo "<pre>";print_r($getDetails);//die;
		if($getDetails){
			foreach($getDetails as $getDetail){
				/* $this->Game->updateAll(
					array('game_status'=>2),
					array('id'=>$getDetail['Game']['id'])
				); */
				//send push to organiser
				/***************************************************************/
				$message	 =	"Game has been completed Please share players rating";
				if(!empty($getDetail['User']['device_id'])){
					$badge 	= 	($getDetail['User']['badge'] + 1);
					$this->User->updateAll(
						array('badge'=>$badge),
						array('id'=>$getDetail['User']['id'])
					);
					
					$msg 		= 	"El juego ha sido completado Por favor, comparte la calificación de los jugadores.";
					$noti_msg	=	($getDetail['User']['lang_type']==2?$msg:$message);
					$this->Pusher->notification($getDetail['User']['device_type'],$getDetail['User']['device_id'],$noti_msg,'OrgRating',$getDetail['Game']['id'],$badge);
				}
				$data1['Notification']['type']			=	"OrgRating";
				$data1['Notification']['message'] 		= 	$message;
				$data1['Notification']['sender_id']		=	0;
				$data1['Notification']['receiver_id']	=	$getDetail['User']['id'];
				$data1['Notification']['game_id']		=	$getDetail['Game']['id'];
				$this->Notification->create();
				$this->Notification->save($data1,false);
				
				/****************************************************************/
				
				
				//send push to Available player
				/****************************************************************/
				$getPlayers = $this->Api->getPlayerAvailabilityByGameId($getDetail['Game']['id']);
				if($getPlayers){
					$message1	 =	"Thanks for joining game,Please share your rating for feedback";
					foreach($getPlayers as $getPlayer){
						if(!empty($getPlayer['User']['device_id'])){
							$badge 	= 	($getPlayer['User']['badge'] + 1);
							$this->User->updateAll(
								array('badge'=>$badge),
								array('id'=>$getPlayer['User']['id'])
							);
							$msg 		= 	"Gracias por unirte al juego. Por favor, comparte tu calificación para recibir comentarios.";
							$noti_msg	=	($getPlayer['User']['lang_type']==2?$msg:$message1);
							$this->Pusher->notification($getPlayer['User']['device_type'],$getPlayer['User']['device_id'],$noti_msg,'PlayerRating',$getPlayer['PlayerAvailability']['game_id'],$badge);
						}
						
						$playerdata['Notification']['type']			=	"PlayerRating";
						$playerdata['Notification']['message'] 		= 	$message1;
						$playerdata['Notification']['sender_id']	=	0;
						$playerdata['Notification']['receiver_id']	=	$getPlayer['User']['id'];
						$playerdata['Notification']['game_id']		=	$getPlayer['PlayerAvailability']['game_id'];
						$this->Notification->create();
						$this->Notification->save($playerdata,false);
					}
				}
				/****************************************************************/
			}
		}
		/* echo "<pre>";print_r($getDetails);
		$log = $this->Game->getDataSource()->getLog(false, false);
					debug($log);die; */
		/******************************END****************************************/
		echo "Success";
		die;
	}
	
	
	
	public function logout(){
		$this->layout="None";
		$this->loadModel("User");
		$this->data = json_decode(file_get_contents('php://input'),true);
		if(isset($this->data) && !empty($this->data)){
			if(empty($this->data['access_key'])){
				$error	= 	"Please enter access key";
			}else{
				$chkUser = $this->Api->checkAccessKey($this->data['access_key']);
				if($chkUser){
					$data['id'] 		=	$chkUser['User']['id'];
					$data['device_id'] 	=	0;
					$data['access_key'] =	0;
					$this->User->save($data,false);
					$message	   =	($chkUser['User']['lang_type']==2?"Sesión cerrada con éxito":"Logout successfully.");
					$response_arry = 	array('status'=> 200, 'message'=>$message);
				}else{
					$response_arry = 	array('status'=> 404, 'message'=>"Invalid access key.");
				}
			}if(!empty($error)){
          		$response_arry = array('status'=> 400, 'message'=> $error);
        	}
		}else{
			$response_arry = array('status'=> 400, 'message'=>"nodata");
		}
		echo json_encode($response_arry);exit;
	}
	
	function sendOTP($to,$otpnumber){
	//function sendOTP(){
		//$otpnumber = "123456";
		$sid 	= 'AC62c9df11fc0c3702567871fbbf0d05e0';
		$token 	= '76169b596cc9c8c07f39a940ce2bd0cf';
		$client = new Client($sid, $token);
		//$to 	= '34617750418';//verified num
		//$to ="+5491136808275";
		//$to ="+4915789154413";
		$msg = 	$client->messages->create(
			$to,
			array(
				//'from' => '+34971110688',//activated twilio num
				'from'=>'+18555345401',
				'body' => 'Your OTP is: '.$otpnumber
			)
		);
		return true;
	}
	
	
	
	function testsendOTP(){
		$to = "+91919991849025234";
		$otpnumber = "123456";
		$sid 	= 'AC62c9df11fc0c3702567871fbbf0d05e0';
		$token 	= '76169b596cc9c8c07f39a940ce2bd0cf';
		$client = new Client($sid, $token);
		//$to 	= '34617750418';//verified num
		//$to ="+5491136808275";
		//$to ="+4915789154413";
		$msg = 	$client->messages->create(
			$to,
			array(
				//'from' => '+34971110688',//activated twilio num
				'from'=>'+18555345401',
				'body' => 'Your OTP is: '.$otpnumber
			)
		);
		echo "hello".($msg->sid);
		echo "<br>";print_r($msg);die;
		return true;
	}
	
	
	public function sendOTP1(){
		
		$this->loadModel("EmailTemplate");
		$from 	   		= 	ADMIN_EMAIL;
		$to 	   		= 	"test.techugo@gmail.com";
		$mail_slug 		= 	"registration";
		$replace_var 	= 	array("name"=>'playerEvery15min2312312');
		$sendMail  		=	$this->sendEmail($mail_slug,$replace_var,$from,$to);
		
		die;
		
		
		
		
		//twilio password:deepak@1234567
		//require __DIR__ . '/twilio-php-master/Twilio/autoload.php';
		//use Twilio\Rest\Client;

		// Your Account SID and Auth Token from twilio.com/console
		$account_sid = 'AC62c9df11fc0c3702567871fbbf0d05e0';
		$auth_token = '76169b596cc9c8c07f39a940ce2bd0cf';
		// In production, these should be environment variables. E.g.:
		// $auth_token = $_ENV["TWILIO_ACCOUNT_SID"]

		// A Twilio number you own with SMS capabilities
		$twilio_number = "+15017122661";

		$client = new Client($account_sid, $auth_token);
		/*$client->messages->create(
			// Where to send a text message (your cell phone?)
			'+15558675310',
			array(
				'from' => $twilio_number,
				'body' => 'I sent this message in under 10 minutes!'
			)
		);
		
		*/
		try{
			$message = $client->messages->create(array(
				"From" => "+34617750418",
				"To" => "+919467168209",
				"Body" => "Test message!",
			));

			// Display a confirmation message on the screen
			echo "Sent message";
		}catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
        }	die;
	}
	
	
	
	
}

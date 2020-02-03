<?php
App::import('Vendor','message'); 
class UsersController extends AppController {
	public $name = 'Users';
    public $helpers = array('Html', 'Form', 'Session', 'Text');
    public $components = array('Email','Auth','Session', 'Cookie');

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
	
	public function admin_manage_users(){
		$this->layout = 'Admin/default';
		$getUsers = $this->User->find('all',array('order'=>array('id desc')));
		$this->set('result',$getUsers);
	}
	
	public function admin_updateStatus(){
		$data['User']['status']  	= $_REQUEST['status'];
		$data['User']['id']			= $_REQUEST['user_id'];
		$data['User']['access_key']	= 'abc@123';
		$datas 	= $this->User->save($data,false);
		echo $datas['User']['status'];
		die;
	}
	
	public function admin_view_user($user_id=null){
		$this->layout = 'Admin/default';
		if($this->request->is('post')){
			$data['full_name'] 		= 	$this->data['full_name'];
			$data['gender']  		= 	$this->data['gender'];
			$data['mobile']			= 	$this->data['mobile'];
			$data['status']			= 	$this->data['status'];
			$data['id']				=	$user_id;
			$this->Session->setFlash(__(_ADMIN2, true), 'message', array('class' => 'alert-success msg'));
			$this->User->save($data,false);
			$this->redirect(SITE_PATH.'admin/Users/manage_users/');
		}if(!empty($user_id)){
			$getUsers = $this->User->find('first',array('conditions'=>array('id'=>$user_id)));
			$this->set('result',$getUsers);
		}
	}
	
	public function admin_verifyStatus(){
		if($_REQUEST['admin_verify'] == 0){
			$admin_verify = 1;
		}else{
			$admin_verify = 0;
		}
		$data['User']['admin_verify']  	= $admin_verify;
		$data['User']['id']				= $_REQUEST['user_id'];
		$datas 	= $this->User->save($data,false);
		echo 1;
		die;
	}
	
	public function admin_deleteAllUser(){
		$this->loadModel('AdminNotification');
		$this->loadModel('Batal');
		$this->loadModel('BlockUser');
		
		$this->loadModel('ChallengeUser');
		$this->loadModel('CommentBlockuser');
		$this->loadModel('FollowUser');
		
		$this->loadModel('LikeBatal');
		$this->loadModel('ReportUser');
		$this->loadModel('Chat');
		
		$this->loadModel('Notification');
		$this->loadModel('BlockComment');
		$this->loadModel('BlockCommentbatal');
		
		$this->loadModel('ReportBatal');
		$this->loadModel('ReportComment');
		$this->loadModel('ReportCommentbatal');
		$this->loadModel('ReportMusic');
		
		
		$this->loadModel('ViewAdvertisement');
		$this->loadModel('ViewBatal');
		$this->loadModel('ViewMusic');
		
		
		$this->loadModel('Draft');
		$this->loadModel('LikeAdvertisement');
		$this->loadModel('LikeMusic');
		$this->loadModel('Payment');
		$this->loadModel('PurchaseBeat');
		$this->loadModel('UserWinner');
		$this->loadModel('Music');
		
		$this->loadModel('CommentAdvertisement');
		$this->loadModel('CommentBatal');
		$this->loadModel('CommentMusic');
		
		$this->loadModel('User');
		
		$user_id = explode(',',$_POST['user_ids']);
		
		$tables = array("comment_advertisements","comment_batals","comment_musics");
		foreach($tables as $table) {
			for($i=0;$i<count($user_id);$i++){
				$getDatas = $this->CommentAdvertisement->query("SELECT id,tag_user_id FROM $table WHERE FIND_IN_SET($user_id[$i],tag_user_id)");
				if($getDatas){
					foreach($getDatas as $getData){
						$tagUser  = $getData[$table]['tag_user_id'];
						$tags 	  =	explode(',',$tagUser);
						$tagCount = count($tags);
						if($tagCount == 1){
							$this->CommentAdvertisement->query("DELETE FROM $table WHERE tag_user_id='$user_id[$i]'");
						}else{
							if($tags[0]==$user_id[$i]){
								$replaceData = str_replace($user_id[$i].",","",$tagUser);
							}else{
								$replaceData = str_replace(",".$user_id[$i],"",$tagUser);
							}
							$id = $getData[$table]['id'];
							$this->CommentAdvertisement->query("update $table set tag_user_id='$replaceData' WHERE id='$id'");
						}
					}
				}
			}
		}
		
		$this->AdminNotification->deleteAll(array('AdminNotification.receiver_id' =>$user_id));
		$this->Batal->deleteAll(array("OR"=>array(array('user_id'=>$user_id),array('challenge_to'=>$user_id))));
		$this->BlockUser->deleteAll(array("OR"=>array(array('block_by'=>$user_id),array('block_to'=>$user_id))));
		
		$this->ChallengeUser->deleteAll(array("OR"=>array(array('challenge_to'=>$user_id),array('challenge_by'=>$user_id))));
		$this->CommentBlockuser->deleteAll(array("OR"=>array(array('block_by'=>$user_id),array('block_to'=>$user_id))));
		$this->FollowUser->deleteAll(array("OR"=>array(array('follow_by'=>$user_id),array('follow_to'=>$user_id))));
		
		$this->LikeBatal->deleteAll(array("OR"=>array(array('user_id'=>$user_id),array('like_by'=>$user_id))));
		$this->ReportUser->deleteAll(array("OR"=>array(array('report_by'=>$user_id),array('report_to'=>$user_id))));
		$this->Chat->deleteAll(array("OR"=>array(array('sender_id'=>$user_id),array('receiver_id'=>$user_id))));
		
		$this->Notification->deleteAll(array("OR"=>array(array('sender_id'=>$user_id),array('receiver_id'=>$user_id))));
		$this->BlockComment->deleteAll(array('BlockComment.block_by' =>$user_id));
		$this->BlockCommentbatal->deleteAll(array('BlockCommentbatal.block_by' =>$user_id));
		
		$this->ReportBatal->deleteAll(array('ReportBatal.reported_by' =>$user_id));
		$this->ReportCommentbatal->deleteAll(array('ReportCommentbatal.reported_by' =>$user_id));
		$this->ReportComment->deleteAll(array('ReportComment.reported_by' =>$user_id));
		$this->ReportMusic->deleteAll(array('ReportMusic.reported_by' =>$user_id));
		
		$this->ViewAdvertisement->deleteAll(array('ViewAdvertisement.viewed_by' =>$user_id));
		$this->ViewBatal->deleteAll(array('ViewBatal.viewed_by' =>$user_id));
		$this->ViewMusic->deleteAll(array('ViewMusic.viewed_by' =>$user_id));
		
		
		$this->Draft->deleteAll(array('Draft.user_id' =>$user_id));
		$this->LikeAdvertisement->deleteAll(array('LikeAdvertisement.user_id' =>$user_id));
		$this->LikeMusic->deleteAll(array('LikeMusic.user_id' =>$user_id));
		$this->Payment->deleteAll(array('Payment.user_id' =>$user_id));
		$this->PurchaseBeat->deleteAll(array('PurchaseBeat.user_id' =>$user_id));
		$this->UserWinner->deleteAll(array('UserWinner.user_id' =>$user_id));
		$this->Music->deleteAll(array('Music.user_id' =>$user_id));
		
		$this->CommentAdvertisement->deleteAll(array('CommentAdvertisement.user_id' =>$user_id));
		$this->CommentBatal->deleteAll(array('CommentBatal.user_id' =>$user_id));
		$this->CommentMusic->deleteAll(array('CommentMusic.user_id' =>$user_id)); 
		
		
		$this->User->delete($user_id);
		
		$this->Session->setFlash(__(_ADMIN3, true), 'message', array('class' => 'alert-success msg'));
        echo 1;die;
	}
	
	public function admin_delete($user_id=null){
		$this->loadModel('Draft');
		$this->loadModel('ViewAdvertisement');
		$this->loadModel('ReportBatal');
		$this->loadModel('BlockComment');
		$this->loadModel('AdminNotification');
		
		$this->loadModel('Batal');
		$this->loadModel('BlockUser');
		$this->loadModel('ChallengeUser');
		$this->loadModel('CommentBlockuser');
		$this->loadModel('FollowUser');
		
		$this->loadModel('LikeBatal');
		$this->loadModel('ReportUser');
		$this->loadModel('User');
		
		$this->loadModel('CommentAdvertisement');
		$this->loadModel('User');
		$this->loadModel('Notification');
		
		$this->loadModel('CommentBatal');
		$this->loadModel('CommentMusic');
		
		
		$tables0 = array("comment_advertisements","comment_batals","comment_musics");
		foreach($tables0 as $table) {
			$getDatas = $this->CommentAdvertisement->query("SELECT id,tag_user_id FROM $table WHERE FIND_IN_SET($user_id,tag_user_id)");
			if($getDatas){
				foreach($getDatas as $getData){
					$tagUser  = $getData[$table]['tag_user_id'];
					$tags 	  =	explode(',',$tagUser);
					$tagCount = count($tags);
					if($tagCount == 1){
						$this->CommentAdvertisement->query("DELETE FROM $table WHERE tag_user_id='$user_id'");
					}else{
						if($tags[0]==$user_id){
							$replaceData = str_replace($user_id.",","",$tagUser);
						}else{
							$replaceData = str_replace(",".$user_id,"",$tagUser);
						}
						$id = $getData[$table]['id'];
						$this->CommentAdvertisement->query("update $table set tag_user_id='$replaceData' WHERE id='$id'");
					}
				}
			}
		}
		
		
		
		$tables = array("drafts","like_advertisements","like_musics","musics","payments","purchase_beats","user_winners");
		foreach($tables as $table) {
			$this->Draft->query("DELETE FROM $table WHERE user_id='$user_id'");
		}
		
		$tables1 = array("view_advertisements","view_batals","view_musics");
		foreach($tables1 as $table) {
			$this->ViewAdvertisement->query("DELETE FROM $table WHERE viewed_by='$user_id'");
		}
		
		$tables2 = array("report_batals","report_commentbatals","report_comments","report_musics");
		foreach($tables2 as $table) {
			$this->ReportBatal->query("DELETE FROM $table WHERE reported_by='$user_id'");
		}
		
		$tables3 = array("block_commentbatals","block_comments");
		foreach($tables3 as $table) {
			$this->BlockComment->query("DELETE FROM $table WHERE block_by='$user_id'");
		}
		
		
		$tables4 = array("chats","notifications");
		foreach($tables4 as $table) {
			$this->BlockComment->query("DELETE FROM $table WHERE sender_id='$user_id' or receiver_id ='$user_id'");
		}
		
		$tables5 = array("comment_advertisements","comment_batals","comment_musics");
		foreach($tables5 as $table) {
			$this->CommentAdvertisement->query("DELETE FROM $table WHERE user_id='$user_id'");
		}
		
		$this->AdminNotification->query("DELETE FROM admin_notifications WHERE receiver_id='$user_id'");
		
		$this->Batal->query("DELETE FROM batals WHERE user_id='$user_id' or challenge_to = '$user_id'");
		
		$this->BlockUser->query("DELETE FROM block_users WHERE block_by='$user_id' or block_to = '$user_id'");
		
		$this->ChallengeUser->query("DELETE FROM challenge_users WHERE challenge_to='$user_id' or challenge_by = '$user_id'");
		
		$this->CommentBlockuser->query("DELETE FROM comment_blockusers WHERE block_by='$user_id' or block_to = '$user_id'");
		
		$this->FollowUser->query("DELETE FROM follow_users WHERE follow_by='$user_id' or follow_to = '$user_id'");
		
		$this->LikeBatal->query("DELETE FROM like_batals WHERE user_id='$user_id' or like_by = '$user_id'");
		
		$this->ReportUser->query("DELETE FROM report_users WHERE report_by='$user_id' or report_to = '$user_id'");
		
		
		$this->User->query("DELETE FROM users WHERE id='$user_id'");
		
		$this->Session->setFlash(__(_ADMIN3, true), 'message', array('class' => 'alert-success msg'));
		$this->redirect(SITE_PATH.'admin/Users/manage_users');die;
	}
	
	
	
	
	
	
}

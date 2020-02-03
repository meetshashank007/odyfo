<?php
App::import('Vendor','message'); 
class VenuesController extends AppController {
	public $name = 'Venues';
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
		$this->Auth->userModel 		= 'Venues';
		if(!$this->Session->check('Auth.Admin')){           
          $this->redirect(SITE_PATH.'admin/admins/login/');
		} 
	}
	
	public function admin_manage_venues(){
		$this->layout = 'Admin/default';
		$getVenues = $this->Venue->find('all',array('order'=>array('id desc')));
		$this->set('result',$getVenues);
	}
	
	public function admin_updateStatus(){
		$data['Venue']['status']  	= $_REQUEST['status'];
		$data['Venue']['id']		= $_REQUEST['venue_id'];
		$datas 	= $this->Venue->save($data,false);
		echo $datas['Venue']['status'];
		die;
	}
	
	public function admin_add_venue($venue_id=null){
		$this->layout = 'Admin/default';
		$this->loadModel('Venue');
		$this->loadModel('VenueImage');
		$this->loadModel('Sport');
		if($this->request->is('post')){
			$data['name']					=	$this->request->data['name'];
			$data['sports_id']				=	$this->request->data['sport_id'];
			if(empty($venue_id)){
				$data['user_id']				=	0;//admin
			}
			
			$data['address']				=	$this->request->data['address'];
			
			/* $address = $data['address']; // Google HQ
			$prepAddr = str_replace(' ','+',$address);
			$geocode=file_get_contents('https://maps.google.com/maps/api/geocode/json?address='.$prepAddr.'&sensor=false');
			echo "<pre>";print_r($geocode);die;
			$output= json_decode($geocode);
			echo $old_latitude = $output->results[0]->geometry->location->lat;
			echo "<br>";echo $old_longitude = $output->results[0]->geometry->location->lng;
			
			die; */
			
			if(!empty($venue_id)){
				$data['id']					=	$venue_id;
			}
			if($this->Venue->save($data,false)){
				$venueId = $this->Venue->id;
				if(!empty($venue_id)){
					$imageId = $this->request->data['imageId'];
					if (isset($_FILES['image']['name']) &&  !empty($_FILES['image']['name'])) {
						$totalImage 		=   count(array_filter($_FILES['image']['name']));
						$image['user_id']	=	0;
						$image['venue_id']	=	$venueId;
						$result = array();
						for($b=0; $b<count($_FILES['image']['name']) ;$b++){
							if(!empty($_FILES['image']['name'][$b])){
								$result[]=$b;
							}
						}
						for($k=0; $k<count($result) ;$k++){
							$i = $result[$k];
							if(!empty($this->request->data['imageId'][$i]) && isset($_FILES['image']['name'][$i]) && !empty($_FILES['image']['name'][$i])){
								$path		=	'../webroot/img/venue/';
								$extArr 	= 	explode('.', $_FILES['image']['name'][$i]);
								$ext 		= 	end($extArr);
								$filename 	= 	time().$i . '.' . $ext;
								$url 		= 	$path . $filename;
								move_uploaded_file($_FILES['image']['tmp_name'][$i], $url);
								$image['image']	= 	$filename;
								$image['id']	=	$this->request->data['imageId'][$i];
								$this->VenueImage->save($image,false);
							}else{
								$result=array();
								for ($j=0; $j< count($imageId) ; $j++) { 
									if(empty($imageId[$j])){
										$result[]=$j;
									}
								}

								for($a=0;$a<count($result);$a++){
									$var = $result[$a];
									if(!empty($_FILES['image']['name'][$var])){
										$path		=	'../webroot/img/venue/';
										$extArr 	= 	explode('.', $_FILES['image']['name'][$var]);
										$ext 		= 	end($extArr);
										$filename 	= 	time().$a . '.' . $ext;
										$url 		= 	$path . $filename;
										move_uploaded_file($_FILES['image']['tmp_name'][$var], $url);
										$image['image']	= 	$filename;
										$this->VenueImage->save($image,false);
									}
								}
							}
						}
					}
				}else{
					$totalImage =  count(array_filter($_FILES['image']['name']));
					for($i=0; $i<$totalImage ;$i++){
						if($_FILES['image']['size'][$i] !=0){
							$path		=	'../webroot/img/venue/';
							$extArr 	= 	explode('.', $_FILES['image']['name'][$i]);
							$ext 		= 	end($extArr);
							$filename 	= 	time().$i . '.' . $ext;
							$url 		= 	$path . $filename;
							
							move_uploaded_file($_FILES['image']['tmp_name'][$i], $url);
							$image['image']= $filename;
						}
						$image['user_id']	=	0;
						$image['venue_id']	=	$venueId;
						$this->VenueImage->create();
						$this->VenueImage->save($image,false);
					}
				}
				$this->Session->setFlash(__(_ADMIN2, true), 'message', array('class' => 'alert-success msg'));
				$this->redirect(SITE_PATH.'admin/Venues/manage_venues/');
			}
		}if(!empty($venue_id)){
			$result		=	$this->Venue->find('first',array('conditions'=>array('id'=>$venue_id)));
			$this->set(compact('result'));
		}
		$getSports	=	$this->Sport->find('all',array("fields"=>array("id","name"),'order'=>array('name')));
		$this->set(compact('getVenues','getSports'));
	}
	
	public function admin_delete($venue_id=null){
		$this->loadModel('Venue');
		$this->loadModel('VenueImage');
		$datas 	= $this->Venue->delete( $venue_id,false);
		//need to delete venue image also 
		$getVenueImages = $this->VenueImage->find('all',array('conditions'=>array('venue_id'=>$venue_id)));
		if($getVenueImages){
			foreach($getVenueImages as $getVenueImage){
				if($getVenueImage['VenueImage']['image']){
					$path 	= 	"../webroot/img/venue/".$getVenueImage['VenueImage']['image'];
					if (file_exists($path)){
						unlink($path);
					}
				}
			}
		}
		$condition = array('venue_id'=>$venue_id);
		$this->VenueImage->deleteAll($condition);
		if($datas == 1){
			$this->Session->setFlash(__(_ADMIN3, true), 'message', array('class' => 'alert-success msg'));
		}else{
			$this->Session->setFlash(__(_ADMIN4, true), 'message', array('class' => 'alert-error msg'));
		}$this->redirect(SITE_PATH.'admin/Venues/manage_venues/');die;
	}
	
	
	
	
	
	
}

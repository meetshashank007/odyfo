<!doctype html>
<html>
<meta http-equiv="content-type" content="text/html;charset=UTF-8" />
<head>
<meta charset="utf-8"/>
<title>Odyfo</title>
<meta name="viewport" content="width=device-width; initial-scale=1.0; maximum-scale=1.0; minimum-scale=1.0; user-scalable=no;" />
<link href="assets/css/bootstrap.css" rel="stylesheet" type="text/css"/>
<link href="assets/css/font-awesome.min.css" media="screen" rel="stylesheet" type="text/css"/>
<link href="assets/css/style.css" rel="stylesheet" type="text/css"/>
<link href="assets/css/media.css" rel="stylesheet" type="text/css"/>
<link rel="icon" href="assets/images/favicon.png" type="image/x-icon" />
<link rel="shortcut icon" href="assets/images/favicon.png" type="image/x-icon" />
<script src="assets/js/jquery-2.1.1.js"></script>
<script src="assets/js/bootstrap.js"></script>
<script src="assets/js/script.js"></script>
<script>
    $(document).ready(function(){
        setTimeout(function(){ $('.message').fadeOut() }, 5000);
    });
</script>
</head>

<body>
<div id="wrapper"> 
  <!--Header Section Start Here-->
  <nav class="navbar navbar-default navbar-fixed-top nav-down">
      <div class="container">
        <div class="navbar-header" id="nav-head">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false"
            aria-controls="navbar">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="<?php echo SITE_PATH;?>">logo</a>
        </div>
        <div id="navbar" class="navbar-collapse collapse">
          <ul class="nav navbar-nav navbar-right">
            <li>
              <a href="<?php echo SITE_PATH;?>#Features">Features</a>
            </li>
            <li>
              <a href="<?php echo SITE_PATH;?>#About">About Us</a>
            </li>
            <li>
              <a href="<?php echo SITE_PATH;?>contact">Contact Us</a>
            </li>
          </ul>
        </div>
      </div>
    </nav>
  <!--Header Section End Here--> 
  
  <!-- Contact Section Start Here -->
  <div class="contact-section clearfix">
    <div class="contact-form">
      <div class="col-md-12">
	  
	  
        <div class="contact-text">
		
			<h4>We would love to hear from you. </h4>
			<h5>Please provide your details and we will be in touch.</h5>
			<div class="message" style="color:green"><?php echo $this->Session->read('contactMessage');?></div>
        </div>
      </div>
      <div class="col-sm-12 col-md-8">
        <form id="contactForm" action = "<?php echo SITE_PATH;?>Webs/contact" method="POST">
          <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="form-group">
              <input type="text" class="form-control" name="contactname" id="contactname" value="" placeholder="Full Name"/>
			  <span class="small" id="contactname-error" style="color:red;"></span> 
            </div>
          </div>
          <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="form-group">
              <input type="email" name="contactemail" id="contactemail" value="" class="form-control" placeholder="Email Address"/>
              <span class="small" id="contactemail-error" style="color:red;"></span>  
            </div>
          </div>
          <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="form-group">
              <input type="text" name="contactmobile" id="contactmobile" value=""  class="form-control" placeholder="Phone Number"/>
              <span class="small" id="contactmobile-error" style="color:red;"></span>  
            </div>
          </div>
          <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="form-group">
              <textarea name="message" id="message" class="form-control" cols="20" rows="5" placeholder="Message"></textarea>
			   <span class="small" id="message-error" style="color:red;"></span>  
            </div>
            <!--<input type="button" name="scsubmit" id="contactSubmit" value="Submit" class="submit-btn"/>-->
			<input type="submit" name="submit" id="contactSubmit" value="Submit" class="submit-btn"/>
            <span class="small" id="success-msg" style="color:green;font-size: 18px;float: left;"></span> 
          </div>
        </form>
      </div>
    </div>
    <div class="contact-image"> </div>
  </div>
  <!-- Contact Section End Here --> 
  
  <!-- Footer Section Start Here -->
  <footer>
    <div class="footer clearfix">
      <div class="container">
        <div class="footer-text">
          <p>Copyright © 2019.All rights reserved.   | <a href="javascript:void(0);"> Terms & Conditions </a> | <a href="#"> Privacy Policy </a> | <a href="javascript:void(0);"> Cookie Policy </a></p>
        </div>
        <div class="footer-icon-div">
          <ul class="list-inline list-unstyled footer-icon">
            <li><a href="javascript:void(0);"><i class="fa fa-twitter" aria-hidden="true"></i> Twitter</a></li>
            <li><a href="javascript:void(0);"><i class="fa fa-facebook" aria-hidden="true"></i> Facebook</a></li>
            <li><a href="javascript:void(0);"><img src="assets/images/google.png" width="28" height="28" alt=""/> Google +</a></li>
            <li><a href="javascript:void(0);"><i class="fa fa-pinterest-p" aria-hidden="true"></i> Pinterest</a></li>
          </ul>
        </div>
      </div>
    </div>
  </footer>
  <!-- Footer Section End Here --> 
</div>
<style>
@media only screen and (max-width:767px){
.navbar.navbar-default.navbar-fixed-top{
	display:block;
}
}
</style>
</body>

<!-- Mirrored from 18.223.100.147/contact.html by HTTrack Website Copier/3.x [XR&CO'2014], Thu, 21 Feb 2019 04:44:53 GMT -->
</html>


<script>
$(document).ready(function() {
    $(window).on('keydown',function(event){
      if(event.keyCode == 13) {
        $('#contactSubmit').click();
      }
    });
  });

$("#contactSubmit").on('click', function(event) {
        var formStatus = true;
		var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;
		if ($( "#contactname" ).val() === "") {
			formStatus = false;
			$( "#contactname-error" ).text( "Please enter name" ).show().fadeOut( 7000 );
		}
		if ($( "#message" ).val() === "") {
			formStatus = false;
			$( "#message-error" ).text( "Please enter message" ).show().fadeOut( 7000 );
		}
		if ($( "#contactmobile" ).val() === "") {
			formStatus = false;
			$( "#contactmobile-error" ).text( "Please enter Mobile No" ).show().fadeOut( 7000 );
		}
		var MOBILE = $( "#contactmobile" ).val();
		if(!/^[0-9\s]*$/.test(MOBILE)){
			formStatus = false;
			$( "#contactmobile-error" ).text( "Please enter valid mobile." ).show().fadeOut( 5000 );
		}
		var NewMOBILE = MOBILE.split(' ').join('');
		if(NewMOBILE.length < 6) {
            formStatus = false;
			$( "#contactmobile-error" ).text( "Please enter valid mobile." ).show().fadeOut( 5000 );
        }
        if(NewMOBILE.length > 15) {
            formStatus = false;
			$( "#contactmobile-error" ).text( "Please enter valid mobile." ).show().fadeOut( 5000 );
        }
		if ($( "#contactemail" ).val() === "") {
			formStatus = false;
			$( "#contactemail-error" ).text( "Please enter email" ).show().fadeOut( 7000 );
		}
		if (!emailReg.test($( "#contactemail" ).val())) {
			formStatus = false;
			$( "#contactemail-error" ).text( "Please enter valid email" ).show().fadeOut( 7000 );
		}
		if (!formStatus) {
			event.preventDefault();
		} else {  
		//	alert('1');
			var myForm = document.getElementById('contactForm');
			formData = new FormData(myForm);
			$.ajax({
				type: "POST",
				url: "http://www.synchronizecare.com/contactProcess.php",
				data: formData,
				processData: false,
				contentType: false,
				type: 'POST',
            //  dataType:'json',
				success: function (res) {  
        //  console.log(res); 
					if (res == 1) {		
						$("#success-msg").text('Thanks We will contact you soon.').show().fadeOut( 10000 );
						$('#contactForm')[0].reset();		
					}
					else{
						$("#error-msg").text('Sorry something went wrong.').show().fadeOut( 7000 );
					}
				}
			});
		}
});

</script>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>BATALHAFUNK</title>
<meta name="viewport" content="width=device-width; initial-scale=1.0; maximum-scale=1.0; minimum-scale=1.0; user-scalable=no;" />
<link rel='stylesheet' id='google-fonts-zozo_options-css'  href='http://fonts.googleapis.com/css?family=Hind%3A300%2C400%2C500%2C600%2C700%7CPoppins%3A100%2C200%2C300%2C400%2C500%2C600%2C700%2C800%2C900%2C100italic%2C200italic%2C300italic%2C400italic%2C500italic%2C600italic%2C700italic%2C800italic%2C900italic' type='text/css' media='all' />
</head>
<style>
body{
font-family: Poppins;
 text-align:center;
 background:#161616;
}
.logo_div img {
    max-width: 100%;
}
h1{
color:#f5b80f;
}
.messagewrap {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: 9999;
    display: table;
}
.messagewrap .messagewrapbox {
    position: relative;
    display: table-row;
    z-index: 99999;
}
.messagewrap .messagewrapbox .wrapper {
    display: table-cell;
    vertical-align: middle;
	padding:4px;
}
</style>
<body> 
    <div class="container">
	   <div class="row">
		   <div class="messagewrap">
			   <div class="messagewrapbox">
				   <div class="wrapper">
					  <div class="col-md-12">
					       <div class="logo_div">
						     <img class="img-responsive zozo-standard-logo" src="http://batalhafunk.com/wp-content/uploads/2017/01/LOGO-WEBSIGE-WHITE.png" alt="BatalhaFUNK">
						   </div>
						   <h1>
						   <?php if($this->params['pass'][1] == 2){//english
								echo "Congratulations !! Your account is now activated.";
						   }elseif($this->params['pass'][1] == 3){//spanish
								echo "Felicitaciones! Tu cuenta ha sido activada";
						   }else{//Portuguese
								echo "Parabéns! Sua conta foi ativada com sucesso";
						   }?>
						   </h1>
					  </div>
				   </div>
			   </div>
		   </div>
	   </div>
	</div>
</body>
</html>

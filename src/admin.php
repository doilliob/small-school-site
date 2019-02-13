<?php
  require_once "libraries/config.php";
  require_once "libraries/admlibs.php";
  session_start();
?>
<!doctype html>
<html lang="ru">
	<head>
		<meta charset="utf-8">
		<link rel="stylesheet" href="css/jquery-ui.min.css">
		<link rel="stylesheet" href="css/jquery-ui.theme.min.css">
		<link rel="stylesheet" href="css/bootstrap.min.css">
		<link rel="stylesheet" href="css/bootstrap-theme.min.css">
		<link rel="stylesheet" href="css/admin.css">
	</head>
	<body>
		<?php 
		   if( is_registered() )
		   {
		   	 	echo '<div class="col-md-12">'.admin_menu().'</div>';
		   	    router();
		   }else{
		   	 registration_form();
		   }
		?>
		


	</body>
	<script src="js/jquery-2.1.1.min.js"></script>
	<script src="js/jquery-ui.min.js"></script>
	<script src="js/bootstrap.min.js"></script>
	<script src="js/admin.js"></script>
</html>
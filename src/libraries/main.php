<?php


//==================================================
function doctype()
//==================================================
{ ?>
<!doctype html>
<?php }


//==================================================
function bootstrap_header() 
//==================================================
{ ?>
	<title>Сайт Школы</title>
	<link rel="stylesheet" href="css/jquery-ui.min.css">
	<link rel="stylesheet" href="css/jquery-ui.theme.min.css">
	<link rel="stylesheet" href="css/bootstrap.min.css">
	<link rel="stylesheet" href="css/bootstrap-theme.min.css">
	<link rel="stylesheet" href="css/coin-slider-styles.css">
	<script src="js/jquery-2.1.1.min.js"></script>
	<script src="js/coin-slider.min.js"></script>

<?php }

//==================================================
function bootstrap_footer() 
//==================================================
{ ?>
	<div id="FooterDiv">Designed by <a href="mailto:doilliob@yandex.ru">Doilliob</a>, 2014</div>
	
	<script src="js/jquery-ui.min.js"></script>
	<script src="js/bootstrap.min.js"></script>
	
	<script src="js/main.js"></script>
	<script>
	 $('.carousel').carousel({
							  interval: 6000
							});
	</script>
<?php }

//==================================================
function headers()
//==================================================
{ ?>
 <html lang="ru">
   <head>
     <meta charset="utf-8">
   	 <?php bootstrap_header(); ?>
   	 <link rel="stylesheet" href="css/main.css">
   </head>
<?php }

//==================================================
function main_menu()
//==================================================
{ 
	$DBH = get_connection();
	$SDB = $DBH->prepare("SELECT * FROM menu ORDER BY priority");
	$SDB->execute();

	$arr = array();
	while( $row = $SDB->fetch() )
	{
		$tmp = array();
		$tmp['id'] = $row['id'];
		$tmp['name'] = $row['name'];
		$tmp['link'] = $row['link'];
		$tmp['parent'] = $row['parent'];
		$tmp['priority'] = $row['priority'];
		array_push($arr, $tmp);
	}
	$SDB = null;
	$DBH = null;
	?>
    <nav class="navbar navbar-default" role="navigation">
	  <div class="container">
	     <ul class="nav navbar-nav">
	     	<li><a href="index.php"><span class="glyphicon glyphicon-home"></span></a></li>
	    	<?php
	    		foreach ($arr as $item)
	    			if( ($item['parent'] == NULL) || ($item['parent'] == 0) )
	    			{
	    				$is_child = false;
	    				foreach ($arr as $itm)
	    					if($itm['parent'] == $item['id'])
	    						$is_child = true;

	    				if($is_child == true)
	    				{
	    					echo ' <li class="dropdown"> 
	    							<a href="'.$item['link'].'" class="dropdown-toggle" data-toggle="dropdown">
	    								'.$item['name'].'
	                  					<span class="caret"></span>
	                  				</a>
	                				<ul class="dropdown-menu" role="menu">';
	                		foreach ($arr as $itm)
	                			if($itm['parent'] == $item['id'])	
	                				echo '<li><a href="'.$itm['link'].'">'.$itm['name'].'</a></li>';
	    					echo '    </ul>
	    						   </li>';
	    				}else{
	    					echo '<li><a href="'.$item['link'].'">'.$item['name'].'</a></li>';
	    				}
	    			}
	    	?>	    	
          </ul>
	  </div>
	</nav>
 	
<?php }

//==================================================
function carousel() 
//==================================================
{ ?>
	<div id="carousel-example-generic" class="carousel slide" data-ride="carousel">
	  <!-- Indicators 
	  <ol class="carousel-indicators">
	    <li data-target="#carousel-example-generic" data-slide-to="0" class="active"></li>
	    <li data-target="#carousel-example-generic" data-slide-to="1"></li>
	    <li data-target="#carousel-example-generic" data-slide-to="2"></li>
	  </ol>-->

	  <!-- Wrapper for slides -->
	  <div class="carousel-inner">
	    <div class="item active">
	      <img src="images/banner.jpg" alt="">
	      <!--<div class="carousel-caption">
	      </div>-->
	    </div>
	    <div class="item">
	      <img src="images/carusel/carusel-1.png" alt="">
	     <!-- <div class="carousel-caption">
	      </div>-->
	    </div>
	    <div class="item">
	      <img src="images/carusel/carusel-2.png" alt="">
	      <!--<div class="carousel-caption">
	      </div>-->
	    </div>
	    <div class="item">
	      <img src="images/carusel/carusel-3.png" alt="">
	      <!--<div class="carousel-caption">
	      </div>-->
	    </div>
	    <div class="item">
	      <img src="images/carusel/carusel-4.png" alt="">
	      <!--<div class="carousel-caption">
	      </div>-->
	    </div>
	    <div class="item">
	      <img src="images/carusel/carusel-5.png" alt="">
	      <!--<div class="carousel-caption">
	      </div>-->
	    </div>
	  </div>
	  <!-- Controls -->
	  <a class="left carousel-control" href="#carousel-example-generic" role="button" data-slide="prev">
	    <span class="glyphicon glyphicon-chevron-left"></span>
	  </a>
	  <a class="right carousel-control" href="#carousel-example-generic" role="button" data-slide="next">
	    <span class="glyphicon glyphicon-chevron-right"></span>
	  </a>
	</div>


<?php }

//==================================================
function show_questions()
//==================================================
{
	echo "<h3>Раздел \"Задай вопрос директору\"</h3><br>";
	$DBH = get_connection();
	$SDB = $DBH->prepare("SELECT question,answer FROM questions WHERE public=1 ORDER BY id");
	$SDB->execute();
	
	while( $row = $SDB->fetch() )
	{
		echo '
			<div class="QuestionRow">
			 <div class="Question">
			  	<span class="glyphicon glyphicon-plus"></span>
			  	<div class="QuestionForm">'.$row['question'].'</div>
			 </div>
			 <div class="Answer AnswerPlus">'.$row['answer'].'</div>
			</div>
		';
	}

	$SDB = null;
	$DBH = null;

	echo '
	  <br>
	  <button class="btn btn-success" id="SendToDirektor">
     	<span class="glyphicon glyphicon-envelope"></span> Написать письмо
     </button>
     <br><br>
	';
}

//==================================================
function news_router()
// page=news&
// page=news&id=all&
// pade=news&id=id&
//==================================================
{
	$mounth_id = array( "01" => "января",
							"02" => "февраля",
							"03" => "марта",
							"04" => "апреля",
							"05" => "мая",
							"06" => "июня",
							"07" => "июля",
							"08" => "августа",
							"09" => "сентября",
							"10" => "октября",
							"11" => "ноября",
							"12" => "декабря");

	if(isset($_GET['news']))
	{
		$id = $_GET['news'];
		
		if( $id == 'all' ){
			$date = date("Y-m-d");
			$DBH = get_connection();
			$SDB = $DBH->prepare("SELECT id,public,title,body1 FROM news WHERE public < date('$date') OR public = date('$date') ORDER BY public DESC");
			$SDB->execute();
			while($row = $SDB->fetch())
			{
				$id = $row['id'];
				$public = $row['public'];
				$title =  $row['title'];
				$public = explode('-', $public);
				$year = $public[0];
				$mounth = $public[1];
				$day = $public[2];
				$body = urldecode($row['body1']);
				?>
					
					<div class="NewsHeader">
						 <div class="NData">
						 	<div class="NDataDay"> <?php echo $day; ?> </div>
						 	<div class="NDataMounth"> <?php echo $mounth_id[$mounth]; ?> </div>
						 	<div class="NDataYear"> <?php echo $year; ?> </div>
						 </div>
						 <div class="NTitle"> <?php echo $title; ?></div>
					</div>
					<div class="NewsBody"><?php echo $body; ?></div>
					<div class="NewsFooter">
							<a class="btn btn-default" href="index.php?id=news&news=<?php echo $id; ?>&"> ПОДРОБНЕЕ </a>
					</div>
					
				<?php	
			}
			$SDB = null;
			$DBH = null;

		}else{
			$DBH = get_connection();
			$SDB = $DBH->prepare("SELECT * FROM news WHERE id=$id LIMIT 1");
			$SDB->execute();
			$row = $SDB->fetch();
			$SDB = null;
			$DBH = null;

			$public = $row['public'];
			$title =  $row['title'];
			$body = replace_gallery(urldecode($row['body2']));
			$public = explode('-', $public);
			$year = $public[0];
			$mounth = $public[1];
			$day = $public[2];
			?>
				<div class="NewsHeader">
					 <div class="NData">
					 	<div class="NDataDay"> <?php echo $day; ?> </div>
					 	<div class="NDataMounth"> <?php echo $mounth_id[$mounth]; ?> </div>
					 	<div class="NDataYear"> <?php echo $year; ?> </div>
					 </div>
					 <div class="NTitle"> <?php echo $title; ?></div>
				</div>
				<div class="NewsBody"><?php echo $body; ?></div>
				<a class="btn btn-default" href="index.php?id=news&"> НАЗАД </a>
				<br>
				<br>
				
			<?php		
		}

	}else{
		$date = date("Y-m-d");
		$DBH = get_connection();	
		$SDB = $DBH->prepare("SELECT * FROM news WHERE public < date('$date') OR public = date('$date')  ORDER BY public DESC LIMIT 5");
		$SDB->execute();
		$count = 0;
		while($row = $SDB->fetch())
		{
			$count++;
			$id = $row['id'];
			$public = $row['public'];
			$title =  $row['title'];
			$public = explode('-', $public);
			$year = $public[0];
			$mounth = $public[1];
			$day = $public[2];
			$body = urldecode($row['body1']);
			?>
				
				<div class="NewsHeader">
					 <div class="NData">
					 	<div class="NDataDay"> <?php echo $day; ?> </div>
					 	<div class="NDataMounth"> <?php echo $mounth_id[$mounth]; ?> </div>
					 	<div class="NDataYear"> <?php echo $year; ?> </div>
					 </div>
					 <div class="NTitle"> <?php echo $title; ?></div>
				</div>
				<div class="NewsBody"><?php echo $body; ?></div>
				<div class="NewsFooter">
						<a class="btn btn-default" href="index.php?id=news&news=<?php echo $id; ?>&"> ПОДРОБНЕЕ </a>
				</div>
				
			<?php	
		}
		$SDB = null;
		$DBH = null;
		if($count > 0)
			echo '<div align="right"><a class="btn btn-default" href="index.php?id=news&news=all&"> ВСЕ НОВОСТИ </a></div><br><br>';
	}
}

//==================================================
function show_presentation()
//==================================================
{
	$id = $_GET['pnum'];
?>
	<div align="center">
	<h2>Просмотр презентации</h2><br>
	<iframe src="http://docs.google.com/gview?url=http://www.edc.samara.ru/~school122/get.php?id=<?php echo $id; ?>&embedded=true" style="width:640px; height:480px;" frameborder="0"></iframe><br>
	<a href="get.php?id=<?php echo $id; ?>&">
	<button class="btn btn-success"> Скачать презентацию </button>
	</a> <br><br><br>
	</div>
<?php
}

//==================================================
function page_router()
//==================================================
{
	if( isset($_GET['id']) )
	{
		$id = $_GET['id'];
		switch ($id) {
			// Если задай вопрос директору
			case 'questions':
					echo '<div id="PageContent">';
					show_questions();
					echo "</div>";
				break;
			
			case 'news':
				  	echo '<div id="PageContent">';
					news_router();
					echo "</div>";
				break;

			case 'presentation':
					echo '<div id="PageContent">';
					show_presentation();
					echo "</div>";
				break;

			default:
				// Если номер страницы - целое число
				$DBH = get_connection();
				$SDB = $DBH->prepare("SELECT * FROM pages WHERE id=$id");
				$SDB->execute();
				$ans = $SDB->fetch();
				$SDB = null;
				$DBH = null;
				echo '<div id="PageContent">'.replace_gallery(urldecode($ans['body'])).'</div>';
				break;
		}//switch
		
	}else{
		$id = 1;
		$DBH = get_connection();
		$SDB = $DBH->prepare("SELECT * FROM pages WHERE id=$id");
		$SDB->execute();
		$ans = $SDB->fetch();
		$SDB = null;
		$DBH = null;
		echo '<div id="PageContent">'.replace_gallery(urldecode($ans['body'])).'</div>';
	}
}

//=======================================
function replace_gallery($text)
//=======================================
{
	$galleries = null;
	$count = preg_match_all('/GETGALLERY\d+/', $text, $galleries);
	if( ($count == 0) || ($count == FALSE)) return $text;

	$count_image = 1;
	$galleries = $galleries[0];
	foreach ($galleries as $gallery) {
		$count_image = $count_image + 1;
		$id = preg_replace('/GETGALLERY/', '', $gallery);
		$proto = 'GALLERY'.$id;

		$replace = '	
						<div align="center">
						<div id="'.$proto.$count_image.'">
					';
		$DBH = get_connection();
		$SDB = $DBH->prepare("SELECT id FROM gallery WHERE gallery_id=$id ORDER BY id");
		$SDB->execute();
		while( $row = $SDB->fetch() )
		{
			$replace .= '
				<a href="get.php?galleryfile='.$row['id'].'&" target="_blank">
					<img src="get.php?galleryfile='.$row['id'].'&" >
				</a>
			';
		}
		$SDB = null;
		$DBH = null;

		$replace .= '
			</div>
			</div>
			<script>
				$("#'.$proto.$count_image.'").coinslider({ width: 600, height: 480, navigation: true, delay: 5000 }); 
			</script>
		';
		$text = preg_replace('/\['.$gallery.'\]/',$replace,$text,1);
	}
	return $text;
}
// ---- replace_gallery


//====================================================
function modalPane() 
//====================================================
{ ?>
 <style>
 #ModalContent {width: 400px; margin: 0 auto 0 auto;}
 </style>
 <div type="hidden">
	<div class="modal fade" id="ModalDialog"> 
	  <div class="modal-dialog"> 
	    <div class="modal-content" id="ModalContent"> 
	      <div class="modal-header"> 
			<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Закрыть</span></button> 
				<h4 class="modal-title" id="ModalTitle"></h4> 
				</div> 
	      <div class="modal-body"  id="ModalBody"></div> 
	    </div><!-- /.modal-content --> 
	  </div><!-- /.modal-dialog --> 
	</div><!-- /.modal -->
 </div>
<?php }

function mainSlide() 
{
	echo file_get_contents("templates/mainslide.html");
}

function get_events() {
	// Написать письмо директору
	if( isset($_GET['sendmessage'])) {
		$fio = $_POST['fio'];
		$email = $_POST['email'];
		$question = $_POST['question'];

		$DBH = get_connection();
		$SDB = $DBH->prepare("INSERT INTO questions (fio,email,question,answer) VALUES ('$fio','$email','$question','')");
		$SDB->execute();
		$ans = $SDB->fetch();
		$SDB = null;
		$DBH = null;
		$_GET['id'] = 8; // Служебное сообщение что письмо отправлено
	}//sendmessage
}
//==================================================
function body()
//==================================================
{ ?>
   <body>
     <?php 
       carousel();  // Карусель
       main_menu();  // Главное меню
       get_events(); //Послать письмо директору
       page_router();  // Получить страницу
 	   modalPane();  // Диалог для письма директору
       bootstrap_footer(); // футер
    ?>
    <script src="js/Diz_alt_pro.js" type="text/javascript" charset="utf-8"></script>
   </body>
 </html>

<?php }


?>
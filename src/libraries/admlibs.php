<?php


//=================================
function admin_menu ()
//=================================
{ 
	$act_page_editor = ($_GET['page'] == "page_editor") ? "active" : "";
	$act_menu_editor = ($_GET['page'] == "menu_editor") ? "active" : "";
	$act_files = ($_GET['page'] == "files") ? "active" : "";
	$questions = ($_GET['page'] == "questions") ? "active" : "";
	$news = ($_GET['page'] == "news") ? "active" : "";
	$gallery = ($_GET['page'] == "gallery") ? "active" : "";
?>
<nav class="navbar navbar-default navbar-fixed-top" role="navigation">
  <a class="navbar-brand" href="index.php"></span></a>
     <ul class="nav navbar-nav">
     	  <li class=<?php echo $news; ?> ><a href="admin.php?page=news&"><span class="glyphicon glyphicon-pencil"></span> Редактор новостей</a></li>
          <li class=<?php echo $act_page_editor; ?> ><a href="admin.php?page=page_editor&"><span class="glyphicon glyphicon-list-alt"></span> Редактор страниц</a></li>
          <li class=<?php echo $act_menu_editor; ?> ><a href="admin.php?page=menu_editor&"><span class="glyphicon glyphicon-list"></span> Редактор меню</a></li>
          <li class=<?php echo $act_files; ?> ><a href="admin.php?page=files&"><span class="glyphicon glyphicon-floppy-open"></span> Загрузка файлов</a></li>
          <li class=<?php echo $gallery; ?> ><a href="admin.php?page=gallery&"><span class="glyphicon glyphicon-th"></span> Галереи</a></li>
          <li class=<?php echo $questions; ?> ><a href="admin.php?page=questions&"><span class="glyphicon glyphicon-user"></span> Вопросы директору</a></li>
          <li><a href="admin.php?page=exit&"><span class="glyphicon glyphicon-log-in"></span> Выход</a></li>
      </ul>
</nav>
<?php }

//=================================
function delete_files ()
//=================================
{
  if( isset($_GET['operation']) && ($_GET['operation'] == "delete"))
  {
  	 $id = $_GET['id'];
  	 try 
	{
		$DBH = get_connection();
 		$sdb = $DBH->prepare("SELECT * FROM files WHERE id=".$id." LIMIT 1");
 		$sdb->execute();
 		$row = $sdb->fetch();
 		$sdb = null;
 		unlink("files/".$row['filename']);
 		$DBH->prepare("DELETE FROM files WHERE id=".$id)->execute();
 		$DBH = null;
	}
	catch(PDOException $e)  {
		echo "Ошибка при удалении файла в БД<br>";
		echo $e->getMessage();
		$DBH = null;
	}  

  }
}

//=================================
function save_files ()
//=================================
{
  if(isset($_POST['upload']) && isset($_FILES['userfile'])){
  	$filescount = count($_FILES['userfile']['name']);
  	for($i=0; $i < $filescount; $i++)
  	{
  		$title = $_FILES['userfile']['name'][$i];
		$tmpName  = $_FILES['userfile']['tmp_name'][$i];
		$fileSize = $_FILES['userfile']['size'][$i];
		$fileType = $_FILES['userfile']['type'][$i];
		$category = $_POST['category'];
		$fileName = time()."_".$i;

		$fp      = fopen($tmpName, 'r');
		$content = fread($fp, filesize($tmpName));
		fclose($fp);
	    
	    $DBH = get_connection();
		try 
		{
			file_put_contents("files/".$fileName, $content);
			$sdb = $DBH->prepare("INSERT INTO files (filename,filesize,filetype,category,title) VALUES (?,?,?,?,?)");
			$sdb->bindParam(1,$fileName);
			$sdb->bindParam(2,$fileSize);
			$sdb->bindParam(3,$fileType);
			$sdb->bindParam(4,$category);
			$sdb->bindParam(5,$title);
			$sdb->execute();
			$DBH = null;
		}  
		catch(PDOException $e)  {
			echo "Ошибка во вставке файла в БД<br>";
			echo $e->getMessage();
			$DBH = null;
		}
  	}
  }
	/*
  if(isset($_POST['upload']) && $_FILES['userfile']['size'] > 0)
  {
	$title = $_FILES['userfile']['name'];
	$tmpName  = $_FILES['userfile']['tmp_name'];
	$fileSize = $_FILES['userfile']['size'];
	$fileType = $_FILES['userfile']['type'];
	$category = $_POST['category'];
	$fileName = time();

	$fp      = fopen($tmpName, 'r');
	$content = fread($fp, filesize($tmpName));
	fclose($fp);
    
    $DBH = get_connection();
	try 
	{
		file_put_contents("files/".$fileName, $content);
		$sdb = $DBH->prepare("INSERT INTO files (filename,filesize,filetype,category,title) VALUES (?,?,?,?,?)");
		$sdb->bindParam(1,$fileName);
		$sdb->bindParam(2,$fileSize);
		$sdb->bindParam(3,$fileType);
		$sdb->bindParam(4,$category);
		$sdb->bindParam(5,$title);
		$sdb->execute();
		$DBH = null;
	}  
	catch(PDOException $e)  {
		echo "Ошибка во вставке файла в БД<br>";
		echo $e->getMessage();
		$DBH = null;
	}  
  } */
}


//=================================
function view_files ()
//=================================
{ 

?>
	<br>
	<div class="col-md-10">
		<h3> Список загруженных файлов </h3>
		<table class="table table-bordered">
			<thead>
				<tr class="info">
					<td class="col-md-1">№</td>
					<td class="col-md-3">Категория</td>
					<td class="col-md-5">Название файла</td>
					<td class="col-md-1">Операции</td>
				</tr>
			</thead>
			<tbody>
<?php
	$DBH = get_connection();
 	$sdb = $DBH->query("SELECT id, title, category, filetype FROM files ORDER BY category");
	$sdb->setFetchMode(PDO::FETCH_ASSOC);
	$i=1;
	while( $row = $sdb->fetch() )
	{

		$link = '"get.php?id='.$row['id'].'&"';
		if( ($row['filetype'] == 'application/vnd.openxmlformats-officedocument.presentationml.presentation') || 
			($row['filetype'] == 'application/vnd.ms-powerpoint') )
			$link = '"index.php?id=presentation&pnum='.$row['id'].'&"';
		
		echo '
		<tr>
			<td>'.$i.'</td>
			<td>'.$row['category'].'</td>
			<td>'.$row['title'].'</td>
			<td>
			   <a class="operation" target="_blank" href='.$link.'>
			   		<span class="glyphicon glyphicon-floppy-save"></span>
			   	</a>
			   	<a class="operation"  href="admin.php?page=files&operation=delete&id='.$row['id'].'&">
			   		<span class="glyphicon glyphicon-remove-circle"></span>
			   	</a>
			</td>
		</tr>
		';
		$i++;
	}
	$DBH = null;

?>
			</tbody>
		</table>

		<br>
		<h3>Загрузить новый файл</h3>
		<hr>
		<!-- Тип кодирования данных, enctype, ДОЛЖЕН БЫТЬ указан ИМЕННО так -->
		<form id="upload_form" class="col-md-10" enctype="multipart/form-data" action="admin.php?page=files&" method="POST">	
		    <!-- Поле MAX_FILE_SIZE должно быть указано до поля загрузки файла -->
		    <input type="hidden" name="MAX_FILE_SIZE" value="300000000" />
		    <!-- Название элемента input определяет имя в массиве $_FILES -->
		    <label> Категория: </label> <input type="text" name="category" id="category" />
		    <label for="userfile"> Загрузить файл(ы): </label> <input id="userfile" multiple="multiple" name="userfile[]" type="file" />		
		  	<button type="submit" class="btn btn-success" name="upload">
		    	<span class="glyphicon glyphicon-floppy-open"></span>
		    </button>
		</form>
		<br>
		<br>

	</div>
<?php }

//*************************************************************
//               РЕДАКТОР СТРАНИЦ
//*************************************************************

//=================================
function view_pages ()
//=================================
{ 

?>
	<br>
	<div class="col-md-10">
		<h3> Список страниц сайта </h3>
		<a href="admin.php?page=page_editor&operation=new&"><button class="btn btn-success" id="addNewPageButton"><strong> + | Создать страницу </strong></button></a>
		<table class="table table-bordered">
			<thead>
				<tr class="info">
					<td class="col-md-1">№</td>
					<td class="col-md-3">Категория</td>
					<td class="col-md-5">Заголовок</td>
					<td class="col-md-1">Ссылка</td>
				</tr>
			</thead>
			<tbody>
<?php
	$DBH = get_connection();
 	$sdb = $DBH->query("SELECT id, title, category FROM pages ORDER BY category");
	$sdb->setFetchMode(PDO::FETCH_ASSOC);
	$i=1;
	while( $row = $sdb->fetch() )
	{

		echo '
		<tr>
			<td>'.$i.'</td>
			<td>'.$row['category'].'</td>
			<td><a href="admin.php?page=page_editor&operation=edit&id='.$row['id'].'">'.$row['title'].'</a></td>
			<td> <a href="index.php?id='.$row['id'].'&"> <span class="glyphicon glyphicon-flash"></span> </a> </td>
		</tr>
		';
		$i++;
	}
	$DBH = null;

?>
			</tbody>
		</table>
	</div>
<?php }

//=================================
function view_pages_new ()
//=================================
{ ?>
	<div id="PageBlock">
	 	<form id="NewPageForm" action="admin.php?page=page_editor&operation=update&" method="POST">
	 		<input type="hidden" name="id" value="-1">
	 		<label>Категория страницы: </label><input type="input" name="category" id="category">
	 		<label>Заголовок страницы: </label><input type="input" name="title" id="title">
			<textarea name="PageText"></textarea>
		</form>
		<button class="btn btn-success" id="AddNewPage"> Создать страницу </button>
		<a href="admin.php?page=page_editor&">
			<button class="btn btn-warning" id="CanselNewPage"> Отмена </button>
		</a>
	</div>
	<script src="ckeditor/ckeditor.js"></script>
	<script type="text/javascript">
		CKEDITOR.replace( 'PageText' );
	</script>

<?php }

//=================================
function view_pages_edit ()
//=================================
{ 
	if( isset($_GET['id']) )
	{
		$id = $_GET['id'];
		$DBH = get_connection();
 		$SDB = $DBH->prepare("SELECT * FROM pages WHERE id=$id LIMIT 1");
 		$SDB->execute();
 		$row = $SDB->fetch();
 		$SDB = null;
 		$DBH = null;

 		echo '
				<div id="PageBlock">
				 	<form id="EditPageForm" action="admin.php?page=page_editor&operation=update&" method="POST">
				 		<input type="hidden" name="id" value="'.$id.'">
				 		<label>Категория страницы: </label><input type="input" name="category" id="category" value="'.$row['category'].'">
				 		<label>Заголовок страницы: </label><input type="input" name="title" id="title" value="'.$row['title'].'">
						<textarea name="PageText">'.urldecode($row['body']).'</textarea>
					</form>
					<button class="btn btn-success" id="EditPageConfirm"> Сохранить страницу </button>
					<a href="admin.php?page=page_editor&">
						<button class="btn btn-warning" id="EditPageCansel"> Отмена редактирования </button>
					</a>
					<button class="btn btn-danger" id="EditPageDelete"> Удалить страницу </button>
					<form id="DeletePageForm" action="admin.php?page=page_editor&operation=delete&" method="POST">
						<input type="hidden" name="id" value="'.$id.'">
					</form>
				</div>
				<script src="ckeditor/ckeditor.js"></script>
				<script type="text/javascript">
					CKEDITOR.replace( "PageText" );
				</script>
			';
   }	
}

//=================================
function view_pages_update ()
//=================================
{
	if( isset($_POST['id']) )
	{
		$id = $_POST['id'];
		$title = $_POST['title'];
		$category = $_POST['category'];	
		$body  = urlencode(stripslashes($_POST['PageText']));
		$DBH = get_connection();
		if($id == -1){
			$DBH->prepare("INSERT INTO pages (title,category,body) VALUES ('$title','$category','$body')")->execute();
		}else{
			$DBH->prepare("UPDATE pages SET title='$title', category='$category', body='$body' WHERE id=$id")->execute();
		}
		$DBH = null;
	}
}

//=================================
function view_pages_delete ()
//=================================
{
	if( isset($_POST['id']) )
	{
		$id = $_POST['id'];
		$DBH = get_connection();
		$DBH->prepare("DELETE FROM pages WHERE id=$id")->execute();
		$DBH = null;
	}
}



//*************************************************************
//               РЕДАКТОР МЕНЮ
//*************************************************************

//=================================
function menu_view ()
//=================================
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
	<div class="col-md-10" id="MenuEditMainForm">
		<h3> Главное меню </h3> 
		<br>
		<button class="btn btn-success editMenuButton" 
			 id="addNewMenu"
			 ids="-1"
			 names=""
			 link=""
			 parent="NULL"
			 priority="0"> Добавить элемент в меню </button>
		<br><br>
		<table id="MenuList" class="table">
			<tbody>
	<?php

    foreach ($arr as $item)
    	if( ($item['parent'] == NULL) || ($item['parent'] == 0) ){
    		echo '<tr class="firstMenuRow col-md-8">
    					<td class="col-md-5"><span class="glyphicon glyphicon-chevron-right"></span> '.$item['name'].'</td>
    					<td class="col-md-3">
    						<button class="btn btn-success editMenuButton"
		    						 ids="'.$item['id'].'"
		    						 names="'.$item['name'].'"
		    						 link="'.$item['link'].'"
		    						 parent="'.$item['parent'].'"
		    						 priority="'.$item['priority'].'" >
		    						  Редактировать </button>
		        					<button class="btn btn-danger deleteMenuButton" ids="'.$item['id'].'"> Удалить </button>
    					</td>
    			  </tr>';
    		foreach ($arr as $itm)
    			if( $itm['parent'] == $item['id'] ) 
    				echo '<tr class="secondMenuRow col-md-8">
    							<td  class="col-md-5"><span class="glyphicon glyphicon-chevron-right"></span> '.$itm['name'].'</td>
    							<td class="col-md-3">
		    						<button class="btn btn-success editMenuButton"
		    						 ids="'.$itm['id'].'"
		    						 names="'.$itm['name'].'"
		    						 link="'.$itm['link'].'"
		    						 parent="'.$itm['parent'].'"
		    						 priority="'.$itm['priority'].'" >
		    						  Редактировать </button>
		        					<button class="btn btn-danger deleteMenuButton" ids="'.$itm['id'].'"> Удалить </button>
		    					</td>
    					  </tr>';
    	}
    
    ?>
          </tbody>
      </table>
      
      <div class="modal fade" id="editMenuModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		  <div class="modal-dialog">
		    <div class="modal-content">
		      <div class="modal-body">
		        <div id="AddAndEditMenuForm">
			        <form action="admin.php?page=menu_editor&operation=update&" method="POST">
			        	<input type="hidden" name="id" id="id">
			        	<label>Название пункта: </label><input type="input" name="name" id="name">
			        	<label>Ссылка: </label><input type="input" name="link" id="link">
			        	<label>Приоритет: </label>
			        		<select name="priority" id="priority">
			        		    <option value="0">0</option>
			        			<?php for($n=1; $n <= 100; $n++) echo '<option value="'.$n.'">'.$n.'</option> '; ?>
			        		</select>
			        	<label>Родительский элемент</label>
			        		<select id="parent" name="parent">
			        			<option value="NULL">Нет родительского элемента</option>
			        			<?php
			        				foreach ($arr as $item)
			        					if( ($item['parent'] == NULL) || ($item['parent'] == 0) )
			        						echo '<option value="'.$item['id'].'">'.$item['name'].'</option>';
			        			?>
			        		</select>
			        	<button class="btn btn-success" type=submit name="SaveButton" id="SaveButton" >
			        		<span class="glyphicon glyphicon-refresh"></span> | Сохранить изменения 
			        	</button>
			        </form>
			      </div>
		      </div>
		      <div class="modal-footer">
		      </div>
		    </div>
		  </div>
		</div>
      

    </div>
<?php }

//=================================
function menu_view_update ()
//=================================
{

	if( isset($_POST['id']) )
	{
		$id = $_POST['id'];
		$name = $_POST['name'];
		$link = $_POST['link'];
		$parent = $_POST['parent'];
		$priority = $_POST['priority'];

		$DBH = get_connection();
	    $SDB;
	    if( $id == -1)
	    {
	    	$SDB = $DBH->prepare("INSERT INTO menu (name,link,parent,priority) VALUES('$name','$link',$parent,$priority)");
	    	$SDB->execute();
	    }else{
	    	$SDB = $DBH->prepare("UPDATE menu SET name='$name',link='$link',parent=$parent,priority=$priority WHERE id=$id");
	    	$SDB->execute();
	    }
	   $SDB = null;
	   $DBH = null;
	}
}

//=================================
function menu_view_delete ()
//=================================
{
	echo "DDDDDD".$_POST['id'];
	if( isset($_POST['id']) )
	{
		$id = $_POST['id'];
		
		$DBH = get_connection();
	    $DBH->prepare("DELETE FROM menu WHERE id=$id or parent=$id")->execute();
	   	$DBH = null;
	   	 
	}
}

//*******************************************************
//                     ВОПРОСЫ
//******************************************************

//============================
function questions_view ()
//============================
{ ?>
	<br>
	<div class="col-md-12">
		<h3> Список вопросов к директору </h3>
		<table class="table table-bordered">
			<thead>
				<tr class="info">
					<td class="col-md-2">Отправитель</td>
					<td class="col-md-4">Вопрос</td>
					<td class="col-md-5">Ответ</td>
					<td class="col-md-1">Операции</td>
				</tr>
			</thead>
			<tbody>
<?php
	$DBH = get_connection();
 	$sdb = $DBH->query("SELECT id, fio, question, email, answer, public FROM questions ORDER BY id");
	$sdb->setFetchMode(PDO::FETCH_ASSOC);
	while( $row = $sdb->fetch() )
	{
		echo '
		<tr>
		  <form method="POST" action="admin.php?page=questions&operation=update&">
		  	<input type="hidden" name="id" value="'.$row['id'].'">
			<td>'.$row['fio'].'<br>'.$row['email'].'</td>
			<td><textarea class="tArea" name="question">'.$row['question'].'</textarea></td>
			<td><textarea class="tArea" name="answer">'.$row['answer'].'</textarea></td>
			<td>
				<button class="btn btn-info" type="submit" name="save">Сохранить изменения</button>
				'.(($row['public'] == 0) ? 
				'<button class="btn btn-success" type="submit" name="public">Опубликовать</button>' :
				'<button class="btn btn-warning" type="submit" name="unpublic">Снять публикацию</button>' ).'
				<button class="btn btn-danger" type="submit" name="delete">Удалить</button>
			</td>
		  </form>
		</tr>
		';
	}
	$sdb = null;
	$DBH = null;

?>
			</tbody>
		</table>
	</div>
<?php }

function questions_update()
{
	if(isset($_GET['operation']) && ($_GET['operation'] == "update") )
	{
		$id = $_POST['id'];
		$question = $_POST['question'];
		$answer = $_POST['answer'];
		
		$DBH = get_connection();
		$query = "SELECT id FROM questions LIMIT 1"; //placebo
		if( isset($_POST['save']) )
			$query = "UPDATE questions SET question='$question',answer='$answer' WHERE id=$id";
		
		if( isset($_POST['public']))
			$query = "UPDATE questions SET question='$question',answer='$answer',public=1 WHERE id=$id";	
		
		if( isset($_POST['unpublic']))
			$query = "UPDATE questions SET question='$question',answer='$answer',public=0 WHERE id=$id";
			
		if( isset($_POST['delete']))
			$query = "DELETE FROM questions WHERE id=$id";
		
		$DBH->prepare($query)->execute();
		$DBH = null;
	}
}


//*************************************************************
//               РЕДАКТОР НОВОСТЕЙ
//*************************************************************

//=================================
function view_news ()
//=================================
{ 

?>
	<br>
	<div class="col-md-10">
		<h3> Список новостей сайта </h3>
		<a href="admin.php?page=news&operation=new&"><button class="btn btn-success" id="addNewNewsButton"><strong> + | Добавить новость </strong></button></a>
		<table class="table table-bordered">
			<thead>
				<tr class="info">
					<td class="col-md-1">№</td>
					<td class="col-md-2">Дата публикации</td>
					<td class="col-md-6">Заголовок</td>
					<td class="col-md-1">Ссылка</td>
				</tr>
			</thead>
			<tbody>
<?php
	$DBH = get_connection();
 	$sdb = $DBH->query("SELECT id, title, public FROM news ORDER BY public DESC");
	$sdb->setFetchMode(PDO::FETCH_ASSOC);
	$i=1;
	while( $row = $sdb->fetch() )
	{

		echo '
		<tr>
			<td>'.$i.'</td>
			<td>'.$row['public'].'</td>
			<td><a href="admin.php?page=news&operation=edit&id='.$row['id'].'">'.$row['title'].'</a></td>
			<td><a href="index.php?news='.$row['id'].'&"> <span class="glyphicon glyphicon-flash"></span> </a> </td>
		</tr>
		';
		$i++;
	}
	$sdb = null;
	$DBH = null;

?>
			</tbody>
		</table>
	</div>
<?php }

//=================================
function view_news_new ()
//=================================
{ ?>
	<div id="NewsBlock">
	 	<form id="NewNewsForm" action="admin.php?page=news&operation=update&" method="POST">
	 		<input type="hidden" name="id" value="-1">
	 		<label>Дата публикации: </label><input type="input" name="public" id="Public">
	 		<br>
	 		<label>Заголовок страницы: </label><input type="input" name="title" id="title">
	 		<br>
	 		<label>Новость кратко:</label>
			<textarea name="NewsText"></textarea>
			<br>
			<label>Новость полная:</label>
			<textarea name="NewsText2"></textarea>
		</form>
		<button class="btn btn-success" id="AddNewNews"> Создать новость </button>
		<a href="admin.php?page=news&">
			<button class="btn btn-warning" id="CanselNewPage"> Отмена </button>
		</a>
	</div>
	<script src="ckeditor/ckeditor.js"></script>
	<script type="text/javascript">
		CKEDITOR.replace( 'NewsText' );
		CKEDITOR.replace( 'NewsText2' );
	</script>

<?php }

//=================================
function view_news_edit ()
//=================================
{ 
	if( isset($_GET['id']) )
	{
		$id = $_GET['id'];
		$DBH = get_connection();
 		$SDB = $DBH->prepare("SELECT id,title,public,body1,body2 FROM news WHERE id=$id LIMIT 1");
 		$SDB->execute();
 		$row = $SDB->fetch();
 		$SDB = null;
 		$DBH = null;
 		echo '
 				<div id="NewsBlock">
				 	<form id="EditNewsForm" action="admin.php?page=news&operation=update&" method="POST">
				 		<input type="hidden" name="id" value="'.$id.'">
				 		<label>Дата публикации: </label><input type="input" name="public" id="Public" value="'.$row['public'].'">
				 		<br>
				 		<label>Заголовок страницы: </label><input type="input" name="title" id="title" value="'.$row['title'].'">
				 		<br>
				 		<label>Новость кратко:</label>
						<textarea name="NewsText">'.urldecode($row['body1']).'</textarea>
						<br>
						<label>Новость полная:</label>
						<textarea name="NewsText2">'.urldecode($row['body2']).'</textarea>
					</form>
					<button class="btn btn-success" id="EditNewsConfirm"> Сохранить новость </button>
					<a href="admin.php?page=news&">
						<button class="btn btn-warning" id="EditNewsCansel"> Отмена редактирования </button>
					</a>
					<button class="btn btn-danger" id="EditNewsDelete"> Удалить новость </button>
					<form id="DeleteNewsForm" action="admin.php?page=news&operation=delete&" method="POST">
						<input type="hidden" name="id" value="'.$id.'">
					</form>
				</div>
				<script src="ckeditor/ckeditor.js"></script>
				<script type="text/javascript">
					CKEDITOR.replace( "NewsText" );
					CKEDITOR.replace( "NewsText2" );
				</script>
			';
   }	
}

//=================================
function view_news_update ()
//=================================
{
	if( isset($_POST['id']) )
	{
		$id = $_POST['id'];
		$title = $_POST['title'];
		$public = $_POST['public'];	
		$body1  = urlencode(stripslashes($_POST['NewsText']));
		$body2  = urlencode(stripslashes($_POST['NewsText2']));
		$DBH = get_connection();
		if($id == -1){
			$DBH->prepare("INSERT INTO news (title,public,body1,body2) VALUES ('$title','$public','$body1','$body2')")->execute();
		}else{
			$DBH->prepare("UPDATE news SET title='$title', public='$public', body1='$body1', body2='$body2' WHERE id=$id")->execute();
		}
		$DBH = null;
	}
}

//=================================
function view_news_delete ()
//=================================
{
	if( isset($_POST['id']) )
	{
		$id = $_POST['id'];
		$DBH = get_connection();
		$DBH->prepare("DELETE FROM news WHERE id=$id")->execute();
		$DBH = null;
	}
}

//*************************************************************
//               РЕДАКТОР ГАЛЕРЕЙ
//
//	page=gallery&                  -> gallery_all_show()
//	operation=addnew&              -> gallery_addnew() -> gallery_all_show()
//	operation=edit&id=id&          -> gallery_editor()
//	operation=delete&id=id&        -> gallery_delete() -> gallery_all_show()
//  operation=insertphoto&         -> gallery_insertphoto() -> gallery_editor()
//  operation=deletephoto&id=id&   -> gallery_deletephoto() -> gallery_editor()
//*************************************************************

//=================================
function gallery_router()
//=================================
{
	if( isset($_GET['operation']) )
	{
		switch ($_GET['operation']) {
			case 'addnew':
					gallery_addnew();
					gallery_all_show();
					break;
			
			case 'edit':
					gallery_editor();
					break;

			case 'delete':
					gallery_delete();
					gallery_all_show();
					break;

			case 'insertphoto':
					gallery_insertphoto();
					gallery_editor();
					break;

			case 'deletephoto':
					gallery_deletephoto();
					gallery_editor();
					break;
		}

	}else{ gallery_all_show(); }
}

//=================================
function gallery_addnew()
//=================================
{
	$title = $_POST['title'];
	$DBH = get_connection();
 	$DBH->prepare("INSERT INTO gallery (title) VALUES ('$title')")->execute();
 	$DBH=null;
}

//=================================
function gallery_delete()
//=================================
{
	$id = $_GET['id'];
	// Удаляем файлы
	$DBH = get_connection();
 	$SDB = $DBH->query("SELECT filename FROM gallery WHERE gallery_id=$id AND is_gallery=0");
	$SDB->setFetchMode(PDO::FETCH_ASSOC);
	while( $row = $SDB->fetch() )
		unlink("gallery/".$row['filename']);
	$SDB = null;
	$DBH = null;

	// Удаляем записи в БД
	$DBH = get_connection();
 	$DBH->prepare("DELETE FROM gallery WHERE id=$id OR gallery_id=$id")->execute();
 	$DBH=null;
}

//=================================
function gallery_deletephoto()
//=================================
{
	$id = $_GET['id'];

	// Удаляем файл фото
	$DBH = get_connection();
 	$SDB = $DBH->prepare("SELECT filename,gallery_id FROM gallery WHERE id=$id AND is_gallery=0 LIMIT 1");
	$SDB->execute();
	$row = $SDB->fetch();
	unlink("gallery/".$row['filename']);	
	$_GET['id'] = $row['gallery_id'];
	$SDB = null;
	$DBH = null;

	// Удаляем запись в БД
	$DBH = get_connection();
 	$DBH->prepare("DELETE FROM gallery WHERE id=$id AND is_gallery=0")->execute();
 	$DBH = null;
}

//=================================
function gallery_insertphoto()
//=================================
{
  if(isset($_POST['upload']) && $_FILES['userfile']['size'] > 0)
  {
	$title = $_FILES['userfile']['name'];
	$tmpName  = $_FILES['userfile']['tmp_name'];
	$fileSize = $_FILES['userfile']['size'];
	$fileType = $_FILES['userfile']['type'];
	$gallery_id = $_POST['gallery_id'];
	$_GET['id'] = $gallery_id;
	$fileName = time();
	$is_gallery = 0;

	$fp      = fopen($tmpName, 'r');
	$content = fread($fp, filesize($tmpName));
	fclose($fp);
    
    $DBH = get_connection();
	try 
	{
		file_put_contents("gallery/".$fileName, $content);
		$sdb = $DBH->prepare("INSERT INTO gallery (filename,filesize,filetype,is_gallery,gallery_id,title) VALUES (?,?,?,?,?,?)");
		$sdb->bindParam(1,$fileName);
		$sdb->bindParam(2,$fileSize);
		$sdb->bindParam(3,$fileType);
		$sdb->bindParam(4,$is_gallery);
		$sdb->bindParam(5,$gallery_id);
		$sdb->bindParam(6,$title);
		$sdb->execute();
		$DBH = null;
	}  
	catch(PDOException $e)  {
		echo "Ошибка во вставке файла в БД<br>";
		echo $e->getMessage();
		$DBH = null;
	}  
  } 
}

//=================================
function gallery_editor()
//=================================
{
	$id = $_GET['id'];
?>
<br>
	<div class="col-md-8">
		<a href="admin.php?page=gallery&"><button class="btn btn-info"> < | Вернуться к списку галерей </button></a>
		<h3> Редактирование галереи </h3>
<?php
	$DBH = get_connection();
 	$SDB = $DBH->prepare("SELECT title FROM gallery WHERE id=$id LIMIT 1");
 	$SDB->execute();
 	$title = $SDB->fetch();
 	$title = $title['title'];
 	$SDB = null;	
 	$DBH = null;
?>
	<h4>Название: <font color="blue"> <?php echo $title; ?> </font></h4>
	<h4>Код вставки в страницу: <font color="red"> [GETGALLERY<?php echo $id; ?>] </font></h4>
	<a href="admin.php?page=gallery&operation=delete&id=<?php echo $id; ?>&"> 
		<button class="btn btn-danger"> Удалить галерею! </button>
	</a>
	<div id="GalleryEditorBody">
<?php
	// Выводим каждое изображение
	$DBH = get_connection();
 	$sdb = $DBH->query("SELECT id, title FROM gallery WHERE gallery_id=$id AND is_gallery=0 ORDER BY id");
	$sdb->setFetchMode(PDO::FETCH_ASSOC);
	$i=1;
	while( $row = $sdb->fetch() )
	{
		?>
			<hr>
			<div class="panel panel-default GalleryPanelView" width="640px">
	  			<div class="panel-body">
	    			<img src="get.php?galleryfile=<?php echo $row['id']; ?>&" width="640px" height="480px">
	  			</div>
	  			<div class="panel-footer">
	  			  <a href="admin.php?page=gallery&operation=deletephoto&id=<?php echo $row['id']; ?>&">
	  				<button class="btn btn-danger"> Удалить фотографию </button>
	  			  </a>
	  			</div>
			</div>
		<?php
	}

	// Выводим форму загрузки нового изображения
	?>
		</div> <!-- /GalleryEditorBody -->
		<hr>
		<!-- Тип кодирования данных, enctype, ДОЛЖЕН БЫТЬ указан ИМЕННО так -->
		<form id="upload_form" class="col-md-10" enctype="multipart/form-data" action="admin.php?page=gallery&operation=insertphoto&" method="POST">	
		    <!-- Поле MAX_FILE_SIZE должно быть указано до поля загрузки файла -->
		    <input type="hidden" name="MAX_FILE_SIZE" value="300000000" />
		    <input type="hidden" name="gallery_id" value="<?php echo $id; ?>" />
		    <!-- Название элемента input определяет имя в массиве $_FILES -->
		    <label for="userfile"> Загрузить файл: </label> <input id="userfile" name="userfile" type="file" />		
		  	<button type="submit" class="btn btn-success" name="upload">
		    	<span class="glyphicon glyphicon-floppy-open"></span>
		    </button>
		</form>
		<br>
		<br>

	<?php

}


//=================================
function gallery_all_show ()
//=================================
{ 
?>
	<br>
	<div class="col-md-8">
		<h3> Список галерей </h3>
		<form method="POST" action="admin.php?page=gallery&operation=addnew&">
			<input type="input"  name="title" id="GalleryAddTitle" value="ИМЯ НОВОЙ ГАЛЕРЕИ">
			<input type="submit" class="btn btn-success" name="addnew" id="GalleryAddSubmit" value="+ | Добавить новую галерею">
		</form>
		<br>		
		<table class="table table-bordered">
			<thead>
				<tr class="info">
					<td class="col-md-1">№</td>
					<td class="col-md-6">Название галереи</td>
					<td class="col-md-1">Операции</td>
				</tr>
			</thead>
			<tbody>
<?php
	$DBH = get_connection();
 	$sdb = $DBH->query("SELECT id, title FROM gallery WHERE is_gallery=1 ORDER BY id");
	$sdb->setFetchMode(PDO::FETCH_ASSOC);
	$i=1;
	while( $row = $sdb->fetch() )
	{

		echo '
		<tr>
			<td>'.$i.'</td>
			<td>'.$row['title'].'</td>
			<td><a href="admin.php?page=gallery&operation=edit&id='.$row['id'].'&"> <span class="glyphicon glyphicon-pencil"></span></a></td>
		</tr>
		';
		$i++;
	}
	$sdb = null;
	$DBH = null;

?>
			</tbody>
		</table>
	</div>
<?php }
//---------------------------------------------

//=============================================
//        РЕГИСТРАЦИЯ ПОЛЬЗОВАТЕЛЯ!
//=============================================

//=================================
function is_registered() 
//=================================
{
   
   // проверка на вход
   if( isset($_POST['login']) && isset($_POST['password']))
   {
   	 if( ($_POST['login'] == "Admin") && (md5($_POST['password']) == 'c94f66af7681c8f27707ad49cd582ca0'))
   	  	$_SESSION['login'] = true;
   }

   if( $_SESSION['login'] == true )
   	 return true;
   return false;
}

//=================================
function registration_form() 
//=================================
{
?>
<div id="LoginForm" align="center">
 <form action="admin.php" method="POST">
  <img src="get.php?id=19&">
  <h3>Система администрирования сайта</h3>
  <bR>
  <label> Имя пользователя: </label> <bR>
  <input type="input" name="login" id="login"><bR>
  <label> Пароль пользователя: </label> <br>
  <input type="password" name="password" id="password"><br><br>
  <input type="submit" class="btn btn-success" value="Войти">
 </form>
</div>
<?php
}

//=================================
function logout()
//=================================
{
	$_SESSION['login'] = false;
	?>
	<br><br><br>
	<div align="center">
	 <h2>Спасибо за работу с сайтом!</h2>
	 <h4>На данный момент Вы не авторизированы!</h4>
	</div>
	<?php
}


//=================================
function router ()
//=================================
{
	if(isset($_GET['page']))
		switch ($_GET['page']) {
			case 'files':
				 delete_files();
				 save_files();
				 view_files();
				break;
			case 'page_editor':
				if( isset($_GET['operation']) && $_GET['operation']=='new')
					view_pages_new();
				if( isset($_GET['operation']) && $_GET['operation']=='edit')
				{
					view_pages_edit();
				}
				if( isset($_GET['operation']) && $_GET['operation']=='update')
				{
					view_pages_update();
					view_pages();
				}
				if( isset($_GET['operation']) && $_GET['operation']=='delete')
				{
					view_pages_delete();
					view_pages();
				}
				if( !isset($_GET['operation']))
					view_pages();
				break;

			case 'news':
				if( isset($_GET['operation']) && $_GET['operation']=='new')
					view_news_new();
				if( isset($_GET['operation']) && $_GET['operation']=='edit')
				{
					view_news_edit();
				}
				if( isset($_GET['operation']) && $_GET['operation']=='update')
				{
					view_news_update();
					view_news();
				}
				if( isset($_GET['operation']) && $_GET['operation']=='delete')
				{
					view_news_delete();
					view_news();
				}
				if( !isset($_GET['operation']))
					view_news();
				break;

			case 'menu_editor':
				if( isset($_GET['operation']) && ($_GET['operation'] == 'update'))
					menu_view_update();
				if( isset($_GET['operation']) && ($_GET['operation'] == 'delete'))
					menu_view_delete();
				menu_view();	
				break;

			case 'questions':
				if( isset($_GET['operation']) && ($_GET['operation'] == 'update'))
					questions_update();
				questions_view();
				break;

			case 'gallery':
				gallery_router();
				break;

			case 'exit':
				logout();
				break;
			
			default:
				# code...
				break;
		}
}






?>
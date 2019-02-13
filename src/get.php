<?php

 require_once "libraries/config.php";

 if( isset($_GET['galleryfile']))
 {
 	try 
	 {
 	 	$id = $_GET['galleryfile'];
 	 	$dbh = get_connection();
	 	$sdb = $dbh->prepare("SELECT filename, filetype, filesize, title FROM gallery WHERE id=$id");
	 	$sdb->execute();
	 	//$sdb->setFetchMode(PDO::FETCH_ASSOC)
	 	$row = $sdb->fetch();
	 	$name = $row['filename'];
	 	$size = $row['filesize'];
	 	$type = $row['filetype'];
	 	$title = $row['title'];
	 	
	 	header("Content-type: $type");
		header("Content-length: $size");
		header("Content-Disposition: attachment; filename=\"$title\"");
		header("Content-transfer-encoding: binary");
		echo file_get_contents("gallery/".$name);
		$dbh = null;
	 }  
	 catch(PDOException $e)  {
	 	header('Content-Type: text/html; charset=utf-8');
	 	echo "Ошибка в получении файлов<br>";
		echo $e->getMessage();
	 }  	 
 } //-----galleryfile

 if( isset($_GET['id']))
 {
 	 try 
	 {
 	 	$id = $_GET['id'];
 	 	$dbh = get_connection();
	 	$sdb = $dbh->prepare("SELECT filename, filetype, filesize, title FROM files WHERE id=$id");
	 	$sdb->execute();
	 	//$sdb->setFetchMode(PDO::FETCH_ASSOC)
	 	$row = $sdb->fetch();
	 	$name = $row['filename'];
	 	$size = $row['filesize'];
	 	$type = $row['filetype'];
	 	$title = $row['title'];
	 	
	 	header("Content-type: $type");
		header("Content-length: $size");
		header("Content-Disposition: attachment; filename=\"$title\"");
		header("Content-transfer-encoding: binary");
		echo file_get_contents("files/".$name);
		$dbh = null;
	 }  
	 catch(PDOException $e)  {
	 	header('Content-Type: text/html; charset=utf-8');
	 	echo "Ошибка в получении файлов<br>";
		echo $e->getMessage();
	 }  	 
 }else{
 		header('Content-Type: text/html; charset=utf-8');
	 	echo "Искомый файл не найден!";
 }
 
?>
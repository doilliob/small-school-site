<?php

	function get_connection() {
		$_DBH = new PDO("sqlite2:database/database.db");
 		$_DBH->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
 		return $_DBH;
	}
	
?>
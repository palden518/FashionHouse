<?php require_once("connect.php");

$runQuery = mysqli_query($connect);

if($runQuery==true){

		echo "<h2>Connection True</h2>";
	}else{		
		echo "<h2>Error connection_timeout</h2>";
	}	



?>



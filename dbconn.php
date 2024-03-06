<?php
  $dbhost = "localhost";
  $dbuser = "luxiktge21a_phpuser";
  $dbpwd = "c[^^%~ff,yDY";
  $dbname = "luxiktge21a_phpexempel";

  //skapar en uppkoppling
  $dbconn = new mysqli($dbhost, $dbuser, $dbpwd, $dbname);

  //kollar om fel uppstått
  if($dbconn->connect_error){
	  die("Uppkoppling misslyckades:<br>".$dbconn->connect_error);
  }

?>
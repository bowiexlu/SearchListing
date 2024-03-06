<!DOCTYPE HTML>
<html>
	<head>
	<title>Pagination</title>
    <meta charset="UTF-8">
	<style>
		body {font-family: Verdana, Arial, Helvetica, sans-serif;font-size: 11px;}
		table {
			font-family:Verdana, Geneva, sans-serif;
			font-size: 14px;	
			border-collapse: collapse;
			width: 60%;
			
		}
		table, th, td {
			border: 1px solid blue;
		}
		th, td {
			height: 25px;
			text-align: center;
			padding: 5px;
		}
		tr:nth-child(even){
			background-color:#85C8FA;
		}
		tr:hover {
			background-color: #f5f5f5;
		}
		th {
			background-color:#1607CF;
			color: #FFFFFF;
		}
		.sidnummer_bakgrund {background-color: #85C8FA; padding:3px; font-size:14px;}
		a {
			color: #3366CC; 
			text-decoration: none;
			font-size:14px;
		}
	</style>
</head>
<body>

	<?php
        // ansluter till databasen
        require "dbconn.php";
        
        echo '<h1>Lista över Företag</h1>';
        echo '<hr>';
        
        /*	SIDNUMMER: om sida är vald används innehållet i variabeln page som sidnummer, 
         * annars används 1
         */
        if (isset($_GET['page'])) {
            $page = mysqli_real_escape_string($dbconn, trim($_GET['page']));
        } else { 
            $page = 1; 
        }
        
        // anger antal rader från sökresultatet som ska visas per sida
		$limit = 10;
        
        // Antalet länkar som ska visas  
        $numLinks = 5;
        
        //  Räknar ut vilken som är första posten som ska visas 
        $offset = ($page -1) * $limit;
        
        /* sökfrågan som hämtar information från den angivna tabellen 
         * - intervallet för visningen anges med LIMIT 
         */
		 $sql = "SELECT * FROM tblCompanyInfo LIMIT $offset, $limit";
		 $result = $dbconn->query($sql) or die(mysqli_error());
        
        // räknar antalet rader och sparar
        $count = "SELECT COUNT(*) AS numRows FROM tblCompanyInfo";
		$countResult = $dbconn->query($count)or die(mysqli_error());
		$numRows = $countResult->fetch_assoc();
        
        // beräknar antal rader som ska visas per sida, funktionen ceil avrundar uppåt
        if($numRows['numRows']>0){
			$totalPages = (ceil($numRows['numRows']/ $limit));
		}
        
        
        
        // skriver ut sökresultatet - antal rader av totalt
        echo 'Din sökning gav resultatet ' . $numRows['numRows'] . ' rader (här visas ' . $limit . ' rader per sida).';
        echo '<br /><br />';
        
        /*FUNKTIONEN FÖR NAVIGERING 1, som får följande utseende:
         *	<< första < föregående | 1 2 3 4 5 | nästa > sista >>
         */
		 function pageNavOne($page, $totalPages){
			 global $numLinks;
			 //beräknar vad som ska visas
			 if($totalPages > $numLinks){
				$startLink = $page - floor($numLinks/2);
				if ($startLink >($totalPages - $numLinks)){
				 	 $startLink = $totalPages - $numLinks +1;
				}
			 	}else{
					$startLink = 1;
			 	}
			 
				 if($startLink < 1){
					 $startLink = 1;
				 }
				 $stopLink = $startLink + $numLinks - 1;
				 
				 if($stopLink > $totalPages){
					 $stopLink = $totalPages;
				 }
			 
			 	/*kollar om ""<< första < föregående" ska vara klickbara eller inte*/
				 if($page > 1){
					 echo"<a href=\"{$_SERVER['PHP_SELF']}?page=1\">första</a>"; 
					 echo"<a href=\"{$_SERVER['PHP_SELF']}?page=".($page-1)."\">föregående</a>";
				 }else{
					 echo'<font color="#cccccc">första föregående</font>';
				 }
			 
			 //skriver ut sidlänkar i navigeringen: 1 2 3 4 5 osv...
				 if($totalPages > 0){
					 echo'|';
					 for ($i=$startLink; $i<=$stopLink; $i++){
					     /* HEJ LU! Här hade du skrivit if($i=$page) istället 
					        för == */
						 if($i==$page){
							 echo'<span class="sidnummer_bakgrund"><strong>'.$i.'</strong></span>';
						 }else{
							 echo"<a href=\"{$_SERVER['PHP_SELF']}?page=$i\">$i</a>";
						 }
						 echo'';
					  }
				  echo'|';
				  }
				
				//kollar om "nästa > sista >> "ska visas eller inte
				  if($page < $totalPages){
					 echo"<a href=\"{$_SERVER['PHP_SELF']}?page=".($page +1)."\">nästa</a>";
					 echo"<a href=\"{$_SERVER['PHP_SELF']}?page=$totalPages\">sista</a>";
				   }else{
					 echo'<font color="#cccccc">nästa sista</font>';
				   }
		 	  }

    
        // HÄR SLUTAR FUNKTIONEN FÖR NAVIGERING 1 
        
        
        /*  START FÖR FUNKTIONEN NAVIGERING 2, som får följande utseende:
         * << första < föregående | sidan 1 av 25 | nästa > sista >>
         */
		 function pageNavTwo($page, $totalPages){
			 //visar"<<första < föregående "om INTE den första sidan
			 if($page > 1){
				 echo"<a href=\"{$_SERVER['PHP_SELF']}?page=1\">&laquo;första</a>"; 
				 echo"<a href=\"{$_SERVER['PHP_SELF']}?page=".($page-1)."\">&laquo;föregående</a>";
			 }else{
				 echo'<font color="#cccccc">första föregående</font>';
			 }
			 //Visar "| sidan 1 av 25|"
			 echo'|';
			 echo'sidan <span class="sidnummer_bakgrund"><strong>'.($page).'</strong></span> av'.($totalPages);
			 echo'|';
			 
			 //visar" nästa > sista >> "om INTE den sista sidan
			 if($page < $totalPages){
				 echo"<a href=\"{$_SERVER['PHP_SELF']}?page=".($page +1)."\">nästa</a>&raquo;";
				 echo"<a href=\"{$_SERVER['PHP_SELF']}?page=$totalPages\">sista</a>&raquo;";
			 }else{
				 echo'<font color="#cccccc">n&auml;sta &raquo; sista &raquo;</font>';
			 }
		 }

        
        // HÄR SLUTAR FUNKTIONEN FÖR NAVIGERING 2 
        
        // Här anropas funktionen för navigering1, som visas ovanför tabellen
        //   första  föregående | 1 2 3 4 5 | nästa  sista 
        pageNavOne($page, $totalPages);
        
        echo "<br><br>";
        // HTML-tabellens formatering - tabellstart
        echo "<table>";
            echo "<tr>";
                echo"<th>CustomerID</th>
                        <th>Company</th>
                        <th>Address</th>
                        <th>Zipcode</th>
                        <th>City</th>
                        <th>Phone</th>";
                echo"</tr>";
        
        // hämtar resultatrader från tabellen och skriver ut
        while($row = $result->fetch_assoc()) {		
            echo "<tr>";
                echo"<td>".$row['CustomerID']."</td>";
                echo"<td>".$row['Company']."</td>";
                echo"<td>".$row['Address']."</td>";
                echo"<td>".$row['Zipcode']."</td>";
                echo"<td>".$row['City']."</td>";
                echo"<td>".$row['Phone']."</td>";
            echo "</tr>"; 
        } 
        echo "</table>";
        echo "<br>"; 
        
        //Här anropas funktionen för navigering2, som visas nedanom tabellen	 
        pageNavTwo($page, $totalPages);
        
        // stänger databasen
        $dbconn->close();
    ?> 
</body>
</html>

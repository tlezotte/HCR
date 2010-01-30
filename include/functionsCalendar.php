<?php
/**
  * -- Generate an iCal file for the iCalendar plug-in
  */
function iCalendar($type) {
	global $dbh;
	global $default;
								   	
							   
	if ($type == 'Actual') {
		$query = "SELECT r.id, request_type, eid, positionTitle, jobDescription, startDate AS targetDate, DATE_ADD( startDate, INTERVAL 1 DAY ) AS targetDate2
				  FROM Requests r
				    INNER JOIN Employees e ON r.id=e.request_id
				  WHERE startDate IS NOT NULL
				  LIMIT 100";
	} else {
		$query = "SELECT id, request_type, positionTitle, jobDescription, targetDate, DATE_ADD( targetDate, INTERVAL 1 DAY ) AS targetDate2
				  FROM Requests
				  WHERE targetDate NOT LIKE '0000-00-00'
				  LIMIT 100";
	}		  		  
	$sql = $dbh->prepare($query);
	$sth = $dbh->execute($sql);
	
	/* Getting position information */								
	$POSITIONTITLE=$dbh->getAssoc("SELECT title_id, title_name 
								   FROM Position 
								   WHERE title_status='0'");	
	/* Get Employee names from Standards database */
	$EMPLOYEES = $dbh->getAssoc("SELECT eid, CONCAT(fst,' ',lst) AS name
								 FROM Standards.Employees
								 WHERE status='0'");	
	
	/* Set iCalendar filename */						 
	$filename = $default['FS_HOME']."/Calendar/calendars/".$type.".ics";


	
	/*
	 * Creating iCalendar file
	 */
	$ics  = "BEGIN:VCALENDAR\n";
	$ics .= "VERSION:2.0\n";
	$ics .= "PRODID:-//Mozilla.org/NONSGML Mozilla Calendar V1.0//EN\n";

	while($sth->fetchInto($data)) {
	//print_r($data);
		/* Process the Position Title */
		if ($data['request_type'] != 'new') {
			$pt=explode(":", $data['positionTitle']);
			$positionTitle=ucwords(strtolower($POSITIONTITLE[$pt[1]] . ' - ' . $EMPLOYEES[$data[employee]]));
		} else {
			$positionTitle=ucwords(strtolower($POSITIONTITLE[$data[positionTitle]])) . " - New Employee";
		}
		
		$ics .= "BEGIN:VEVENT\n";
		$ics .= "CREATED:".date("Ymd")."T".date("His")."Z\n";
		$ics .= "LAST-MODIFIED:".date("Ymd")."T".date("His")."Z\n";
		$ics .= "DTSTAMP:".date("Ymd")."T".date("His")."Z\n";
		$ics .= "UID:uuid:".date("Ymd", strtotime($data['targetDate'])).date("His")."\n";
		$ics .= "SUMMARY:".$positionTitle."\n";
		$ics .= "STATUS:".strtoupper($type)." START DATE\n";
		$ics .= "CLASS:PUBLIC\n";
		$ics .= "DTSTART;VALUE=DATE:".date("Ymd", strtotime($data['targetDate']))."\n";
		$ics .= "DTEND;VALUE=DATE:".date("Ymd", strtotime($data['targetDate2']))."\n";
		$ics .= "URL:".$default['URL_HOME']."/Requests/detail.php?id=".$data['id']."\n";
		$ics .= "LOCATION:HC-".$data['id']."\n";
		$ics .= "CATEGORIES:".$positionTitle."\n";
		$ics .= "DESCRIPTION:".$data['description']."\n";
		$ics .= "END:VEVENT\n";
	}
	
	$ics .= "END:VCALENDAR\n";	


	/*
	 * Writing iCalendar to a file
	 */
	// Let's make sure the file exists and is writable first.
	if (is_writable($filename)) {
		// Open $filename for writing
	   if (!$handle = fopen($filename, 'w')) {
			$_SESSION['error'] = "Cannot open file ($filename)";
			
			header("Location: ../error.php");
			exit();
	   }
	   // Write $rss to our opened file.
	   if (fwrite($handle, $ics) === FALSE) {
			$_SESSION['error'] = "Cannot write to file ($filename)";
			
			header("Location: ../error.php");   
			exit();
	   }
	   //echo "Success, wrote ($somecontent) to file ($filename)";
	   fclose($handle);
	} else {
		$_SESSION['error'] = "The file $filename is not writable";
		
		header("Location: ../error.php");   
		exit();
	}
}	
?>
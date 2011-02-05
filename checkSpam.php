<?php

include("localinc/UrlCheck.php");

// neuer als 6 Stunden => alle 1 Stunden
check( " DATE_ADD(added, INTERVAL 6 HOUR) > now() AND DATE_ADD(lastCheck, INTERVAL 59 MINUTE) < now() ");
// neuer als 2 Tage => alle 2 Stunden
check( " DATE_ADD(added, INTERVAL 2 DAY) > now() AND DATE_ADD(lastCheck, INTERVAL 2 HOUR) < now() ");
// neuer als 7 Tage => alle 4 Stunden
check( " DATE_ADD(added, INTERVAL 7 DAY) > now() AND DATE_ADD(lastCheck, INTERVAL 4 HOUR) < now() ");
// neuer als 21 Tage => alle 12 Stunden
check( " DATE_ADD(added, INTERVAL 21 DAY) > now() AND DATE_ADD(lastCheck, INTERVAL 12 HOUR) < now() ");
// neuer als 60 Tage => alle 24 Stunden
check( " DATE_ADD(added, INTERVAL 60 DAY) > now() AND DATE_ADD(lastCheck, INTERVAL 24 HOUR) < now() ");
// neuer als 180 Tage => alle 4 Tage
check( " DATE_ADD(added, INTERVAL 180 DAY) > now() AND DATE_ADD(lastCheck, INTERVAL 4 DAY) < now() ");
// neuer als 365 Tage => alle  14 Tage 
check( " DATE_ADD(added, INTERVAL 365 DAY) > now() AND DATE_ADD(lastCheck, INTERVAL 14 DAY) < now() ");
// neuer als 700 Tage => alle  28 Tage 
check( " DATE_ADD(added, INTERVAL 700 DAY) > now() AND DATE_ADD(lastCheck, INTERVAL 28 DAY) < now() ");

function check($where) {
   	$c = new UrlCheck();
$dbh = new PDO('mysql:host=localhost;dbname=liip_to', "liip_to", "oP3phuki");
foreach ($dbh->query("SELECT * from urls WHERE lastCheck = '0000-00-00 00:00:00' OR (  $where ) order by RAND() LIMIT 50; ") as $row) {
$reason = array();
print $row['url'] ."\n";
	if(   $c->isListed($row['url'],null,true)) {
		$reason = $c->reason;
        }
		if (!isset($reason['httperror'])) {
			$reason['httperror'] = '';
		}

		
		if ($reason['httperror'] != $row['httperror']) {
			print "Update with httperror ".  $reason['httperror'] . "\n";

		        $query = 'UPDATE urls SET httperror = :reason where code = :code';

		        $stm = $dbh->prepare($query);
		        $stm->execute(array(
		            ':reason' => $reason['httperror'],
		            ':code' => $row['code'],
		        ));
		}

		unset($reason['httperror']);

	
		if (count($reason) > 0) {
        		$reason = implode("\n",$reason);
		} else {
			$reason = '';
		}

		if ($reason != $row['spamreason']) {
			print "Update with spamreason ".  $reason . "\n";
		        $query = 'UPDATE urls SET spamreason = :reason where code = :code';
        		$stm = $dbh->prepare($query);
		        $stm->execute(array(
        		    ':reason' => $reason,
	        	    ':code' => $row['code'],
	        	));
		}
	        $query = 'UPDATE urls SET changed  = changed, lastCheck = now() where code = :code';
        	$stm = $dbh->prepare($query);
	        $stm->execute(array(
	            ':code' => $row['code'],
        	));
}
   
        unset($c);

}

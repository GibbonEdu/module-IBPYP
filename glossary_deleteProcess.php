<?
/*
Gibbon, Flexible & Open School System
Copyright (C) 2010, Ross Parker

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

include "../../functions.php" ;
include "../../config.php" ;

include "./moduleFunctions.php" ;

//New PDO DB connection
try {
    $connection2=new PDO("mysql:host=$databaseServer;dbname=$databaseName", $databaseUsername, $databasePassword);
	$connection2->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$connection2->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
}
catch(PDOException $e) {
    echo $e->getMessage();
}


session_start() ;

//Set timezone from session variable
date_default_timezone_set($_SESSION[$guid]["timezone"]);

$ibPYPGlossaryID=$_POST["ibPYPGlossaryID"] ;
$URL=$_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/" . getModuleName($_POST["address"]) . "/glossary_delete.php&ibPYPGlossaryID=$ibPYPGlossaryID" ;
$URLDelete=$_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/" . getModuleName($_POST["address"]) . "/glossary.php" ;

if (isActionAccessible($guid, $connection2, "/modules/IB PYP/glossary_delete.php")==FALSE) {

	//Fail 0
	$URL = $URL . "&deleteReturn=fail0" ;
	header("Location: {$URL}");
}
else {
	$role=getRole($_SESSION[$guid]["gibbonPersonID"], $connection2) ;
	if ($role!="Coordinator" AND $role!="Teacher (Curriculum)") {
		//Fail 0
		$URL = $URL . "&addReturn=fail0" ;
		header("Location: {$URL}");
	}
	else {
		//Proceed!
		if ($ibPYPGlossaryID=="") {
			//Fail1
			$URL = $URL . "&deleteReturn=fail1" ;
			header("Location: {$URL}");
		}
		else {
			try {
				$data=array("ibPYPGlossaryID"=>$ibPYPGlossaryID);  
				$sql="SELECT * FROM ibPYPGlossary WHERE ibPYPGlossaryID=:ibPYPGlossaryID" ;
				$result=$connection2->prepare($sql);
				$result->execute($data);
			}
			catch(PDOException $e) { 
				//Fail2
				$URL = $URL . "&deleteReturn=fail2" ;
				header("Location: {$URL}");
				break ;
			}
			
			if ($result->rowCount()!=1) {
				//Fail 2
				$URL = $URL . "&deleteReturn=fail2" ;
				header("Location: {$URL}");
			}
			else {
				//Write to database
				try {
					$data=array("ibPYPGlossaryID"=>$ibPYPGlossaryID);  
					$sql="DELETE FROM ibPYPGlossary WHERE ibPYPGlossaryID=:ibPYPGlossaryID" ;
					$result=$connection2->prepare($sql);
					$result->execute($data);  
				}
				catch(PDOException $e) { 
					//Fail 2
					$URL = $URL . "&deleteReturn=fail2" ;
					header("Location: {$URL}");
					break ;
				}

				//Success 0
				$URLDelete = $URLDelete . "&deleteReturn=success0" ;
				header("Location: {$URLDelete}");
			}
		}
	}
}
?>
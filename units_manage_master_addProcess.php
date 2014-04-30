<?php
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

@session_start() ;
$_SESSION[$guid]["ibPYPUnitsTab"]=1 ;

//Set timezone from session variable
date_default_timezone_set($_SESSION[$guid]["timezone"]);

$gibbonSchoolYearID=$_GET["gibbonSchoolYearID"] ;
$URL=$_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/" . getModuleName($_POST["address"]) . "/units_manage_master_add.php&gibbonSchoolYearID=$gibbonSchoolYearID" ;

if (isActionAccessible($guid, $connection2, "/modules/IB PYP/units_manage_master_add.php")==FALSE) {

	//Fail 0
	$URL=$URL . "&addReturn=fail0" ;
	header("Location: {$URL}");
}
else {
	//Check if school year specified
	if ($gibbonSchoolYearID=="") {
		//Fail1
		$URL=$URL . "&updateReturn=fail1" ;
		header("Location: {$URL}");
	}
	else {
		$role=getRole($_SESSION[$guid]["gibbonPersonID"], $connection2) ;
		if ($role!="Coordinator" AND $role!="Teacher (Curriculum)") {
			//Fail 0
			$URL=$URL . "&addReturn=fail0" ;
			header("Location: {$URL}");
		}
		else {
			//Proceed!
			$gibbonPersonIDCreator=$_SESSION[$guid]["gibbonPersonID"] ;
			$timestamp=date("Y-m-d H:i:s") ;
			$name=$_POST["unitname"] ;
			$active=$_POST["active"] ;
			$gibbonCourseID=$_POST["gibbonCourseID"] ;
			$theme=$_POST["theme"] ;
			$centralIdea=$_POST["centralIdea"] ;
			$summativeAssessment=$_POST["summativeAssessment"] ;
			$relatedConcepts=$_POST["relatedConcepts"] ;
			$linesOfInquiry=$_POST["linesOfInquiry"] ;
			$teacherQuestions=$_POST["teacherQuestions"] ;
			$provocation=$_POST["provocation"] ;
			$preAssessment=$_POST["preAssessment"] ;
			$formativeAssessment=$_POST["formativeAssessment"] ;
			$resources=$_POST["resources"] ;
			$action=$_POST["action"] ;
			$environments=$_POST["environments"] ;
		
			if ($name=="" OR $active=="") {
				//Fail 3
				$URL=$URL . "&addReturn=fail3" ;
				header("Location: {$URL}");
			}
			else {
				//Lock table
				try {
					$data=array();  
					$sql="LOCK TABLES ibPYPUnitMaster WRITE, ibPYPUnitMasterBlock WRITE, ibPYPUnitMasterSmartBlock WRITE" ;
					$result=$connection2->prepare($sql);
					$result->execute($data);  
				}
				catch(PDOException $e) { 
					//Fail 2
					$URL=$URL . "&addReturn=fail2" ;
					header("Location: {$URL}");
					break ;
				}
			
				//Get next autoincrement
				try {
					$dataAI=array();  
					$sqlAI="SHOW TABLE STATUS LIKE 'ibPYPUnitMaster'";
					$resultAI=$connection2->prepare($sqlAI);
					$resultAI->execute($dataAI);
				}
				catch(PDOException $e) { 
					//Fail 2
					$URL=$URL . "&addReturn=fail2" ;
					header("Location: {$URL}");
					break ;
				}

				if ($resultAI->rowCount()!=1) {
					//Fail 2
					$URL=$URL . "&addReturn=fail2" ;
					header("Location: {$URL}");
					break ;
				}
				else {
					$rowAI=$resultAI->fetch();
					$AI=str_pad($rowAI['Auto_increment'], 10, "0", STR_PAD_LEFT) ;
				
					$partialFail=false ;
				
					//Insert outcomes
					$count=0 ;
					if (isset($_POST["outcomeorder"])) {
						if (count($_POST["outcomeorder"])>0) {
							foreach ($_POST["outcomeorder"] AS $outcome) {
								if ($_POST["outcomeibPYPGlossaryID$outcome"]!="") {
									try {
										$dataInsert=array("AI"=>$AI, "gibbonOutcomeID"=>$_POST["outcomeibPYPGlossaryID$outcome"], "content"=>$_POST["outcomecontents$outcome"], "count"=>$count);  
										$sqlInsert="INSERT INTO ibPYPUnitMasterBlock SET ibPYPUnitMasterID=:AI, gibbonOutcomeID=:gibbonOutcomeID, content=:content, sequenceNumber=:count" ;
										$resultInsert=$connection2->prepare($sqlInsert);
										$resultInsert->execute($dataInsert);
									}
									catch(PDOException $e) {
										$partialFail=true ;
									}
								}
								$count++ ;
							}	
						}
					}
				
					//Insert key concepts
					$count=0 ;
					if (isset($_POST["conceptorder"])) {
						if (count($_POST["conceptorder"])>0) {
							foreach ($_POST["conceptorder"] AS $concept) {
								if ($_POST["conceptibPYPGlossaryID$concept"]!="") {
									try {
										$dataInsert=array("AI"=>$AI, "ibPYPGlossaryID"=>$_POST["conceptibPYPGlossaryID$concept"], "content"=>$_POST["conceptcontents$concept"], "count"=>$count);  
										$sqlInsert="INSERT INTO ibPYPUnitMasterBlock SET ibPYPUnitMasterID=:AI, ibPYPGlossaryID=:ibPYPGlossaryID, content=:content, sequenceNumber=:count" ;
										$resultInsert=$connection2->prepare($sqlInsert);
										$resultInsert->execute($dataInsert);
									}
									catch(PDOException $e) { 
										$partialFail=true ;
									}
								}
								$count++ ;
							}	
						}
					}
				
					//Insert trans skills
					$count=0 ;
					if (isset($_POST["skillsorder"])) {
						if (count($_POST["skillsorder"])>0) {
							foreach ($_POST["skillsorder"] AS $skill) {
								if ($_POST["skillsibPYPGlossaryID$skill"]!="") {
									try {
										$dataInsert=array("AI"=>$AI, "ibPYPGlossaryID"=>$_POST["skillsibPYPGlossaryID$skill"], "content"=>$_POST["skillscontents$skill"], "count"=>$count);  
										$sqlInsert="INSERT INTO ibPYPUnitMasterBlock SET ibPYPUnitMasterID=:AI, ibPYPGlossaryID=:ibPYPGlossaryID, content=:content, sequenceNumber=:count" ;
										$resultInsert=$connection2->prepare($sqlInsert);
										$resultInsert->execute($dataInsert);
									}
									catch(PDOException $e) { 
										$partialFail=true ;
									}
								}
								$count++ ;
							}	
						}
					}
				
					//Insert learner profiles
					$count=0 ;
					if (isset($_POST["learnerProfileorder"])) {
						if (count($_POST["learnerProfileorder"])>0) {
							foreach ($_POST["learnerProfileorder"] AS $learnerProfile) {
								if ($_POST["learnerProfileibPYPGlossaryID$learnerProfile"]!="") {
									try {
										$dataInsert=array("AI"=>$AI, "ibPYPGlossaryID"=>$_POST["learnerProfileibPYPGlossaryID$learnerProfile"], "content"=>$_POST["learnerProfilecontents$learnerProfile"], "count"=>$count);  
										$sqlInsert="INSERT INTO ibPYPUnitMasterBlock SET ibPYPUnitMasterID=:AI, ibPYPGlossaryID=:ibPYPGlossaryID, content=:content, sequenceNumber=:count" ;
										$resultInsert=$connection2->prepare($sqlInsert);
										$resultInsert->execute($dataInsert);
									}
									catch(PDOException $e) { 
										$partialFail=true ;
									}
								}
								$count++ ;
							}	
						}
					}
				
					//ADD BLOCKS
					$blockCount=($_POST["blockCount"]-1) ;
					$sequenceNumber=0 ;
					if ($blockCount>0) {
						if (isset($_POST["order"])) {
							$order=$_POST["order"] ;
							foreach ($order as $i) {
								$title="";
								if ($_POST["title$i"]!="Block $i") {
									$title=$_POST["title$i"] ;
								}
								$type2="";
								if ($_POST["type$i"]!="type (e.g. discussion, outcome)") {
									$type2=$_POST["type$i"];
								}
								$length="";
								if ($_POST["length$i"]!="length (min)") {
									$length=$_POST["length$i"];
								}
								$contents=$_POST["contents$i"];
								$teachersNotes=$_POST["teachersNotes$i"];
								
								if ($title!="" OR $contents!="") {
									try {
										$dataBlock=array("ibPYPUnitMasterID"=>$AI, "title"=>$title, "type"=>$type2, "length"=>$length, "contents"=>$contents, "teachersNotes"=>$teachersNotes, "sequenceNumber"=>$sequenceNumber); 
										$sqlBlock="INSERT INTO ibPYPUnitMasterSmartBlock SET ibPYPUnitMasterID=:ibPYPUnitMasterID, title=:title, type=:type, length=:length, contents=:contents, teachersNotes=:teachersNotes, sequenceNumber=:sequenceNumber" ;
										$resultBlock=$connection2->prepare($sqlBlock);
										$resultBlock->execute($dataBlock);
									}
									catch(PDOException $e) { 
										print "here" . $e->getMessage() ;
										$partialFail=TRUE ;
									}
									$sequenceNumber++ ;
								}
							}
						}
					}
				
					//Write to database
					try {
						$data=array("gibbonPersonIDCreator"=>$gibbonPersonIDCreator, "timestamp"=>$timestamp, "name"=>$name, "active"=>$active, "gibbonCourseID"=>$gibbonCourseID, "theme"=>$theme, "centralIdea"=>$centralIdea, "summativeAssessment"=>$summativeAssessment, "relatedConcepts"=>$relatedConcepts, "linesOfInquiry"=>$linesOfInquiry, "teacherQuestions"=>$teacherQuestions, "provocation"=>$provocation, "preAssessment"=>$preAssessment, "formativeAssessment"=>$formativeAssessment, "resources"=>$resources, "action"=>$action, "environments"=>$environments );  
						$sql="INSERT INTO ibPYPUnitMaster SET gibbonPersonIDCreator=:gibbonPersonIDCreator, timestamp=:timestamp, name=:name, active=:active, gibbonCourseID=:gibbonCourseID, theme=:theme, centralIdea=:centralIdea, summativeAssessment=:summativeAssessment, relatedConcepts=:relatedConcepts, linesOfInquiry=:linesOfInquiry, teacherQuestions=:teacherQuestions, provocation=:provocation, preAssessment=:preAssessment, formativeAssessment=:formativeAssessment, resources=:resources, action=:action, environments=:environments" ;
						$result=$connection2->prepare($sql);
						$result->execute($data);  
					}
					catch(PDOException $e) { 
						//Fail 2
						$URL=$URL . "&addReturn=fail2" ;
						header("Location: {$URL}");
						break ;
					}
				
					//Unlock module table
					try {
						$sql="UNLOCK TABLES" ;
						$result=$connection2->query($sql);    
					}
					catch(PDOException $e) { }
				
					if ($partialFail==true) {
						//Fail 5
						$URL=$URL . "&addReturn=fail5" ;
						header("Location: {$URL}");
					}
					else {
						//Success 0
						$URL=$URL . "&addReturn=success0" ;
						header("Location: {$URL}");
					}
				}
			}	
		}
	}
}
?>
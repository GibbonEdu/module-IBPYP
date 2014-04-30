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
$_SESSION[$guid]["ibPYPUnitsTab"]=0 ;

//Set timezone from session variable
date_default_timezone_set($_SESSION[$guid]["timezone"]);

$ibPYPUnitWorkingID=$_GET["ibPYPUnitWorkingID"] ;
$gibbonSchoolYearID=$_GET["gibbonSchoolYearID"] ;
$step=$_POST["step"] ;
if ($step!=1 AND $step!=2) {
	$step=1 ;
}
$URL=$_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/" . getModuleName($_POST["address"]) . "/units_manage_working_edit.php&ibPYPUnitWorkingID=$ibPYPUnitWorkingID&gibbonSchoolYearID=$gibbonSchoolYearID" ;

if (isActionAccessible($guid, $connection2, "/modules/IB PYP/units_manage_working_edit.php")==FALSE) {
	//Fail 0
	$URL=$URL . "&updateReturn=fail0&step=1" ;
	header("Location: {$URL}");
}
else {
	//Proceed!
	//Check if school year specified
	if ($ibPYPUnitWorkingID=="" OR $gibbonSchoolYearID=="") {
		//Fail1
		$URL=$URL . "&updateReturn=fail1&step=1" ;
		header("Location: {$URL}");
	}
	else {
		try {
			$data=array("ibPYPUnitWorkingID"=>$ibPYPUnitWorkingID);  
			$sql="SELECT * FROM ibPYPUnitWorking WHERE ibPYPUnitWorkingID=:ibPYPUnitWorkingID" ;
			$result=$connection2->prepare($sql);
			$result->execute($data);
		}
		catch(PDOException $e) { 
			//Fail2
			$URL=$URL . "&updateReturn=fail2&step=1" ;
			header("Location: {$URL}");
			break ;
		}
		
		if ($result->rowCount()!=1) {
			//Fail 2
			$URL=$URL . "&updateReturn=fail2&step=1" ;
			header("Location: {$URL}");
		}
		else {
			$row=$result->fetch() ;
			$ibPYPUnitMasterID=$row["ibPYPUnitMasterID"] ;
			
			if ($step==1) {
				//Validate Inputs
				$dateStart=dateConvert($_POST["dateStart"]);
				if ($_POST["dateStart"]=="") {
					$dateStart=NULL ;
				}
				$gibbonRubricID=$_POST["gibbonRubricID"];
				if ($gibbonRubricID=="") {
					$gibbonRubricID=NULL ;
				}
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
				
				//Delete all blocks
				try {
					$dataDelete=array("ibPYPUnitWorkingID"=>$ibPYPUnitWorkingID);  
					$sqlDelete="DELETE FROM ibPYPUnitWorkingBlock WHERE ibPYPUnitWorkingID=:ibPYPUnitWorkingID" ;
					$resultDelete=$connection2->prepare($sqlDelete);
					$resultDelete->execute($dataDelete);  
				}
				catch(PDOException $e) { 
					//Fail2
					$URL=$URL . "&updateReturn=fail2" ;
					header("Location: {$URL}");
					break ;
				}
				
				//Insert outcomes
				$count=0 ;
				if (isset($_POST["outcomeorder"])) {
					if (count($_POST["outcomeorder"])>0) {
						foreach ($_POST["outcomeorder"] AS $outcome) {
							if ($_POST["outcomeibPYPGlossaryID$outcome"]!="") {
								try {
									$dataInsert=array("ibPYPUnitWorkingID"=>$ibPYPUnitWorkingID, "gibbonOutcomeID"=>$_POST["outcomeibPYPGlossaryID$outcome"], "content"=>$_POST["outcomecontents$outcome"], "count"=>$count);  
									$sqlInsert="INSERT INTO ibPYPUnitWorkingBlock SET ibPYPUnitWorkingID=:ibPYPUnitWorkingID, gibbonOutcomeID=:gibbonOutcomeID, content=:content, sequenceNumber=:count" ;
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
									$dataInsert=array("ibPYPUnitWorkingID"=>$ibPYPUnitWorkingID, "ibPYPGlossaryID"=>$_POST["conceptibPYPGlossaryID$concept"], "content"=>$_POST["conceptcontents$concept"], "count"=>$count);  
									$sqlInsert="INSERT INTO ibPYPUnitWorkingBlock SET ibPYPUnitWorkingID=:ibPYPUnitWorkingID, ibPYPGlossaryID=:ibPYPGlossaryID, content=:content, sequenceNumber=:count" ;
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
									$dataInsert=array("ibPYPUnitWorkingID"=>$ibPYPUnitWorkingID, "ibPYPGlossaryID"=>$_POST["skillsibPYPGlossaryID$skill"], "content"=>$_POST["skillscontents$skill"], "count"=>$count);  
									$sqlInsert="INSERT INTO ibPYPUnitWorkingBlock SET ibPYPUnitWorkingID=:ibPYPUnitWorkingID, ibPYPGlossaryID=:ibPYPGlossaryID, content=:content, sequenceNumber=:count" ;
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
				if (isset($_POST["skillsorder"])) {
					if (count($_POST["learnerProfileorder"])>0) {
						foreach ($_POST["learnerProfileorder"] AS $learnerProfile) {
							if ($_POST["learnerProfileibPYPGlossaryID$learnerProfile"]!="") {
								try {
									$dataInsert=array("ibPYPUnitWorkingID"=>$ibPYPUnitWorkingID, "ibPYPGlossaryID"=>$_POST["learnerProfileibPYPGlossaryID$learnerProfile"], "content"=>$_POST["learnerProfilecontents$learnerProfile"], "count"=>$count);  
									$sqlInsert="INSERT INTO ibPYPUnitWorkingBlock SET ibPYPUnitWorkingID=:ibPYPUnitWorkingID, ibPYPGlossaryID=:ibPYPGlossaryID, content=:content, sequenceNumber=:count" ;
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
				
				//Update smart blocks
				$order=NULL ;
				if (isset($_POST["order"])) {
					$order=$_POST["order"] ;
				}
				$sequenceNumber=0 ;
				$dataRemove=array() ;
				$whereRemove="" ;
				if (count($order)<0) {
					//Fail 3
					$URL=$URL . "&updateReturn=fail3" ;
					header("Location: {$URL}");
				}
				else {
					if (is_array($order)) {
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
							$ibPYPUnitMasterSmartBlockID=NULL ;
							if (isset($_POST["ibPYPUnitMasterSmartBlockID$i"])) {
								$ibPYPUnitMasterSmartBlockID=$_POST["ibPYPUnitMasterSmartBlockID$i"];
							}
							
							if ($ibPYPUnitMasterSmartBlockID!="") {
								try {
									$dataBlock=array("ibPYPUnitMasterID"=>$ibPYPUnitMasterID, "title"=>$title, "type"=>$type2, "length"=>$length, "contents"=>$contents, "teachersNotes"=>$teachersNotes, "sequenceNumber"=>$sequenceNumber, "ibPYPUnitMasterSmartBlockID"=>$ibPYPUnitMasterSmartBlockID); 
									$sqlBlock="UPDATE ibPYPUnitMasterSmartBlock SET ibPYPUnitMasterID=:ibPYPUnitMasterID, title=:title, type=:type, length=:length, contents=:contents, teachersNotes=:teachersNotes, sequenceNumber=:sequenceNumber WHERE ibPYPUnitMasterSmartBlockID=:ibPYPUnitMasterSmartBlockID" ;
									$resultBlock=$connection2->prepare($sqlBlock);
									$resultBlock->execute($dataBlock);
								}
								catch(PDOException $e) { 
									$partialFail=TRUE ;
								}
								$dataRemove["ibPYPUnitMasterSmartBlockID$sequenceNumber"]=$ibPYPUnitMasterSmartBlockID ;
								$whereRemove.="AND NOT ibPYPUnitMasterSmartBlockID=:ibPYPUnitMasterSmartBlockID$sequenceNumber " ;
							}
							else {
								try {
									$dataBlock=array("ibPYPUnitMasterID"=>$ibPYPUnitMasterID, "title"=>$title, "type"=>$type2, "length"=>$length, "contents"=>$contents, "teachersNotes"=>$teachersNotes, "sequenceNumber"=>$sequenceNumber); 
									$sqlBlock="INSERT INTO ibPYPUnitMasterSmartBlock SET ibPYPUnitMasterID=:ibPYPUnitMasterID, title=:title, type=:type, length=:length, contents=:contents, teachersNotes=:teachersNotes, sequenceNumber=:sequenceNumber" ;
									$resultBlock=$connection2->prepare($sqlBlock);
									$resultBlock->execute($dataBlock);
								}
								catch(PDOException $e) {
									print $e->getMessage() ; 
									$partialFail=TRUE ;
								}
								$dataRemove["ibPYPUnitMasterSmartBlockID$sequenceNumber"]=$connection2->lastInsertId() ;
								$whereRemove.="AND NOT ibPYPUnitMasterSmartBlockID=:ibPYPUnitMasterSmartBlockID$sequenceNumber " ;
							}
							
							$sequenceNumber++ ;
						}
					}
				}
				
				//Remove orphaned smart blocks
				if ($whereRemove!="(") {
					try {
						$dataRemove["ibPYPUnitMasterID"]=$ibPYPUnitMasterID ; 
						$sqlRemove="DELETE FROM ibPYPUnitMasterSmartBlock WHERE ibPYPUnitMasterID=:ibPYPUnitMasterID $whereRemove" ;
						$resultRemove=$connection2->prepare($sqlRemove);
						$resultRemove->execute($dataRemove);
					}
					catch(PDOException $e) { 
						$partialFail=TRUE ;
					}
				}
			
				//Write to database
				try {
					$data=array("dateStart"=>$dateStart, "gibbonRubricID"=>$gibbonRubricID, "theme"=>$theme, "centralIdea"=>$centralIdea, "summativeAssessment"=>$summativeAssessment, "relatedConcepts"=>$relatedConcepts, "linesOfInquiry"=>$linesOfInquiry, "teacherQuestions"=>$teacherQuestions, "provocation"=>$provocation, "preAssessment"=>$preAssessment, "formativeAssessment"=>$formativeAssessment, "resources"=>$resources, "action"=>$action, "environments"=>$environments, "ibPYPUnitWorkingID"=>$ibPYPUnitWorkingID);  
					$sql="UPDATE ibPYPUnitWorking SET dateStart=:dateStart, gibbonRubricID=:gibbonRubricID, theme=:theme, centralIdea=:centralIdea, summativeAssessment=:summativeAssessment, relatedConcepts=:relatedConcepts, linesOfInquiry=:linesOfInquiry, teacherQuestions=:teacherQuestions, provocation=:provocation, preAssessment=:preAssessment, formativeAssessment=:formativeAssessment, resources=:resources, action=:action, environments=:environments WHERE ibPYPUnitWorkingID=:ibPYPUnitWorkingID" ;
					$result=$connection2->prepare($sql);
					$result->execute($data);  
				}
				catch(PDOException $e) { 
					//Fail 2
					$URL=$URL . "&updateReturn=fail2" ;
					header("Location: {$URL}");
					break ;
				}
				
				//Success 0
				$URL=$URL . "&updateReturn=success0" ;
				header("Location: {$URL}");
			}
			else if ($step==2) {
				//Validate Inputs
				$assessOutcomes=$_POST["assessOutcomes"] ;
				$assessmentImprovements=$_POST["assessmentImprovements"] ;
				$ideasThemes=$_POST["ideasThemes"] ;
				$learningExperiencesConcepts=$_POST["learningExperiencesConcepts"] ;
				$learningExperiencesTransSkills=$_POST["learningExperiencesTransSkills"] ;
				$learningExperiencesProfileAttitudes=$_POST["learningExperiencesProfileAttitudes"] ;
				$inquiriesQuestions=$_POST["inquiriesQuestions"] ;
				$questionsProvocations=$_POST["questionsProvocations"] ;
				$studentInitAction=$_POST["studentInitAction"] ;
				$teachersNotes=$_POST["teachersNotes"] ;
				
				
				//Write to database
				try {
					$data=array("assessOutcomes"=>$assessOutcomes, "assessmentImprovements"=>$assessmentImprovements, "ideasThemes"=>$ideasThemes, "learningExperiencesConcepts"=>$learningExperiencesConcepts, "learningExperiencesTransSkills"=>$learningExperiencesTransSkills, "learningExperiencesProfileAttitudes"=>$learningExperiencesProfileAttitudes, "inquiriesQuestions"=>$inquiriesQuestions, "questionsProvocations"=>$questionsProvocations, "studentInitAction"=>$studentInitAction, "teachersNotes"=>$teachersNotes, "ibPYPUnitWorkingID"=>$ibPYPUnitWorkingID);  
					$sql="UPDATE ibPYPUnitWorking SET assessOutcomes=:assessOutcomes, assessmentImprovements=:assessmentImprovements, ideasThemes=:ideasThemes, learningExperiencesConcepts=:learningExperiencesConcepts, learningExperiencesTransSkills=:learningExperiencesTransSkills, learningExperiencesProfileAttitudes=:learningExperiencesProfileAttitudes, inquiriesQuestions=:inquiriesQuestions, questionsProvocations=:questionsProvocations, studentInitAction=:studentInitAction, teachersNotes=:teachersNotes WHERE ibPYPUnitWorkingID=:ibPYPUnitWorkingID" ;
					$result=$connection2->prepare($sql);
					$result->execute($data);
				}
				catch(PDOException $e) { 
					//Fail 5
					$URL=$URL . "&updateReturn=fail5&step=2" ;
					header("Location: {$URL}");
					break ;
				}

				//Success 0
				$URL=$URL . "&updateReturn=success0&step=2" ;
				header("Location: {$URL}");
			}
		}
	}
}
?>
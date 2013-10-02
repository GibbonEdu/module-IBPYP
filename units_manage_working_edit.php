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

session_start() ;
$_SESSION[$guid]["ibPYPUnitsTab"]=0 ;

//Module includes
include "./modules/IB PYP/moduleFunctions.php" ;

if (isActionAccessible($guid, $connection2, "/modules/IB PYP/units_manage_working_edit.php")==FALSE) {

	//Acess denied
	print "<div class='error'>" ;
		print "You do not have access to this action." ;
	print "</div>" ;
}
else {
	print "<div class='trail'>" ;
	print "<div class='trailHead'><a href='" . $_SESSION[$guid]["absoluteURL"] . "'>Home</a> > <a href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/" . getModuleName($_GET["q"]) . "/" . getModuleEntry($_GET["q"], $connection2, $guid) . "'>" . getModuleName($_GET["q"]) . "</a> > <a href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/" . getModuleName($_GET["q"]) . "/units_manage.php&gibbonSchoolYearID=" . $_GET["gibbonSchoolYearID"] .  "'>Manage Units</a> > </div><div class='trailEnd'>Edit Working Unit</div>" ;
	print "</div>" ;
	
	$updateReturn = $_GET["updateReturn"] ;
	$updateReturnMessage ="" ;
	$class="error" ;
	if (!($updateReturn=="")) {
		if ($updateReturn=="fail0") {
			$updateReturnMessage ="Update failed because you do not have access to this action." ;	
		}
		else if ($updateReturn=="fail2") {
			$updateReturnMessage ="Update failed due to a database error." ;	
		}
		else if ($updateReturn=="fail3") {
			$updateReturnMessage ="Update failed because your inputs were invalid." ;	
		}
		else if ($updateReturn=="fail5") {
			$updateReturnMessage ="Update succeeded, but there were problems saving one or more items." ;	
		}
		else if ($updateReturn=="success0") {
			$updateReturnMessage ="Update was successful." ;	
			$class="success" ;
		}
		print "<div class='$class'>" ;
			print $updateReturnMessage;
		print "</div>" ;
	} 
	
	$role=getRole($_SESSION[$guid]["gibbonPersonID"], $connection2) ;
	$ibPYPUnitWorkingID=$_GET["ibPYPUnitWorkingID"];
	$gibbonSchoolYearID=$_GET["gibbonSchoolYearID"] ;
	if ($ibPYPUnitWorkingID=="" OR $gibbonSchoolYearID=="") {
		print "<div class='error'>" ;
			print "You have not specified a unit." ;
		print "</div>" ;
	}
	else {
		try {
			if ($role=="Coordinator" OR $role=="Teacher (Curriculum)") {
				$data=array("gibbonSchoolYearID"=>$gibbonSchoolYearID, "ibPYPUnitWorkingID"=>$ibPYPUnitWorkingID);  
				$sql="SELECT ibPYPUnitWorking.*, gibbonCourse.nameShort AS course, gibbonCourseClass.nameShort AS class, gibbonYearGroupIDList, gibbonDepartmentID FROM ibPYPUnitWorking JOIN gibbonCourseClass ON (ibPYPUnitWorking.gibbonCourseClassID=gibbonCourseClass.gibbonCourseClassID) JOIN gibbonCourse ON (gibbonCourse.gibbonCourseID=gibbonCourseClass.gibbonCourseID) WHERE gibbonCourse.gibbonSchoolYearID=:gibbonSchoolYearID AND ibPYPUnitWorkingID=:ibPYPUnitWorkingID ORDER BY ibPYPUnitWorking.name" ; 
			}
			else {
				$data=array("gibbonSchoolYearID"=>$_SESSION[$guid]["gibbonSchoolYearID"], "gibbonPersonID"=>$_SESSION[$guid]["gibbonPersonID"], "ibPYPUnitWorkingID"=>$ibPYPUnitWorkingID);  
				$sql="SELECT DISTINCT ibPYPUnitWorking.*, gibbonCourse.nameShort AS course, gibbonCourseClass.nameShort AS class, gibbonYearGroupIDList, gibbonDepartmentID FROM ibPYPUnitWorking JOIN gibbonCourseClass ON (ibPYPUnitWorking.gibbonCourseClassID=gibbonCourseClass.gibbonCourseClassID) JOIN gibbonCourse ON (ibPYPUnitWorking.gibbonCourseID=gibbonCourse.gibbonCourseID) JOIN gibbonCourseClassPerson ON (gibbonCourseClassPerson.gibbonCourseClassID=gibbonCourseClass.gibbonCourseClassID) WHERE gibbonCourse.gibbonSchoolYearID=:gibbonSchoolYearID AND gibbonCourseClassPerson.gibbonPersonID=:gibbonPersonID AND ibPYPUnitWorkingID=:ibPYPUnitWorkingID ORDER BY name" ; 
			}
			$result=$connection2->prepare($sql);
			$result->execute($data);
		}
		catch(PDOException $e) { 
			print "<div class='error'>" . $e->getMessage() . "</div>" ; 
		}
		
		if ($result->rowCount()!=1) {
			print "<div class='error'>" ;
				print "The selected unit does not exist." ;
			print "</div>" ;
		}
		else {
			//Let's go!
			$row=$result->fetch() ;
			$gibbonYearGroupIDList=$row["gibbonYearGroupIDList"] ;
			$gibbonDepartmentID=$row["gibbonDepartmentID"] ;
									
			
			$step=$_GET["step"] ;
			if ($step!=1 AND $step!=2) {
				$step=1 ;
			}
			
			if ($step==1) {
				?>
				<form method="post" action="<? print $_SESSION[$guid]["absoluteURL"] . "/modules/IB PYP/units_manage_working_editProcess.php?ibPYPUnitWorkingID=$ibPYPUnitWorkingID&gibbonSchoolYearID=$gibbonSchoolYearID" ?>">
					<table class='smallIntBorder' cellspacing='0' style="width: 100%;">	
						<? $bg="#EAEBEC" ; ?>
						<tr class='break'>
							<td colspan=3> 
								<div class='linkTop'>
									<a href='<? print $_SESSION[$guid]["absoluteURL"] ?>/index.php?q=/modules/IB PYP/units_manage_working_edit.php&ibPYPUnitWorkingID=<? print $ibPYPUnitWorkingID ?>&step=2&gibbonSchoolYearID=<? print $gibbonSchoolYearID ?>'>Jump to Reflection</a>
								</div>
								<h3 class='top'>Step 1 - Planning</h3><br/>
							</td>
						</tr>
						<tr>
							<td style='background: none!important; background-color: <? print $bg ?>!important'></td> 
							<td> 
								<b>Unit Name *</b><br/>
							</td>
							<td class="right">
								<input readonly name="unitname" id="unitname" maxlength=50 value="<? print htmlPrep($row["name"]) ?>" type="text" style="width: 300px">
							</td>
						</tr>
						<tr>
							<td style='background: none!important; background-color: <? print $bg ?>!important'></td> 
							<td> 
								<b>Class</b><br/>
							</td>
							<td class="right">
								<?
								print "<input readonly name='classes' id='classes' value='" . $row["course"] . "." . $row["class"] . "' type='text' style='width: 300px'>" ;			
								?>
							</td>
						</tr>
						<tr>
							<td style='background: none!important; background-color: <? print $bg ?>!important'></td> 
							<td colspan=2> 
								<br/><b>Planning</b><br/>
								<a href='#1'>1. What is our purpose?</a><br/>
								<a href='#2'>2. What do we want to learn?</a><br/>
								<a href='#3'>3. How might we know what we have learned?</a><br/>
								<a href='#4'>4. How best might we learn?</a><br/>
								<a href='#5'>5. What resources need to be gathered?</a><br/>
							</td>
						</tr>
						
						<? $bg="#EDC951" ; ?>
						<tr class='break'>
							<td style='background: none!important; background-color: <? print $bg ?>!important'></td> 
							<td colspan=2> 
								<a id='1'>
								<h3>1. What Is Our Purpose?</h3><br/>
							</td>
						</tr>
						<tr>
							<td style='background: none!important; background-color: <? print $bg ?>!important'></td> 
							<td colspan=2> 
								<div style='font-weight: bold; text-decoration: underline; font-size: 130%'>Transdisciplinary Theme</div> 
								<? print getEditor($guid,  $connection2, "theme", $row["theme"], 30 ) ?>
							</td>
						</tr>
						<tr>
							<td style='background: none!important; background-color: <? print $bg ?>!important'></td> 
							<td colspan=2> 
								<div style='margin-top: 40px; font-weight: bold; text-decoration: underline; font-size: 130%'>Central Idea</div> 
								<? print getEditor($guid,  $connection2, "centralIdea", $row["centralIdea"], 30 ) ?>
							</td>
						</tr>
						<tr>
							<td style='background: none!important; background-color: <? print $bg ?>!important'></td> 
							<td colspan=2> 
								<div style='margin-top: 40px;font-weight: bold; text-decoration: underline; font-size: 130%'>Outcomes</div> 
								<p>What would you like students to accomplish in this unit? These outcomes are drawn from the system-wide collection stored in the Planner module.</p>
							</td>
						</tr>
						<? 
						$type="outcome" ;
						$allowOutcomeEditing=getSettingByScope($connection2, "Planner", "allowOutcomeEditing") ;
						$categories=array() ;
						$categoryCount=0 ;
						?> 
						<style>
							#<? print $type ?> { list-style-type: none; margin: 0; padding: 0; width: 100%; }
							#<? print $type ?> div.ui-state-default { margin: 0 0px 5px 0px; padding: 5px; font-size: 100%; min-height: 72px; }
							div.ui-state-default_dud { margin: 5px 0px 5px 0px; padding: 5px; font-size: 100%; min-height: 72px; }
							html>body #<? print $type ?> li { min-height: 72px; line-height: 1.2em; }
							.<? print $type ?>-ui-state-highlight { margin-bottom: 5px; min-height: 72px; line-height: 1.2em; width: 100%; }
							.<? print $type ?>-ui-state-highlight {border: 1px solid #fcd3a1; background: #fbf8ee url(images/ui-bg_glass_55_fbf8ee_1x400.png) 50% 50% repeat-x; color: #444444; }
						</style>
						<script>
							$(function() {
								$( "#<? print $type ?>" ).sortable({
									placeholder: "<? print $type ?>-ui-state-highlight";
									axis: 'y'
								});
							});
						</script>
						<tr>
							<td style='background: none!important; background-color: <? print $bg ?>!important'></td> 
							<td colspan=2> 
								<div class="outcome" id="outcome" style='width: 100%; padding: 5px 0px 0px 0px; min-height: 72px'>
									<?
									try {
										$dataBlocks=array("ibPYPUnitWorkingID"=>$ibPYPUnitWorkingID);  
										$sqlBlocks="SELECT ibPYPUnitWorkingBlock.*, scope, name, category FROM ibPYPUnitWorkingBlock JOIN gibbonOutcome ON (ibPYPUnitWorkingBlock.gibbonOutcomeID=gibbonOutcome.gibbonOutcomeID) WHERE ibPYPUnitWorkingID=:ibPYPUnitWorkingID AND active='Y' ORDER BY sequenceNumber" ;
										$resultBlocks=$connection2->prepare($sqlBlocks);
										$resultBlocks->execute($dataBlocks);
									}
									catch(PDOException $e) { 
										print "<div class='error'>" . $e->getMessage() . "</div>" ; 
									}
									if ($resultBlocks->rowCount()<1) {
										print "<div id='outcomeOuter0'>" ;
											print "<div style='color: #ddd; font-size: 230%; margin: 15px 0 0 6px'>Outcomes listed here...</div>" ;
										print "</div>" ;
									}
									else {
										$usedArrayFill="" ;
										$i=1 ;
										while ($rowBlocks=$resultBlocks->fetch()) {
											pypMakeBlock($guid, $i, "outcome", $rowBlocks["gibbonOutcomeID"],  $rowBlocks["name"],  $rowBlocks["category"], $rowBlocks["content"],"",TRUE, $allowOutcomeEditing) ;
											$usedArrayFill.="\"" . $rowBlocks["gibbonOutcomeID"] . "\"," ;
											$i++ ;
										}
									}
									?>
								</div>
								<div style='width: 100%; padding: 0px 0px 0px 0px; border-bottom: 1px solid #333'>
									<div class="ui-state-default_dud" style='padding: 0px; height: 60px'>
										<table cellspacing='0' style='width: 100%'>
											<tr>
												<td style='width: 50%'>
													<script type="text/javascript">
														<? if (is_numeric($i)==FALSE) { $i=0 ; } ?>
														var outcomeCount=<? print $i ?> ;
														/* Unit type control */
														$(document).ready(function(){
															$("#new").click(function(){
																
															 });
														});
													</script>
													<select id='newOutcome' onChange='outcomeDisplayElements(this.value);' style='float: none; margin-left: 3px; margin-top: 0px; margin-bottom: 3px; width: 350px'>
														<option class='all' value='0'>Choose an outcome to add it to this unit</option>
														<?
														$currentCategory="" ;
														$lastCategory="" ;
														$switchContents="" ;
														try {
															$countClause=0 ;
															$years=explode(",", $gibbonYearGroupIDList) ;
															$dataSelect=array();  
															$sqlSelect="" ;
															foreach ($years as $year) {
																$dataSelect["clause" . $countClause]="%" . $year . "%" ;
																$sqlSelect.="(SELECT * FROM gibbonOutcome WHERE active='Y' AND scope='School' AND gibbonYearGroupIDList LIKE :clause" . $countClause . ") UNION " ;
																$countClause++ ;
															}
															$resultSelect=$connection2->prepare(substr($sqlSelect,0,-6) . "ORDER BY category, name");
															$resultSelect->execute($dataSelect);
														}
														catch(PDOException $e) { 
															print "<div class='error'>" . $e->getMessage() . "</div>" ; 
														}
														print "<optgroup label='--SCHOOL OUTCOMES--'>" ;
														while ($rowSelect=$resultSelect->fetch()) {
															$currentCategory=$rowSelect["category"] ;
															if (($currentCategory!=$lastCategory) AND $currentCategory!="") {
																print "<optgroup label='--" . $currentCategory . "--'>" ;
																print "<option class='$currentCategory' value='0'>Choose an outcome to add it to this unit</option>" ;
																$categories[$categoryCount]= $currentCategory ;
																$categoryCount++ ;
															}
															print "<option class='all " . $rowSelect["category"] . "'  value='" . $rowSelect["gibbonOutcomeID"] . "'>" . $rowSelect["name"] . "</option>" ;
															$switchContents.="case \"" . $rowSelect["gibbonOutcomeID"] . "\": " ;
															$switchContents.="$(\"#outcome\").append('<div id=\'outcomeBlockOuter' + outcomeCount + '\'><img style=\'margin: 10px 0 5px 0\' src=\'" . $_SESSION[$guid]["absoluteURL"] . "/themes/Default/img/loading.gif\' alt=\'Loading\' onclick=\'return false;\' /><br/>Loading</div>');" ;
															$switchContents.="$(\"#outcomeBlockOuter\" + outcomeCount).load(\"" . $_SESSION[$guid]["absoluteURL"] . "/modules/IB%20PYP/units_manage_add_blockAjax.php\",\"type=outcome&id=\" + outcomeCount + \"&title=" . urlencode($rowSelect["name"]) . "\&category=" . urlencode($rowSelect["category"]) . "&ibPYPGlossaryID=" . urlencode($rowSelect["gibbonOutcomeID"]) . "&contents=" . urlencode($rowSelect["description"]) . "&allowOutcomeEditing=" . urlencode($allowOutcomeEditing) . "\") ;" ;
															$switchContents.="outcomeCount++ ;" ;
															$switchContents.="$('#newOutcome').val('0');" ;
															$switchContents.="break;" ;
															$lastCategory=$rowSelect["category"] ;
														}
														
														$currentCategory="" ;
														$lastCategory="" ;
														$currentLA="" ;
														$lastLA="" ;
														try {
															$countClause=0 ;
															$years=explode(",", $gibbonYearGroupIDList) ;
															$dataSelect=array("gibbonDepartmentID"=>$gibbonDepartmentID); 
															$sqlSelect="" ;
															foreach ($years as $year) {
																$dataSelect["clause" . $countClause]="%" . $year . "%" ;
																$sqlSelect.="(SELECT gibbonOutcome.*, gibbonDepartment.name AS learningArea FROM gibbonOutcome JOIN gibbonDepartment ON (gibbonOutcome.gibbonDepartmentID=gibbonDepartment.gibbonDepartmentID) WHERE active='Y' AND scope='Learning Area' AND gibbonDepartment.gibbonDepartmentID=:gibbonDepartmentID AND gibbonYearGroupIDList LIKE :clause" . $countClause . ") UNION " ;
																$countClause++ ;
															}
															$resultSelect=$connection2->prepare(substr($sqlSelect,0,-6) . "ORDER BY learningArea, category, name");
															$resultSelect->execute($dataSelect);
														}
														catch(PDOException $e) { 
															print "<div class='error'>" . $e->getMessage() . "</div>" ; 
														}
														while ($rowSelect=$resultSelect->fetch()) {
															$currentCategory=$rowSelect["category"] ;
															$currentLA=$rowSelect["learningArea"] ;
															if (($currentLA!=$lastLA) AND $currentLA!="") {
																print "<optgroup label='--" . strToUpper($currentLA) . " OUTCOMES--'>" ;
															}
															if (($currentCategory!=$lastCategory) AND $currentCategory!="") {
																print "<optgroup label='--" . $currentCategory . "--'>" ;
																print "<option class='$currentCategory' value='0'>Choose an outcome to add it to this unit</option>" ;
																$categories[$categoryCount]= $currentCategory ;
																$categoryCount++ ;
															}
															print "<option class='all " . $rowSelect["category"] . "'  value='" . $rowSelect["gibbonOutcomeID"] . "'>" . $rowSelect["name"] . "</option>" ;
															$switchContents.="case \"" . $rowSelect["gibbonOutcomeID"] . "\": " ;
															$switchContents.="$(\"#outcome\").append('<div id=\'outcomeBlockOuter' + outcomeCount + '\'><img style=\'margin: 10px 0 5px 0\' src=\'" . $_SESSION[$guid]["absoluteURL"] . "/themes/Default/img/loading.gif\' alt=\'Loading\' onclick=\'return false;\' /><br/>Loading</div>');" ;
															$switchContents.="$(\"#outcomeBlockOuter\" + outcomeCount).load(\"" . $_SESSION[$guid]["absoluteURL"] . "/modules/IB%20PYP/units_manage_add_blockAjax.php\",\"type=outcome&id=\" + outcomeCount + \"&title=" . urlencode($rowSelect["name"]) . "\&category=" . urlencode($rowSelect["category"]) . "&ibPYPGlossaryID=" . urlencode($rowSelect["gibbonOutcomeID"]) . "&contents=" . urlencode($rowSelect["description"]) . "&allowOutcomeEditing=" . urlencode($allowOutcomeEditing) . "\") ;" ;
															$switchContents.="outcomeCount++ ;" ;
															$switchContents.="$('#newOutcome').val('0');" ;
															$switchContents.="break;" ;
															$lastCategory=$rowSelect["category"] ;
															$lastLA=$rowSelect["learningArea"] ;
														}
														
														?>
													</select><br/>
													<?
													if (count($categories)>0) {
														?>
														<select id='outcomeFilter' style='float: none; margin-left: 3px; margin-top: 0px; width: 350px'>
															<option value='all'>View All</option>
															<?
															$categories=array_unique($categories) ;
															$categories=msort($categories) ;
															foreach ($categories AS $category) {
																print "<option value='$category'>$category</option>" ;
															}
															?>
														</select>
														<script type="text/javascript">
															$("#newOutcome").chainedTo("#outcomeFilter");
														</script>
														<?
													}
													?>
													<script type='text/javascript'>
														var <? print $type ?>Used=new Array(<? print substr($usedArrayFill,0,-1) ?>);
														var <? print $type ?>UsedCount=0 ;
															
														function outcomeDisplayElements(number) {
															$("#<? print $type ?>Outer0").css("display", "none") ;
															if (<? print $type ?>Used.indexOf(number)<0) {
																<? print $type ?>Used[<? print $type ?>UsedCount]=number ;
																<? print $type ?>UsedCount++ ;
																switch(number) {
																	<? print $switchContents ?>
																}
															}
															else {
																alert("This element has already been selected!") ;
																$('#newOutcome').val('0');
															}
														}
													</script>
												</td>
											</tr>
										</table>
									</div>
								</div>
							</td>
						</tr>
						<tr>
							<td style='background: none!important; background-color: <? print $bg ?>!important'></td> 
							<td colspan=2> 
								<div style='margin-top: 40px; font-weight: bold; text-decoration: underline; font-size: 130%'>Summative Assessment</div>
								<p>What are the possible ways of assessing students’ understanding of the central idea? What evidence, including student initiated actions will we look for?</p>
								<? print getEditor($guid,  $connection2, "summativeAssessment", $row["summativeAssessment"], 30, TRUE ) ?>
							</td>
						</tr>
						
						<? $bg="#6A4A3C" ; ?>
						<tr class='break'>
							<td style='background: none!important; background-color: <? print $bg ?>!important'></td> 
							<td colspan=2> 
								<a id='2'>
								<h3>2. What Do We Want To Learn?</h3><br/>
							</td>
						</tr>
						<tr>
							<td style='background: none!important; background-color: <? print $bg ?>!important'></td> 
							<td colspan=2> 
								<div style='font-weight: bold; text-decoration: underline; font-size: 130%'>Key Concepts</div> 
								<p>What are the key concepts to be emphasized within this inquiry?</p>
							</td>
						</tr>
						
						<? $type="concept" ; ?> 
						<style>
							#<? print $type ?> { list-style-type: none; margin: 0; padding: 0; width: 100%; }
							#<? print $type ?> div.ui-state-default { margin: 0 0px 5px 0px; padding: 5px; font-size: 100%; min-height: 72px; }
							div.ui-state-default_dud { margin: 5px 0px 5px 0px; padding: 5px; font-size: 100%; min-height: 72px; }
							html>body #<? print $type ?> li { min-height: 72px; line-height: 1.2em; }
							.<? print $type ?>-ui-state-highlight { margin-bottom: 5px; min-height: 72px; line-height: 1.2em; width: 100%; }
							.<? print $type ?>-ui-state-highlight {border: 1px solid #fcd3a1; background: #fbf8ee url(images/ui-bg_glass_55_fbf8ee_1x400.png) 50% 50% repeat-x; color: #444444; }
						</style>
						<script>
							$(function() {
								$( "#<? print $type ?>" ).sortable({
									placeholder: "<? print $type ?>-ui-state-highlight";
									axis: 'y'
								});
							});
						</script>
						<tr>
							<td style='background: none!important; background-color: <? print $bg ?>!important'></td> 
							<td colspan=2> 
								<div class="concept" id="concept" style='width: 100%; padding: 5px 0px 0px 0px; min-height: 72px'>
									<?
									try {
										$dataBlocks=array("ibPYPUnitWorkingID"=>$ibPYPUnitWorkingID);  
										$sqlBlocks="SELECT ibPYPUnitWorkingBlock.*, type, title, category FROM ibPYPUnitWorkingBlock JOIN ibPYPGlossary ON (ibPYPUnitWorkingBlock.ibPYPGlossaryID=ibPYPGlossary.ibPYPGlossaryID) WHERE ibPYPUnitWorkingID=:ibPYPUnitWorkingID AND type='Concept' ORDER BY sequenceNumber" ;
										$resultBlocks=$connection2->prepare($sqlBlocks);
										$resultBlocks->execute($dataBlocks);
									}
									catch(PDOException $e) { 
										print "<div class='error'>" . $e->getMessage() . "</div>" ; 
									}
									if ($resultBlocks->rowCount()<1) {
										print "<div id='conceptOuter0'>" ;
											print "<div style='color: #ddd; font-size: 230%; margin: 15px 0 0 6px'>Key concepts listed here...</div>" ;
										print "</div>" ;
									}
									else {
										$usedArrayFill="" ;
										$i=1 ;
										while ($rowBlocks=$resultBlocks->fetch()) {
											pypMakeBlock($guid, $i, "concept", $rowBlocks["ibPYPGlossaryID"],  $rowBlocks["title"],  $rowBlocks["category"], $rowBlocks["content"]) ;
											$usedArrayFill.="\"" . $rowBlocks["ibPYPGlossaryID"] . "\"," ;
											$i++ ;
										}
									}
									?>
								</div>
								<div style='width: 100%; padding: 0px 0px 0px 0px; border-bottom: 1px solid #333'>
									<div class="ui-state-default_dud" style='padding: 0px; height: 60px'>
										<table cellspacing='0' style='width: 100%'>
											<tr>
												<td style='width: 50%'>
													<script type="text/javascript">
														<? if (is_numeric($i)==FALSE) { $i=0 ; } ?>
														var conceptCount=<? print $i ?> ;
														/* Unit type control */
														$(document).ready(function(){
															$("#new").click(function(){
																
															 });
														});
													</script>
													<select id='newConcept' onChange='conceptDisplayElements(this.value);' style='float: none; margin-left: 3px; margin-top: 0px; width: 350px'>
														<option value='0'>Chose a concept to add it to this unit</option>
														<?
														$currentCategory="" ;
														$lastCategory="" ;
														$switchContents="" ;
														try {
															$dataSelect=array();  
															$sqlSelect="SELECT * FROM ibPYPGlossary WHERE type='Concept' ORDER BY category, title" ;
															$resultSelect=$connection2->prepare($sqlSelect);
															$resultSelect->execute($dataSelect);
														}
														catch(PDOException $e) { }
														while ($rowSelect=$resultSelect->fetch()) {
															$currentCategory=$rowSelect["category"] ;
															if (($currentCategory!=$lastCategory) AND $currentCategory!="") {
																print "<optgroup label='--" . $currentCategory . "--'>" ;
															}
															print "<option value='" . $rowSelect["ibPYPGlossaryID"] . "'>" . $rowSelect["title"] . "</option>" ;
															$switchContents.="case \"" . $rowSelect["ibPYPGlossaryID"] . "\": " ;
															$switchContents.="$(\"#concept\").append('<div id=\'conceptBlockOuter' + conceptCount + '\'><img style=\'margin: 10px 0 5px 0\' src=\'" . $_SESSION[$guid]["absoluteURL"] . "/themes/Default/img/loading.gif\' alt=\'Loading\' onclick=\'return false;\' /><br/>Loading</div>');" ;
															$switchContents.="$(\"#conceptBlockOuter\" + conceptCount).load(\"" . $_SESSION[$guid]["absoluteURL"] . "/modules/IB%20PYP/units_manage_add_blockAjax.php\",\"type=concept&id=\" + conceptCount + \"&title=" . urlencode($rowSelect["title"]) . "\&category=" . urlencode($rowSelect["category"]) . "&ibPYPGlossaryID=" . urlencode($rowSelect["ibPYPGlossaryID"]) . "&contents=" . urlencode($rowSelect["content"]) . "\") ;" ;
															$switchContents.="conceptCount++ ;" ;
															$switchContents.="$('#newConcept').val('0');" ;
															$switchContents.="break;" ;
															$lastCategory=$rowSelect["category"] ;
														}
														?>
													</select>
													<script type='text/javascript'>
														var <? print $type ?>Used=new Array(<? print substr($usedArrayFill,0,-1) ?>);
														var <? print $type ?>UsedCount=0 ;
															
														function conceptDisplayElements(number) {
															$("#<? print $type ?>Outer0").css("display", "none") ;
															if (<? print $type ?>Used.indexOf(number)<0) {
																<? print $type ?>Used[<? print $type ?>UsedCount]=number ;
																<? print $type ?>UsedCount++ ;
																switch(number) {
																	<? print $switchContents ?>
																}
															}
															else {
																alert("This element has already been selected!") ;
																$('#newConcept').val('0');
															}
														}
													</script>
												</td>
											</tr>
										</table>
									</div>
								</div>
							</td>
						</tr>
						
						<tr>
							<td style='background: none!important; background-color: <? print $bg ?>!important'></td> 
							<td colspan=2> 
								<div style='margin-top: 40px; font-weight: bold; text-decoration: underline; font-size: 130%'>Related Concepts</div> 
								<p>What are the concepts that are related to this inquiry?</p>
								<? print getEditor($guid,  $connection2, "relatedConcepts", $row["relatedConcepts"], 10 ) ?>
							</td>
						</tr>
						
						<tr>
							<td style='background: none!important; background-color: <? print $bg ?>!important'></td> 
							<td colspan=2> 
								<div style='margin-top: 40px; font-weight: bold; text-decoration: underline; font-size: 130%'>Lines of Inquiry</div> 
								<p>What lines of inquiry will define the scope of the inquiry into the central idea?</p>
								<? print getEditor($guid,  $connection2, "linesOfInquiry", $row["linesOfInquiry"], 10 ) ?>
							</td>
						</tr>
						<tr>
							<td style='background: none!important; background-color: <? print $bg ?>!important'></td> 
							<td colspan=2> 
								<div style='margin-top: 40px; font-weight: bold; text-decoration: underline; font-size: 130%'>Teacher Questions<br/></div>
								<p>What teacher questions will drive these inquiries?<br/><br/></p>
								<? print getEditor($guid,  $connection2, "teacherQuestions", $row["teacherQuestions"], 10 ) ?>
							</td>
						</tr>
						
						<tr>
							<td style='background: none!important; background-color: <? print $bg ?>!important'></td> 
							<td colspan=2> 
								<div style='margin-top: 40px; font-weight: bold; text-decoration: underline; font-size: 130%'>Provocation</div> 
								<? print getEditor($guid,  $connection2, "provocation", $row["provocation"], 30, true, false, false, true, "purpose=Provocation", true ) ?>
							</td>
						</tr>
						
						<? $bg="#00A0B0" ; ?>
						<tr class='break'>
							<td style='background: none!important; background-color: <? print $bg ?>!important'></td> 
							<td colspan=2>  
								<a id='3'>
								<h3>3. How Might We Know What We Have Learned?</h3><br/>
							</td>
						</tr>
						<tr>
							<td style='background: none!important; background-color: <? print $bg ?>!important'></td> 
							<td colspan=2> 
								<div style='font-weight: bold; text-decoration: underline; font-size: 130%'>Assessing Prior Knowledge & Skills</div> 
								<p>What are the possible ways of assessing students’ prior knowledge and skills? What evidence will we look for? </p>
								<? print getEditor($guid,  $connection2, "preAssessment", $row["preAssessment"], 30, true, false, false, true, "purpose=Assessment%20Aid", true ) ?>
							</td>
						</tr>
						<tr>
							<td style='background: none!important; background-color: <? print $bg ?>!important'></td> 
							<td colspan=2> 
								<div style='margin-top: 40px; font-weight: bold; text-decoration: underline; font-size: 130%'>Formative Assessment</div> 
								<p>What are the possible ways of assessing student learning in the context of the lines of inquiry? What evidence will we look for?</p>
								<? print getEditor($guid,  $connection2, "formativeAssessment", $row["formativeAssessment"], 30, true, false, false, true, "purpose=Assessment%20Aid", true ) ?>
							</td>
						</tr>
						
						<? $bg="#C44D58" ; ?>
						<tr class='break'>
							<td style='background: none!important; background-color: <? print $bg ?>!important'></td> 
							<td colspan=2>  
								<a id='4'>
								<h3>4. How Best Might We Learn?</h3><br/>
							</td>
						</tr>
						<tr>
							<td style='background: none!important; background-color: <? print $bg ?>!important'></td> 
							<td colspan=2> 
								<div style='font-weight: bold; text-decoration: underline; font-size: 130%'>Learning Experiences</div> 
							</td>
						</tr>
						<tr>
							<td style='background: none!important; background-color: <? print $bg ?>!important'></td> 
							<td colspan=2> 
								<p style='color: black'>Smart content blocks are Gibbon's way of helping you organise and manage the content in your units. <b>These blocks are shared across the master unit, and all of it's working units: so, changes here are collaborative, and will impact other version of this unit.</p>
								<style>
									#sortable { list-style-type: none; margin: 0; padding: 0; width: 100%; }
									#sortable div.ui-state-default { margin: 0 0px 5px 0px; padding: 5px; font-size: 100%; min-height: 72px; }
									div.ui-state-default_dud { margin: 5px 0px 5px 0px; padding: 5px; font-size: 100%; min-height: 72px; }
									html>body #sortable li { min-height: 72px; line-height: 1.2em; }
									.ui-state-highlight { margin-bottom: 5px; min-height: 72px; line-height: 1.2em; width: 100%; }
								</style>
								<script>
									$(function() {
										$( "#sortable" ).sortable({
											placeholder: "ui-state-highlight", 
											axis: 'y'
										});
									});
								</script>
								
								
								<div class="sortable" id="sortable" style='width: 100%; padding: 5px 0px 0px 0px; border-top: 1px solid #333; border-bottom: 1px solid #333'>
									<? 
									try {
										$dataBlocks=array("ibPYPUnitWorkingID"=>$ibPYPUnitWorkingID); 
										$sqlBlocks="SELECT ibPYPUnitMasterSmartBlock.* FROM ibPYPUnitMasterSmartBlock JOIN ibPYPUnitMaster ON (ibPYPUnitMasterSmartBlock.ibPYPUnitMasterID=ibPYPUnitMaster.ibPYPUnitMasterID) JOIN ibPYPUnitWorking ON (ibPYPUnitWorking.ibPYPUnitMasterID=ibPYPUnitMaster.ibPYPUnitMasterID) WHERE ibPYPUnitWorkingID=:ibPYPUnitWorkingID ORDER BY sequenceNumber" ;
										$resultBlocks=$connection2->prepare($sqlBlocks);
										$resultBlocks->execute($dataBlocks);
									}
									catch(PDOException $e) { 
										print "<div class='error'>" . $e->getMessage() . "</div>" ; 
									}
									$i=1 ;
									while ($rowBlocks=$resultBlocks->fetch()) {
										makeBlock($guid, $connection2, $i, "masterEdit", $rowBlocks["title"], $rowBlocks["type"], $rowBlocks["length"], $rowBlocks["contents"], "N", $rowBlocks["ibPYPUnitMasterSmartBlockID"], "", $rowBlocks["teachersNotes"]) ;
										$i++ ;
									}
									?>
								</div>
								<div style='width: 100%; padding: 0px 0px 0px 0px; border-bottom: 1px solid #333'>
									<div class="ui-state-default_dud" style='padding: 0px;'>
										<table cellspacing='0' style='width: 100%'>
											<tr>
												<td style='width: 50%'>
													<script type="text/javascript">
														var count=<? print ($resultBlocks->rowCount()+1) ?> ;
														$(document).ready(function(){
															$("#new").click(function(){
																$("#sortable").append('<div id=\'blockOuter' + count + '\'><img style=\'margin: 10px 0 5px 0\' src=\'<? print $_SESSION[$guid]["absoluteURL"] ?>/themes/Default/img/loading.gif\' alt=\'Loading\' onclick=\'return false;\' /><br/>Loading</div>');
																$("#blockOuter" + count).load("<? print $_SESSION[$guid]["absoluteURL"] ?>/modules/Planner/units_add_blockAjax.php","id=" + count + "&mode=masterAdd") ;
																count++ ;
															 });
														});
													</script>
													<div id='new' style='cursor: default; float: none; border: 1px dotted #aaa; background: none; margin-left: 3px; color: #999; margin-top: 0px; font-size: 140%; font-weight: bold; width: 350px'>Click to create a new block</div><br/>
												</td>
											</tr>
										</table>
									</div>
								</div>
							</td>
						</tr>
						<tr>
							<td style='background: none!important; background-color: <? print $bg ?>!important'></td> 
							<td colspan=2> 
								<div style='margin-top: 40px; font-weight: bold; text-decoration: underline; font-size: 130%'>Transdisciplinary Skills</div> 
								<p>What opportunities will occur for transdisciplinary skills?</p>
							</td>
						</tr>
						<tr>
							<td style='background: none!important; background-color: <? print $bg ?>!important'></td> 
							<? $type="skills" ; ?> 
							<style>
								#<? print $type ?> { list-style-type: none; margin: 0; padding: 0; width: 100%; }
								#<? print $type ?> div.ui-state-default { margin: 0 0px 5px 0px; padding: 5px; font-size: 100%; min-height: 72px; }
								div.ui-state-default_dud { margin: 5px 0px 5px 0px; padding: 5px; font-size: 100%; min-height: 72px; }
								html>body #<? print $type ?> li { min-height: 72px; line-height: 1.2em; }
								.<? print $type ?>-ui-state-highlight { margin-bottom: 5px; min-height: 72px; line-height: 1.2em; width: 100%; }
								.<? print $type ?>-ui-state-highlight {border: 1px solid #fcd3a1; background: #fbf8ee url(images/ui-bg_glass_55_fbf8ee_1x400.png) 50% 50% repeat-x; color: #444444; }
							</style>
							<script>
								$(function() {
									$( "#<? print $type ?>" ).sortable({
										placeholder: "<? print $type ?>-ui-state-highlight";
										axis: 'y'
									});
								});
							</script>
							<td colspan=2> 
								<div class="skills" id="skills" style='width: 100%; padding: 5px 0px 0px 0px; min-height: 72px'>
									<?
									try {
										$dataBlocks=array("ibPYPUnitWorkingID"=>$ibPYPUnitWorkingID);  
										$sqlBlocks="SELECT ibPYPUnitWorkingBlock.*, type, title, category FROM ibPYPUnitWorkingBlock JOIN ibPYPGlossary ON (ibPYPUnitWorkingBlock.ibPYPGlossaryID=ibPYPGlossary.ibPYPGlossaryID) WHERE ibPYPUnitWorkingID=:ibPYPUnitWorkingID AND type='Transdisciplinary Skill' ORDER BY sequenceNumber" ;
										$resultBlocks=$connection2->prepare($sqlBlocks);
										$resultBlocks->execute($dataBlocks);
									}
									catch(PDOException $e) { 
										print "<div class='error'>" . $e->getMessage() . "</div>" ; 
									}
									if ($resultBlocks->rowCount()<1) {
										print "<div id='skillsOuter0'>" ;
											print "<div style='color: #ddd; font-size: 230%; margin: 15px 0 0 6px'>Transdisciplinary Skills listed here...</div>" ;
										print "</div>" ;
									}
									else {
										$usedArrayFill="" ;
										$i=1 ;
										while ($rowBlocks=$resultBlocks->fetch()) {
											pypMakeBlock($guid, $i, "skills", $rowBlocks["ibPYPGlossaryID"],  $rowBlocks["title"],  $rowBlocks["category"], $rowBlocks["content"]) ;
											$usedArrayFill.="\"" . $rowBlocks["ibPYPGlossaryID"] . "\"," ;
											$i++ ;
										}
									}
									?>
								</div>
								<div style='width: 100%; padding: 0px 0px 0px 0px; border-bottom: 1px solid #333'>
									<div class="ui-state-default_dud" style='padding: 0px; height: 60px'>
										<table cellspacing='0' style='width: 100%'>
											<tr>
												<td style='width: 50%'>
													<script type="text/javascript">
														<? if (is_numeric($i)==FALSE) { $i=0 ; } ?>
														var skillsCount=<? print $i ?> ;
														/* Unit type control */
														$(document).ready(function(){
															$("#new").click(function(){
																
															 });
														});
													</script>
													<select id='newSkill' onChange='skillsDisplayElements(this.value);' style='float: none; margin-left: 3px; margin-top: 0px; width: 350px'>
														<option value='0'>Chose a skill to add it to this unit</option>
														<?
														$currentCategory="" ;
														$lastCategory="" ;
														$switchContents="" ;
														try {
															$dataSelect=array();  
															$sqlSelect="SELECT * FROM ibPYPGlossary WHERE type='Transdisciplinary Skill' ORDER BY category, title" ;
															$resultSelect=$connection2->prepare($sqlSelect);
															$resultSelect->execute($dataSelect);
														}
														catch(PDOException $e) { }
														while ($rowSelect=$resultSelect->fetch()) {
															$currentCategory=$rowSelect["category"] ;
															if (($currentCategory!=$lastCategory) AND $currentCategory!="") {
																print "<optgroup label='--" . $currentCategory . "--'>" ;
															}
															print "<option value='" . $rowSelect["ibPYPGlossaryID"] . "'>" . $rowSelect["title"] . "</option>" ;
															$switchContents.="case \"" . $rowSelect["ibPYPGlossaryID"] . "\": " ;
															$switchContents.="$(\"#skills\").append('<div id=\'skillsBlockOuter' + skillsCount + '\'><img style=\'margin: 10px 0 5px 0\' src=\'" . $_SESSION[$guid]["absoluteURL"] . "/themes/Default/img/loading.gif\' alt=\'Loading\' onclick=\'return false;\' /><br/>Loading</div>');" ;
															$switchContents.="$(\"#skillsBlockOuter\" + skillsCount).load(\"" . $_SESSION[$guid]["absoluteURL"] . "/modules/IB%20PYP/units_manage_add_blockAjax.php\",\"type=skills&id=\" + skillsCount + \"&title=" . urlencode($rowSelect["title"]) . "\&category=" . urlencode($rowSelect["category"]) . "&ibPYPGlossaryID=" . urlencode($rowSelect["ibPYPGlossaryID"]) . "&contents=" . urlencode($rowSelect["content"]) . "\") ;" ;
															$switchContents.="skillsCount++ ;" ;
															$switchContents.="$('#newSkill').val('0');" ;
															$switchContents.="break;" ;
															$lastCategory=$rowSelect["category"] ;
														}
														?>
													</select>
													<script type='text/javascript'>
														var <? print $type ?>Used=new Array(<? print substr($usedArrayFill,0,-1) ?>);
														var <? print $type ?>UsedCount=0 ;
															
														function skillsDisplayElements(number) {
															$("#<? print $type ?>Outer0").css("display", "none") ;
															if (<? print $type ?>Used.indexOf(number)<0) {
																<? print $type ?>Used[<? print $type ?>UsedCount]=number ;
																<? print $type ?>UsedCount++ ;
																switch(number) {
																	<? print $switchContents ?>
																}
															}
															else {
																alert("This element has already been selected!") ;
																$('#newSkill').val('0');
															}
														}
													</script>
												</td>
											</tr>
										</table>
									</div>
								</div>
							</td>
						</tr>
						<tr>
							<td style='background: none!important; background-color: <? print $bg ?>!important'></td> 
							<td colspan=2> 
								<div style='margin-top: 40px; font-weight: bold; text-decoration: underline; font-size: 130%'>Learner Profile & Attitudes</div> 
								<p>What opportunity will occur for the development of the attributes of the learner profile and attitudes?</p>
							</td>
						</tr>
						<tr>
							<td style='background: none!important; background-color: <? print $bg ?>!important'></td> 
							<? $type="learnerProfile" ; ?> 
							<style>
								#<? print $type ?> { list-style-type: none; margin: 0; padding: 0; width: 100%; }
								#<? print $type ?> div.ui-state-default { margin: 0 0px 5px 0px; padding: 5px; font-size: 100%; min-height: 72px; }
								div.ui-state-default_dud { margin: 5px 0px 5px 0px; padding: 5px; font-size: 100%; min-height: 72px; }
								html>body #<? print $type ?> li { min-height: 72px; line-height: 1.2em; }
								.<? print $type ?>-ui-state-highlight { margin-bottom: 5px; min-height: 72px; line-height: 1.2em; width: 100%; }
								.<? print $type ?>-ui-state-highlight {border: 1px solid #fcd3a1; background: #fbf8ee url(images/ui-bg_glass_55_fbf8ee_1x400.png) 50% 50% repeat-x; color: #444444; }
							</style>
							<script>
								$(function() {
									$( "#<? print $type ?>" ).sortable({
										placeholder: "<? print $type ?>-ui-state-highlight";
										axis: 'y'
									});
								});
							</script>
							<td colspan=2> 
								<div class="learnerProfile" id="learnerProfile" style='width: 100%; padding: 5px 0px 0px 0px; min-height: 72px'>
									<?
									try {
										$dataBlocks=array("ibPYPUnitWorkingID"=>$ibPYPUnitWorkingID);  
										$sqlBlocks="SELECT ibPYPUnitWorkingBlock.*, type, title, category FROM ibPYPUnitWorkingBlock JOIN ibPYPGlossary ON (ibPYPUnitWorkingBlock.ibPYPGlossaryID=ibPYPGlossary.ibPYPGlossaryID) WHERE ibPYPUnitWorkingID=:ibPYPUnitWorkingID AND (type='Attitude' OR type='Learner Profile') ORDER BY sequenceNumber" ;
										$resultBlocks=$connection2->prepare($sqlBlocks);
										$resultBlocks->execute($dataBlocks);
									}
									catch(PDOException $e) { 
										print "<div class='error'>" . $e->getMessage() . "</div>" ; 
									}
									if ($resultBlocks->rowCount()<1) {
										print "<div id='learnerProfileOuter0'>" ;
											print "<div style='color: #ddd; font-size: 230%; margin: 15px 0 0 6px'>Learner Profile & Attitudes listed here...</div>" ;
										print "</div>" ;
									}
									else {
										$usedArrayFill="" ;
										$i=1 ;
										while ($rowBlocks=$resultBlocks->fetch()) {
											pypMakeBlock($guid, $i, "learnerProfile", $rowBlocks["ibPYPGlossaryID"],  $rowBlocks["title"],  $rowBlocks["category"], $rowBlocks["content"]) ;
											$usedArrayFill.="\"" . $rowBlocks["ibPYPGlossaryID"] . "\"," ;
											$i++ ;
										}
									}
									?>
								</div>
								<div style='width: 100%; padding: 0px 0px 0px 0px; border-bottom: 1px solid #333'>
									<div class="ui-state-default_dud" style='padding: 0px; height: 60px'>
										<table cellspacing='0' style='width: 100%'>
											<tr>
												<td style='width: 50%'>
													<script type="text/javascript">
														<? if (is_numeric($i)==FALSE) { $i=0 ; } ?>
														var learnerProfileCount=<? print $i ?> ;
														/* Unit type control */
														$(document).ready(function(){
															$("#new").click(function(){
																
															 });
														});
													</script>
													<select id='newLearnerProfile' onChange='learnerProfileDisplayElements(this.value);' style='float: none; margin-left: 3px; margin-top: 0px; width: 350px'>
														<option value='0'>Chose a learner profile or attitude to add it to this unit</option>
														<?
														$currentType="" ;
														$lastType="" ;
														$switchContents="" ;
														try {
															$dataSelect=array();  
															$sqlSelect="SELECT * FROM ibPYPGlossary WHERE type='Learner Profile' OR type='Attitude' ORDER BY type, category, title" ;
															$resultSelect=$connection2->prepare($sqlSelect);
															$resultSelect->execute($dataSelect);
														}
														catch(PDOException $e) { }
														while ($rowSelect=$resultSelect->fetch()) {
															$currentType=$rowSelect["type"] ;
															if (($currentType!=$lastType) AND $currentType!="") {
																print "<optgroup label='--" . $currentType . "--'>" ;
															}
															print "<option value='" . $rowSelect["ibPYPGlossaryID"] . "'>" . $rowSelect["title"] . "</option>" ;
															$switchContents.="case \"" . $rowSelect["ibPYPGlossaryID"] . "\": " ;
															$switchContents.="$(\"#learnerProfile\").append('<div id=\'learnerProfileBlockOuter' + learnerProfileCount + '\'><img style=\'margin: 10px 0 5px 0\' src=\'" . $_SESSION[$guid]["absoluteURL"] . "/themes/Default/img/loading.gif\' alt=\'Loading\' onclick=\'return false;\' /><br/>Loading</div>');" ;
															$switchContents.="$(\"#learnerProfileBlockOuter\" + learnerProfileCount).load(\"" . $_SESSION[$guid]["absoluteURL"] . "/modules/IB%20PYP/units_manage_add_blockAjax.php\",\"type=learnerProfile&id=\" + learnerProfileCount + \"&title=" . urlencode($rowSelect["title"]) . "\&category=" . urlencode($rowSelect["category"]) . "&ibPYPGlossaryID=" . urlencode($rowSelect["ibPYPGlossaryID"]) . "&contents=" . urlencode($rowSelect["content"]) . "\") ;" ;
															$switchContents.="learnerProfileCount++ ;" ;
															$switchContents.="$('#newLearnerProfile').val('0');" ;
															$switchContents.="break;" ;
															$lastType=$rowSelect["type"] ;
														}
														?>
													</select>
													<script type='text/javascript'>
														var <? print $type ?>Used=new Array(<? print substr($usedArrayFill,0,-1) ?>);
														var <? print $type ?>UsedCount=0 ;
															
														function learnerProfileDisplayElements(number) {
															$("#<? print $type ?>Outer0").css("display", "none") ;
															if (<? print $type ?>Used.indexOf(number)<0) {
																<? print $type ?>Used[<? print $type ?>UsedCount]=number ;
																<? print $type ?>UsedCount++ ;
																switch(number) {
																	<? print $switchContents ?>
																}
															}
															else {
																alert("This element has already been selected!") ;
																$('#newLearnerProfile').val('0');
															}
														}
													</script>
													
												</td>
											</tr>
										</table>
									</div>
								</div>
							</td>
						</tr>
						
						<? $bg="#EB6841" ; ?>
						<tr class='break'>
							<td style='background: none!important; background-color: <? print $bg ?>!important'></td> 
							<td colspan=2>  
								<a id='5'>
								<h3>5. What Resources Need To Be Gathered?</h3><br/>
							</td>
						</tr>
						<tr>
							<td style='background: none!important; background-color: <? print $bg ?>!important'></td> 
							<td colspan=2> 
								<div style='font-weight: bold; text-decoration: underline; font-size: 130%'>Resources</div> 
								<p>What people, places, audio-visual materials, related literature, music, art, computer software etc will be available?</p>
								<? print getEditor($guid,  $connection2, "resources", $row["resources"], 30, true, false, false, true, "", true) ?>
							</td>
						</tr>
						<tr>
							<td style='background: none!important; background-color: <? print $bg ?>!important'></td> 
							<td colspan=2> 
								<div style='margin-top: 40px; font-weight: bold; text-decoration: underline; font-size: 130%'>Action</div> 
								<p>What possible action could be inspired by this inquiry?</p>
								<? print getEditor($guid,  $connection2, "action", $row["action"], 30, false, false, false, true, "", true) ?>
							</td>
						</tr>
						<tr>
							<td style='background: none!important; background-color: <? print $bg ?>!important'></td> 
							<td colspan=2> 
								<div style='margin-top: 40px; font-weight: bold; text-decoration: underline; font-size: 130%'>Classroom Environment</div> 
								<p>How will the classroom environment, local environment and or community be used to facilitate the inquiry? </p>
								<? print getEditor($guid,  $connection2, "environments", $row["environments"], 30 ) ?>
							</td>
						</tr>
						
						<tr>
							<td colspan=2>
								<span style="font-size: 90%"><i>* denotes a required field</i></span>
							</td>
							<td class="right">
								<input type="hidden" name="address" value="<? print $_SESSION[$guid]["address"] ?>">
								<input type="hidden" name="step" value="1">
								<input type="reset" value="Reset"> <input type="submit" value="Submit">
							</td>
						</tr>
					</table>
				</form>
				<?
			}
			else if ($step==2) {
				?>
				<form method="post" action="<? print $_SESSION[$guid]["absoluteURL"] . "/modules/IB PYP/units_manage_working_editProcess.php?ibPYPUnitWorkingID=$ibPYPUnitWorkingID&gibbonSchoolYearID=$gibbonSchoolYearID" ?>">
					<table class='smallIntBorder' cellspacing='0' style="width: 100%;">	
						<? $bg="#EAEBEC" ; ?>
						<tr class='break'>
							<td style='background: none!important; background-color: <? print $bg ?>!important'></td> 
							<td colspan=2>  
								<div class='linkTop'>
									<a href='<? print $_SESSION[$guid]["absoluteURL"] ?>/index.php?q=/modules/IB PYP/units_manage_working_edit.php&ibPYPUnitWorkingID=<? print $ibPYPUnitWorkingID ?>&step=1&gibbonSchoolYearID=<? print $gibbonSchoolYearID ?>'>Back to Planning</a>
								</div>
								<h3 class='top'>Step 2 - Reflection</h3><br/>
							</td>
						</tr>
						<tr>
							<td style='background: none!important; background-color: <? print $bg ?>!important'></td> 
							<td> 
								<b>Unit Name *</b><br/>
							</td>
							<td class="right">
								<input readonly name="unitname" id="unitname" maxlength=50 value="<? print htmlPrep($row["name"]) ?>" type="text" style="width: 300px">
							</td>
						</tr>
						<tr>
							<td style='background: none!important; background-color: <? print $bg ?>!important'></td> 
							<td> 
								<b>Class</b><br/>
							</td>
							<td class="right">
								<?
								print "<input readonly name='classes' id='classes' value='" . $row["course"] . "." . $row["class"] . "' type='text' style='width: 300px'>" ;			
								?>
							</td>
						</tr>
						<tr>
							<td style='background: none!important; background-color: <? print $bg ?>!important'></td> 
							<td colspan=2>  
								<br/><b>Reflection</b><br/>
								<a href='#6'>6. To what extent did we achieve our purpose?</a><br/>
								<a href='#7'>7. To what extent did we include the elements of the PYP?</a><br/>
								<a href='#8'>8. What student-initiated inquiries arose from the learning?</a><br/>
								<a href='#9'>9. Teacher's Notes</a><br/>
							</td>
						</tr>
						
						<? $bg="#EDC951" ; ?>
						<tr class='break'>
							<td style='background: none!important; background-color: <? print $bg ?>!important'></td> 
							<td colspan=2>  
								<a id='6'>
								<h3>6. To what extent did we achieve our purpose?</h3><br/>
							</td>
						</tr>
						<tr>
							<td style='background: none!important; background-color: <? print $bg ?>!important'></td> 
							<td colspan=2> 
								<div style='font-weight: bold; text-decoration: underline; font-size: 130%'>Assess Outcomes</div> 
								<p>Assess the outcome of the inquiry by providing evidence of students’ understanding of the central idea. </p>
								<? print getEditor($guid,  $connection2, "assessOutcomes", $row["assessOutcomes"], 30, true, false, false, true, "", true ) ?>
							</td>
						</tr>
						<tr>
							<td style='background: none!important; background-color: <? print $bg ?>!important'></td> 
							<td colspan=2> 
								<div style='margin-top: 40px; font-weight: bold; text-decoration: underline; font-size: 130%'>Assessment Improvements</div> 
								<p>How could you improve on the assessment task(s) so that you would have a more accurate picture of each students’ understanding of the central idea?</p>
								<? print getEditor($guid,  $connection2, "assessmentImprovements", $row["assessmentImprovements"], 30 ) ?>
							</td>
						</tr>
						<tr>
							<td style='background: none!important; background-color: <? print $bg ?>!important'></td> 
							<td colspan=2> 
								<div style='margin-top: 40px; font-weight: bold; text-decoration: underline; font-size: 130%'>Central Ideas & Transdisciplinary Theme</div> 
								<p>What was the evidence that connections were made between the central idea and the transdisciplinary theme?</p>
								<? print getEditor($guid,  $connection2, "ideasThemes", $row["ideasThemes"], 30 ) ?>
							</td>
						</tr>

						<? $bg="#6A4A3C" ; ?>
						<tr class='break'>
							<td style='background: none!important; background-color: <? print $bg ?>!important'></td> 
							<td colspan=2>  
								<a id='7'>
								<h3>7. To what extent did we include elements of the PYP?</h3><br/>
							</td>
						</tr>
						<tr>
							<td style='background: none!important; background-color: <? print $bg ?>!important'></td> 
							<td colspan=2> 
								<div style='font-weight: bold; text-decoration: underline; font-size: 130%'>Learning Experiences & Concepts</div> 
								<p>What were the learning experiences that enabled students to develop an understanding of the concepts identified in “What do we want to learn?</p>
								<? print getEditor($guid,  $connection2, "learningExperiencesConcepts", $row["learningExperiencesConcepts"], 30, true, false, false, true, "", true ) ?>
							</td>
						</tr>
						<tr>
							<td style='background: none!important; background-color: <? print $bg ?>!important'></td> 
							<td colspan=2> 
								<div style='margin-top: 40px; font-weight: bold; text-decoration: underline; font-size: 130%'>Learning Experiences & Transdisciplinary Skills</div> 
								<p>What were the learning experiences that enabled students to demonstrate the learning and application of particular transdisciplinary skills?</p>
								<? print getEditor($guid,  $connection2, "learningExperiencesTransSkills", $row["learningExperiencesTransSkills"], 30, true, false, false, true, "", true ) ?>
							</td>
						</tr>
						<tr>
							<td style='background: none!important; background-color: <? print $bg ?>!important'></td> 
							<td colspan=2> 
								<div style='margin-top: 40px; font-weight: bold; text-decoration: underline; font-size: 130%'>Learning Experiences & Learner Profile</div> 
								<p>What were the learning experiences that enabled students to develop attributes of the learner profile and attitudes?</p>
								<? print getEditor($guid,  $connection2, "learningExperiencesProfileAttitudes", $row["learningExperiencesProfileAttitudes"], 30, true, false, false, true, "", true ) ?>
							</td>
						</tr>
						
						<? $bg="#00A0B0" ; ?>
						<tr class='break'>
							<td style='background: none!important; background-color: <? print $bg ?>!important'></td> 
							<td colspan=2>  
								<a id='8'>
								<h3>8. What student-initiated inquiries arose?</h3><br/>
							</td>
						</tr>
						<tr>
							<td style='background: none!important; background-color: <? print $bg ?>!important'></td> 
							<td colspan=2> 
								<div style='font-weight: bold; text-decoration: underline; font-size: 130%'>Inquiries & Questions</div> 
								<p>Record a range of student-initiated inquiries and student questions and highlight any that were incorporated into the teaching and learning.</p>
								<? print getEditor($guid,  $connection2, "inquiriesQuestions", $row["inquiriesQuestions"], 30, true, false, false, true, "", true ) ?>
							</td>
						</tr>
						<tr>
							<td style='background: none!important; background-color: <? print $bg ?>!important'></td> 
							<td colspan=2> 
								<div style='margin-top: 40px; font-weight: bold; text-decoration: underline; font-size: 130%'>Questions & Provocations</div> 
								<p>What teacher questions / provocations were the most effective in driving the inquiries? Why?</p>
								<? print getEditor($guid,  $connection2, "questionsProvocations", $row["questionsProvocations"], 30, true, false, false, true, "", true ) ?>
							</td>
						</tr>
						<tr>
							<td style='background: none!important; background-color: <? print $bg ?>!important'></td> 
							<td colspan=2> 
								<div style='margin-top: 40px; font-weight: bold; text-decoration: underline; font-size: 130%'>Student-Initiated Action</div> 
								<p>What student-initiated actions arose from the learning? What student-initiated actions taken by individuals or groups showing their ability to reflect, to choose, to act.</p>
								<? print getEditor($guid,  $connection2, "studentInitAction", $row["studentInitAction"], 30, true, false, false, true, "", true ) ?>
							</td>
						</tr>
						
						<? $bg="#C44D58" ; ?>
						<tr class='break'>
							<td style='background: none!important; background-color: <? print $bg ?>!important'></td> 
							<td colspan=2>  
								<a id='9'>
								<h3>9. Teacher's Notes</h3><br/>
							</td>
						</tr>
						<tr>
							<td style='background: none!important; background-color: <? print $bg ?>!important'></td> 
							<td colspan=2> 
								<div style='font-weight: bold; text-decoration: underline; font-size: 130%'>Teachers Notes</div> 
								<? print getEditor($guid,  $connection2, "teachersNotes", $row["teachersNotes"], 30, true, false, false, true, "", true ) ?>
							</td>
						</tr>
						
						<tr>
							<td colspan=2>
								<span style="font-size: 90%"><i>* denotes a required field</i></span>
							</td>
							<td class="right">
								<input type="hidden" name="address" value="<? print $_SESSION[$guid]["address"] ?>">
								<input type="hidden" name="step" value="<? print $step ?>">
								<input type="reset" value="Reset"> <input type="submit" value="Submit">
							</td>
						</tr>
					</table>
				</form>
				<?
			}
		}
	}
}
?>
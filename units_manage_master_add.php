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
$_SESSION[$guid]["ibPYPUnitsTab"]=1 ;

//Module includes
include "./modules/IB PYP/moduleFunctions.php" ;

if (isActionAccessible($guid, $connection2, "/modules/IB PYP/units_manage_master_add.php")==FALSE) {
	//Acess denied
	print "<div class='error'>" ;
		print "You do not have access to this action." ;
	print "</div>" ;
}
else {
	print "<div class='trail'>" ;
	print "<div class='trailHead'><a href='" . $_SESSION[$guid]["absoluteURL"] . "'>Home</a> > <a href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/" . getModuleName($_GET["q"]) . "/" . getModuleEntry($_GET["q"], $connection2, $guid) . "'>" . getModuleName($_GET["q"]) . "</a> > <a href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/" . getModuleName($_GET["q"]) . "/units_manage.php&gibbonSchoolYearID=" . $_GET["gibbonSchoolYearID"] . "'>Manage Units</a> > </div><div class='trailEnd'>Add Master Unit</div>" ;
	print "</div>" ;
	
	$addReturn = $_GET["addReturn"] ;
	$addReturnMessage ="" ;
	$class="error" ;
	if (!($addReturn=="")) {
		if ($addReturn=="fail0") {
			$addReturnMessage ="Add failed because you do not have access to this action." ;	
		}
		else if ($addReturn=="fail2") {
			$addReturnMessage ="Add failed due to a database error." ;	
		}
		else if ($addReturn=="fail3") {
			$addReturnMessage ="Add failed because your inputs were invalid." ;	
		}
		else if ($addReturn=="fail5") {
			$addReturnMessage ="Add succeeded, but there were problems saving one or more items." ;	
		}
		else if ($addReturn=="success0") {
			$addReturnMessage ="Add was successful. You can add another record if you wish." ;	
			$class="success" ;
		}
		print "<div class='$class'>" ;
			print $addReturnMessage;
		print "</div>" ;
	} 
	
	$role=getRole($_SESSION[$guid]["gibbonPersonID"], $connection2) ;
	if ($role!="Coordinator" AND $role!="Teacher (Curriculum)") {
		print "<div class='error'>" ;
			print "You do not have access to this action." ;
		print "</div>" ;
	}
	else {
		$gibbonSchoolYearID=$_GET["gibbonSchoolYearID"];
		if ($gibbonSchoolYearID=="") {
			print "<div class='error'>" ;
				print "You have not specified a school year." ;
			print "</div>" ;
		}
		else {
			$step=$_GET["step"] ;
			if ($step!=1 AND $step!=2) {
				$step=1 ;
			}
			
			if ($step==1) {
				?>
				<form method="post" action="<? print $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/" . $_SESSION[$guid]["module"] . "/units_manage_master_add.php&gibbonSchoolYearID=$gibbonSchoolYearID&step=2" ?>">
					<table style="width: 100%;">	
						<tr><td style="width: 4px"></td><td style="width: 50%"></td><td style="width: 50%"></td></tr>
						<? $bg="#fff" ; ?>
						<tr>
							<td colspan=3> 
								<h3 class='top'>Step 1 - Basics</h3><br/>
							</td>
						</tr>
						<tr>
							<td style='background-color: <? print $bg ?>'></td> 
							<td> 
								<b>Unit Name *</b><br/>
							</td>
							<td class="right">
								<input name="unitname" id="unitname" maxlength=50 value="" type="text" style="width: 300px">
								<script type="text/javascript">
									var unitname = new LiveValidation('unitname');
									unitname.add(Validate.Presence);
								 </script>
							</td>
						</tr>
						<tr>
							<td style='background-color: <? print $bg ?>'></td> 
							<td> 
								<b>Active *</b><br/>
								<span style="font-size: 90%"><i></i></span>
							</td>
							<td class="right">
								<select name="active" id="active" style="width: 302px">
									<option value="Y">Y</option>
									<option value="N">N</option>
								</select>
							</td>
						</tr>
						<tr>
							<td style='background-color: <? print $bg ?>'></td> 
							<td> 
								<b>Course *</b><br/>
								<span style="font-size: 90%"><i>Which course does this unit belong to?<br/></i></span>
							</td>
							<td class="right">
								<select name="gibbonCourseID" id="gibbonCourseID" style="width: 302px">
									<option value="Please select...">Please select...</option>
									<?
									try {
										$dataSelect=array("gibbonSchoolYearID"=>$gibbonSchoolYearID); 
										$sqlSelect="SELECT * FROM gibbonCourse WHERE gibbonSchoolYearID=:gibbonSchoolYearID ORDER BY nameShort" ;
										$resultSelect=$connection2->prepare($sqlSelect);
										$resultSelect->execute($dataSelect);
									}
									catch(PDOException $e) { }
									while ($rowSelect=$resultSelect->fetch()) {
										print "<option value='" . $rowSelect["gibbonCourseID"] . "'>" . $rowSelect["nameShort"] . "</option>" ; 
									}
									?>
								</select>
								<script type="text/javascript">
									var gibbonCourseID = new LiveValidation('gibbonCourseID');
									gibbonCourseID.add(Validate.Exclusion, { within: ['Please select...'], failureMessage: "Select something!"});
								 </script>
							</td>
						</tr>
						<tr>
							<td class="right" colspan=3>
								<script type="text/javascript">
									$(document).ready(function(){
										$("#submit").click(function(){
											$("#blockCount").val(count) ;
										 });
									});
								</script>
								<input name="blockCount" id=blockCount value="5" type="hidden">
								<input type="hidden" name="address" value="<? print $_SESSION[$guid]["address"] ?>">
								<input type="reset" value="Reset"> <input type="submit" value="Submit">
							</td>
						</tr>
						<tr>
							<td class="right" colspan=3>
								<span style="font-size: 90%"><i>* denotes a required field</i></span>
							</td>
						</tr>
					</table>
				</form>		
			<?
			}
			else {
				?>
				<form method="post" action="<? print $_SESSION[$guid]["absoluteURL"] . "/modules/" . $_SESSION[$guid]["module"] . "/units_manage_master_addProcess.php?gibbonSchoolYearID=$gibbonSchoolYearID" ?>">
					<table style="width: 100%;">	
						<tr><td style="width: 4px"></td><td style="width: 50%"></td><td style="width: 50%"></td></tr>
						<tr>
							<td colspan=3> 
								<h3 class='top'>Step 2 - Details</h3><br/>
							</td>
						</tr>
						<tr>
							<td style='background-color: <? print $bg ?>'></td> 
							<td> 
								<b>Unit Name *</b><br/>
								<span style="font-size: 90%"><i>This value cannot be changed.</i></span>
							</td>
							<td class="right">
								<input readonly name="unitname" id="unitname" maxlength=50 value="<? print $_POST["unitname"] ?>" type="text" style="width: 300px">
							</td>
						</tr>
						<tr>
							<td style='background-color: <? print $bg ?>'></td> 
							<td> 
								<b>Active *</b><br/>
								<span style="font-size: 90%"><i>This value cannot be changed.</i></span>
							</td>
							<td class="right">
								<input readonly name="active" id="active" maxlength=50 value="<? print $_POST["active"] ?>" type="text" style="width: 300px">
							</td>
						</tr>
						<tr>
							<td style='background-color: <? print $bg ?>'></td> 
							<td> 
								<b>Course *</b><br/>
								<span style="font-size: 90%"><i>This value cannot be changed.</i></span>
							</td>
							<td class="right">
								<input name="gibbonCourseID" id="gibbonCourseID" value="<? print $_POST["gibbonCourseID"] ?>" type="hidden" style="width: 300px">
								<?
								try {
									$dataSelect=array("gibbonCourseID"=>$_POST["gibbonCourseID"]); 
									$sqlSelect="SELECT * FROM gibbonCourse WHERE gibbonCourseID=:gibbonCourseID" ;
									$resultSelect=$connection2->prepare($sqlSelect);
									$resultSelect->execute($dataSelect);
								}
								catch(PDOException $e) { }
								if ($resultSelect->rowCount()==1) {
									$rowSelect=$resultSelect->fetch() ;
									$gibbonYearGroupIDList=$rowSelect["gibbonYearGroupIDList"] ;
									$gibbonDepartmentID=$rowSelect["gibbonDepartmentID"] ;
									print "<input readonly name=\"course\" id=\"course\" value=\"" . $rowSelect["nameShort"] . "\" type=\"text\" style=\"width: 300px\">" ;
								}
								?>
							</td>
						</tr>
						<tr>
							<td style='padding-top: 20px; background-color: <? print $bg ?>'></td> 
							<td style='padding-top: 20px'> 
								<b>Section Menu</b><br/>
								<a href='#1'>1. What is our purpose?</a><br/>
								<a href='#2'>2. What do we want to learn?</a><br/>
								<a href='#3'>3. How might we know what we have learned?</a><br/>
								<a href='#4'>4. How best might we learn?</a><br/>
								<a href='#5'>5. What resources need to be gathered?</a><br/>
								<a href='#6'>6. Smart Content Blocks</a><br/>
							<td style='padding-top: 20px' class="right">
						
							</td>
						</tr>
				
						<? $bg="#EDC951" ; ?>
						<tr>
							<td colspan=3> 
								<a id='1'>
								<h3>1. What Is Our Purpose?</h3><br/>
							</td>
						</tr>
						<tr>
							<td style='background-color: <? print $bg ?>'></td> 
							<td colspan=2> 
								<div style='font-weight: bold; text-decoration: underline; font-size: 130%'>Transdisciplinary Theme</div> 
								<? print getEditor($guid,  $connection2, "theme", $row["theme"], 30 ) ?>
							</td>
						</tr>
						<tr>
							<td style='background-color: <? print $bg ?>'></td> 
							<td colspan=2> 
								<div style='margin-top: 40px; font-weight: bold; text-decoration: underline; font-size: 130%'>Central Idea</div> 
								<? print getEditor($guid,  $connection2, "centralIdea", $row["centralIdea"], 30 ) ?>
							</td>
						</tr>
						<tr>
							<td style='background-color: <? print $bg ?>'></td> 
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
							#<? print $type ?> div.ui-state-default { margin: 0 0px 5px 0px; padding: 5px; font-size: 100%; min-height: 58px; }
							div.ui-state-default_dud { margin: 5px 0px 5px 0px; padding: 5px; font-size: 100%; min-height: 58px; }
							html>body #<? print $type ?> li { min-height: 58px; line-height: 1.2em; }
							.<? print $type ?>-ui-state-highlight { margin-bottom: 5px; min-height: 58px; line-height: 1.2em; width: 100%; }
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
							<td style='background-color: <? print $bg ?>'></td> 
							<td colspan=2> 
								<div class="outcome" id="outcome" style='width: 100%; padding: 5px 0px 0px 0px; border-top: 1px solid #333; border-bottom: 1px solid #333; min-height: 66px'>
										<div id="outcomeOuter0">
											<div style='color: #ddd; font-size: 230%; margin: 15px 0 0 6px'>Key outcomes listed here...</div>
										</div>
									</div>
								<div style='width: 100%; padding: 0px 0px 0px 0px; border-bottom: 1px solid #333'>
									<div class="ui-state-default_dud odd" style='padding: 0px; height: 60px'>
										<table style='width: 100%'>
											<tr>
												<td style='width: 50%'>
													<script type="text/javascript">
														var outcomeCount=1 ;
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
														var <? print $type ?>Used=new Array();
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
							<td style='background-color: <? print $bg ?>'></td> 
							<td colspan=2> 
								<div style='margin-top: 40px; font-weight: bold; text-decoration: underline; font-size: 130%'>Summative Assessment</div>
								<p>What are the possible ways of assessing students’ understanding of the central idea? What evidence, including student initiated actions will we look for?</p>
								<? print getEditor($guid,  $connection2, "summativeAssessment", $row["summativeAssessment"], 30 ) ?>
							</td>
						</tr>
				
						<? $bg="#6A4A3C" ; ?>
						<tr>
							<td colspan=3> 
								<a id='2'>
								<h3>2. What Do We Want To Learn?</h3><br/>
							</td>
						</tr>
						<tr>
							<td style='background-color: <? print $bg ?>'></td> 
							<td colspan=2> 
								<div style='font-weight: bold; text-decoration: underline; font-size: 130%'>Key Concepts</div> 
								<p>What are the key concepts to be emphasized within this inquiry?</p>
							</td>
						</tr>
				
						<? $type="concept" ; ?> 
						<style>
							#<? print $type ?> { list-style-type: none; margin: 0; padding: 0; width: 100%; }
							#<? print $type ?> div.ui-state-default { margin: 0 0px 5px 0px; padding: 5px; font-size: 100%; min-height: 58px; }
							div.ui-state-default_dud { margin: 5px 0px 5px 0px; padding: 5px; font-size: 100%; min-height: 58px; }
							html>body #<? print $type ?> li { min-height: 58px; line-height: 1.2em; }
							.<? print $type ?>-ui-state-highlight { margin-bottom: 5px; min-height: 58px; line-height: 1.2em; width: 100%; }
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
							<td style='background-color: <? print $bg ?>'></td> 
							<td colspan=2> 
								<div class="concept" id="concept" style='width: 100%; padding: 5px 0px 0px 0px; border-top: 1px solid #333; border-bottom: 1px solid #333; min-height: 66px'>
										<div id="conceptOuter0">
											<div style='color: #ddd; font-size: 230%; margin: 15px 0 0 6px'>Key concepts listed here...</div>
										</div>
									</div>
								<div style='width: 100%; padding: 0px 0px 0px 0px; border-bottom: 1px solid #333'>
									<div class="ui-state-default_dud odd" style='padding: 0px; height: 60px'>
										<table style='width: 100%'>
											<tr>
												<td style='width: 50%'>
													<script type="text/javascript">
														var conceptCount=1 ;
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
														catch(PDOException $e) { 
															print "<div class='error'>" . $e->getMessage() . "</div>" ; 
														}
												
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
														var <? print $type ?>Used=new Array();
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
							<td style='background-color: <? print $bg ?>'></td> 
							<td colspan=2> 
								<div style='margin-top: 40px; font-weight: bold; text-decoration: underline; font-size: 130%'>Related Concepts</div> 
								<p>What are the concepts that are related to this inquiry?</p>
								<? print getEditor($guid,  $connection2, "relatedConcepts", "<ul><li></li><li></li><li></li></ul>", 10 ) ?>
							</td>
						</tr>
						
						<tr>
							<td style='background-color: <? print $bg ?>'></td> 
							<td colspan=2> 
								<div style='margin-top: 40px; font-weight: bold; text-decoration: underline; font-size: 130%'>Lines of Inquiry</div> 
								<p>What lines of inquiry will define the scope of the inquiry into the central idea?</p>
								<? print getEditor($guid,  $connection2, "linesOfInquiry", "<ul><li></li><li></li><li></li></ul>", 10 ) ?>
							</td>
						</tr>
						<tr>
							<td style='background-color: <? print $bg ?>'></td> 
							<td colspan=2> 
								<div style='margin-top: 40px; font-weight: bold; text-decoration: underline; font-size: 130%'>Teacher Questions<br/></div>
								<p>What teacher questions will drive these inquiries?<br/><br/></p>
								<? print getEditor($guid,  $connection2, "teacherQuestions", "<ol><li></li><li></li><li></li></ol>", 10 ) ?>
							</td>
						</tr>
					
						<tr>
							<td style='background-color: <? print $bg ?>'></td> 
							<td colspan=2> 
								<div style='margin-top: 40px; font-weight: bold; text-decoration: underline; font-size: 130%'>Provocation</div> 
								<? print getEditor($guid,  $connection2, "provocation", $row["provocation"], 30, true, false, false, true, "purpose=Provocation", true ) ?>
							</td>
						</tr>
				
						<? $bg="#00A0B0" ; ?>
						<tr>
							<td colspan=3> 
								<a id='3'>
								<h3>3. How Might We Know What We Have Learned?</h3><br/>
							</td>
						</tr>
						<tr>
							<td style='background-color: <? print $bg ?>'></td> 
							<td colspan=2> 
								<div style='font-weight: bold; text-decoration: underline; font-size: 130%'>Assessing Prior Knowledge & Skills</div> 
								<p>What are the possible ways of assessing students’ prior knowledge and skills? What evidence will we look for? </p>
								<? print getEditor($guid,  $connection2, "preAssessment", $row["preAssessment"], 30, true, false, false, true, "purpose=Assessment%20Aid", true ) ?>
							</td>
						</tr>
						<tr>
							<td style='background-color: <? print $bg ?>'></td> 
							<td colspan=2> 
								<div style='margin-top: 40px; font-weight: bold; text-decoration: underline; font-size: 130%'>Formative Assessment</div> 
								<p>What are the possible ways of assessing student learning in the context of the lines of inquiry? What evidence will we look for?</p>
								<? print getEditor($guid,  $connection2, "formativeAssessment", $row["formativeAssessment"], 30, true, false, false, true, "purpose=Assessment%20Aid", true ) ?>
							</td>
						</tr>
				
						<? $bg="#C44D58" ; ?>
						<tr>
							<td colspan=3> 
								<a id='4'>
								<h3>4. How Best Might We Learn?</h3><br/>
							</td>
						</tr>
						<tr>
							<td style='background-color: <? print $bg ?>'></td> 
							<td colspan=2> 
								<div style='font-weight: bold; text-decoration: underline; font-size: 130%'>Learning Experiences</div> 
								<p>What are the learning experiences suggested by the teacher and / or students to encourage the students to engage with the inquiries and address the driving questions?</p>
								<? print getEditor($guid,  $connection2, "learningExperiences", $row["learningExperiences"], 30, true, false, false, true, "purpose=Teaching%20and%20Learning%20Strategy", true) ?>
							</td>
						</tr>
						<tr>
							<td style='background-color: <? print $bg ?>'></td> 
							<td colspan=2> 
								<div style='margin-top: 40px; font-weight: bold; text-decoration: underline; font-size: 130%'>Transdisciplinary Skills</div> 
								<p>What opportunities will occur for transdisciplinary skills?</p>
							</td>
						</tr>
						<tr>
							<td style='background-color: <? print $bg ?>'></td> 
							<? $type="skills" ; ?> 
							<style>
								#<? print $type ?> { list-style-type: none; margin: 0; padding: 0; width: 100%; }
								#<? print $type ?> div.ui-state-default { margin: 0 0px 5px 0px; padding: 5px; font-size: 100%; min-height: 58px; }
								div.ui-state-default_dud { margin: 5px 0px 5px 0px; padding: 5px; font-size: 100%; min-height: 58px; }
								html>body #<? print $type ?> li { min-height: 58px; line-height: 1.2em; }
								.<? print $type ?>-ui-state-highlight { margin-bottom: 5px; min-height: 58px; line-height: 1.2em; width: 100%; }
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
								<div class="skills" id="skills" style='width: 100%; padding: 5px 0px 0px 0px; border-top: 1px solid #333; border-bottom: 1px solid #333; min-height: 66px'>
									<div id="skillsOuter0">
										<div style='color: #ddd; font-size: 230%; margin: 15px 0 0 6px'>Transdisciplinary Skills listed here...</div>
									</div>
								</div>
								<div style='width: 100%; padding: 0px 0px 0px 0px; border-bottom: 1px solid #333'>
									<div class="ui-state-default_dud odd" style='padding: 0px; height: 60px'>
										<table style='width: 100%'>
											<tr>
												<td style='width: 50%'>
													<script type="text/javascript">
														var skillsCount=1 ;
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
														catch(PDOException $e) { 
															print "<div class='error'>" . $e->getMessage() . "</div>" ; 
														}
												
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
														var <? print $type ?>Used=new Array();
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
							<td style='background-color: <? print $bg ?>'></td> 
							<td colspan=2> 
								<div style='margin-top: 40px; font-weight: bold; text-decoration: underline; font-size: 130%'>Learner Profile & Attitudes</div> 
								<p>What opportunity will occur for the development of the attributes of the learner profile and attitudes?</p>
							</td>
						</tr>
						<tr>
							<td style='background-color: <? print $bg ?>'></td> 
							<? $type="learnerProfile" ; ?> 
							<style>
								#<? print $type ?> { list-style-type: none; margin: 0; padding: 0; width: 100%; }
								#<? print $type ?> div.ui-state-default { margin: 0 0px 5px 0px; padding: 5px; font-size: 100%; min-height: 58px; }
								div.ui-state-default_dud { margin: 5px 0px 5px 0px; padding: 5px; font-size: 100%; min-height: 58px; }
								html>body #<? print $type ?> li { min-height: 58px; line-height: 1.2em; }
								.<? print $type ?>-ui-state-highlight { margin-bottom: 5px; min-height: 58px; line-height: 1.2em; width: 100%; }
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
								<div class="learnerProfile" id="learnerProfile" style='width: 100%; padding: 5px 0px 0px 0px; border-top: 1px solid #333; border-bottom: 1px solid #333; min-height: 66px'>
									<div id="learnerProfileOuter0">
										<div style='color: #ddd; font-size: 230%; margin: 15px 0 0 6px'>Learner Profile & Attitudes listed here...</div>
									</div>
								</div>
								<div style='width: 100%; padding: 0px 0px 0px 0px; border-bottom: 1px solid #333'>
									<div class="ui-state-default_dud odd" style='padding: 0px; height: 60px'>
										<table style='width: 100%'>
											<tr>
												<td style='width: 50%'>
													<script type="text/javascript">
														var learnerProfileCount=1 ;
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
														catch(PDOException $e) { 
															print "<div class='error'>" . $e->getMessage() . "</div>" ; 
														}
												
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
														var <? print $type ?>Used=new Array();
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
						<tr>
							<td colspan=3> 
								<a id='5'>
								<h3>5. What Resources Need To Be Gathered?</h3><br/>
							</td>
						</tr>
						<tr>
							<td style='background-color: <? print $bg ?>'></td> 
							<td colspan=2> 
								<div style='font-weight: bold; text-decoration: underline; font-size: 130%'>Resources</div> 
								<p>What people, places, audio-visual materials, related literature, music, art, computer software etc will be available?</p>
								<? print getEditor($guid,  $connection2, "resources", $row["resources"], 30, true, false, false, true, "", true) ?>
							</td>
						</tr>
						<tr>
							<td style='background-color: <? print $bg ?>'></td> 
							<td colspan=2> 
								<div style='margin-top: 40px; font-weight: bold; text-decoration: underline; font-size: 130%'>Action</div> 
								<p>What possible action could be inspired by this inquiry?</p>
								<? print getEditor($guid,  $connection2, "action", $row["action"], 30, false, false, false, true, "", true) ?>
							</td>
						</tr>
						<tr>
							<td style='background-color: <? print $bg ?>'></td> 
							<td colspan=2> 
								<div style='margin-top: 40px; font-weight: bold; text-decoration: underline; font-size: 130%'>Classroom Environment</div> 
								<p>How will the classroom environment, local environment and or community be used to facilitate the inquiry? </p>
								<? print getEditor($guid,  $connection2, "environments", $row["environments"], 30 ) ?>
							</td>
						</tr>
				
						<? $bg="#6767EF" ; ?>
						<tr>
							<td colspan=3> 
								<a id='6'>
								<h3>6. Smart Content Blocks</h3><br/>
								<p style='color: black'>Smart content blocks are Gibbon's way of helping you organise and manage the content in your units. Create the content here, and you can quickly use it to make lesson plans in the future.</p>
							</td>
						</tr>
						<tr>
							<td style='background-color: <? print $bg ?>'></td> 
							<td colspan=2> 
								<style>
										#sortable { list-style-type: none; margin: 0; padding: 0; width: 100%; }
										#sortable div.ui-state-default { margin: 0 0px 5px 0px; padding: 5px; font-size: 100%; min-height: 58px; }
										div.ui-state-default_dud { margin: 5px 0px 5px 0px; padding: 5px; font-size: 100%; min-height: 58px; }
										html>body #sortable li { min-height: 58px; line-height: 1.2em; }
										.ui-state-highlight { margin-bottom: 5px; min-height: 58px; line-height: 1.2em; width: 100%; }
									</style>
									<script>
										$(function() {
											$( "#sortable" ).sortable({
												placeholder: "ui-state-highlight";
												axis: 'y'
											});
										});
									</script>
					
									<div class="sortable" id="sortable" style='width: 100%; padding: 5px 0px 0px 0px; border-top: 1px solid #333; border-bottom: 1px solid #333'>
										<? 
										for ($i=1; $i<=5; $i++) {
											makeBlock($guid, $connection2, $i) ;
										}
										?>
									</div>
					
									<div style='width: 100%; padding: 0px 0px 0px 0px; border-bottom: 1px solid #333'>
										<div class="ui-state-default_dud odd" style='padding: 0px;'>
											<table style='width: 100%'>
												<tr>
													<td style='width: 50%'>
														<script type="text/javascript">
															var count=6 ;
															/* Unit type control */
															$(document).ready(function(){
																$("#new").click(function(){
																	$("#sortable").append('<div id=\'blockOuter' + count + '\'><img style=\'margin: 10px 0 5px 0\' src=\'<? print $_SESSION[$guid]["absoluteURL"] ?>/themes/Default/img/loading.gif\' alt=\'Loading\' onclick=\'return false;\' /><br/>Loading</div>');
																	$("#blockOuter" + count).load("<? print $_SESSION[$guid]["absoluteURL"] ?>/modules/Planner/units_add_blockAjax.php","id=" + count) ;
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
							</td>
						</tr>
						<tr>
							<td class="right" colspan=3>
								<script type="text/javascript">
									$(document).ready(function(){
										$("#submit").click(function(){
											$("#blockCount").val(count) ;
										 });
									});
								</script>
								<input name="blockCount" id=blockCount value="5" type="hidden">
								<input type="hidden" name="address" value="<? print $_SESSION[$guid]["address"] ?>">
								<input type="reset" value="Reset"> <input type="submit" value="Submit">
							</td>
						</tr>
						<tr>
							<td class="right" colspan=3>
								<span style="font-size: 90%"><i>* denotes a required field</i></span>
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
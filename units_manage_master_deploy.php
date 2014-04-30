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

@session_start() ;
$_SESSION[$guid]["ibPYPUnitsTab"]=1 ;

//Module includes
include "./modules/IB PYP/moduleFunctions.php" ;

if (isActionAccessible($guid, $connection2, "/modules/IB PYP/units_manage_master_deploy.php")==FALSE) {
	//Acess denied
	print "<div class='error'>" ;
		print "You do not have access to this action." ;
	print "</div>" ;
}
else {
	//Proceed!
	print "<div class='trail'>" ;
	print "<div class='trailHead'><a href='" . $_SESSION[$guid]["absoluteURL"] . "'>Home</a> > <a href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/" . getModuleName($_GET["q"]) . "/" . getModuleEntry($_GET["q"], $connection2, $guid) . "'>" . getModuleName($_GET["q"]) . "</a> > <a href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/" . getModuleName($_GET["q"]) . "/units_manage.php&gibbonSchoolYearID=" . $_GET["gibbonSchoolYearID"] . "'>Manage Units</a> > </div><div class='trailEnd'>Deploy Working Copy</div>" ;
	print "</div>" ;
	
	if (isset($_GET["deployReturn"])) { $deployReturn=$_GET["deployReturn"] ; } else { $deployReturn="" ; }
	$deployReturnMessage ="" ;
	$class="error" ;
	if (!($deployReturn=="")) {
		if ($deployReturn=="fail0") {
			$deployReturnMessage ="Deploy failed because you do not have access to this action." ;	
		}
		else if ($deployReturn=="fail2") {
			$deployReturnMessage ="Deploy failed due to a database error." ;	
		}
		else if ($deployReturn=="fail3") {
			$deployReturnMessage ="Deploy failed because your inputs were invalid." ;	
		}
		else if ($deployReturn=="fail4") {
			$deployReturnMessage ="Deploy failed because you do not have access to the specified course." ;	
		}
		else if ($deployReturn=="fail5") {
			$deployReturnMessage ="Some aspects of the deploy failed." ;	
		}
		print "<div class='$class'>" ;
			print $deployReturnMessage;
		print "</div>" ;
	} 
	
	$role=getRole($_SESSION[$guid]["gibbonPersonID"], $connection2) ;
	if ($role=="Coordinator" OR $role=="Teacher (Curriculum)") {
		//Check if courseschool year specified
		$gibbonSchoolYearID=$_GET["gibbonSchoolYearID"] ;
		$ibPYPUnitMasterID=$_GET["ibPYPUnitMasterID"]; 
		if ($ibPYPUnitMasterID=="" OR $gibbonSchoolYearID=="") {
			print "<div class='error'>" ;
				print "You have not specified a unit or school year." ;
			print "</div>" ;
		}
		else {
			try {
				$data=array("ibPYPUnitMasterID"=>$ibPYPUnitMasterID);  
				$sql="SELECT ibPYPUnitMaster.*, gibbonYearGroupIDList, gibbonDepartmentID FROM ibPYPUnitMaster JOIN gibbonCourse ON (ibPYPUnitMaster.gibbonCourseID=gibbonCourse.gibbonCourseID) WHERE ibPYPUnitMasterID=:ibPYPUnitMasterID" ;
				$result=$connection2->prepare($sql);
				$result->execute($data);
			}
			catch(PDOException $e) { 
				print "<div class='error'>" . $e->getMessage() . "</div>" ; 
			}

			if ($result->rowCount()!=1) {
				print "<div class='error'>" ;
					print "The selected master unit does not exist." ;
				print "</div>" ;
			}
			else {
				//Let's go!
				$row=$result->fetch() ;
					
				try {
					$dataSchoolYear=array("gibbonSchoolYearID"=>$gibbonSchoolYearID);  
					$sqlSchoolYear="SELECT * FROM gibbonSchoolYear WHERE gibbonSchoolYearID=:gibbonSchoolYearID" ;
					$resultSchoolYear=$connection2->prepare($sqlSchoolYear);
					$resultSchoolYear->execute($dataSchoolYear);
				}
				catch(PDOException $e) { 
					print "<div class='error'>" . $e->getMessage() . "</div>" ; 
				}

				if ($resultSchoolYear->rowCount()!=1) {
					print "<div class='error'>" ;
						print "You cannot proceed, as the specified school year cannot be found." ;
					print "</div>" ;
				}	
				else {
					$rowSchoolYear=$resultSchoolYear->fetch() ;
					?>
					<form method="post" action="<?php print $_SESSION[$guid]["absoluteURL"] . "/modules/IB PYP/units_manage_master_deployProcess.php?ibPYPUnitMasterID=$ibPYPUnitMasterID" ?>">
						<table class='smallIntBorder' cellspacing='0' style="width: 100%">	
							<tr>
								<td> 
									<b>Unit *</b><br/>
									<span style="font-size: 90%"><i>This value cannot be changed</i></span>
								</td>
								<td class="right">
									<input readonly name="name" id="name" value="<?php print $row["name"] ?>" type="text" style="width: 300px">
								</td>
							</tr>
							<tr>
								<td> 
									<b>School Year *</b><br/>
									<span style="font-size: 90%"><i>This value cannot be changed</i></span>
								</td>
								<td class="right">
									<?php
									print "<input readonly name='schoolYear' id='schoolYear' value='" . $rowSchoolYear["name"] . "' type='text' style='width: 300px'>" ;
									?>
								</td>
							</tr>
							<tr>
								<td> 
									<b>Classes</b><br/>
									<span style="font-size: 90%"><i>Use Control and/or Shift to select multiple.</i></span>
								</td>
								<td class="right">
									<input type='hidden' name='gibbonCourseID' value='<?php print $row["gibbonCourseID"] ?>'>
									<select name="classes[]" id="classes" multiple style="width: 302px; height: 150px">
										<?php
										try {
											$dataSelect=array("gibbonSchoolYearID"=>$gibbonSchoolYearID, "gibbonCourseID"=>$row["gibbonCourseID"]);  
											$sqlSelect="SELECT gibbonCourse.gibbonCourseID, gibbonCourseClassID, gibbonCourse.name, gibbonCourse.nameShort AS course, gibbonCourseClass.nameShort AS class FROM gibbonCourse JOIN gibbonCourseClass ON (gibbonCourse.gibbonCourseID=gibbonCourseClass.gibbonCourseID) WHERE gibbonSchoolYearID=:gibbonSchoolYearID AND gibbonCourse.gibbonCourseID=:gibbonCourseID ORDER BY course, class" ;
											$resultSelect=$connection2->prepare($sqlSelect);
											$resultSelect->execute($dataSelect);
										}
										catch(PDOException $e) { 
											print "<div class='error'>" . $e->getMessage() . "</div>" ; 
										}
										while ($rowSelect=$resultSelect->fetch()) {
											print "<option class='" . $rowSelect["gibbonCourseID"] . "' value='" . $rowSelect["gibbonCourseClassID"] . "'>" . htmlPrep($rowSelect["course"]) . "." . htmlPrep($rowSelect["class"]) . " - " . $rowSelect["name"] . "</option>" ;
										}
										?>
									</select>
								</td>
							</tr>
							<tr>
								<td> 
									<b>Unit Start Date</b><br/>
									<span style="font-size: 90%"><i>When will this class start this unit?<br/>dd/mm/yyyy</i></span>
								</td>
								<td class="right">
									<input name="dateStart" id="dateStart" maxlength=10 value="" type="text" style="width: 300px">
									<script type="text/javascript">
										var dateStart=new LiveValidation('dateStart');
										dateStart.add( Validate.Format, {pattern: /^(0[1-9]|[12][0-9]|3[01])[- /.](0[1-9]|1[012])[- /.](19|20)\d\d$/i, failureMessage: "Use dd/mm/yyyy." } ); 
									 </script>
									 <script type="text/javascript">
										$(function() {
											$( "#dateStart" ).datepicker();
										});
									</script>
								</td>
							</tr>
							<tr>
								<td> 
									<b>Rubric</b><br/>
								</td>
								<td class="right">
									<?php
									$defaultRubric=getSettingByScope( $connection2, "IB PYP", "defaultRubric" ) ;
									?>
									<select name="gibbonRubricID" id="gibbonRubricID" style="width: 302px">
										<option><option>
										<optgroup label='--School Rubrics --'>
										<?php
										try {
											$dataSelect=array(); 
											$sqlSelectWhere="" ;
											$years=explode(",",$row["gibbonYearGroupIDList"]) ;
											foreach ($years as $year) {
												$dataSelect[$year]="%$year%" ;
												$sqlSelectWhere.=" AND gibbonYearGroupIDList LIKE :$year" ;
											}
											$sqlSelect="SELECT * FROM gibbonRubric WHERE active='Y' AND scope='School' $sqlSelectWhere ORDER BY category, name" ;
											$resultSelect=$connection2->prepare($sqlSelect);
											$resultSelect->execute($dataSelect);
										}
										catch(PDOException $e) { }
										while ($rowSelect=$resultSelect->fetch()) {
											$label="" ;
											if ($rowSelect["category"]=="") {
												$label=$rowSelect["name"] ;
											}
											else {
												$label=$rowSelect["category"] . " - " . $rowSelect["name"] ;
											}
											$selected="" ;
											if ($defaultRubric==$rowSelect["gibbonRubricID"]) {
												$selected="selected" ;
											}
											print "<option $selected value='" . $rowSelect["gibbonRubricID"] . "'>$label</option>" ;
										}
										if ($row["gibbonDepartmentID"]!="") {
											?>
											<optgroup label='--Learning Area Rubrics --'>
											<?php
											try {
												$dataSelect=array("gibbonDepartmentID"=>$row["gibbonDepartmentID"]); 
												$sqlSelectWhere="" ;
												$years=explode(",",$row["gibbonYearGroupIDList"]) ;
												foreach ($years as $year) {
													$dataSelect[$year]="%$year%" ;
													$sqlSelectWhere.=" AND gibbonYearGroupIDList LIKE :$year" ;
												}
												$sqlSelect="SELECT * FROM gibbonRubric WHERE active='Y' AND scope='Learning Area' AND gibbonDepartmentID=:gibbonDepartmentID $sqlSelectWhere ORDER BY category, name" ;
												$resultSelect=$connection2->prepare($sqlSelect);
												$resultSelect->execute($dataSelect);
											}
											catch(PDOException $e) { }
											while ($rowSelect=$resultSelect->fetch()) {
												$label="" ;
												if ($rowSelect["category"]=="") {
													$label=$rowSelect["name"] ;
												}
												else {
													$label=$rowSelect["category"] . " - " . $rowSelect["name"] ;
												}
												$selected="" ;
												if ($defaultRubric==$rowSelect["gibbonRubricID"]) {
													$selected="selected" ;
												}
												print "<option $selected value='" . $rowSelect["gibbonRubricID"] . "'>$label</option>" ;
											}
										}
										?>				
									</select>
								</td>
							</tr>
							<?php
							print "<tr>" ;
								print "<td class='right' colspan=2>" ;
									print "<input type='hidden' name='ibPYPUnitMasterID' value='$ibPYPUnitMasterID'>" ;
									print "<input type='hidden' name='gibbonSchoolYearID' value='$gibbonSchoolYearID'>" ;
									print "<input type='hidden' name='address' value='" . $_GET["q"] . "'>" ;
									print "<input id='submit' type='submit' value='Submit'>" ;
								print "</td>" ;
							print "</tr>" ;
						print "</table>" ;
					print "</form>" ;
				}
			}
		}
	}
}
?>
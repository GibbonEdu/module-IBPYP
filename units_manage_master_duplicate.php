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
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program. If not, see <http://www.gnu.org/licenses/>.
*/

@session_start() ;
$_SESSION[$guid]["ibPYPUnitsTab"]=1 ;

//Module includes
include "./modules/IB PYP/moduleFunctions.php" ;

if (isActionAccessible($guid, $connection2, "/modules/IB PYP/units_manage_master_edit.php")==FALSE) {
	//Acess denied
	print "<div class='error'>" ;
		print "You do not have access to this action." ;
	print "</div>" ;
}
else {
	print "<div class='trail'>" ;
	print "<div class='trailHead'><a href='" . $_SESSION[$guid]["absoluteURL"] . "'>Home</a> > <a href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/" . getModuleName($_GET["q"]) . "/" . getModuleEntry($_GET["q"], $connection2, $guid) . "'>" . getModuleName($_GET["q"]) . "</a> > <a href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/" . getModuleName($_GET["q"]) . "/units_manage.php&gibbonSchoolYearID=" . $_GET["gibbonSchoolYearID"] . "'>Manage Units</a> > </div><div class='trailEnd'>Duplicate Unit</div>" ;
	print "</div>" ;
	
	//Proceed!
	if (isset($_GET["updateReturn"])) { $updateReturn=$_GET["updateReturn"] ; } else { $updateReturn="" ; }
	$updateReturnMessage ="" ;
	$class="error" ;
	if (!($updateReturn=="")) {
		if ($updateReturn=="fail0") {
			$updateReturnMessage ="Update failed because you do not have access to this action." ;	
		}
		else if ($updateReturn=="fail1") {
			$updateReturnMessage ="Update failed because a required parameter was not set." ;	
		}
		else if ($updateReturn=="fail2") {
			$updateReturnMessage ="Update failed due to a database error." ;	
		}
		else if ($updateReturn=="fail3") {
			$updateReturnMessage ="Update failed because your inputs were invalid." ;	
		}
		else if ($updateReturn=="fail4") {
			$updateReturnMessage ="Update failed some values need to be unique but were not." ;	
		}
		else if ($updateReturn=="fail5") {
			$updateReturnMessage ="Add failed due to an attachment error." ;	
		}
		else if ($updateReturn=="fail6") {
			$updateReturnMessage ="Update succeeded, although some lessons or resources could not be saved." ;	
		}
		else if ($updateReturn=="success0") {
			$updateReturnMessage ="Update was successful." ;	
			$class="success" ;
		}
		print "<div class='$class'>" ;
			print $updateReturnMessage;
		print "</div>" ;
	} 
	
	//Check if school year specified
	$gibbonSchoolYearID=$_GET["gibbonSchoolYearID"];
	$ibPYPUnitMasterID=$_GET["ibPYPUnitMasterID"]; 
	if ($ibPYPUnitMasterID=="" OR $gibbonSchoolYearID=="") {
		print "<div class='error'>" ;
			print "You have not specified a unit." ;
		print "</div>" ;
	}
	else {	
		try {
			$data=array("ibPYPUnitMasterID"=>$ibPYPUnitMasterID); 
			$sql="SELECT ibPYPUnitMaster.*, gibbonCourse.name AS courseName FROM ibPYPUnitMaster JOIN gibbonCourse ON (ibPYPUnitMaster.gibbonCourseID=gibbonCourse.gibbonCourseID) WHERE ibPYPUnitMasterID=$ibPYPUnitMasterID" ;
			$result=$connection2->prepare($sql);
			$result->execute($data);
		}
		catch(PDOException $e) { 
			print "<div class='error'>" . $e->getMessage() . "</div>" ; 
		}
		
		if ($result->rowCount()!=1) {
			print "<div class='error'>" ;
				print "The specified unit cannot be found." ;
			print "</div>" ;
		}
		else {
			//Let's go!
			$row=$result->fetch() ;
				
			?>
			<form method="post" action="<? print $_SESSION[$guid]["absoluteURL"] . "/modules/IB PYP/units_manage_master_duplicateProcess.php?ibPYPUnitMasterID=$ibPYPUnitMasterID&gibbonSchoolYearID=$gibbonSchoolYearID&address=" . $_GET["q"] ?>">
				<table class='smallIntBorder' cellspacing='0' style="width: 100%">	
					<tr class='break'>
						<td colspan=2> 
							<h3 class='top'>Source</h3>
						</td>
					</tr>
					<tr>
						<td> 
							<b>School Year *</b><br/>
							<span style="font-size: 90%"><i>This value cannot be changed.</i></span>
						</td>
						<td class="right">
							<?
							try {
								$dataYear=array("gibbonSchoolYearID"=>$gibbonSchoolYearID); 
								$sqlYear="SELECT * FROM gibbonSchoolYear WHERE gibbonSchoolYearID=:gibbonSchoolYearID" ;
								$resultYear=$connection2->prepare($sqlYear);
								$resultYear->execute($dataYear);
							}
							catch(PDOException $e) { 
								print "<div class='error'>" . $e->getMessage() . "</div>" ; 
							}
							
							if ($resultYear->rowCount()!=1) {
								print "<i>Unknown</i>" ;
							}
							else {
								$rowYear=$resultYear->fetch() ;
								print "<input readonly value='" . $rowYear["name"] . "' type='text' style='width: 300px'>" ;
							}
							?>
						</td>
					</tr>
					<tr>
						<td> 
							<b>Course *</b><br/>
							<span style="font-size: 90%"><i>This value cannot be changed.</i></span>
						</td>
						<td class="right">
							<? print "<input readonly value='" . $row["courseName"] . "' type='text' style='width: 300px'>" ; ?>
						</td>
					</tr>
					<tr>
						<td> 
							<b>Unit *</b><br/>
							<span style="font-size: 90%"><i>This value cannot be changed.</i></span>
						</td>
						<td class="right">
							<? print "<input readonly value='" . $row["name"] . "' type='text' style='width: 300px'>" ; ?>
						</td>
					</tr>
					
					<tr class='break'>
						<td colspan=2> 
							<h3 class='top'>Target</h3>
						</td>
					</tr>
							
					<tr>
						<td> 
							<b>Year*</b><br/>
						</td>
						<td class="right">
							<select name="gibbonSchoolYearIDCopyTo" id="gibbonSchoolYearIDCopyTo" style="width: 302px">
								<?
								print "<option value='Please select...'>Please select...</option>" ;
								try {
									$dataSelect=array("gibbonSchoolYearID"=>$_SESSION[$guid]["gibbonSchoolYearID"]); 
									$sqlSelect="SELECT * FROM gibbonSchoolYear WHERE gibbonSchoolYearID=:gibbonSchoolYearID" ;
									$resultSelect=$connection2->prepare($sqlSelect);
									$resultSelect->execute($dataSelect);
								}
								catch(PDOException $e) { }
								if ($resultSelect->rowCount()==1) {
									$rowSelect=$resultSelect->fetch() ;
									try {
										$dataSelect2=array("sequenceNumber"=>$rowSelect["sequenceNumber"]); 
										$sqlSelect2="SELECT * FROM gibbonSchoolYear WHERE sequenceNumber>=:sequenceNumber ORDER BY sequenceNumber ASC" ;
										$resultSelect2=$connection2->prepare($sqlSelect2);
										$resultSelect2->execute($dataSelect2);
									}
									catch(PDOException $e) { }
									while ($rowSelect2=$resultSelect2->fetch()) {
										print "<option value='" . $rowSelect2["gibbonSchoolYearID"] . "'>" . htmlPrep($rowSelect2["name"]) . "</option>" ;
									}
								}		
								?>				
							</select>
							<script type="text/javascript">
								var gibbonSchoolYearIDCopyTo=new LiveValidation('gibbonSchoolYearIDCopyTo');
								gibbonSchoolYearIDCopyTo.add(Validate.Exclusion, { within: ['Please select...'], failureMessage: "Select something!"});
							 </script>
						</td>
					</tr>
					<tr>
						<td> 
							<b>Course *</b><br/>
						</td>
						<td class="right">
							<select name="gibbonCourseIDTarget" id="gibbonCourseIDTarget" style="width: 302px">
								<?
								try {
									$dataSelect=array(); 
									$sqlSelect="SELECT gibbonCourse.nameShort AS course, gibbonSchoolYear.name AS year, gibbonCourseID, gibbonSchoolYear.gibbonSchoolYearID FROM gibbonCourse JOIN gibbonSchoolYear ON (gibbonCourse.gibbonSchoolYearID=gibbonSchoolYear.gibbonSchoolYearID) ORDER BY nameShort" ;
									$resultSelect=$connection2->prepare($sqlSelect);
									$resultSelect->execute($dataSelect);
								}
								catch(PDOException $e) { }
								while ($rowSelect=$resultSelect->fetch()) {
									print "<option class='" . $rowSelect["gibbonSchoolYearID"] . "' value='" . $rowSelect["gibbonCourseID"] . "'>" . htmlPrep($rowSelect["course"]) . "</option>" ;
								}
								?>				
							</select>
							<script type="text/javascript">
								$("#gibbonCourseIDTarget").chainedTo("#gibbonSchoolYearIDCopyTo");
							</script>
						</td>
					</tr>
					<tr>
						<td> 
							<b>Unit *</b><br/>
							<span style="font-size: 90%"><i>This value cannot be changed.</i></span>
						</td>
						<td class="right">
							<? print "<input readonly value='" . $row["name"] . "' type='text' style='width: 300px'>" ; ?>
						</td>
					</tr>
					
					<tr>
						<td>
							<span style="font-size: 90%"><i>* denotes a required field</i></span>
						</td>
						<td class="right">
							<input type="hidden" name="address" value="<? print $_SESSION[$guid]["address"] ?>">
							<input type="submit" value="Submit">
						</td>
					</tr>
				</table>
			</form>
			<?
		}
	}
}
?>
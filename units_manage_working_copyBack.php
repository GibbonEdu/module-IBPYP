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

@session_start() ;
$_SESSION[$guid]["ibPYPUnitsTab"]=0 ;

//Module includes
include "./modules/IB PYP/moduleFunctions.php" ;

if (isActionAccessible($guid, $connection2, "/modules/IB PYP/units_manage_working_copyBack.php")==FALSE) {
	//Acess denied
	print "<div class='error'>" ;
		print "You do not have access to this action." ;
	print "</div>" ;
}
else {
	//Proceed!
	print "<div class='trail'>" ;
	print "<div class='trailHead'><a href='" . $_SESSION[$guid]["absoluteURL"] . "'>Home</a> > <a href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/" . getModuleName($_GET["q"]) . "/" . getModuleEntry($_GET["q"], $connection2, $guid) . "'>" . getModuleName($_GET["q"]) . "</a> > <a href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/" . getModuleName($_GET["q"]) . "/units_manage.php&gibbonSchoolYearID=" . $_GET["gibbonSchoolYearID"] . "'>Manage Units</a> > </div><div class='trailEnd'>Copy Back Working Unit</div>" ;
	print "</div>" ;
	
	if (isset($_GET["copyReturn"])) { $copyReturn=$_GET["copyReturn"] ; } else { $copyReturn="" ; }
	$copyReturnMessage ="" ;
	$class="error" ;
	if (!($copyReturn=="")) {
		if ($copyReturn=="fail0") {
			$copyReturnMessage ="Copy failed because you do not have access to this action." ;	
		}
		else if ($copyReturn=="fail2") {
			$copyReturnMessage ="Copy failed due to a database error." ;	
		}
		else if ($copyReturn=="fail3") {
			$copyReturnMessage ="Copy failed because your inputs were invalid." ;	
		}
		else if ($copyReturn=="fail6") {
			$copyReturnMessage ="Copy succeeded, but there were problems copying one or more elements." ;	
		}
		print "<div class='$class'>" ;
			print $copyReturnMessage;
		print "</div>" ;
	} 
	
	$role=getRole($_SESSION[$guid]["gibbonPersonID"], $connection2) ;
	if ($role!="Coordinator" AND $role!="Teacher (Curriculum)") {
		print "<div class='error'>" ;
			print "You do not have access to this action." ;
		print "</div>" ;
	}
	else {
		//Check if school year specified
		$ibPYPUnitWorkingID=$_GET["ibPYPUnitWorkingID"];
		$gibbonSchoolYearID=$_GET["gibbonSchoolYearID"] ;
		if ($ibPYPUnitWorkingID=="" OR $gibbonSchoolYearID=="") {
			print "<div class='error'>" ;
				print "You have not specified a master unit." ;
			print "</div>" ;
		}
		else {
			try {
				$data=array("gibbonSchoolYearID"=>$gibbonSchoolYearID, "ibPYPUnitWorkingID"=>$ibPYPUnitWorkingID);  
				$sql="SELECT ibPYPUnitWorking.*, gibbonCourse.nameShort AS course, gibbonCourseClass.nameShort AS class FROM ibPYPUnitWorking JOIN gibbonCourseClass ON (ibPYPUnitWorking.gibbonCourseClassID=gibbonCourseClass.gibbonCourseClassID) JOIN gibbonCourse ON (gibbonCourse.gibbonCourseID=gibbonCourseClass.gibbonCourseID) WHERE gibbonCourse.gibbonSchoolYearID=:gibbonSchoolYearID AND ibPYPUnitWorkingID=:ibPYPUnitWorkingID ORDER BY ibPYPUnitWorking.name" ; 
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
				
				print "<table class='smallIntBorder' cellspacing='0' style='width: 100%'>" ;
					print "<tr>" ;
						print "<td style='width: 34%; vertical-align: top'>" ;
							print "<span style='font-size: 115%; font-weight: bold'>Unit</span><br/>" ;
							print "<i>" . $row["name"] . "</i>" ;
						print "<td style='width: 33%; vertical-align: top'>" ;
							print "<span style='font-size: 115%; font-weight: bold'>Class</span><br/>" ;
							print $row["course"] . "." . $row["class"] ;
						print "</td>" ;
						print "<td style='width: 34%; vertical-align: top'>" ;
							
						print "</td>" ;
					print "</tr>" ;
				print "</table>" ;
				
				
				print "<p>" ;
				print "This function allows you to take all of the content from the selected working unit and use them to replace the content in the master unit. In this way you can use your refined and improved unit as your master next time you deploy." ;
				print "</p>" ;
				
				?>
				<form method="post" action="<? print $_SESSION[$guid]["absoluteURL"] . "/modules/IB PYP/units_manage_working_copyBackProcess.php?ibPYPUnitWorkingID=$ibPYPUnitWorkingID&gibbonSchoolYearID=$gibbonSchoolYearID" ?>">
					<table class='smallIntBorder' cellspacing='0' style="width: 100%">	
						<tr>
							<td> 
								<b>Are you sure you want to proceed with the unit copy back?</b><br/>
								<span style="font-size: 90%; color: #cc0000"><i>This operation cannot be undone. PROCEED WITH CAUTION!</i></span>
							</td>
							<td class="right">
								
							</td>
						</tr>
						<tr>
							<td>
								<input name="ibPYPUnitWorkingID" id="ibPYPUnitWorkingID" value="<? print $ibPYPUnitWorkingID ?>" type="hidden">
								<input type="hidden" name="address" value="<? print $_SESSION[$guid]["address"] ?>">
								<input type="submit" value="Yes">
							</td>
							<td class="right">
								
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
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

//Module includes
include "./modules/IB PYP/moduleFunctions.php" ;

if (isActionAccessible($guid, $connection2, "/modules/IB PYP/units_manage.php")==FALSE) {
	//Acess denied
	print "<div class='error'>" ;
		print "You do not have access to this action." ;
	print "</div>" ;
}
else {
	print "<div class='trail'>" ;
	print "<div class='trailHead'><a href='" . $_SESSION[$guid]["absoluteURL"] . "'>Home</a> > <a href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/" . getModuleName($_GET["q"]) . "/" . getModuleEntry($_GET["q"], $connection2, $guid) . "'>" . getModuleName($_GET["q"]) . "</a> > </div><div class='trailEnd'>Manage Units</div>" ;
	print "</div>" ;
	print "<p>" ;
		print "Master Units holds the school stock of teaching units: these can be deployed to a particular group to create a Working Unit. Units exist in a particular school year, and are tied to a specific course." ;
	print "</p>" ;
	
	$deleteReturn = $_GET["deleteReturn"] ;
	$deleteReturnMessage ="" ;
	$class="error" ;
	if (!($deleteReturn=="")) {
		if ($deleteReturn=="fail4") {
			$deleteReturnMessage ="Delete was successful, bust some related data could not be deleted." ;	
		}
		else if ($deleteReturn=="success0") {
			$deleteReturnMessage ="Delete was successful." ;	
			$class="success" ;
		}
		print "<div class='$class'>" ;
			print $deleteReturnMessage;
		print "</div>" ;
	}
	
	$copyReturn = $_GET["copyReturn"] ;
	$copyReturnMessage ="" ;
	$class="error" ;
	if (!($copyReturn=="")) {
		if ($copyReturn=="success0") {
			$copyReturnMessage ="Copy was successful. The contents from the selected working unit have replaced that of the master unit." ;	
			$class="success" ;
		}
		print "<div class='$class'>" ;
			print $copyReturnMessage;
		print "</div>" ;
	} 
	
	$deployReturn = $_GET["deployReturn"] ;
	$deployReturnMessage ="" ;
	$class="error" ;
	if (!($deployReturn=="")) {
		if ($deployReturn=="success0") {
			$deployReturnMessage ="Deploy was successful." ;	
			$class="success" ;
		}
		print "<div class='$class'>" ;
			print $deployReturnMessage;
		print "</div>" ;
	} 
	
	$gibbonSchoolYearID=$_GET["gibbonSchoolYearID"] ;
	if ($gibbonSchoolYearID=="") {
		$gibbonSchoolYearID=$_SESSION[$guid]["gibbonSchoolYearID"] ;
		$gibbonSchoolYearName=$_SESSION[$guid]["gibbonSchoolYearName"] ;
	}
	if ($_GET["gibbonSchoolYearID"]!="") {
		try {
			$data=array("gibbonSchoolYearID"=>$_GET["gibbonSchoolYearID"]); 
			$sql="SELECT * FROM gibbonSchoolYear WHERE gibbonSchoolYearID=:gibbonSchoolYearID" ;
			$result=$connection2->prepare($sql);
			$result->execute($data);
		}
		catch(PDOException $e) { 
			print "<div class='error'>" . $e->getMessage() . "</div>" ; 
		}
		
		if ($result->rowCount()!=1) {
			print "<div class='error'>" ;
				print "The specified year does not exist." ;
			print "</div>" ;
		}
		else {
			$row=$result->fetch() ;
			$gibbonSchoolYearID=$row["gibbonSchoolYearID"] ;
			$gibbonSchoolYearName=$row["name"] ;
		}
	}
	
	if ($gibbonSchoolYearID!="") {
		print "<h2 class='top'>" ;
			print $gibbonSchoolYearName ;
		print "</h2>" ;
		
		print "<div class='linkTop'>" ;
			//Print year picker
			if (getPreviousSchoolYearID($gibbonSchoolYearID, $connection2)!=FALSE) {
				print "<a href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/IB PYP/units_manage.php&gibbonSchoolYearID=" . getPreviousSchoolYearID($gibbonSchoolYearID, $connection2) . "'>Previous Year</a> " ;
			}
			else {
				print "Previous Year " ;
			}
			print " | " ;
			if (getNextSchoolYearID($gibbonSchoolYearID, $connection2)!=FALSE) {
				print "<a href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/IB PYP/units_manage.php&gibbonSchoolYearID=" . getNextSchoolYearID($gibbonSchoolYearID, $connection2) . "'>Next Year</a> " ;
			}
			else {
				print "Next Year " ;
			}
		print "</div>" ;
	
		//Set pagination variable
		$page=$_GET["page"] ;
		if ((!is_numeric($page)) OR $page<1) {
			$page=1 ;
		}
	
		$role=getRole($_SESSION[$guid]["gibbonPersonID"], $connection2) ;
	
		if ($_SESSION[$guid]["ibPYPUnitsTab"]=="" OR !(is_numeric($_SESSION[$guid]["ibPYPUnitsTab"]))) {
			$_SESSION[$guid]["ibPYPUnitsTab"]=0 ;
		}
		?><script type='text/javascript'>
			$(function() {
				$( "#tabs" ).tabs({
					active: <? print $_SESSION[$guid]["ibPYPUnitsTab"] ?>, 
					ajaxOptions: {
						error: function( xhr, status, index, anchor ) {
							$( anchor.hash ).html(
								"Couldn't load this tab." );
						}
					}
				});
			});
		</script><?
	
		if ($role!="") {
			print "<div id='tabs' style='margin: 20px 0'>" ;
				print "<ul>" ;
					print "<li><a href='#tabs-1'>Working Units</a></li>" ;
					if ($role=="Coordinator" OR $role=="Teacher (Curriculum)") {
						print "<li><a href='#tabs-2'>Master Units</a></li>" ;
					}
				print "</ul>" ;
			
				print "<div id='tabs-1'>" ;
					try {
						if ($role=="Coordinator" OR $role=="Teacher (Curriculum)") {
							$data=array("gibbonSchoolYearID"=>$gibbonSchoolYearID);  
							$sql="SELECT ibPYPUnitWorking.*, gibbonCourse.nameShort AS course, gibbonCourseClass.nameShort AS class FROM ibPYPUnitWorking JOIN gibbonCourseClass ON (ibPYPUnitWorking.gibbonCourseClassID=gibbonCourseClass.gibbonCourseClassID) JOIN gibbonCourse ON (gibbonCourse.gibbonCourseID=gibbonCourseClass.gibbonCourseID) WHERE gibbonCourse.gibbonSchoolYearID=:gibbonSchoolYearID ORDER BY ibPYPUnitWorking.name, course, class" ; 
						}
						else {
							$data=array("gibbonSchoolYearID"=>$gibbonSchoolYearID, "gibbonPersonID"=>$_SESSION[$guid]["gibbonPersonID"]);  
							$sql="SELECT DISTINCT ibPYPUnitWorking.*, gibbonCourse.nameShort AS course, gibbonCourseClass.nameShort AS class FROM ibPYPUnitWorking JOIN gibbonCourseClass ON (ibPYPUnitWorking.gibbonCourseClassID=gibbonCourseClass.gibbonCourseClassID) JOIN gibbonCourse ON (ibPYPUnitWorking.gibbonCourseID=gibbonCourse.gibbonCourseID) JOIN gibbonCourseClassPerson ON (gibbonCourseClassPerson.gibbonCourseClassID=gibbonCourseClass.gibbonCourseClassID) WHERE gibbonCourse.gibbonSchoolYearID=:gibbonSchoolYearID AND gibbonCourseClassPerson.gibbonPersonID=:gibbonPersonID ORDER BY name, course, class" ; 
						}
						$result=$connection2->prepare($sql);
						$result->execute($data);
					}
					catch(PDOException $e) { 
						print "<div class='error'>" . $e->getMessage() . "</div>" ; 
					}

					if ($result->rowCount()<1) {
						print "<div class='error'>" ;
						print "There are no units to display." ;
						print "</div>" ;
					}
					else {
						print "<table style='width: 100%'>" ;
							print "<tr class='head'>" ;
								print "<th>" ;
									print "Name" ;
								print "</th>" ;
								print "<th>" ;
									print "Class" ;
								print "</th>" ;
								print "<th>" ;
									print "Actions" ;
								print "</th>" ;
							print "</tr>" ;
						
							$count=0;
							$rowNum="odd" ;
							while ($row=$result->fetch()) {
								if ($count%2==0) {
									$rowNum="even" ;
								}
								else {
									$rowNum="odd" ;
								}
								$count++ ;
							
								if ($row["active"]=="N") {
									$rowNum="error" ;
								}
			
								//COLOR ROW BY STATUS!
								print "<tr class=$rowNum>" ;
									print "<td>" ;
										print "<b>" . $row["name"] . "<br/>" ;
									print "</td>" ;
									print "<td>" ;
										print $row["course"] . "." . $row["class"] ;
									print "</td>" ;
									print "<td>" ;
										print "<a href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/IB PYP/units_manage_working_edit.php&ibPYPUnitWorkingID=" . $row["ibPYPUnitWorkingID"] . "&gibbonSchoolYearID=$gibbonSchoolYearID'><img title='Edit' src='./themes/" . $_SESSION[$guid]["gibbonThemeName"] . "/img/config.png'/></a> " ;
										if ($role=="Coordinator" OR $role=="Teacher (Curriculum)") {
											print "<a href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/IB PYP/units_manage_working_delete.php&ibPYPUnitWorkingID=" . $row["ibPYPUnitWorkingID"] . "&gibbonSchoolYearID=$gibbonSchoolYearID&tab=0'><img title='Delete' src='./themes/" . $_SESSION[$guid]["gibbonThemeName"] . "/img/garbage.png'/></a> " ;
											print "<a href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/IB PYP/units_manage_working_copyBack.php&ibPYPUnitWorkingID=" . $row["ibPYPUnitWorkingID"] . "&gibbonSchoolYearID=$gibbonSchoolYearID&tab=0'><img title='Copy Back' src='./themes/" . $_SESSION[$guid]["gibbonThemeName"] . "/img/copyback.png'/></a> " ;
										}
									print "</td>" ;
								print "</tr>" ;
							}
						print "</table>" ;
					}
				print "</div>" ;
				if ($role=="Coordinator" OR $role=="Teacher (Curriculum)") {
					print "<div id='tabs-2'>" ;
						try {
							$data=array("gibbonSchoolYearID"=>$gibbonSchoolYearID);  
							$sql="SELECT ibPYPUnitMaster.*, gibbonCourse.nameShort FROM ibPYPUnitMaster LEFT JOIN gibbonCourse ON (ibPYPUnitMaster.gibbonCourseID=gibbonCourse.gibbonCourseID) WHERE gibbonCourse.gibbonSchoolYearID=:gibbonSchoolYearID ORDER BY ibPYPUnitMaster.name, nameShort" ; 
							$result=$connection2->prepare($sql);
							$result->execute($data);
						}
						catch(PDOException $e) { 
							print "<div class='error'>" . $e->getMessage() . "</div>" ; 
						}
					
						print "<div class='linkTop'>" ;
						print "<a href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/IB PYP/units_manage_master_add.php&gibbonSchoolYearID=$gibbonSchoolYearID'><img title='New' src='./themes/" . $_SESSION[$guid]["gibbonThemeName"] . "/img/page_new.gif'/></a>" ;
						print "</div>" ;
					
						if ($result->rowCount()<1) {
							print "<div class='error'>" ;
							print "There are no units to display." ;
							print "</div>" ;
						}
						else {
							print "<table style='width: 100%'>" ;
								print "<tr class='head'>" ;
									print "<th>" ;
										print "Name" ;
									print "</th>" ;
									print "<th>" ;
										print "Course" ;
									print "</th>" ;
									print "<th>" ;
										print "Active" ;
									print "</th>" ;
									print "<th>" ;
										print "Actions" ;
									print "</th>" ;
								print "</tr>" ;
							
								$count=0;
								$rowNum="odd" ;
								while ($row=$result->fetch()) {
									if ($count%2==0) {
										$rowNum="even" ;
									}
									else {
										$rowNum="odd" ;
									}
									$count++ ;
								
									if ($row["active"]=="N") {
										$rowNum="error" ;
									}
				
									//COLOR ROW BY STATUS!
									print "<tr class=$rowNum>" ;
										print "<td>" ;
											print "<b>" . $row["name"] . "<br/>" ;
										print "</td>" ;
										print "<td>" ;
											print $row["nameShort"] ;
										print "</td>" ;
										print "<td>" ;
											print $row["active"] ;
										print "</td>" ;
										print "<td>" ;
											print "<a href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/IB PYP/units_manage_master_edit.php&ibPYPUnitMasterID=" . $row["ibPYPUnitMasterID"] . "&gibbonSchoolYearID=$gibbonSchoolYearID&tab=1'><img title='Edit' src='./themes/" . $_SESSION[$guid]["gibbonThemeName"] . "/img/config.png'/></a> " ;
											print "<a href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/IB PYP/units_manage_master_delete.php&ibPYPUnitMasterID=" . $row["ibPYPUnitMasterID"] . "&gibbonSchoolYearID=$gibbonSchoolYearID&tab=1'><img title='Delete' src='./themes/" . $_SESSION[$guid]["gibbonThemeName"] . "/img/garbage.png'/></a> " ;
											print "<a href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/IB PYP/units_manage_master_duplicate.php&ibPYPUnitMasterID=" . $row["ibPYPUnitMasterID"] . "&gibbonSchoolYearID=$gibbonSchoolYearID&tab=1'><img title='Duplicate' src='./themes/" . $_SESSION[$guid]["gibbonThemeName"] . "/img/copy.png'/></a> " ;										
											if ($row["active"]=="Y") {
												print "<a href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/IB PYP/units_manage_master_deploy.php&ibPYPUnitMasterID=" . $row["ibPYPUnitMasterID"] . "&gibbonSchoolYearID=$gibbonSchoolYearID&tab=1'><img title='Deploy' src='./themes/" . $_SESSION[$guid]["gibbonThemeName"] . "/img/page_right.png'/></a> " ;
											}
										print "</td>" ;
									print "</tr>" ;
								}
							print "</table>" ;
						}
					print "</div>" ;
				}
			print "</div>" ;
		}
	}
	else {
		print "<div class='error'>" ;
		print "You do not have access to this action." ;
		print "</div>" ;
	}
}	
?>
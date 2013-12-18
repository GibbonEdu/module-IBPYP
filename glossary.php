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

//Module includes
include "./modules/IB PYP/moduleFunctions.php" ;

if (isActionAccessible($guid, $connection2, "/modules/IB PYP/glossary.php")==FALSE) {
	//Acess denied
	print "<div class='error'>" ;
		print "You do not have access to this action." ;
	print "</div>" ;
}
else {
	print "<div class='trail'>" ;
	print "<div class='trailHead'><a href='" . $_SESSION[$guid]["absoluteURL"] . "'>Home</a> > <a href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/" . getModuleName($_GET["q"]) . "/" . getModuleEntry($_GET["q"], $connection2, $guid) . "'>" . getModuleName($_GET["q"]) . "</a> > </div><div class='trailEnd'>Essential Elements</div>" ;
	print "</div>" ;
	
	if (isset($_GET["deleteReturn"])) { $deleteReturn=$_GET["deleteReturn"] ; } else { $deleteReturn="" ; }
	$deleteReturnMessage ="" ;
	$class="error" ;
	if (!($deleteReturn=="")) {
		if ($deleteReturn=="success0") {
			$deleteReturnMessage ="Delete was successful." ;	
			$class="success" ;
		}
		print "<div class='$class'>" ;
			print $deleteReturnMessage;
		print "</div>" ;
	} 
	
	$role=getRole($_SESSION[$guid]["gibbonPersonID"], $connection2) ;
	
	print "<p>" ;
		if ($role=="Coordinator") {
			print "Essential Elements allows users to view and edit the school's stock of concepts, transdisciplinary skills, learner profiles and attitudes." ;
		}
		else {
			print "Essential Elements allows users to view the school's stock of concepts, transdisciplinary skills, learner profiles and attitudes." ;
		}
	print "</p>" ;
	
	if ($role!="") {
		try {
			$data=array();  
			$sql="SELECT * FROM ibPYPGlossary ORDER BY type, category, title" ; 
			$result=$connection2->prepare($sql);
			$result->execute($data);
		}
		catch(PDOException $e) { 
			print "<div class='error'>" . $e->getMessage() . "</div>" ; 
		}
		
		if ($role=="Coordinator") {
			print "<div class='linkTop'>" ;
			print "<a href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/IB PYP/glossary_add.php'><img title='New' src='./themes/" . $_SESSION[$guid]["gibbonThemeName"] . "/img/page_new.gif'/></a>" ;
			print "</div>" ;
		}
		if ($result->rowCount()<1) {
			print "<div class='error'>" ;
			print "There are no glossary terms to display." ;
			print "</div>" ;
		}
		else {
			print "<table cellspacing='0' style='width: 100%'>" ;
				print "<tr class='head'>" ;
					print "<th>" ;
						print "Type/Category" ;
					print "</th>" ;
					print "<th>" ;
						print "Title" ;
					print "</th>" ;
					print "<th>" ;
						print "Content" ;
					print "</th>" ;
					if ($role=="Coordinator") {
						print "<th style='width: 75px'>" ;
							print "Actions" ;
						print "</th>" ;
					}
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
					
					//COLOR ROW BY STATUS!
					print "<tr class=$rowNum>" ;
						print "<td>" ;
							print "<b>" . $row["type"] . "</b><br/>" ;
							print "<i>" . $row["category"] . "</i>" ;
						print "</td>" ;
						print "<td>" ;
							print $row["title"] ;
						print "</td>" ;
						print "<td>" ;
							print $row["content"] ;
						print "</td>" ;
						if ($role=="Coordinator") {
							print "<td>" ;
								print "<a href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/IB PYP/glossary_edit.php&ibPYPGlossaryID=" . $row["ibPYPGlossaryID"] . "'><img title='Edit' src='./themes/" . $_SESSION[$guid]["gibbonThemeName"] . "/img/config.png'/></a> " ;
								print "<a href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/IB PYP/glossary_delete.php&ibPYPGlossaryID=" . $row["ibPYPGlossaryID"] . "'><img title='Delete' src='./themes/" . $_SESSION[$guid]["gibbonThemeName"] . "/img/garbage.png'/></a> " ;
							print "</td>" ;
						}
					print "</tr>" ;
				}
			print "</table>" ;
		}
	}
	else {
		print "<div class='error'>" ;
		print "You do not have access to this action." ;
		print "</div>" ;
	}
}	
?>
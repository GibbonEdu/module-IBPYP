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

@session_start();

//Module includes
include './modules/IB PYP/moduleFunctions.php';

if (isActionAccessible($guid, $connection2, '/modules/IB PYP/student_manage_delete.php') == false) {

    //Acess denied
    echo "<div class='error'>";
    echo 'You do not have access to this action.';
    echo '</div>';
} else {
    //Proceed!
    echo "<div class='trail'>";
    echo "<div class='trailHead'><a href='".$_SESSION[$guid]['absoluteURL']."'>Home</a> > <a href='".$_SESSION[$guid]['absoluteURL'].'/index.php?q=/modules/'.getModuleName($_GET['q']).'/'.getModuleEntry($_GET['q'], $connection2, $guid)."'>".getModuleName($_GET['q'])."</a> > <a href='".$_SESSION[$guid]['absoluteURL'].'/index.php?q=/modules/'.getModuleName($_GET['q'])."/student_manage.php'>Student Enrolment</a> > </div><div class='trailEnd'>Delete Student Enrolment</div>";
    echo '</div>';

    if (isset($_GET['return'])) {
        returnProcess($guid, $_GET['return'], null, null);
    }

    //Check if school year specified
    $ibPYPStudentID = $_GET['ibPYPStudentID'];
    if ($ibPYPStudentID == '') { echo "<div class='error'>";
        echo 'You have not specified a student member.';
        echo '</div>';
    } else {
        try {
            $data = array('gibbonSchoolYearID' => $_SESSION[$guid]['gibbonSchoolYearID'], 'ibPYPStudentID' => $ibPYPStudentID);
            $sql = "SELECT ibPYPStudentID, surname, preferredName, start.name AS start, end.name AS end, gibbonYearGroup.nameShort AS yearGroup, gibbonRollGroup.nameShort AS rollGroup FROM ibPYPStudent JOIN gibbonPerson ON (ibPYPStudent.gibbonPersonID=gibbonPerson.gibbonPersonID) JOIN gibbonStudentEnrolment ON (ibPYPStudent.gibbonPersonID=gibbonStudentEnrolment.gibbonPersonID) LEFT JOIN gibbonSchoolYear AS start ON (start.gibbonSchoolYearID=ibPYPStudent.gibbonSchoolYearIDStart) LEFT JOIN gibbonSchoolYear AS end ON (end.gibbonSchoolYearID=ibPYPStudent.gibbonSchoolYearIDEnd) LEFT JOIN gibbonYearGroup ON (gibbonStudentEnrolment.gibbonYearGroupID=gibbonYearGroup.gibbonYearGroupID) LEFT JOIN gibbonRollGroup ON (gibbonStudentEnrolment.gibbonRollGroupID=gibbonRollGroup.gibbonRollGroupID) WHERE gibbonStudentEnrolment.gibbonSchoolYearID=:gibbonSchoolYearID AND gibbonPerson.status='Full' AND ibPYPStudentID=:ibPYPStudentID ORDER BY start.sequenceNumber, surname, preferredName";
            $result = $connection2->prepare($sql);
            $result->execute($data);
        } catch (PDOException $e) {
            echo "<div class='error'>".$e->getMessage().'</div>';
        }

        if ($result->rowCount() != 1) {
            echo "<div class='error'>";
            echo 'The selected student member does not exist.';
            echo '</div>';
        } else {
            //Let's go!
            $row = $result->fetch();
            ?>
			<form method="post" action="<?php echo $_SESSION[$guid]['absoluteURL']."/modules/IB PYP/student_manage_deleteProcess.php?ibPYPStudentID=$ibPYPStudentID" ?>">
				<table class='smallIntBorder' cellspacing='0' style="width: 100%">
					<tr>
						<td>
							<b>Are you sure you want to delete "<?php echo formatName('', $row['preferredName'], $row['surname'], 'Student', true, true) ?>" from the IB PYP programme?</b><br/>
							<span style="font-size: 90%; color: #cc0000"><i>This operation cannot be undone, and may lead to loss of vital data in your system.<br/>PROCEED WITH CAUTION!</i></span>
						</td>
						<td class="right">

						</td>
					</tr>
					<tr>
						<td>
							<input name="ibPYPStudentID" id="ibPYPStudentID" value="<?php echo $ibPYPStudentID ?>" type="hidden">
							<input type="hidden" name="address" value="<?php echo $_SESSION[$guid]['address'] ?>">
							<input type="submit" value="Yes">
						</td>
						<td class="right">

						</td>
					</tr>
				</table>
			</form>
			<?php

        }
    }
}
?>

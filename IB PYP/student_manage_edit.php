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

if (isActionAccessible($guid, $connection2, '/modules/IB PYP/student_manage_edit.php') == false) {
    //Acess denied
    echo "<div class='error'>";
    echo 'You do not have access to this action.';
    echo '</div>';
} else {
    //Proceed!
    echo "<div class='trail'>";
    echo "<div class='trailHead'><a href='".$_SESSION[$guid]['absoluteURL']."'>Home</a> > <a href='".$_SESSION[$guid]['absoluteURL'].'/index.php?q=/modules/'.getModuleName($_GET['q']).'/'.getModuleEntry($_GET['q'], $connection2, $guid)."'>".getModuleName($_GET['q'])."</a> > <a href='".$_SESSION[$guid]['absoluteURL'].'/index.php?q=/modules/'.getModuleName($_GET['q'])."/student_manage.php'>Student Enrolment</a> > </div><div class='trailEnd'>Edit Student Enrolment</div>";
    echo '</div>';

    if (isset($_GET['return'])) {
        returnProcess($guid, $_GET['return'], null, null);
    }

    //Check if school year specified
    $ibPYPStudentID = $_GET['ibPYPStudentID'];
    if ($ibPYPStudentID == 'Y') { echo "<div class='error'>";
        echo 'You have not specified an activity.';
        echo '</div>';
    } else {
        try {
            $data = array('gibbonSchoolYearID' => $_SESSION[$guid]['gibbonSchoolYearID'], 'ibPYPStudentID' => $ibPYPStudentID);
            $sql = "SELECT ibPYPStudentID, surname, preferredName, start.name AS start, start.gibbonSchoolYearID AS gibbonSchoolYearIDStart, end.name AS end, end.gibbonSchoolYearID AS gibbonSchoolYearIDEnd, gibbonYearGroup.nameShort AS yearGroup, gibbonRollGroup.nameShort AS rollGroup FROM ibPYPStudent JOIN gibbonPerson ON (ibPYPStudent.gibbonPersonID=gibbonPerson.gibbonPersonID) JOIN gibbonStudentEnrolment ON (ibPYPStudent.gibbonPersonID=gibbonStudentEnrolment.gibbonPersonID) LEFT JOIN gibbonSchoolYear AS start ON (start.gibbonSchoolYearID=ibPYPStudent.gibbonSchoolYearIDStart) LEFT JOIN gibbonSchoolYear AS end ON (end.gibbonSchoolYearID=ibPYPStudent.gibbonSchoolYearIDEnd) LEFT JOIN gibbonYearGroup ON (gibbonStudentEnrolment.gibbonYearGroupID=gibbonYearGroup.gibbonYearGroupID) LEFT JOIN gibbonRollGroup ON (gibbonStudentEnrolment.gibbonRollGroupID=gibbonRollGroup.gibbonRollGroupID) WHERE gibbonStudentEnrolment.gibbonSchoolYearID=:gibbonSchoolYearID AND gibbonPerson.status='Full' AND ibPYPStudentID=:ibPYPStudentID ORDER BY start.sequenceNumber, surname, preferredName";
            $result = $connection2->prepare($sql);
            $result->execute($data);
        } catch (PDOException $e) {
            echo "<div class='error'>".$e->getMessage().'</div>';
        }

        if ($result->rowCount() != 1) {
            echo "<div class='error'>";
            echo 'The selected activity does not exist.';
            echo '</div>';
        } else {
            //Let's go!
            $row = $result->fetch();
            ?>
			<form method="post" action="<?php echo $_SESSION[$guid]['absoluteURL']."/modules/IB PYP/student_manage_editProcess.php?ibPYPStudentID=$ibPYPStudentID" ?>">
				<table class='smallIntBorder' cellspacing='0' style="width: 100%">
					<tr>
						<td>
							<b>Student *</b><br/>
							<span style="font-size: 90%"><i>This value cannot be changed</i></span>
						</td>
						<td class="right">
							<input readonly type='text' style='width: 302px' value='<?php echo formatName('', $row['preferredName'], $row['surname'], 'Student', true, true) ?>'>
							<script type="text/javascript">
								var gibbonPersonID=new LiveValidation('gibbonPersonID');
								gibbonPersonID.add(Validate.Exclusion, { within: ['Please select...'], failureMessage: "Select something!"});
							 </script>
						</td>
					</tr>
					<tr>
						<td>
							<b>Start Year *</b><br/>
							<span style="font-size: 90%"><i></i></span>
						</td>
						<td class="right">
							<select name="gibbonSchoolYearIDStart" id="gibbonSchoolYearIDStart" style="width: 302px">
								<option value="Please select...">Please select...</option>
								<?php
                                    try {
                                        $dataSelect = array();
                                        $sqlSelect = 'SELECT * FROM gibbonSchoolYear ORDER BY sequenceNumber';
                                        $resultSelect = $connection2->prepare($sqlSelect);
                                        $resultSelect->execute($dataSelect);
                                    } catch (PDOException $e) {
                                    }
									while ($rowSelect = $resultSelect->fetch()) {
										$selected = '';
										if ($row['gibbonSchoolYearIDStart'] == $rowSelect['gibbonSchoolYearID']) {
											$selected = 'selected';
										}
										echo "<option $selected value=".$rowSelect['gibbonSchoolYearID'].'>'.$rowSelect['name'].'</option>';
									}
									?>
							</select>
							<script type="text/javascript">
								var gibbonSchoolYearIDStart=new LiveValidation('gibbonSchoolYearIDStart');
								gibbonSchoolYearIDStart.add(Validate.Exclusion, { within: ['Please select...'], failureMessage: "Select something!"});
							 </script>
						</td>
					</tr>
					<tr>
						<td>
							<b>End Year *</b><br/>
							<span style="font-size: 90%"><i></i></span>
						</td>
						<td class="right">
							<select name="gibbonSchoolYearIDEnd" id="gibbonSchoolYearIDEnd" style="width: 302px">
								<option value="Please select...">Please select...</option>
								<?php
                                    try {
                                        $dataSelect = array();
                                        $sqlSelect = 'SELECT * FROM gibbonSchoolYear ORDER BY sequenceNumber';
                                        $resultSelect = $connection2->prepare($sqlSelect);
                                        $resultSelect->execute($dataSelect);
                                    } catch (PDOException $e) {
                                    }
									while ($rowSelect = $resultSelect->fetch()) {
										$selected = '';
										if ($row['gibbonSchoolYearIDEnd'] == $rowSelect['gibbonSchoolYearID']) {
											$selected = 'selected';
										}
										echo "<option $selected value=".$rowSelect['gibbonSchoolYearID'].'>'.$rowSelect['name'].'</option>';
									}
									?>
							</select>
							<script type="text/javascript">
								var gibbonSchoolYearIDEnd=new LiveValidation('gibbonSchoolYearIDEnd');
								gibbonSchoolYearIDEnd.add(Validate.Exclusion, { within: ['Please select...'], failureMessage: "Select something!"});
							 </script>
						</td>
					</tr>
					<tr>
						<td>
							<span style="font-size: 90%"><i>* denotes a required field</i></span>
						</td>
						<td class="right">
							<input type="hidden" name="address" value="<?php echo $_SESSION[$guid]['address'] ?>">
							<input type="submit" value="Submit">
						</td>
					</tr>
				</table>
			</form>
			<?php

        }
    }
}
?>

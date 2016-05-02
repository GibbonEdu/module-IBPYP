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

if (isActionAccessible($guid, $connection2, '/modules/IB PYP/student_manage_add.php') == false) {

    //Acess denied
    echo "<div class='error'>";
    echo 'You do not have access to this action.';
    echo '</div>';
} else {
    echo "<div class='trail'>";
    echo "<div class='trailHead'><a href='".$_SESSION[$guid]['absoluteURL']."'>Home</a> > <a href='".$_SESSION[$guid]['absoluteURL'].'/index.php?q=/modules/'.getModuleName($_GET['q']).'/'.getModuleEntry($_GET['q'], $connection2, $guid)."'>".getModuleName($_GET['q'])."</a> > <a href='".$_SESSION[$guid]['absoluteURL'].'/index.php?q=/modules/'.getModuleName($_GET['q'])."/student_manage.php'>Student Enrolment</a> > </div><div class='trailEnd'>Add Student Enrolment</div>";
    echo '</div>';

    if (isset($_GET['return'])) {
        returnProcess($guid, $_GET['return'], null, null);
    }

    ?>
	<form method="post" action="<?php echo $_SESSION[$guid]['absoluteURL'].'/modules/IB PYP/student_manage_addProcess.php' ?>">
		<table class='smallIntBorder' cellspacing='0' style="width: 100%">
			<tr>
				<td>
					<b>Students *</b><br/>
				</td>
				<td class="right">
					<select name="Members[]" id="Members[]" multiple style="width: 302px; height: 150px">
						<?php
                        try {
                            $dataSelect = array('gibbonSchoolYearID' => $_SESSION[$guid]['gibbonSchoolYearID']);
                            $sqlSelect = "SELECT gibbonPerson.gibbonPersonID, preferredName, surname, gibbonRollGroup.name AS name FROM gibbonPerson, gibbonStudentEnrolment, gibbonRollGroup WHERE gibbonPerson.gibbonPersonID=gibbonStudentEnrolment.gibbonPersonID AND gibbonStudentEnrolment.gibbonRollGroupID=gibbonRollGroup.gibbonRollGroupID AND status='FULL' AND gibbonRollGroup.gibbonSchoolYearID=:gibbonSchoolYearID ORDER BY name, surname, preferredName";
                            $resultSelect = $connection2->prepare($sqlSelect);
                            $resultSelect->execute($dataSelect);
                        } catch (PDOException $e) {
                        }
    while ($rowSelect = $resultSelect->fetch()) {
        echo "<option value='".$rowSelect['gibbonPersonID']."'>".htmlPrep($rowSelect['name']).' - '.formatName('', $rowSelect['preferredName'], $rowSelect['surname'], 'Student', true, true).'</option>';
    }
    ?>
					</select>
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
        echo '<option value='.$rowSelect['gibbonSchoolYearID'].'>'.$rowSelect['name'].'</option>';
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
        echo '<option value='.$rowSelect['gibbonSchoolYearID'].'>'.$rowSelect['name'].'</option>';
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
?>

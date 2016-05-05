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

if (isActionAccessible($guid, $connection2, '/modules/IB PYP/staff_manage_edit.php') == false) {

    //Acess denied
    echo "<div class='error'>";
    echo 'You do not have access to this action.';
    echo '</div>';
} else {
    //Proceed!
    echo "<div class='trail'>";
    echo "<div class='trailHead'><a href='".$_SESSION[$guid]['absoluteURL']."'>Home</a> > <a href='".$_SESSION[$guid]['absoluteURL'].'/index.php?q=/modules/'.getModuleName($_GET['q']).'/'.getModuleEntry($_GET['q'], $connection2, $guid)."'>".getModuleName($_GET['q'])."</a> > <a href='".$_SESSION[$guid]['absoluteURL'].'/index.php?q=/modules/'.getModuleName($_GET['q'])."/staff_manage.php'>Manage Teaching Staff</a> > </div><div class='trailEnd'>Edit CAS Staff</div>";
    echo '</div>';

    if (isset($_GET['return'])) {
        returnProcess($guid, $_GET['return'], null, null);
    }

    //Check if school year specified
    $ibPYPStaffTeachingID = $_GET['ibPYPStaffTeachingID'];
    if ($ibPYPStaffTeachingID == '') { echo "<div class='error'>";
        echo 'You have not specified a member of staff.';
        echo '</div>';
    } else {
        try {
            $data = array('ibPYPStaffTeachingID' => $ibPYPStaffTeachingID);
            $sql = "SELECT ibPYPStaffTeachingID, ibPYPStaffTeaching.role, surname, preferredName FROM ibPYPStaffTeaching JOIN gibbonPerson ON (ibPYPStaffTeaching.gibbonPersonID=gibbonPerson.gibbonPersonID) WHERE status='Full' AND ibPYPStaffTeachingID=:ibPYPStaffTeachingID";
            $result = $connection2->prepare($sql);
            $result->execute($data);
        } catch (PDOException $e) {
            echo "<div class='error'>".$e->getMessage().'</div>';
        }

        if ($result->rowCount() != 1) {
            echo "<div class='error'>";
            echo 'The selected member of staff does not exist.';
            echo '</div>';
        } else {
            //Let's go!
            $row = $result->fetch();
            ?>
			<form method="post" action="<?php echo $_SESSION[$guid]['absoluteURL']."/modules/IB PYP/staff_manage_editProcess.php?ibPYPStaffTeachingID=$ibPYPStaffTeachingID" ?>">
				<table class='smallIntBorder' cellspacing='0' style="width: 100%">
					<tr>
						<td>
							<b>Staff *</b><br/>
							<span style="font-size: 90%"><i>This value cannot be changed</i></span>
						</td>
						<td class="right">
							<input readonly type='text' style='width: 302px' value='<?php echo formatName('', $row['preferredName'], $row['surname'], 'Staff', true, true) ?>'>
							<script type="text/javascript">
								var gibbonPersonID=new LiveValidation('gibbonPersonID');
								gibbonPersonID.add(Validate.Exclusion, { within: ['Please select...'], failureMessage: "Select something!"});
							 </script>
						</td>
					</tr>
					<tr>
						<td>
							<b>Role *</b><br/>
							<span style="font-size: 90%"><i></i></span>
						</td>
						<td class="right">
							<select name="role" id="role" style="width: 302px">
								<option value="Please select...">Please select...</option>
								<option <?php if ($row['role'] == 'Coordinator') { echo 'selected '; } ?>value="Coordinator">Coordinator</option>
								<option <?php if ($row['role'] == 'Teacher (Curriculum)') { echo 'selected '; } ?>value="Teacher (Curriculum)">Teacher (Curriculum)</option>
								<option <?php if ($row['role'] == 'Teacher') { echo 'selected '; } ?>value="Teacher">Teacher</option>
							</select>
							<script type="text/javascript">
								var role=new LiveValidation('role');
								role.add(Validate.Exclusion, { within: ['Please select...'], failureMessage: "Select something!"});
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

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
$_SESSION[$guid]['ibPYPUnitsTab'] = 1;

//Module includes
include './modules/IB PYP/moduleFunctions.php';

if (isActionAccessible($guid, $connection2, '/modules/IB PYP/units_manage_master_delete.php') == false) {

    //Acess denied
    echo "<div class='error'>";
    echo 'You do not have access to this action.';
    echo '</div>';
} else {
    //Proceed!
    echo "<div class='trail'>";
    echo "<div class='trailHead'><a href='".$_SESSION[$guid]['absoluteURL']."'>Home</a> > <a href='".$_SESSION[$guid]['absoluteURL'].'/index.php?q=/modules/'.getModuleName($_GET['q']).'/'.getModuleEntry($_GET['q'], $connection2, $guid)."'>".getModuleName($_GET['q'])."</a> > <a href='".$_SESSION[$guid]['absoluteURL'].'/index.php?q=/modules/'.getModuleName($_GET['q']).'/units_manage.php&gibbonSchoolYearID='.$_GET['gibbonSchoolYearID']."'>Manage Units</a> > </div><div class='trailEnd'>Delete Master Unit</div>";
    echo '</div>';

    if (isset($_GET['return'])) {
        returnProcess($guid, $_GET['return'], null, null);
    }

    $role = getRole($_SESSION[$guid]['gibbonPersonID'], $connection2);
    if ($role != 'Coordinator' and $role != 'Teacher (Curriculum)') { echo "<div class='error'>";
        echo 'You do not have access to this action.';
        echo '</div>';
    } else {
        //Check if school year specified
        $gibbonSchoolYearID = $_GET['gibbonSchoolYearID'];
        $ibPYPUnitMasterID = $_GET['ibPYPUnitMasterID'];
        if ($ibPYPUnitMasterID == '' or $gibbonSchoolYearID == '') {
            echo "<div class='error'>";
            echo 'You have not specified a master unit.';
            echo '</div>';
        } else {
            try {
                $data = array('ibPYPUnitMasterID' => $ibPYPUnitMasterID);
                $sql = 'SELECT * FROM ibPYPUnitMaster WHERE ibPYPUnitMasterID=:ibPYPUnitMasterID';
                $result = $connection2->prepare($sql);
                $result->execute($data);
            } catch (PDOException $e) {
                echo "<div class='error'>".$e->getMessage().'</div>';
            }

            if ($result->rowCount() != 1) {
                echo "<div class='error'>";
                echo 'The selected master unit does not exist.';
                echo '</div>';
            } else {
                //Let's go!
                $row = $result->fetch();
                ?>
				<form method="post" action="<?php echo $_SESSION[$guid]['absoluteURL']."/modules/IB PYP/units_manage_master_deleteProcess.php?ibPYPUnitMasterID=$ibPYPUnitMasterID&gibbonSchoolYearID=$gibbonSchoolYearID" ?>">
					<table class='smallIntBorder' cellspacing='0' style="width: 100%">
						<tr>
							<td>
								<b>Are you sure you want to delete "<?php echo $row['name']; ?>" from the PYP programme?</b><br/>
								<span style="font-size: 90%; color: #cc0000"><i>This operation cannot be undone, and may lead to loss of vital data in your system.<br/>PROCEED WITH CAUTION!</i></span>
							</td>
							<td class="right">

							</td>
						</tr>
						<tr>
							<td>
								<input name="ibPYPUnitMasterID" id="ibPYPUnitMasterID" value="<?php echo $ibPYPUnitMasterID ?>" type="hidden">
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
}
?>

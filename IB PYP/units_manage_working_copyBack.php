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
$_SESSION[$guid]['ibPYPUnitsTab'] = 0;

//Module includes
include './modules/IB PYP/moduleFunctions.php';

if (isActionAccessible($guid, $connection2, '/modules/IB PYP/units_manage_working_copyBack.php') == false) {
    //Acess denied
    echo "<div class='error'>";
    echo 'You do not have access to this action.';
    echo '</div>';
} else {
    //Proceed!
    echo "<div class='trail'>";
    echo "<div class='trailHead'><a href='".$_SESSION[$guid]['absoluteURL']."'>Home</a> > <a href='".$_SESSION[$guid]['absoluteURL'].'/index.php?q=/modules/'.getModuleName($_GET['q']).'/'.getModuleEntry($_GET['q'], $connection2, $guid)."'>".getModuleName($_GET['q'])."</a> > <a href='".$_SESSION[$guid]['absoluteURL'].'/index.php?q=/modules/'.getModuleName($_GET['q']).'/units_manage.php&gibbonSchoolYearID='.$_GET['gibbonSchoolYearID']."'>Manage Units</a> > </div><div class='trailEnd'>Copy Back Working Unit</div>";
    echo '</div>';

    if (isset($_GET['return'])) {
        returnProcess($guid, $_GET['return'], null, null);
    }

    $role = getRole($_SESSION[$guid]['gibbonPersonID'], $connection2);
    if ($role != 'Coordinator' and $role != 'Teacher (Curriculum)') {
        echo "<div class='error'>";
        echo 'You do not have access to this action.';
        echo '</div>';
    } else {
        //Check if school year specified
        $ibPYPUnitWorkingID = $_GET['ibPYPUnitWorkingID'];
        $gibbonSchoolYearID = $_GET['gibbonSchoolYearID'];
        if ($ibPYPUnitWorkingID == '' or $gibbonSchoolYearID == '') {
            echo "<div class='error'>";
            echo 'You have not specified a master unit.';
            echo '</div>';
        } else {
            try {
                $data = array('gibbonSchoolYearID' => $gibbonSchoolYearID, 'ibPYPUnitWorkingID' => $ibPYPUnitWorkingID);
                $sql = 'SELECT ibPYPUnitWorking.*, gibbonCourse.nameShort AS course, gibbonCourseClass.nameShort AS class FROM ibPYPUnitWorking JOIN gibbonCourseClass ON (ibPYPUnitWorking.gibbonCourseClassID=gibbonCourseClass.gibbonCourseClassID) JOIN gibbonCourse ON (gibbonCourse.gibbonCourseID=gibbonCourseClass.gibbonCourseID) WHERE gibbonCourse.gibbonSchoolYearID=:gibbonSchoolYearID AND ibPYPUnitWorkingID=:ibPYPUnitWorkingID ORDER BY ibPYPUnitWorking.name';
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

                echo "<table class='smallIntBorder' cellspacing='0' style='width: 100%'>";
                echo '<tr>';
                echo "<td style='width: 34%; vertical-align: top'>";
                echo "<span style='font-size: 115%; font-weight: bold'>Unit</span><br/>";
                echo '<i>'.$row['name'].'</i>';
                echo "<td style='width: 33%; vertical-align: top'>";
                echo "<span style='font-size: 115%; font-weight: bold'>Class</span><br/>";
                echo $row['course'].'.'.$row['class'];
                echo '</td>';
                echo "<td style='width: 34%; vertical-align: top'>";

                echo '</td>';
                echo '</tr>';
                echo '</table>';

                echo '<p>';
                echo 'This function allows you to take all of the content from the selected working unit and use them to replace the content in the master unit. In this way you can use your refined and improved unit as your master next time you deploy.';
                echo '</p>';

                ?>
				<form method="post" action="<?php echo $_SESSION[$guid]['absoluteURL']."/modules/IB PYP/units_manage_working_copyBackProcess.php?ibPYPUnitWorkingID=$ibPYPUnitWorkingID&gibbonSchoolYearID=$gibbonSchoolYearID" ?>">
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
								<input name="ibPYPUnitWorkingID" id="ibPYPUnitWorkingID" value="<?php echo $ibPYPUnitWorkingID ?>" type="hidden">
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

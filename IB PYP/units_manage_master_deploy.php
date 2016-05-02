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

if (isActionAccessible($guid, $connection2, '/modules/IB PYP/units_manage_master_deploy.php') == false) {
    //Acess denied
    echo "<div class='error'>";
    echo 'You do not have access to this action.';
    echo '</div>';
} else {
    //Proceed!
    echo "<div class='trail'>";
    echo "<div class='trailHead'><a href='".$_SESSION[$guid]['absoluteURL']."'>Home</a> > <a href='".$_SESSION[$guid]['absoluteURL'].'/index.php?q=/modules/'.getModuleName($_GET['q']).'/'.getModuleEntry($_GET['q'], $connection2, $guid)."'>".getModuleName($_GET['q'])."</a> > <a href='".$_SESSION[$guid]['absoluteURL'].'/index.php?q=/modules/'.getModuleName($_GET['q']).'/units_manage.php&gibbonSchoolYearID='.$_GET['gibbonSchoolYearID']."'>Manage Units</a> > </div><div class='trailEnd'>Deploy Working Copy</div>";
    echo '</div>';

    if (isset($_GET['return'])) {
        returnProcess($guid, $_GET['return'], null, null);
    }

    $role = getRole($_SESSION[$guid]['gibbonPersonID'], $connection2);
    if ($role == 'Coordinator' or $role == 'Teacher (Curriculum)') {
        //Check if courseschool year specified
        $gibbonSchoolYearID = $_GET['gibbonSchoolYearID'];
        $ibPYPUnitMasterID = $_GET['ibPYPUnitMasterID'];
        if ($ibPYPUnitMasterID == '' or $gibbonSchoolYearID == '') {
            echo "<div class='error'>";
            echo 'You have not specified a unit or school year.';
            echo '</div>';
        } else {
            try {
                $data = array('ibPYPUnitMasterID' => $ibPYPUnitMasterID);
                $sql = 'SELECT ibPYPUnitMaster.*, gibbonYearGroupIDList, gibbonDepartmentID FROM ibPYPUnitMaster JOIN gibbonCourse ON (ibPYPUnitMaster.gibbonCourseID=gibbonCourse.gibbonCourseID) WHERE ibPYPUnitMasterID=:ibPYPUnitMasterID';
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

                try {
                    $dataSchoolYear = array('gibbonSchoolYearID' => $gibbonSchoolYearID);
                    $sqlSchoolYear = 'SELECT * FROM gibbonSchoolYear WHERE gibbonSchoolYearID=:gibbonSchoolYearID';
                    $resultSchoolYear = $connection2->prepare($sqlSchoolYear);
                    $resultSchoolYear->execute($dataSchoolYear);
                } catch (PDOException $e) {
                    echo "<div class='error'>".$e->getMessage().'</div>';
                }

                if ($resultSchoolYear->rowCount() != 1) {
                    echo "<div class='error'>";
                    echo 'You cannot proceed, as the specified school year cannot be found.';
                    echo '</div>';
                } else {
                    $rowSchoolYear = $resultSchoolYear->fetch();
                    ?>
					<form method="post" action="<?php echo $_SESSION[$guid]['absoluteURL']."/modules/IB PYP/units_manage_master_deployProcess.php?ibPYPUnitMasterID=$ibPYPUnitMasterID" ?>">
						<table class='smallIntBorder' cellspacing='0' style="width: 100%">
							<tr>
								<td>
									<b>Unit *</b><br/>
									<span style="font-size: 90%"><i>This value cannot be changed</i></span>
								</td>
								<td class="right">
									<input readonly name="name" id="name" value="<?php echo $row['name'] ?>" type="text" style="width: 300px">
								</td>
							</tr>
							<tr>
								<td>
									<b>School Year *</b><br/>
									<span style="font-size: 90%"><i>This value cannot be changed</i></span>
								</td>
								<td class="right">
									<?php
                                    echo "<input readonly name='schoolYear' id='schoolYear' value='".$rowSchoolYear['name']."' type='text' style='width: 300px'>";
                    ?>
								</td>
							</tr>
							<tr>
								<td>
									<b>Classes</b><br/>
									<span style="font-size: 90%"><i>Use Control and/or Shift to select multiple.</i></span>
								</td>
								<td class="right">
									<input type='hidden' name='gibbonCourseID' value='<?php echo $row['gibbonCourseID'] ?>'>
									<select name="classes[]" id="classes" multiple style="width: 302px; height: 150px">
										<?php
                                        try {
                                            $dataSelect = array('gibbonSchoolYearID' => $gibbonSchoolYearID, 'gibbonCourseID' => $row['gibbonCourseID']);
                                            $sqlSelect = 'SELECT gibbonCourse.gibbonCourseID, gibbonCourseClassID, gibbonCourse.name, gibbonCourse.nameShort AS course, gibbonCourseClass.nameShort AS class FROM gibbonCourse JOIN gibbonCourseClass ON (gibbonCourse.gibbonCourseID=gibbonCourseClass.gibbonCourseID) WHERE gibbonSchoolYearID=:gibbonSchoolYearID AND gibbonCourse.gibbonCourseID=:gibbonCourseID ORDER BY course, class';
                                            $resultSelect = $connection2->prepare($sqlSelect);
                                            $resultSelect->execute($dataSelect);
                                        } catch (PDOException $e) {
                                            echo "<div class='error'>".$e->getMessage().'</div>';
                                        }
                    while ($rowSelect = $resultSelect->fetch()) {
                        echo "<option class='".$rowSelect['gibbonCourseID']."' value='".$rowSelect['gibbonCourseClassID']."'>".htmlPrep($rowSelect['course']).'.'.htmlPrep($rowSelect['class']).' - '.$rowSelect['name'].'</option>';
                    }
                    ?>
									</select>
								</td>
							</tr>
							<tr>
								<td>
									<b>Unit Start Date</b><br/>
									<span style="font-size: 90%"><i>When will this class start this unit?<br/>dd/mm/yyyy</i></span>
								</td>
								<td class="right">
									<input name="dateStart" id="dateStart" maxlength=10 value="" type="text" style="width: 300px">
									<script type="text/javascript">
										var dateStart=new LiveValidation('dateStart');
										dateStart.add( Validate.Format, {pattern: /^(0[1-9]|[12][0-9]|3[01])[- /.](0[1-9]|1[012])[- /.](19|20)\d\d$/i, failureMessage: "Use dd/mm/yyyy." } );
									 </script>
									 <script type="text/javascript">
										$(function() {
											$( "#dateStart" ).datepicker();
										});
									</script>
								</td>
							</tr>
							<tr>
								<td>
									<b>Rubric</b><br/>
								</td>
								<td class="right">
									<?php
                                    $defaultRubric = getSettingByScope($connection2, 'IB PYP', 'defaultRubric');
                    ?>
									<select name="gibbonRubricID" id="gibbonRubricID" style="width: 302px">
										<option><option>
										<optgroup label='--School Rubrics --'>
										<?php
                                        try {
                                            $dataSelect = array();
                                            $sqlSelectWhere = '';
                                            $years = explode(',', $row['gibbonYearGroupIDList']);
                                            foreach ($years as $year) {
                                                $dataSelect[$year] = "%$year%";
                                                $sqlSelectWhere .= " AND gibbonYearGroupIDList LIKE :$year";
                                            }
                                            $sqlSelect = "SELECT * FROM gibbonRubric WHERE active='Y' AND scope='School' $sqlSelectWhere ORDER BY category, name";
                                            $resultSelect = $connection2->prepare($sqlSelect);
                                            $resultSelect->execute($dataSelect);
                                        } catch (PDOException $e) {
                                        }
                    while ($rowSelect = $resultSelect->fetch()) {
                        $label = '';
                        if ($rowSelect['category'] == '') {
                            $label = $rowSelect['name'];
                        } else {
                            $label = $rowSelect['category'].' - '.$rowSelect['name'];
                        }
                        $selected = '';
                        if ($defaultRubric == $rowSelect['gibbonRubricID']) {
                            $selected = 'selected';
                        }
                        echo "<option $selected value='".$rowSelect['gibbonRubricID']."'>$label</option>";
                    }
                    if ($row['gibbonDepartmentID'] != '') {
                        ?>
											<optgroup label='--Learning Area Rubrics --'>
											<?php
                                            try {
                                                $dataSelect = array('gibbonDepartmentID' => $row['gibbonDepartmentID']);
                                                $sqlSelectWhere = '';
                                                $years = explode(',', $row['gibbonYearGroupIDList']);
                                                foreach ($years as $year) {
                                                    $dataSelect[$year] = "%$year%";
                                                    $sqlSelectWhere .= " AND gibbonYearGroupIDList LIKE :$year";
                                                }
                                                $sqlSelect = "SELECT * FROM gibbonRubric WHERE active='Y' AND scope='Learning Area' AND gibbonDepartmentID=:gibbonDepartmentID $sqlSelectWhere ORDER BY category, name";
                                                $resultSelect = $connection2->prepare($sqlSelect);
                                                $resultSelect->execute($dataSelect);
                                            } catch (PDOException $e) {
                                            }
                        while ($rowSelect = $resultSelect->fetch()) {
                            $label = '';
                            if ($rowSelect['category'] == '') {
                                $label = $rowSelect['name'];
                            } else {
                                $label = $rowSelect['category'].' - '.$rowSelect['name'];
                            }
                            $selected = '';
                            if ($defaultRubric == $rowSelect['gibbonRubricID']) {
                                $selected = 'selected';
                            }
                            echo "<option $selected value='".$rowSelect['gibbonRubricID']."'>$label</option>";
                        }
                    }
                    ?>
									</select>
								</td>
							</tr>
							<?php
                            echo '<tr>';
                    echo "<td class='right' colspan=2>";
                    echo "<input type='hidden' name='ibPYPUnitMasterID' value='$ibPYPUnitMasterID'>";
                    echo "<input type='hidden' name='gibbonSchoolYearID' value='$gibbonSchoolYearID'>";
                    echo "<input type='hidden' name='address' value='".$_GET['q']."'>";
                    echo "<input id='submit' type='submit' value='Submit'>";
                    echo '</td>';
                    echo '</tr>';
                    echo '</table>';
                    echo '</form>';
                }
            }
        }
    }
}
?>

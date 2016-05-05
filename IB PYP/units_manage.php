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

if (isActionAccessible($guid, $connection2, '/modules/IB PYP/units_manage.php') == false) {
    //Acess denied
    echo "<div class='error'>";
    echo 'You do not have access to this action.';
    echo '</div>';
} else {
    echo "<div class='trail'>";
    echo "<div class='trailHead'><a href='".$_SESSION[$guid]['absoluteURL']."'>Home</a> > <a href='".$_SESSION[$guid]['absoluteURL'].'/index.php?q=/modules/'.getModuleName($_GET['q']).'/'.getModuleEntry($_GET['q'], $connection2, $guid)."'>".getModuleName($_GET['q'])."</a> > </div><div class='trailEnd'>Manage Units</div>";
    echo '</div>';
    echo '<p>';
    echo 'Master Units holds the school stock of teaching units: these can be deployed to a particular group to create a Working Unit. Units exist in a particular school year, and are tied to a specific course.';
    echo '</p>';

    if (isset($_GET['return'])) {
        returnProcess($guid, $_GET['return'], null, null);
    }

    $gibbonSchoolYearID = null;
    if (isset($_GET['gibbonSchoolYearID'])) {
        $gibbonSchoolYearID = $_GET['gibbonSchoolYearID'];
    }
    if ($gibbonSchoolYearID == '') {
        $gibbonSchoolYearID = $_SESSION[$guid]['gibbonSchoolYearID'];
        $gibbonSchoolYearName = $_SESSION[$guid]['gibbonSchoolYearName'];
    }
    if (isset($_GET['gibbonSchoolYearID'])) {
        try {
            $data = array('gibbonSchoolYearID' => $_GET['gibbonSchoolYearID']);
            $sql = 'SELECT * FROM gibbonSchoolYear WHERE gibbonSchoolYearID=:gibbonSchoolYearID';
            $result = $connection2->prepare($sql);
            $result->execute($data);
        } catch (PDOException $e) {
            echo "<div class='error'>".$e->getMessage().'</div>';
        }

        if ($result->rowCount() != 1) {
            echo "<div class='error'>";
            echo 'The specified year does not exist.';
            echo '</div>';
        } else {
            $row = $result->fetch();
            $gibbonSchoolYearID = $row['gibbonSchoolYearID'];
            $gibbonSchoolYearName = $row['name'];
        }
    }

    if ($gibbonSchoolYearID != '') { echo "<h2 class='top'>";
        echo $gibbonSchoolYearName;
        echo '</h2>';

        echo "<div class='linkTop'>";
            //Print year picker
            if (getPreviousSchoolYearID($gibbonSchoolYearID, $connection2) != false) {
                echo "<a href='".$_SESSION[$guid]['absoluteURL'].'/index.php?q=/modules/IB PYP/units_manage.php&gibbonSchoolYearID='.getPreviousSchoolYearID($gibbonSchoolYearID, $connection2)."'>Previous Year</a> ";
            } else {
                echo 'Previous Year ';
            }
        echo ' | ';
        if (getNextSchoolYearID($gibbonSchoolYearID, $connection2) != false) {
            echo "<a href='".$_SESSION[$guid]['absoluteURL'].'/index.php?q=/modules/IB PYP/units_manage.php&gibbonSchoolYearID='.getNextSchoolYearID($gibbonSchoolYearID, $connection2)."'>Next Year</a> ";
        } else {
            echo 'Next Year ';
        }
        echo '</div>';

        //Set pagination variable
        $page = null;
        if (isset($_GET['page'])) {
            $page = $_GET['page'];
        }
        if ((!is_numeric($page)) or $page < 1) {
            $page = 1;
        }

        $role = getRole($_SESSION[$guid]['gibbonPersonID'], $connection2);

        if (isset($_GET['tab'])) {
            $_SESSION[$guid]['ibPYPUnitsTab'] = $_GET['tab'];
        }
        if ($_SESSION[$guid]['ibPYPUnitsTab'] == '' or !(is_numeric($_SESSION[$guid]['ibPYPUnitsTab']))) {
            $_SESSION[$guid]['ibPYPUnitsTab'] = 0;
        }
        ?><script type='text/javascript'>
			$(function() {
				$( "#tabs" ).tabs({
					active: <?php echo $_SESSION[$guid]['ibPYPUnitsTab'] ?>,
					ajaxOptions: {
						error: function( xhr, status, index, anchor ) {
							$( anchor.hash ).html(
								"Couldn't load this tab." );
						}
					}
				});
			});
		</script><?php

        if ($role != '') {
            echo "<div id='tabs' style='margin: 20px 0'>";
            echo '<ul>';
            echo "<li><a href='#tabs-1'>Working Units</a></li>";
            if ($role == 'Coordinator' or $role == 'Teacher (Curriculum)') {
                echo "<li><a href='#tabs-2'>Master Units</a></li>";
            }
            echo '</ul>';

            echo "<div id='tabs-1'>";
            echo '<h3>';
            echo 'Filter';
            echo '</h3>';

            $search = null;
            if (isset($_GET['search'])) {
                $search = $_GET['search'];
            }

            ?>
			<form method="get" action="<?php echo $_SESSION[$guid]['absoluteURL']?>/index.php">
				<table class='noIntBorder' cellspacing='0' style="width: 100%">
					<tr><td style="width: 30%"></td><td></td></tr>
					<tr>
						<td>
							<b>Search By Name</b><br/>
							<span style="font-size: 90%"><i>Unit name, course name</i></span>
						</td>
						<td class="right">
							<input name="search" id="search" maxlength=20 value="<?php echo $search ?>" type="text" style="width: 300px">
						</td>
					</tr>
					<tr>
						<td colspan=2 class="right">
							<input type="hidden" name="q" value="/modules/<?php echo $_SESSION[$guid]['module'] ?>/units_manage.php">
							<input type="hidden" name="address" value="<?php echo $_SESSION[$guid]['address'] ?>">
							<input type="hidden" name="tab" value="0">
							<?php
							echo "<a href='".$_SESSION[$guid]['absoluteURL'].'/index.php?q=/modules/'.$_SESSION[$guid]['module']."/units_manage.php&tab=0'>Clear Filters</a>";
							?>
							<input type="submit" value="Submit">
						</td>
					</tr>
				</table>
			</form>
			<?php

			echo '<h3>';
            echo 'Units';
            echo '</h3>';

            try {
                if ($role == 'Coordinator' or $role == 'Teacher (Curriculum)') {
                    $data = array('gibbonSchoolYearID' => $gibbonSchoolYearID);
                    $sql = 'SELECT ibPYPUnitWorking.*, gibbonCourse.nameShort AS course, gibbonCourseClass.nameShort AS class FROM ibPYPUnitWorking JOIN gibbonCourseClass ON (ibPYPUnitWorking.gibbonCourseClassID=gibbonCourseClass.gibbonCourseClassID) JOIN gibbonCourse ON (gibbonCourse.gibbonCourseID=gibbonCourseClass.gibbonCourseID) WHERE gibbonCourse.gibbonSchoolYearID=:gibbonSchoolYearID ORDER BY ibPYPUnitWorking.name, course, class';
                    if ($search != '') {
                        $data = array('gibbonSchoolYearID' => $gibbonSchoolYearID, 'search1' => "%$search%", 'search2' => "%$search%");
                        $sql = 'SELECT ibPYPUnitWorking.*, gibbonCourse.nameShort AS course, gibbonCourseClass.nameShort AS class FROM ibPYPUnitWorking JOIN gibbonCourseClass ON (ibPYPUnitWorking.gibbonCourseClassID=gibbonCourseClass.gibbonCourseClassID) JOIN gibbonCourse ON (gibbonCourse.gibbonCourseID=gibbonCourseClass.gibbonCourseID) WHERE gibbonCourse.gibbonSchoolYearID=:gibbonSchoolYearID AND (ibPYPUnitWorking.name LIKE :search1 OR gibbonCourse.nameShort LIKE :search2) ORDER BY ibPYPUnitWorking.name, course, class';
                    }
                } else {
                    $data = array('gibbonSchoolYearID' => $gibbonSchoolYearID, 'gibbonPersonID' => $_SESSION[$guid]['gibbonPersonID']);
                    $sql = 'SELECT DISTINCT ibPYPUnitWorking.*, gibbonCourse.nameShort AS course, gibbonCourseClass.nameShort AS class FROM ibPYPUnitWorking JOIN gibbonCourseClass ON (ibPYPUnitWorking.gibbonCourseClassID=gibbonCourseClass.gibbonCourseClassID) JOIN gibbonCourse ON (ibPYPUnitWorking.gibbonCourseID=gibbonCourse.gibbonCourseID) JOIN gibbonCourseClassPerson ON (gibbonCourseClassPerson.gibbonCourseClassID=gibbonCourseClass.gibbonCourseClassID) WHERE gibbonCourse.gibbonSchoolYearID=:gibbonSchoolYearID AND gibbonCourseClassPerson.gibbonPersonID=:gibbonPersonID ORDER BY name, course, class';
                    if ($search != '') {
                        $data = array('gibbonSchoolYearID' => $gibbonSchoolYearID, 'gibbonPersonID' => $_SESSION[$guid]['gibbonPersonID'], 'search1' => "%$search%", 'search2' => "%$search%");
                        $sql = 'SELECT DISTINCT ibPYPUnitWorking.*, gibbonCourse.nameShort AS course, gibbonCourseClass.nameShort AS class FROM ibPYPUnitWorking JOIN gibbonCourseClass ON (ibPYPUnitWorking.gibbonCourseClassID=gibbonCourseClass.gibbonCourseClassID) JOIN gibbonCourse ON (ibPYPUnitWorking.gibbonCourseID=gibbonCourse.gibbonCourseID) JOIN gibbonCourseClassPerson ON (gibbonCourseClassPerson.gibbonCourseClassID=gibbonCourseClass.gibbonCourseClassID) WHERE gibbonCourse.gibbonSchoolYearID=:gibbonSchoolYearID AND gibbonCourseClassPerson.gibbonPersonID=:gibbonPersonID AND (ibPYPUnitWorking.name LIKE :search1 OR gibbonCourse.nameShort LIKE :search2) ORDER BY name, course, class';
                    }
                }
                $result = $connection2->prepare($sql);
                $result->execute($data);
            } catch (PDOException $e) {
                echo "<div class='error'>".$e->getMessage().'</div>';
            }

            if ($result->rowCount() < 1) {
                echo "<div class='error'>";
                echo 'There are no units to display.';
                echo '</div>';
            } else {
                echo "<table cellspacing='0' style='width: 100%'>";
                echo "<tr class='head'>";
                echo '<th>';
                echo 'Name';
                echo '</th>';
                echo '<th>';
                echo 'Class';
                echo '</th>';
                echo '<th>';
                echo 'Actions';
                echo '</th>';
                echo '</tr>';

                $count = 0;
                $rowNum = 'odd';
                while ($row = $result->fetch()) {
                    if ($count % 2 == 0) {
                        $rowNum = 'even';
                    } else {
                        $rowNum = 'odd';
                    }
                    ++$count;

					//COLOR ROW BY STATUS!
					echo "<tr class=$rowNum>";
                    echo '<td>';
                    echo '<b>'.$row['name'].'<br/>';
                    echo '</td>';
                    echo '<td>';
                    echo $row['course'].'.'.$row['class'];
                    echo '</td>';
                    echo '<td>';
                    echo "<a href='".$_SESSION[$guid]['absoluteURL'].'/index.php?q=/modules/IB PYP/units_manage_working_edit.php&ibPYPUnitWorkingID='.$row['ibPYPUnitWorkingID']."&gibbonSchoolYearID=$gibbonSchoolYearID'><img title='Edit' src='./themes/".$_SESSION[$guid]['gibbonThemeName']."/img/config.png'/></a> ";
                    if ($role == 'Coordinator' or $role == 'Teacher (Curriculum)') {
                        echo "<a href='".$_SESSION[$guid]['absoluteURL'].'/index.php?q=/modules/IB PYP/units_manage_working_delete.php&ibPYPUnitWorkingID='.$row['ibPYPUnitWorkingID']."&gibbonSchoolYearID=$gibbonSchoolYearID&tab=0'><img title='Delete' src='./themes/".$_SESSION[$guid]['gibbonThemeName']."/img/garbage.png'/></a> ";
                        echo "<a href='".$_SESSION[$guid]['absoluteURL'].'/index.php?q=/modules/IB PYP/units_manage_working_copyBack.php&ibPYPUnitWorkingID='.$row['ibPYPUnitWorkingID']."&gibbonSchoolYearID=$gibbonSchoolYearID&tab=0'><img title='Copy Back' src='./themes/".$_SESSION[$guid]['gibbonThemeName']."/img/copyback.png'/></a> ";
                    }
                    echo '</td>';
                    echo '</tr>';
                }
                echo '</table>';
            }
            echo '</div>';
            if ($role == 'Coordinator' or $role == 'Teacher (Curriculum)') {
                echo "<div id='tabs-2'>";
                echo '<h3>';
                echo 'Filter';
                echo '</h3>';

                $search = null;
                if (isset($_GET['search'])) {
                    $search = $_GET['search'];
                }

                ?>
				<form method="get" action="<?php echo $_SESSION[$guid]['absoluteURL']?>/index.php">
					<table class='noIntBorder' cellspacing='0' style="width: 100%">
						<tr><td style="width: 30%"></td><td></td></tr>
						<tr>
							<td>
								<b>Search By Name</b><br/>
								<span style="font-size: 90%"><i>Unit name, course name</i></span>
							</td>
							<td class="right">
								<input name="search" id="search" maxlength=20 value="<?php echo $search ?>" type="text" style="width: 300px">
							</td>
						</tr>
						<tr>
							<td colspan=2 class="right">
								<input type="hidden" name="q" value="/modules/<?php echo $_SESSION[$guid]['module'] ?>/units_manage.php">
								<input type="hidden" name="address" value="<?php echo $_SESSION[$guid]['address'] ?>">
								<input type="hidden" name="tab" value="1">
								<?php
								echo "<a href='".$_SESSION[$guid]['absoluteURL'].'/index.php?q=/modules/'.$_SESSION[$guid]['module']."/units_manage.php&tab=1'>Clear Filters</a>";
								?>
								<input type="submit" value="Submit">
							</td>
						</tr>
					</table>
				</form>
				<?php

				echo '<h3>';
                echo 'Units';
                echo '</h3>';

                try {
                    $data = array('gibbonSchoolYearID' => $gibbonSchoolYearID);
                    $sql = 'SELECT ibPYPUnitMaster.*, gibbonCourse.nameShort FROM ibPYPUnitMaster LEFT JOIN gibbonCourse ON (ibPYPUnitMaster.gibbonCourseID=gibbonCourse.gibbonCourseID) WHERE gibbonCourse.gibbonSchoolYearID=:gibbonSchoolYearID ORDER BY ibPYPUnitMaster.name, nameShort';
                    if ($search != '') {
                        $data = array('gibbonSchoolYearID' => $gibbonSchoolYearID, 'search1' => "%$search%", 'search2' => "%$search%");
                        $sql = 'SELECT ibPYPUnitMaster.*, gibbonCourse.nameShort FROM ibPYPUnitMaster LEFT JOIN gibbonCourse ON (ibPYPUnitMaster.gibbonCourseID=gibbonCourse.gibbonCourseID) WHERE gibbonCourse.gibbonSchoolYearID=:gibbonSchoolYearID AND (ibPYPUnitMaster.name LIKE :search1 OR gibbonCourse.nameShort LIKE :search2) ORDER BY ibPYPUnitMaster.name, nameShort';
                    }
                    $result = $connection2->prepare($sql);
                    $result->execute($data);
                } catch (PDOException $e) {
                    echo "<div class='error'>".$e->getMessage().'</div>';
                }

                echo "<div class='linkTop'>";
                echo "<a href='".$_SESSION[$guid]['absoluteURL']."/index.php?q=/modules/IB PYP/units_manage_master_add.php&gibbonSchoolYearID=$gibbonSchoolYearID'><img title='New' src='./themes/".$_SESSION[$guid]['gibbonThemeName']."/img/page_new.png'/></a>";
                echo '</div>';

                if ($result->rowCount() < 1) {
                    echo "<div class='error'>";
                    echo 'There are no units to display.';
                    echo '</div>';
                } else {
                    echo "<table cellspacing='0' style='width: 100%'>";
                    echo "<tr class='head'>";
                    echo '<th>';
                    echo 'Name';
                    echo '</th>';
                    echo '<th>';
                    echo 'Course';
                    echo '</th>';
                    echo '<th>';
                    echo 'Active';
                    echo '</th>';
                    echo '<th>';
                    echo 'Actions';
                    echo '</th>';
                    echo '</tr>';

                    $count = 0;
                    $rowNum = 'odd';
                    while ($row = $result->fetch()) {
                        if ($count % 2 == 0) {
                            $rowNum = 'even';
                        } else {
                            $rowNum = 'odd';
                        }
                        ++$count;

                        if ($row['active'] == 'N') {
                            $rowNum = 'error';
                        }

						//COLOR ROW BY STATUS!
						echo "<tr class=$rowNum>";
                        echo '<td>';
                        echo '<b>'.$row['name'].'<br/>';
                        echo '</td>';
                        echo '<td>';
                        echo $row['nameShort'];
                        echo '</td>';
                        echo '<td>';
                        echo $row['active'];
                        echo '</td>';
                        echo '<td>';
                        echo "<a href='".$_SESSION[$guid]['absoluteURL'].'/index.php?q=/modules/IB PYP/units_manage_master_edit.php&ibPYPUnitMasterID='.$row['ibPYPUnitMasterID']."&gibbonSchoolYearID=$gibbonSchoolYearID&tab=1'><img title='Edit' src='./themes/".$_SESSION[$guid]['gibbonThemeName']."/img/config.png'/></a> ";
                        echo "<a href='".$_SESSION[$guid]['absoluteURL'].'/index.php?q=/modules/IB PYP/units_manage_master_delete.php&ibPYPUnitMasterID='.$row['ibPYPUnitMasterID']."&gibbonSchoolYearID=$gibbonSchoolYearID&tab=1'><img title='Delete' src='./themes/".$_SESSION[$guid]['gibbonThemeName']."/img/garbage.png'/></a> ";
                        echo "<a href='".$_SESSION[$guid]['absoluteURL'].'/index.php?q=/modules/IB PYP/units_manage_master_duplicate.php&ibPYPUnitMasterID='.$row['ibPYPUnitMasterID']."&gibbonSchoolYearID=$gibbonSchoolYearID&tab=1'><img title='Duplicate' src='./themes/".$_SESSION[$guid]['gibbonThemeName']."/img/copy.png'/></a> ";
                        if ($row['active'] == 'Y') {
                            echo "<a href='".$_SESSION[$guid]['absoluteURL'].'/index.php?q=/modules/IB PYP/units_manage_master_deploy.php&ibPYPUnitMasterID='.$row['ibPYPUnitMasterID']."&gibbonSchoolYearID=$gibbonSchoolYearID&tab=1'><img title='Deploy' src='./themes/".$_SESSION[$guid]['gibbonThemeName']."/img/page_right.png'/></a> ";
                        }
                        echo '</td>';
                        echo '</tr>';
                    }
                    echo '</table>';
                }
                echo '</div>';
            }
            echo '</div>';
        }
    } else {
        echo "<div class='error'>";
        echo 'You do not have access to this action.';
        echo '</div>';
    }
}
?>

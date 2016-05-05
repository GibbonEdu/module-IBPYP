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
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program. If not, see <http://www.gnu.org/licenses/>.
*/

@session_start();

if (isActionAccessible($guid, $connection2, '/modules/IB PYP/settings_manage.php') == false) {
    //Acess denied
    echo "<div class='error'>";
    echo 'You do not have access to this action.';
    echo '</div>';
} else {
    //Proceed!
    echo "<div class='trail'>";
    echo "<div class='trailHead'><a href='".$_SESSION[$guid]['absoluteURL']."'>Home</a> > <a href='".$_SESSION[$guid]['absoluteURL'].'/index.php?q=/modules/'.getModuleName($_GET['q']).'/'.getModuleEntry($_GET['q'], $connection2, $guid)."'>".getModuleName($_GET['q'])."</a> > </div><div class='trailEnd'>Manage Settings</div>";
    echo '</div>';

    if (isset($_GET['return'])) {
        returnProcess($guid, $_GET['return'], null, null);
    }
    
    ?>

	<form method="post" action="<?php echo $_SESSION[$guid]['absoluteURL'].'/modules/'.$_SESSION[$guid]['module'].'/settings_manageProcess.php' ?>">
		<table class='smallIntBorder' cellspacing='0' style="width: 100%">
			<tr class='break'>
				<td colspan=2>
					<h3>General Options</h3>
				</td>
			</tr>
			<tr>
				<?php
                try {
                    $data = array();
                    $sql = "SELECT * FROM gibbonSetting WHERE scope='IB PYP' AND name='defaultRubric'";
                    $result = $connection2->prepare($sql);
                    $result->execute($data);
                } catch (PDOException $e) {
                    echo "<div class='error'>".$e->getMessage().'</div>';
                }
				$row = $result->fetch();
				?>
				<td>
					<b><?php echo $row['nameDisplay'] ?></b><br/>
					<span style="font-size: 90%"><i><?php echo $row['description'] ?></i></span>
				</td>
				<td class="right">
					<select name="<?php echo $row['name'] ?>" id="<?php echo $row['name'] ?>" style="width: 302px">
						<option><option>
						<?php
                        try {
                            $dataSelect = array();
                            $sqlSelect = "SELECT * FROM gibbonRubric WHERE active='Y' AND scope='School' ORDER BY category, name";
                            $resultSelect = $connection2->prepare($sqlSelect);
                            $resultSelect->execute($dataSelect);
                        } catch (PDOException $e) {
                        }
						while ($rowSelect = $resultSelect->fetch()) {
							$selected = '';
							if ($row['value'] == $rowSelect['gibbonRubricID']) {
								$selected = 'selected';
							}
							echo "<option $selected value='".$rowSelect['gibbonRubricID']."'>".$rowSelect['name'].'</option>';
						}
						?>
					</select>
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

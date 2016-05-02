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

if (isActionAccessible($guid, $connection2, '/modules/IB PYP/glossary_add.php') == false) {

    //Acess denied
    echo "<div class='error'>";
    echo 'You do not have access to this action.';
    echo '</div>';
} else {
    echo "<div class='trail'>";
    echo "<div class='trailHead'><a href='".$_SESSION[$guid]['absoluteURL']."'>Home</a> > <a href='".$_SESSION[$guid]['absoluteURL'].'/index.php?q=/modules/'.getModuleName($_GET['q']).'/'.getModuleEntry($_GET['q'], $connection2, $guid)."'>".getModuleName($_GET['q'])."</a> > <a href='".$_SESSION[$guid]['absoluteURL'].'/index.php?q=/modules/'.getModuleName($_GET['q'])."/glossary.php'>Essential Elements</a> > </div><div class='trailEnd'>Add Item</div>";
    echo '</div>';

    $returns = array();
    $editLink = '';
    if (isset($_GET['editID'])) {
        $editLink = $_SESSION[$guid]['absoluteURL'].'/index.php?q=/modules/IB PYP/glossary_edit.php&ibPYPGlossaryID='.$_GET['editID'];
    }
    if (isset($_GET['return'])) {
        returnProcess($guid, $_GET['return'], $editLink, $returns);
    }

    $role = getRole($_SESSION[$guid]['gibbonPersonID'], $connection2);
    if ($role != 'Coordinator' and $role != 'Teacher (Curriculum)') {
        echo "<div class='error'>";
        echo 'You do not have access to this action.';
        echo '</div>';
    } else {
        ?>
		<form method="post" action="<?php echo $_SESSION[$guid]['absoluteURL'].'/modules/IB PYP/glossary_addProcess.php' ?>">
			<table class='smallIntBorder' cellspacing='0' style="width: 100%">
				<tr>
					<td>
						<b>Type *</b><br/>
						<span style="font-size: 90%"><i></i></span>
					</td>
					<td class="right">
						<select name="type" id="type" style="width: 302px">
							<option value="Please select...">Please select...</option>
							<option value="Attitude">Attitude</option>
							<option value="Concept">Concept</option>
							<option value="Learner Profile">Learner Profile</option>
							<option value="Transdisciplinary Skill">Transdisciplinary Skill</option>
						</select>
						<script type="text/javascript">
							var type=new LiveValidation('type');
							type.add(Validate.Exclusion, { within: ['Please select...'], failureMessage: "Select something!"});
						 </script>
					</td>
				</tr>
				<tr>
					<td>
						<b>Title *</b><br/>
					</td>
					<td class="right">
						<input name="title" id="title" maxlength=100 value="" type="text" style="width: 300px">
						<script type="text/javascript">
							var title=new LiveValidation('title');
							title.add(Validate.Presence);
						</script>
					</td>
				</tr>
				<tr>
					<td>
						<b>Category</b><br/>
					</td>
					<td class="right">
						<input name="category" id="category" maxlength=100 value="" type="text" style="width: 300px">
						<script type="text/javascript">
							$(function() {
								var availableTags=[
									<?php
                                    try {
                                        $dataAuto = array();
                                        $sqlAuto = 'SELECT DISTINCT category FROM ibPYPGlossary ORDER BY category';
                                        $resultAuto = $connection2->prepare($sqlAuto);
                                        $resultAuto->execute($dataAuto);
                                    } catch (PDOException $e) {
                                    }

        while ($rowAuto = $resultAuto->fetch()) {
            echo '"'.$rowAuto['category'].'", ';
        }
        ?>
								];
								$( "#category" ).autocomplete({source: availableTags});
							});
						</script>
					</td>
				</tr>
				<tr>
					<td>
						<b>Content</b><br/>
					</td>
					<td class="right">
						<textarea name='content' id='contentText' rows=5 style='width: 300px'></textarea>
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
?>

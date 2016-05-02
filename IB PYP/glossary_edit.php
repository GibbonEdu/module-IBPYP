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

if (isActionAccessible($guid, $connection2, '/modules/IB PYP/glossary_edit.php') == false) {

    //Acess denied
    echo "<div class='error'>";
    echo 'You do not have access to this action.';
    echo '</div>';
} else {
    //Proceed!
    echo "<div class='trail'>";
    echo "<div class='trailHead'><a href='".$_SESSION[$guid]['absoluteURL']."'>Home</a> > <a href='".$_SESSION[$guid]['absoluteURL'].'/index.php?q=/modules/'.getModuleName($_GET['q']).'/'.getModuleEntry($_GET['q'], $connection2, $guid)."'>".getModuleName($_GET['q'])."</a> > <a href='".$_SESSION[$guid]['absoluteURL'].'/index.php?q=/modules/'.getModuleName($_GET['q'])."/glossary.php'>Essential Elements</a> > </div><div class='trailEnd'>Edit Item</div>";
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
        $ibPYPGlossaryID = $_GET['ibPYPGlossaryID'];
        if ($ibPYPGlossaryID == '') {
            echo "<div class='error'>";
            echo 'You have not specified a glossary term.';
            echo '</div>';
        } else {
            try {
                $data = array('ibPYPGlossaryID' => $ibPYPGlossaryID);
                $sql = 'SELECT * FROM ibPYPGlossary WHERE ibPYPGlossaryID=:ibPYPGlossaryID';
                $result = $connection2->prepare($sql);
                $result->execute($data);
            } catch (PDOException $e) {
                echo "<div class='error'>".$e->getMessage().'</div>';
            }

            if ($result->rowCount() != 1) {
                echo "<div class='error'>";
                echo 'The selected glossary term does not exist.';
                echo '</div>';
            } else {
                //Let's go!
                $row = $result->fetch();
                ?>
				<form method="post" action="<?php echo $_SESSION[$guid]['absoluteURL']."/modules/IB PYP/glossary_editProcess.php?ibPYPGlossaryID=$ibPYPGlossaryID" ?>">
					<table class='smallIntBorder' cellspacing='0' style="width: 100%">
						<tr>
							<td>
								<b>Type *</b><br/>
								<span style="font-size: 90%"><i></i></span>
							</td>
							<td class="right">
								<select name="type" id="type" style="width: 302px">
									<option value="Please select...">Please select...</option>
									<option <?php if ($row['type'] == 'Attitude') {
    echo 'selected ';
}
                ?>value="Attitude">Attitude</option>
									<option <?php if ($row['type'] == 'Concept') {
    echo 'selected ';
}
                ?>value="Concept">Concept</option>
									<option <?php if ($row['type'] == 'Learner Profile') {
    echo 'selected ';
}
                ?>value="Learner Profile">Learner Profile</option>
									<option <?php if ($row['type'] == 'Transdisciplinary Skill') {
    echo 'selected ';
}
                ?>value="Transdisciplinary Skill">Transdisciplinary Skill</option>
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
								<input name="title" id="title" maxlength=100 value="<?php echo htmlPrep($row['title']) ?>" type="text" style="width: 300px">
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
								<input name="category" id="category" maxlength=100 value="<?php echo htmlPrep($row['category']) ?>" type="text" style="width: 300px">
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
								<textarea name='content' id='contentText' rows=5 style='width: 300px'><?php echo htmlPrep($row['content']) ?></textarea>
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
}
?>

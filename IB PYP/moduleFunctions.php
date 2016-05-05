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

//Make the display for a block, according to the input provided, where $i is a unique number appended to the block's field ids.
//Mode can be masterAdd, masterEdit, workingDeploy, workingEdit, plannerEdit
function makeBlock($guid, $connection2, $i, $mode = 'masterAdd', $title = '', $type = '', $length = '', $contents = '', $complete = 'N', $ibPYPUnitMasterSmartBlockID = '', $ibPYPUnitWorkingSmartBlockID = '', $teachersNotes = '', $outerBlock = true)
{
    if ($outerBlock) { echo "<div id='blockOuter$i' class='blockOuter'>";
    }
    ?>
		<script>
			$(function() {
				$( "#sortable" ).sortable({
					placeholder: "ui-state-highlight"
				});
				
				$( "#sortable" ).bind( "sortstart", function(event, ui) { 
					$("#blockInner<?php echo $i ?>").css("display","none") ;
					$("#block<?php echo $i ?>").css("height","72px") ;
					$('#show<?php echo $i ?>').css("background-image", "<?php echo "url(\'".$_SESSION[$guid]['absoluteURL'].'/themes/'.$_SESSION[$guid]['gibbonThemeName']."/img/plus.png\'"?>)"); 
					tinyMCE.execCommand('mceRemoveEditor', false, 'contents<?php echo $i ?>') ;
					tinyMCE.execCommand('mceRemoveEditor', false, 'teachersNotes<?php echo $i ?>') ;
					$("#sortable").sortable( "refreshPositions" ) ;
				});
				
				$( "#sortable" ).bind( "sortstop", function(event, ui) {
					//These two lines have been removed to improve performance with long lists
					//tinyMCE.execCommand('mceAddEditor', false, 'contents<?php echo $i ?>') ;
					//tinyMCE.execCommand('mceAddEditor', false, 'teachersNotes<?php echo $i ?>') ;
					$("#block<?php echo $i ?>").css("height","72px") ;
				});
			});
		</script>
		<script type="text/javascript">
			$(document).ready(function(){
				$("#blockInner<?php echo $i ?>").css("display","none");
				$("#block<?php echo $i ?>").css("height","72px")
				
				//Block contents control
				$('#show<?php echo $i ?>').unbind('click').click(function() {
					if ($("#blockInner<?php echo $i ?>").is(":visible")) {
						$("#blockInner<?php echo $i ?>").css("display","none");
						$("#block<?php echo $i ?>").css("height","72px")
						$('#show<?php echo $i ?>').css("background-image", "<?php echo "url(\'".$_SESSION[$guid]['absoluteURL'].'/themes/'.$_SESSION[$guid]['gibbonThemeName']."/img/plus.png\'"?>)"); 
						tinyMCE.execCommand('mceRemoveEditor', false, 'contents<?php echo $i ?>') ;
						tinyMCE.execCommand('mceRemoveEditor', false, 'teachersNotes<?php echo $i ?>') ;
					} else {
						$("#blockInner<?php echo $i ?>").slideDown("fast", $("#blockInner<?php echo $i ?>").css("display","table-row")); //Slide Down Effect
						$("#block<?php echo $i ?>").css("height","auto")
						$('#show<?php echo $i ?>').css("background-image", "<?php echo "url(\'".$_SESSION[$guid]['absoluteURL'].'/themes/'.$_SESSION[$guid]['gibbonThemeName']."/img/minus.png\'"?>)"); 
						tinyMCE.execCommand('mceRemoveEditor', false, 'contents<?php echo $i ?>') ;	
						tinyMCE.execCommand('mceAddEditor', false, 'contents<?php echo $i ?>') ;
						tinyMCE.execCommand('mceRemoveEditor', false, 'teachersNotes<?php echo $i ?>') ;	
						tinyMCE.execCommand('mceAddEditor', false, 'teachersNotes<?php echo $i ?>') ;
					}
				});
				
				<?php if ($mode == 'masterAdd') { ?>
					var titleClick<?php echo $i ?>=false ;
					$('#title<?php echo $i ?>').focus(function() {
						if (titleClick<?php echo $i ?>==false) {
							$('#title<?php echo $i ?>').css("color", "#000") ;
							$('#title<?php echo $i ?>').val("") ;
							titleClick<?php echo $i ?>=true ;
						}
					});
					
					var typeClick<?php echo $i ?>=false ;
					$('#type<?php echo $i ?>').focus(function() {
						if (typeClick<?php echo $i ?>==false) {
							$('#type<?php echo $i ?>').css("color", "#000") ;
							$('#type<?php echo $i ?>').val("") ;
							typeClick<?php echo $i ?>=true ;
						}
					});
					
					var lengthClick<?php echo $i ?>=false ;
					$('#length<?php echo $i ?>').focus(function() {
						if (lengthClick<?php echo $i ?>==false) {
							$('#length<?php echo $i ?>').css("color", "#000") ;
							$('#length<?php echo $i ?>').val("") ;
							lengthClick<?php echo $i ?>=true ;
						}
					});
				<?php 
				}
				?>
				
				$('#delete<?php echo $i ?>').unbind('click').click(function() {
					if (confirm("Are you sure you want to delete this block?")) {
						$('#blockOuter<?php echo $i ?>').fadeOut(600, function(){ $('#block<?php echo $i ?>').remove(); });
					}
				});
			});
		</script>
		<div style='border: 1px solid #d8dcdf; margin: 0 0 5px' id="block<?php echo $i ?>" style='padding: 0px'>
			<table class='noIntBorder' cellspacing='0' style='width: 100%'>
				<tr>
					<td style='width: 50%'>
						<input name='order[]' type='hidden' value='<?php echo $i ?>'>
						<input maxlength=100 id='title<?php echo $i ?>' name='title<?php echo $i ?>' type='text' style='float: left; border: 1px dotted #aaa; background: none; margin-left: 3px; <?php if ($mode == 'masterAdd') { echo 'color: #999;'; } ?> margin-top: 0px; font-size: 140%; font-weight: bold; width: 350px' value='<?php if ($mode == 'masterAdd') { echo "Block $i"; } else { echo htmlPrep($title); } ?>'><br/>
						<input maxlength=50 id='type<?php echo $i ?>' name='type<?php echo $i ?>' type='text' style='float: left; border: 1px dotted #aaa; background: none; margin-left: 3px; <?php if ($mode == 'masterAdd') { echo 'color: #999;'; } ?> margin-top: 2px; font-size: 110%; font-style: italic; width: 250px' value='<?php if ($mode == 'masterAdd') { echo 'type (e.g. discussion, outcome)'; } else { echo htmlPrep($type); } ?>'>
						<input maxlength=3 id='length<?php echo $i ?>' name='length<?php echo $i ?>' type='text' style='float: left; border: 1px dotted #aaa; background: none; margin-left: 3px; <?php if ($mode == 'masterAdd') { echo 'color: #999;'; } ?> margin-top: 2px; font-size: 110%; font-style: italic; width: 95px' value='<?php if ($mode == 'masterAdd') { echo 'length (min)'; } else { echo htmlPrep($length); } ?>'>
					</td>
					<td style='text-align: right; width: 50%'>
						<div style='margin-bottom: 5px'>
							<?php
                            if ($mode != 'plannerEdit') {
                                echo "<img id='delete$i' title='Delete' src='./themes/".$_SESSION[$guid]['gibbonThemeName']."/img/garbage.png'/> ";
                            }
							if ($mode == 'workingEdit') {
								//Check that block is still connected to master (poor design in original smart units means that they might be disconnected, and so copyback will not work.
                                try {
                                    $dataCheck = array('ibPYPUnitMasterSmartBlockID' => $ibPYPUnitMasterSmartBlockID, 'ibPYPUnitWorkingSmartBlockID' => $ibPYPUnitWorkingSmartBlockID);
                                    $sqlCheck = 'SELECT * FROM ibPYPUnitMasterSmartBlock JOIN ibPYPUnitWorkingSmartBlock ON (ibPYPUnitWorkingSmartBlock.ibPYPUnitMasterSmartBlockID=ibPYPUnitMasterSmartBlock.ibPYPUnitMasterSmartBlockID) WHERE ibPYPUnitWorkingSmartBlockID=:ibPYPUnitWorkingSmartBlockID AND ibPYPUnitMasterSmartBlock.ibPYPUnitMasterSmartBlockID=:ibPYPUnitMasterSmartBlockID';
                                    $resultCheck = $connection2->prepare($sqlCheck);
                                    $resultCheck->execute($dataCheck);
                                } catch (PDOException $e) {
                                    echo "<div class='error'>".$e->getMessage().'</div>';
                                }
								if ($resultCheck->rowCount() == 1) {
									echo "<a onclick='return confirm(\"Are you sure you want to leave this page? Any unsaved changes will be lost.\")' style='font-weight: normal; font-style: normal; color: #fff' href='".$_SESSION[$guid]['absoluteURL'].'/index.php?q=/modules/Planner/units_edit_working_copyback.php&gibbonSchoolYearID='.$_GET['gibbonSchoolYearID'].'&gibbonCourseID='.$_GET['gibbonCourseID'].'&gibbonCourseClassID='.$_GET['gibbonCourseClassID'].'&gibbonUnitID='.$_GET['gibbonUnitID']."&ibPYPUnitMasterSmartBlockID=$ibPYPUnitMasterSmartBlockID&ibPYPUnitWorkingSmartBlockID=$ibPYPUnitWorkingSmartBlockID'><img id='copyback$i' title='Copy Back' src='./themes/".$_SESSION[$guid]['gibbonThemeName']."/img/copyback.png'/></a>";
								}
							}
							echo "<div id='show$i' style='margin-top: -1px; margin-left: 3px; padding-right: 1px; float: right; width: 25px; height: 25px; background-image: url(\"".$_SESSION[$guid]['absoluteURL'].'/themes/'.$_SESSION[$guid]['gibbonThemeName']."/img/plus.png\")'></div></br>"; ?>
						</div>
						<?php
                        if ($mode == 'plannerEdit') {
                            echo '</br>';
                        }
   						?>
						<div style='margin-right: 5px'>Complete? <input id='complete<?php echo $i ?>' name='complete<?php echo $i ?>' style='margin-right: 2px' type="checkbox" <?php if ($mode == 'masterAdd' or $mode == 'masterEdit') { echo 'disabled'; } else { if ($complete == 'Y') { echo 'checked'; } } ?>></div>
						<input type='hidden' name='ibPYPUnitMasterSmartBlockID<?php echo $i ?>' value='<?php echo $ibPYPUnitMasterSmartBlockID ?>'>
						<input type='hidden' name='ibPYPUnitWorkingSmartBlockID<?php echo $i ?>' value='<?php echo $ibPYPUnitWorkingSmartBlockID ?>'>
					</td>
				</tr>
				<tr id="blockInner<?php echo $i ?>">
					<td colspan=2 style='vertical-align: top'>
						<?php 
                        if ($mode == 'masterAdd') {
                            $contents = getSettingByScope($connection2, 'Planner', 'smartBlockTemplate');
                        }
						echo "<div style='text-align: left; font-weight: bold; margin-top: 15px'>Block Contents</div>";
						echo getEditor($guid, false, "contents$i", $contents, 20, true, false, false, true);
						echo "<div style='text-align: left; font-weight: bold; margin-top: 15px'>Teacher's Notes</div>";
						echo getEditor($guid, false, "teachersNotes$i", $teachersNotes, 20, true, false, false, true);
						?>
					</td>
				</tr>
			</table>
		</div>
	<?php
    if ($outerBlock) {
        echo '</div>';
    }
}

function enroled($guid,  $gibbonPersonID, $connection2)
{
    $output = false;

    try {
        $data = array('gibbonPersonID' => $_SESSION[$guid]['gibbonPersonID'], 'sequenceStart' => $_SESSION[$guid]['gibbonSchoolYearSequenceNumber'], 'sequenceEnd' => $_SESSION[$guid]['gibbonSchoolYearSequenceNumber']);
        $sql = 'SELECT ibPYPStudent.*, start.sequenceNumber AS start, end.sequenceNumber AS end FROM ibPYPStudent JOIN gibbonSchoolYear AS start ON (start.gibbonSchoolYearID=ibPYPStudent.gibbonSchoolYearIDStart) JOIN gibbonSchoolYear AS end ON (end.gibbonSchoolYearID=ibPYPStudent.gibbonSchoolYearIDEnd) WHERE gibbonPersonID=:gibbonPersonID AND start.sequenceNumber<=:sequenceStart AND end.sequenceNumber>=:sequenceEnd';
        $result = $connection2->prepare($sql);
        $result->execute($data);
    } catch (PDOException $e) {
    }

    if ($result->rowCount() == 1) {
        $output = true;
    }

    return $output;
}

function getRole($gibbonPersonID, $connection2)
{
    $role = false;

    try {
        $data = array('gibbonPersonID' => $gibbonPersonID);
        $sql = 'SELECT * FROM ibPYPStaffTeaching WHERE gibbonPersonID=:gibbonPersonID';
        $result = $connection2->prepare($sql);
        $result->execute($data);
    } catch (PDOException $e) { echo "<div class='error'>".$e->getMessage().'</div>';
    }

    if ($result->rowCount() == 1) {
        $row = $result->fetch();
        if ($row['role'] != '') {
            $role = $row['role'];
        }
    }

    return $role;
}

//Make the display for a block, according to the input provided, where $i is a unique number appended to the block's field ids.
function pypMakeBlock($guid,  $i, $type = '', $ibPYPGlossaryID = '', $title = '', $category = '', $contents = '', $id = '', $outerBlock = true, $allowOutcomeEditing)
{
    if ($outerBlock) { echo "<div id='".$type."BlockOuter$i'>";
    }
    ?>
		<script>
			$(function() {
				$( "#<?php echo $type ?>" ).sortable({
					placeholder: "<?php echo $type ?>-ui-state-highlight"
				});
				
				$( "#<?php echo $type ?>" ).bind( "sortstart", function(event, ui) { 
					$("#<?php echo $type ?>BlockInner<?php echo $i ?>").css("display","none") ;
					$("#<?php echo $type ?>Block<?php echo $i ?>").css("height","72px") ;
					$('#<?php echo $type ?>show<?php echo $i ?>').css("background-image", "<?php echo "url(\'".$_SESSION[$guid]['absoluteURL'].'/themes/'.$_SESSION[$guid]['gibbonThemeName']."/img/plus.png\'"?>)");  
					tinyMCE.execCommand('mceRemoveEditor', false, '<?php echo $type ?>contents<?php echo $i ?>') ;
					$("#<?php echo $type ?>").sortable( "refreshPositions" ) ;
				});
				
				$( "#<?php echo $type ?>" ).bind( "sortstop", function(event, ui) {
					//Removed to improve performance
					//tinyMCE.execCommand('mceAddEditor', false, '<?php echo $type ?>contents<?php echo $i ?>') ;
					$("#<?php echo $type ?>Block<?php echo $i ?>").css("height","72px") ;
				});
			});
		</script>
		<script type="text/javascript">
			$(document).ready(function(){
				$("#<?php echo $type ?>BlockInner<?php echo $i ?>").css("display","none");
				$("#<?php echo $type ?>Block<?php echo $i ?>").css("height","72px")
				
				//Block contents control
				$('#<?php echo $type ?>show<?php echo $i ?>').unbind('click').click(function() {
					if ($("#<?php echo $type ?>BlockInner<?php echo $i ?>").is(":visible")) {
						$("#<?php echo $type ?>BlockInner<?php echo $i ?>").css("display","none");
						$("#<?php echo $type ?>Block<?php echo $i ?>").css("height","72px") ;
						$('#<?php echo $type ?>show<?php echo $i ?>').css("background-image", "<?php echo "url(\'".$_SESSION[$guid]['absoluteURL'].'/themes/'.$_SESSION[$guid]['gibbonThemeName']."/img/plus.png\'"?>)");  
						tinyMCE.execCommand('mceRemoveEditor', false, '<?php echo $type ?>contents<?php echo $i ?>') ;
					} else {
						$("#<?php echo $type ?>BlockInner<?php echo $i ?>").slideDown("fast", $("#<?php echo $type ?>BlockInner<?php echo $i ?>").css("display","table-row")); //Slide Down Effect
						$("#<?php echo $type ?>Block<?php echo $i ?>").css("height","auto")
						$('#<?php echo $type ?>show<?php echo $i ?>').css("background-image", "<?php echo "url(\'".$_SESSION[$guid]['absoluteURL'].'/themes/'.$_SESSION[$guid]['gibbonThemeName']."/img/minus.png\'"?>)");  
						tinyMCE.execCommand('mceRemoveEditor', false, '<?php echo $type ?>contents<?php echo $i ?>') ;	
						tinyMCE.execCommand('mceAddEditor', false, '<?php echo $type ?>contents<?php echo $i ?>') ;
					}
				});
				
				$('#<?php echo $type ?>delete<?php echo $i ?>').unbind('click').click(function() {
					if (confirm("Are you sure you want to delete this block?")) {
						$('#<?php echo $type ?>BlockOuter<?php echo $i ?>').fadeOut(600, function(){ $('#<?php echo $type ?><?php echo $i ?>'); });
						$('#<?php echo $type ?>BlockOuter<?php echo $i ?>').remove();
						<?php echo $type ?>Used[<?php echo $type ?>Used.indexOf("<?php echo $ibPYPGlossaryID ?>")]="x" ;
					}
				});
				
			});
		</script>
		<div style='border: 1px solid #d8dcdf; margin: 0 0 5px' id="<?php echo $type ?>Block<?php echo $i ?>" style='padding: 0px'>
			<table class='noIntBorder' cellspacing='0' style='width: 100%'>
				<tr>
					<td style='width: 50%'>
						<input name='<?php echo $type ?>order[]' type='hidden' value='<?php echo $i ?>'>
						<input name='<?php echo $type ?>ibPYPGlossaryID<?php echo $i ?>' type='hidden' value='<?php echo $ibPYPGlossaryID ?>'>
						<input readonly maxlength=100 id='<?php echo $type ?>title<?php echo $i ?>' name='<?php echo $type ?>title<?php echo $i ?>' type='text' style='float: none; border: 1px dotted #aaa; background: none; margin-left: 3px; margin-top: 0px; font-size: 140%; font-weight: bold; width: 350px' value='<?php echo $title; ?>'><br/>
						<input readonly maxlength=100 id='<?php echo $type ?>category<?php echo $i ?>' name='<?php echo $type ?>category<?php echo $i ?>' type='text' style='float: left; border: 1px dotted #aaa; background: none; margin-left: 3px; margin-top: 2px; font-size: 110%; font-style: italic; width: 250px' value='<?php echo $category; ?>'>
						<script type="text/javascript">
							if($('#<?php echo $type ?>category<?php echo $i ?>').val()=="") {
								$('#<?php echo $type ?>category<?php echo $i ?>').css("border","none") ;
							}
						</script>
					</td>
					<td style='text-align: right; width: 50%'>
						<div style='margin-bottom: 25px'>
							<?php
                            echo "<img id='".$type."delete$i' title='Delete' src='./themes/".$_SESSION[$guid]['gibbonThemeName']."/img/garbage.png'/> ";
    						echo "<div id='".$type."show$i' style='margin-left: 3px; padding-right: 1px; float: right; width: 25px; height: 25px; background-image: url(\"".$_SESSION[$guid]['absoluteURL'].'/themes/'.$_SESSION[$guid]['gibbonThemeName']."/img/plus.png\")'></div>"; ?>
						</div>
						<input type='hidden' name='id<?php echo $i ?>' value='<?php echo $id ?>'>
					</td>
				</tr>
				<tr id="<?php echo $type ?>BlockInner<?php echo $i ?>">
					<td colspan=2 style='vertical-align: top'>
						<?php 
                        if ($allowOutcomeEditing == 'N' and $type == 'outcome') {
                            echo "<div style='padding: 5px'>$contents</div>";
                            echo "<input type='hidden' name='".$type.'contents'.$i."' value='".htmlPrep($contents)."'/>";
                        } else {
                            echo getEditor($guid, false, $type.'contents'.$i, $contents, 20, false, false, false, true);
                        }
   						?>
					</td>
				</tr>
			</table>
		</div>
	<?php
    if ($outerBlock) {
        echo '</div>';
    }
}
?>

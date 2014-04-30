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
function makeBlock($guid, $connection2, $i, $mode="masterAdd", $title="", $type="", $length="", $contents="", $complete="N", $ibPYPUnitMasterSmartBlockID="", $ibPYPUnitWorkingSmartBlockID="", $teachersNotes="", $outerBlock=TRUE) {	
	if ($outerBlock) {
		print "<div id='blockOuter$i' class='blockOuter'>" ;
	}
	?>
		<script>
			$(function() {
				$( "#sortable" ).sortable({
					placeholder: "ui-state-highlight"
				});
				
				$( "#sortable" ).bind( "sortstart", function(event, ui) { 
					$("#blockInner<?php print $i ?>").css("display","none") ;
					$("#block<?php print $i ?>").css("height","72px") ;
					$('#show<?php print $i ?>').css("background-image", "<?php print "url(\'" . $_SESSION[$guid]["absoluteURL"] . "/themes/" . $_SESSION[$guid]["gibbonThemeName"] . "/img/plus.png\'"?>)"); 
					tinyMCE.execCommand('mceRemoveControl', false, 'contents<?php print $i ?>') ;
					tinyMCE.execCommand('mceRemoveControl', false, 'teachersNotes<?php print $i ?>') ;
					$("#sortable").sortable( "refreshPositions" ) ;
				});
				
				$( "#sortable" ).bind( "sortstop", function(event, ui) {
					//These two lines have been removed to improve performance with long lists
					//tinyMCE.execCommand('mceAddControl', false, 'contents<?php print $i ?>') ;
					//tinyMCE.execCommand('mceAddControl', false, 'teachersNotes<?php print $i ?>') ;
					$("#block<?php print $i ?>").css("height","72px") ;
				});
			});
		</script>
		<script type="text/javascript">
			$(document).ready(function(){
				$("#blockInner<?php print $i ?>").css("display","none");
				$("#block<?php print $i ?>").css("height","72px")
				
				//Block contents control
				$('#show<?php print $i ?>').unbind('click').click(function() {
					if ($("#blockInner<?php print $i ?>").is(":visible")) {
						$("#blockInner<?php print $i ?>").css("display","none");
						$("#block<?php print $i ?>").css("height","72px")
						$('#show<?php print $i ?>').css("background-image", "<?php print "url(\'" . $_SESSION[$guid]["absoluteURL"] . "/themes/" . $_SESSION[$guid]["gibbonThemeName"] . "/img/plus.png\'"?>)"); 
						tinyMCE.execCommand('mceRemoveControl', false, 'contents<?php print $i ?>') ;
						tinyMCE.execCommand('mceRemoveControl', false, 'teachersNotes<?php print $i ?>') ;
					} else {
						$("#blockInner<?php print $i ?>").slideDown("fast", $("#blockInner<?php print $i ?>").css("display","table-row")); //Slide Down Effect
						$("#block<?php print $i ?>").css("height","auto")
						$('#show<?php print $i ?>').css("background-image", "<?php print "url(\'" . $_SESSION[$guid]["absoluteURL"] . "/themes/" . $_SESSION[$guid]["gibbonThemeName"] . "/img/minus.png\'"?>)"); 
						tinyMCE.execCommand('mceRemoveControl', false, 'contents<?php print $i ?>') ;	
						tinyMCE.execCommand('mceAddControl', false, 'contents<?php print $i ?>') ;
						tinyMCE.execCommand('mceRemoveControl', false, 'teachersNotes<?php print $i ?>') ;	
						tinyMCE.execCommand('mceAddControl', false, 'teachersNotes<?php print $i ?>') ;
					}
				});
				
				<?php if ($mode=="masterAdd") { ?>
					var titleClick<?php print $i ?>=false ;
					$('#title<?php print $i ?>').focus(function() {
						if (titleClick<?php print $i ?>==false) {
							$('#title<?php print $i ?>').css("color", "#000") ;
							$('#title<?php print $i ?>').val("") ;
							titleClick<?php print $i ?>=true ;
						}
					});
					
					var typeClick<?php print $i ?>=false ;
					$('#type<?php print $i ?>').focus(function() {
						if (typeClick<?php print $i ?>==false) {
							$('#type<?php print $i ?>').css("color", "#000") ;
							$('#type<?php print $i ?>').val("") ;
							typeClick<?php print $i ?>=true ;
						}
					});
					
					var lengthClick<?php print $i ?>=false ;
					$('#length<?php print $i ?>').focus(function() {
						if (lengthClick<?php print $i ?>==false) {
							$('#length<?php print $i ?>').css("color", "#000") ;
							$('#length<?php print $i ?>').val("") ;
							lengthClick<?php print $i ?>=true ;
						}
					});
				<?php } ?>
				
				$('#delete<?php print $i ?>').unbind('click').click(function() {
					if (confirm("Are you sure you want to delete this block?")) {
						$('#blockOuter<?php print $i ?>').fadeOut(600, function(){ $('#block<?php print $i ?>').remove(); });
					}
				});
			});
		</script>
		<div style='border: 1px solid #d8dcdf; margin: 0 0 5px' id="block<?php print $i ?>" style='padding: 0px'>
			<table class='noIntBorder' cellspacing='0' style='width: 100%'>
				<tr>
					<td style='width: 50%'>
						<input name='order[]' type='hidden' value='<?php print $i ?>'>
						<input maxlength=100 id='title<?php print $i ?>' name='title<?php print $i ?>' type='text' style='float: left; border: 1px dotted #aaa; background: none; margin-left: 3px; <?php if ($mode=="masterAdd") { print "color: #999;" ;} ?> margin-top: 0px; font-size: 140%; font-weight: bold; width: 350px' value='<?php if ($mode=="masterAdd") { print "Block $i" ;} else { print htmlPrep($title) ;} ?>'><br/>
						<input maxlength=50 id='type<?php print $i ?>' name='type<?php print $i ?>' type='text' style='float: left; border: 1px dotted #aaa; background: none; margin-left: 3px; <?php if ($mode=="masterAdd") { print "color: #999;" ;} ?> margin-top: 2px; font-size: 110%; font-style: italic; width: 250px' value='<?php if ($mode=="masterAdd") { print "type (e.g. discussion, outcome)" ;} else { print htmlPrep($type) ;} ?>'>
						<input maxlength=3 id='length<?php print $i ?>' name='length<?php print $i ?>' type='text' style='float: left; border: 1px dotted #aaa; background: none; margin-left: 3px; <?php if ($mode=="masterAdd") { print "color: #999;" ;} ?> margin-top: 2px; font-size: 110%; font-style: italic; width: 95px' value='<?php if ($mode=="masterAdd") { print "length (min)" ;} else { print htmlPrep($length) ;} ?>'>
					</td>
					<td style='text-align: right; width: 50%'>
						<div style='margin-bottom: 5px'>
							<?php
							if ($mode!="plannerEdit") {
								print "<img id='delete$i' title='Delete' src='./themes/" . $_SESSION[$guid]["gibbonThemeName"] . "/img/garbage.png'/> " ;
							}
							if ($mode=="workingEdit") {
								//Check that block is still connected to master (poor design in original smart units means that they might be disconnected, and so copyback will not work.
								try {
									$dataCheck=array("ibPYPUnitMasterSmartBlockID"=>$ibPYPUnitMasterSmartBlockID, "ibPYPUnitWorkingSmartBlockID"=>$ibPYPUnitWorkingSmartBlockID); 
									$sqlCheck="SELECT * FROM ibPYPUnitMasterSmartBlock JOIN ibPYPUnitWorkingSmartBlock ON (ibPYPUnitWorkingSmartBlock.ibPYPUnitMasterSmartBlockID=ibPYPUnitMasterSmartBlock.ibPYPUnitMasterSmartBlockID) WHERE ibPYPUnitWorkingSmartBlockID=:ibPYPUnitWorkingSmartBlockID AND ibPYPUnitMasterSmartBlock.ibPYPUnitMasterSmartBlockID=:ibPYPUnitMasterSmartBlockID" ;
									$resultCheck=$connection2->prepare($sqlCheck);
									$resultCheck->execute($dataCheck);
								}
								catch(PDOException $e) { 
									print "<div class='error'>" . $e->getMessage() . "</div>" ; 
								}
								if ($resultCheck->rowCount()==1) {
									print "<a onclick='return confirm(\"Are you sure you want to leave this page? Any unsaved changes will be lost.\")' style='font-weight: normal; font-style: normal; color: #fff' href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/Planner/units_edit_working_copyback.php&gibbonSchoolYearID=" . $_GET["gibbonSchoolYearID"] . "&gibbonCourseID=" . $_GET["gibbonCourseID"] . "&gibbonCourseClassID=" . $_GET["gibbonCourseClassID"] . "&gibbonUnitID=" . $_GET["gibbonUnitID"] . "&ibPYPUnitMasterSmartBlockID=$ibPYPUnitMasterSmartBlockID&ibPYPUnitWorkingSmartBlockID=$ibPYPUnitWorkingSmartBlockID'><img id='copyback$i' title='Copy Back' src='./themes/" . $_SESSION[$guid]["gibbonThemeName"] . "/img/copyback.png'/></a>" ;
								}
							}
							print "<div id='show$i' style='margin-top: -1px; margin-left: 3px; padding-right: 1px; float: right; width: 25px; height: 25px; background-image: url(\"" . $_SESSION[$guid]["absoluteURL"] . "/themes/" . $_SESSION[$guid]["gibbonThemeName"] . "/img/plus.png\")'></div></br>" ;
							?>
						</div>
						<?php
						if ($mode=="plannerEdit") {
							print "</br>" ;
						}
						?>
						<div style='margin-right: 5px'>Complete? <input id='complete<?php print $i ?>' name='complete<?php print $i ?>' style='margin-right: 2px' type="checkbox" <?php if ($mode=="masterAdd" OR $mode=="masterEdit") { print "disabled" ; } else { if ($complete=="Y") { print "checked" ; }}?>></div>
						<input type='hidden' name='ibPYPUnitMasterSmartBlockID<?php print $i ?>' value='<?php print $ibPYPUnitMasterSmartBlockID ?>'>
						<input type='hidden' name='ibPYPUnitWorkingSmartBlockID<?php print $i ?>' value='<?php print $ibPYPUnitWorkingSmartBlockID ?>'>
					</td>
				</tr>
				<tr id="blockInner<?php print $i ?>">
					<td colspan=2 style='vertical-align: top'>
						<?php 
						if ($mode=="masterAdd") { 
							$contents=getSettingByScope($connection2, "Planner", "smartBlockTemplate" ) ; 
						}
						print "<div style='text-align: left; font-weight: bold; margin-top: 15px'>Block Contents</div>" ;
						print getEditor($guid, FALSE, "contents$i", $contents, 20, true, false, false, true) ;
						print "<div style='text-align: left; font-weight: bold; margin-top: 15px'>Teacher's Notes</div>" ;
						print getEditor($guid, FALSE, "teachersNotes$i", $teachersNotes, 20, true, false, false, true) ;
						?>
					</td>
				</tr>
			</table>
		</div>
	<?php
	if ($outerBlock) {
		print "</div>" ;
	}
}

function enroled($guid,  $gibbonPersonID, $connection2) {
	$output=FALSE ;
	
	try {
		$data=array("gibbonPersonID"=>$_SESSION[$guid]["gibbonPersonID"], "sequenceStart"=>$_SESSION[$guid]["gibbonSchoolYearSequenceNumber"], "sequenceEnd"=>$_SESSION[$guid]["gibbonSchoolYearSequenceNumber"]);  
		$sql="SELECT ibPYPStudent.*, start.sequenceNumber AS start, end.sequenceNumber AS end FROM ibPYPStudent JOIN gibbonSchoolYear AS start ON (start.gibbonSchoolYearID=ibPYPStudent.gibbonSchoolYearIDStart) JOIN gibbonSchoolYear AS end ON (end.gibbonSchoolYearID=ibPYPStudent.gibbonSchoolYearIDEnd) WHERE gibbonPersonID=:gibbonPersonID AND start.sequenceNumber<=:sequenceStart AND end.sequenceNumber>=:sequenceEnd" ;
		$result=$connection2->prepare($sql);
		$result->execute($data);
	}
	catch(PDOException $e) { }
	
	if ($result->rowCount()==1) {
		$output=TRUE ;
	}
	
	return $output ;
}

function getRole($gibbonPersonID, $connection2) {
	$role=FALSE ;
	
	try {
		$data=array("gibbonPersonID"=>$gibbonPersonID);  
		$sql="SELECT * FROM ibPYPStaffTeaching WHERE gibbonPersonID=:gibbonPersonID" ;
		$result=$connection2->prepare($sql);
		$result->execute($data);
	}
	catch(PDOException $e) { 
		print "<div class='error'>" . $e->getMessage() . "</div>" ; 
	}
	
	if ($result->rowCount()==1) {
		$row=$result->fetch() ;
		if ($row["role"]!="") {
			$role=$row["role"] ;
		}
	}
	return $role ;
}

//Make the display for a block, according to the input provided, where $i is a unique number appended to the block's field ids.
function pypMakeBlock($guid,  $i, $type="", $ibPYPGlossaryID="", $title="", $category="", $contents="", $id="", $outerBlock=TRUE, $allowOutcomeEditing) {	
	if ($outerBlock) {
		print "<div id='" . $type . "BlockOuter$i'>" ;
	}
	?>
		<script>
			$(function() {
				$( "#<?php print $type ?>" ).sortable({
					placeholder: "<?php print $type ?>-ui-state-highlight"
				});
				
				$( "#<?php print $type ?>" ).bind( "sortstart", function(event, ui) { 
					$("#<?php print $type ?>BlockInner<?php print $i ?>").css("display","none") ;
					$("#<?php print $type ?>Block<?php print $i ?>").css("height","72px") ;
					$('#<?php print $type ?>show<?php print $i ?>').css("background-image", "<?php print "url(\'" . $_SESSION[$guid]["absoluteURL"] . "/themes/" . $_SESSION[$guid]["gibbonThemeName"] . "/img/plus.png\'"?>)");  
					tinyMCE.execCommand('mceRemoveControl', false, '<?php print $type ?>contents<?php print $i ?>') ;
					$("#<?php print $type ?>").sortable( "refreshPositions" ) ;
				});
				
				$( "#<?php print $type ?>" ).bind( "sortstop", function(event, ui) {
					//Removed to improve performance
					//tinyMCE.execCommand('mceAddControl', false, '<?php print $type ?>contents<?php print $i ?>') ;
					$("#<?php print $type ?>Block<?php print $i ?>").css("height","72px") ;
				});
			});
		</script>
		<script type="text/javascript">
			$(document).ready(function(){
				$("#<?php print $type ?>BlockInner<?php print $i ?>").css("display","none");
				$("#<?php print $type ?>Block<?php print $i ?>").css("height","72px")
				
				//Block contents control
				$('#<?php print $type ?>show<?php print $i ?>').unbind('click').click(function() {
					if ($("#<?php print $type ?>BlockInner<?php print $i ?>").is(":visible")) {
						$("#<?php print $type ?>BlockInner<?php print $i ?>").css("display","none");
						$("#<?php print $type ?>Block<?php print $i ?>").css("height","72px") ;
						$('#<?php print $type ?>show<?php print $i ?>').css("background-image", "<?php print "url(\'" . $_SESSION[$guid]["absoluteURL"] . "/themes/" . $_SESSION[$guid]["gibbonThemeName"] . "/img/plus.png\'"?>)");  
						tinyMCE.execCommand('mceRemoveControl', false, '<?php print $type ?>contents<?php print $i ?>') ;
					} else {
						$("#<?php print $type ?>BlockInner<?php print $i ?>").slideDown("fast", $("#<?php print $type ?>BlockInner<?php print $i ?>").css("display","table-row")); //Slide Down Effect
						$("#<?php print $type ?>Block<?php print $i ?>").css("height","auto")
						$('#<?php print $type ?>show<?php print $i ?>').css("background-image", "<?php print "url(\'" . $_SESSION[$guid]["absoluteURL"] . "/themes/" . $_SESSION[$guid]["gibbonThemeName"] . "/img/minus.png\'"?>)");  
						tinyMCE.execCommand('mceRemoveControl', false, '<?php print $type ?>contents<?php print $i ?>') ;	
						tinyMCE.execCommand('mceAddControl', false, '<?php print $type ?>contents<?php print $i ?>') ;
					}
				});
				
				$('#<?php print $type ?>delete<?php print $i ?>').unbind('click').click(function() {
					if (confirm("Are you sure you want to delete this block?")) {
						$('#<?php print $type ?>BlockOuter<?php print $i ?>').fadeOut(600, function(){ $('#<?php print $type ?><?php print $i ?>'); });
						$('#<?php print $type ?>BlockOuter<?php print $i ?>').remove();
						<?php print $type ?>Used[<?php print $type ?>Used.indexOf("<?php print $ibPYPGlossaryID ?>")]="x" ;
					}
				});
				
			});
		</script>
		<div style='border: 1px solid #d8dcdf; margin: 0 0 5px' id="<?php print $type ?>Block<?php print $i ?>" style='padding: 0px'>
			<table class='noIntBorder' cellspacing='0' style='width: 100%'>
				<tr>
					<td style='width: 50%'>
						<input name='<?php print $type ?>order[]' type='hidden' value='<?php print $i ?>'>
						<input name='<?php print $type ?>ibPYPGlossaryID<?php print $i ?>' type='hidden' value='<?php print $ibPYPGlossaryID ?>'>
						<input readonly maxlength=100 id='<?php print $type ?>title<?php print $i ?>' name='<?php print $type ?>title<?php print $i ?>' type='text' style='float: none; border: 1px dotted #aaa; background: none; margin-left: 3px; margin-top: 0px; font-size: 140%; font-weight: bold; width: 350px' value='<?php print $title ; ?>'><br/>
						<input readonly maxlength=100 id='<?php print $type ?>category<?php print $i ?>' name='<?php print $type ?>category<?php print $i ?>' type='text' style='float: left; border: 1px dotted #aaa; background: none; margin-left: 3px; margin-top: 2px; font-size: 110%; font-style: italic; width: 250px' value='<?php print $category ; ?>'>
						<script type="text/javascript">
							if($('#<?php print $type ?>category<?php print $i ?>').val()=="") {
								$('#<?php print $type ?>category<?php print $i ?>').css("border","none") ;
							}
						</script>
					</td>
					<td style='text-align: right; width: 50%'>
						<div style='margin-bottom: 25px'>
							<?php
							print "<img id='" . $type  . "delete$i' title='Delete' src='./themes/" . $_SESSION[$guid]["gibbonThemeName"] . "/img/garbage.png'/> " ;
							print "<div id='" . $type . "show$i' style='margin-left: 3px; padding-right: 1px; float: right; width: 25px; height: 25px; background-image: url(\"" . $_SESSION[$guid]["absoluteURL"] . "/themes/" . $_SESSION[$guid]["gibbonThemeName"] . "/img/plus.png\")'></div>" ;
							?>
						</div>
						<input type='hidden' name='id<?php print $i ?>' value='<?php print $id ?>'>
					</td>
				</tr>
				<tr id="<?php print $type ?>BlockInner<?php print $i ?>">
					<td colspan=2 style='vertical-align: top'>
						<?php 
						if ($allowOutcomeEditing=="N" AND $type=="outcome") {
							print "<div style='padding: 5px'>$contents</div>" ;
							print "<input type='hidden' name='" . $type . "contents" . $i . "' value='" . htmlPrep($contents) . "'/>" ;
						}
						else {
							print getEditor($guid, FALSE, $type . "contents" . $i, $contents, 20, false, false, false, true) ;
						}
						?>
					</td>
				</tr>
			</table>
		</div>
	<?php
	if ($outerBlock) {
		print "</div>" ;
	}
}
?>

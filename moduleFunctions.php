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
					$("#blockInner<? print $i ?>").css("display","none") ;
					$("#block<? print $i ?>").css("height","72px") ;
					$('#show<? print $i ?>').css("background-image", "<? print "url(\'" . $_SESSION[$guid]["absoluteURL"] . "/themes/" . $_SESSION[$guid]["gibbonThemeName"] . "/img/plus.png\'"?>)"); 
					tinyMCE.execCommand('mceRemoveControl', false, 'contents<? print $i ?>') ;
					tinyMCE.execCommand('mceRemoveControl', false, 'teachersNotes<? print $i ?>') ;
					$("#sortable").sortable( "refreshPositions" ) ;
				});
				
				$( "#sortable" ).bind( "sortstop", function(event, ui) {
					//These two lines have been removed to improve performance with long lists
					//tinyMCE.execCommand('mceAddControl', false, 'contents<? print $i ?>') ;
					//tinyMCE.execCommand('mceAddControl', false, 'teachersNotes<? print $i ?>') ;
					$("#block<? print $i ?>").css("height","72px") ;
				});
			});
		</script>
		<script type="text/javascript">
			$(document).ready(function(){
				$("#blockInner<? print $i ?>").css("display","none");
				$("#block<? print $i ?>").css("height","72px")
				
				//Block contents control
				$('#show<? print $i ?>').unbind('click').click(function() {
					if ($("#blockInner<? print $i ?>").is(":visible")) {
						$("#blockInner<? print $i ?>").css("display","none");
						$("#block<? print $i ?>").css("height","72px")
						$('#show<? print $i ?>').css("background-image", "<? print "url(\'" . $_SESSION[$guid]["absoluteURL"] . "/themes/" . $_SESSION[$guid]["gibbonThemeName"] . "/img/plus.png\'"?>)"); 
						tinyMCE.execCommand('mceRemoveControl', false, 'contents<? print $i ?>') ;
						tinyMCE.execCommand('mceRemoveControl', false, 'teachersNotes<? print $i ?>') ;
					} else {
						$("#blockInner<? print $i ?>").slideDown("fast", $("#blockInner<? print $i ?>").css("display","table-row")); //Slide Down Effect
						$("#block<? print $i ?>").css("height","auto")
						$('#show<? print $i ?>').css("background-image", "<? print "url(\'" . $_SESSION[$guid]["absoluteURL"] . "/themes/" . $_SESSION[$guid]["gibbonThemeName"] . "/img/minus.png\'"?>)"); 
						tinyMCE.execCommand('mceRemoveControl', false, 'contents<? print $i ?>') ;	
						tinyMCE.execCommand('mceAddControl', false, 'contents<? print $i ?>') ;
						tinyMCE.execCommand('mceRemoveControl', false, 'teachersNotes<? print $i ?>') ;	
						tinyMCE.execCommand('mceAddControl', false, 'teachersNotes<? print $i ?>') ;
					}
				});
				
				<? if ($mode=="masterAdd") { ?>
					var titleClick<? print $i ?>=false ;
					$('#title<? print $i ?>').focus(function() {
						if (titleClick<? print $i ?>==false) {
							$('#title<? print $i ?>').css("color", "#000") ;
							$('#title<? print $i ?>').val("") ;
							titleClick<? print $i ?>=true ;
						}
					});
					
					var typeClick<? print $i ?>=false ;
					$('#type<? print $i ?>').focus(function() {
						if (typeClick<? print $i ?>==false) {
							$('#type<? print $i ?>').css("color", "#000") ;
							$('#type<? print $i ?>').val("") ;
							typeClick<? print $i ?>=true ;
						}
					});
					
					var lengthClick<? print $i ?>=false ;
					$('#length<? print $i ?>').focus(function() {
						if (lengthClick<? print $i ?>==false) {
							$('#length<? print $i ?>').css("color", "#000") ;
							$('#length<? print $i ?>').val("") ;
							lengthClick<? print $i ?>=true ;
						}
					});
				<? } ?>
				
				$('#delete<? print $i ?>').unbind('click').click(function() {
					if (confirm("Are you sure you want to delete this block?")) {
						$('#blockOuter<? print $i ?>').fadeOut(600, function(){ $('#block<? print $i ?>').remove(); });
					}
				});
			});
		</script>
		<div style='border: 1px solid #d8dcdf; margin: 0 0 5px' id="block<? print $i ?>" style='padding: 0px'>
			<table class='noIntBorder' cellspacing='0' style='width: 100%'>
				<tr>
					<td style='width: 50%'>
						<input name='order[]' type='hidden' value='<? print $i ?>'>
						<input maxlength=100 id='title<? print $i ?>' name='title<? print $i ?>' type='text' style='float: left; border: 1px dotted #aaa; background: none; margin-left: 3px; <? if ($mode=="masterAdd") { print "color: #999;" ;} ?> margin-top: 0px; font-size: 140%; font-weight: bold; width: 350px' value='<? if ($mode=="masterAdd") { print "Block $i" ;} else { print htmlPrep($title) ;} ?>'><br/>
						<input maxlength=50 id='type<? print $i ?>' name='type<? print $i ?>' type='text' style='float: left; border: 1px dotted #aaa; background: none; margin-left: 3px; <? if ($mode=="masterAdd") { print "color: #999;" ;} ?> margin-top: 2px; font-size: 110%; font-style: italic; width: 250px' value='<? if ($mode=="masterAdd") { print "type (e.g. discussion, outcome)" ;} else { print htmlPrep($type) ;} ?>'>
						<input maxlength=3 id='length<? print $i ?>' name='length<? print $i ?>' type='text' style='float: left; border: 1px dotted #aaa; background: none; margin-left: 3px; <? if ($mode=="masterAdd") { print "color: #999;" ;} ?> margin-top: 2px; font-size: 110%; font-style: italic; width: 95px' value='<? if ($mode=="masterAdd") { print "length (min)" ;} else { print htmlPrep($length) ;} ?>'>
					</td>
					<td style='text-align: right; width: 50%'>
						<div style='margin-bottom: 5px'>
							<?
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
						<?
						if ($mode=="plannerEdit") {
							print "</br>" ;
						}
						?>
						<div style='margin-right: 5px'>Complete? <input id='complete<? print $i ?>' name='complete<? print $i ?>' style='margin-right: 2px' type="checkbox" <? if ($mode=="masterAdd" OR $mode=="masterEdit") { print "disabled" ; } else { if ($complete=="Y") { print "checked" ; }}?>></div>
						<input type='hidden' name='ibPYPUnitMasterSmartBlockID<? print $i ?>' value='<? print $ibPYPUnitMasterSmartBlockID ?>'>
						<input type='hidden' name='ibPYPUnitWorkingSmartBlockID<? print $i ?>' value='<? print $ibPYPUnitWorkingSmartBlockID ?>'>
					</td>
				</tr>
				<tr id="blockInner<? print $i ?>">
					<td colspan=2 style='vertical-align: top'>
						<? 
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
	<?
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
				$( "#<? print $type ?>" ).sortable({
					placeholder: "<? print $type ?>-ui-state-highlight"
				});
				
				$( "#<? print $type ?>" ).bind( "sortstart", function(event, ui) { 
					$("#<? print $type ?>BlockInner<? print $i ?>").css("display","none") ;
					$("#<? print $type ?>Block<? print $i ?>").css("height","72px") ;
					$('#<? print $type ?>show<? print $i ?>').css("background-image", "<? print "url(\'" . $_SESSION[$guid]["absoluteURL"] . "/themes/" . $_SESSION[$guid]["gibbonThemeName"] . "/img/plus.png\'"?>)");  
					tinyMCE.execCommand('mceRemoveControl', false, '<? print $type ?>contents<? print $i ?>') ;
					$("#<? print $type ?>").sortable( "refreshPositions" ) ;
				});
				
				$( "#<? print $type ?>" ).bind( "sortstop", function(event, ui) {
					//Removed to improve performance
					//tinyMCE.execCommand('mceAddControl', false, '<? print $type ?>contents<? print $i ?>') ;
					$("#<? print $type ?>Block<? print $i ?>").css("height","72px") ;
				});
			});
		</script>
		<script type="text/javascript">
			$(document).ready(function(){
				$("#<? print $type ?>BlockInner<? print $i ?>").css("display","none");
				$("#<? print $type ?>Block<? print $i ?>").css("height","72px")
				
				//Block contents control
				$('#<? print $type ?>show<? print $i ?>').unbind('click').click(function() {
					if ($("#<? print $type ?>BlockInner<? print $i ?>").is(":visible")) {
						$("#<? print $type ?>BlockInner<? print $i ?>").css("display","none");
						$("#<? print $type ?>Block<? print $i ?>").css("height","72px") ;
						$('#<? print $type ?>show<? print $i ?>').css("background-image", "<? print "url(\'" . $_SESSION[$guid]["absoluteURL"] . "/themes/" . $_SESSION[$guid]["gibbonThemeName"] . "/img/plus.png\'"?>)");  
						tinyMCE.execCommand('mceRemoveControl', false, '<? print $type ?>contents<? print $i ?>') ;
					} else {
						$("#<? print $type ?>BlockInner<? print $i ?>").slideDown("fast", $("#<? print $type ?>BlockInner<? print $i ?>").css("display","table-row")); //Slide Down Effect
						$("#<? print $type ?>Block<? print $i ?>").css("height","auto")
						$('#<? print $type ?>show<? print $i ?>').css("background-image", "<? print "url(\'" . $_SESSION[$guid]["absoluteURL"] . "/themes/" . $_SESSION[$guid]["gibbonThemeName"] . "/img/minus.png\'"?>)");  
						tinyMCE.execCommand('mceRemoveControl', false, '<? print $type ?>contents<? print $i ?>') ;	
						tinyMCE.execCommand('mceAddControl', false, '<? print $type ?>contents<? print $i ?>') ;
					}
				});
				
				$('#<? print $type ?>delete<? print $i ?>').unbind('click').click(function() {
					if (confirm("Are you sure you want to delete this block?")) {
						$('#<? print $type ?>BlockOuter<? print $i ?>').fadeOut(600, function(){ $('#<? print $type ?><? print $i ?>'); });
						$('#<? print $type ?>BlockOuter<? print $i ?>').remove();
						<? print $type ?>Used[<? print $type ?>Used.indexOf("<? print $ibPYPGlossaryID ?>")]="x" ;
					}
				});
				
			});
		</script>
		<div style='border: 1px solid #d8dcdf; margin: 0 0 5px' id="<? print $type ?>Block<? print $i ?>" style='padding: 0px'>
			<table class='noIntBorder' cellspacing='0' style='width: 100%'>
				<tr>
					<td style='width: 50%'>
						<input name='<? print $type ?>order[]' type='hidden' value='<? print $i ?>'>
						<input name='<? print $type ?>ibPYPGlossaryID<? print $i ?>' type='hidden' value='<? print $ibPYPGlossaryID ?>'>
						<input readonly maxlength=100 id='<? print $type ?>title<? print $i ?>' name='<? print $type ?>title<? print $i ?>' type='text' style='float: none; border: 1px dotted #aaa; background: none; margin-left: 3px; margin-top: 0px; font-size: 140%; font-weight: bold; width: 350px' value='<? print $title ; ?>'><br/>
						<input readonly maxlength=100 id='<? print $type ?>category<? print $i ?>' name='<? print $type ?>category<? print $i ?>' type='text' style='float: left; border: 1px dotted #aaa; background: none; margin-left: 3px; margin-top: 2px; font-size: 110%; font-style: italic; width: 250px' value='<? print $category ; ?>'>
						<script type="text/javascript">
							if($('#<? print $type ?>category<? print $i ?>').val()=="") {
								$('#<? print $type ?>category<? print $i ?>').css("border","none") ;
							}
						</script>
					</td>
					<td style='text-align: right; width: 50%'>
						<div style='margin-bottom: 25px'>
							<?
							print "<img id='" . $type  . "delete$i' title='Delete' src='./themes/" . $_SESSION[$guid]["gibbonThemeName"] . "/img/garbage.png'/> " ;
							print "<div id='" . $type . "show$i' style='margin-left: 3px; padding-right: 1px; float: right; width: 25px; height: 25px; background-image: url(\"" . $_SESSION[$guid]["absoluteURL"] . "/themes/" . $_SESSION[$guid]["gibbonThemeName"] . "/img/plus.png\")'></div>" ;
							?>
						</div>
						<input type='hidden' name='id<? print $i ?>' value='<? print $id ?>'>
					</td>
				</tr>
				<tr id="<? print $type ?>BlockInner<? print $i ?>">
					<td colspan=2 style='vertical-align: top'>
						<? 
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
	<?
	if ($outerBlock) {
		print "</div>" ;
	}
}
?>

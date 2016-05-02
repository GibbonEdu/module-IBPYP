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

include '../../functions.php';
include '../../config.php';

//New PDO DB connection
try {
    $connection2 = new PDO("mysql:host=$databaseServer;dbname=$databaseName;charset=utf8", $databaseUsername, $databasePassword);
    $connection2->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $connection2->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo $e->getMessage();
}

@session_start();
$_SESSION[$guid]['ibPYPUnitsTab'] = 1;

//Module includes
include './moduleFunctions.php';

//Set timezone from session variable
date_default_timezone_set($_SESSION[$guid]['timezone']);

$gibbonSchoolYearID = $_GET['gibbonSchoolYearID'];
$ibPYPUnitMasterID = $_GET['ibPYPUnitMasterID'];
$gibbonCourseIDTarget = $_POST['gibbonCourseIDTarget'];
$URL = $_SESSION[$guid]['absoluteURL'].'/index.php?q=/modules/'.getModuleName($_GET['address'])."/units_manage_master_duplicate.php&ibPYPUnitMasterID=$ibPYPUnitMasterID&gibbonSchoolYearID=$gibbonSchoolYearID";

if (isActionAccessible($guid, $connection2, '/modules/IB PYP/units_manage_master_edit.php') == false) {
    //Fail 0
    $URL = $URL.'&return=error0';
    header("Location: {$URL}");
} else {
    //Proceed!
    //Validate Inputs
    if ($gibbonSchoolYearID == '' or  $ibPYPUnitMasterID == '' or $gibbonCourseIDTarget == '') {
        //Fail 3
        $URL = $URL.'&return=error3';
        header("Location: {$URL}");
    } else {
        //Lock table
        try {
            $sql = 'LOCK TABLE ibPYPUnitMaster WRITE';
            $result = $connection2->query($sql);
        } catch (PDOException $e) {
            //Fail 2
            $URL = $URL.'&return=error2';
            header("Location: {$URL}");
            exit();
        }

        //Get next autoincrement for unit
        try {
            $sqlAI = "SHOW TABLE STATUS LIKE 'ibPYPUnitMaster'";
            $resultAI = $connection2->query($sqlAI);
        } catch (PDOException $e) {
            //Fail 2
            $URL = $URL.'&return=error2';
            header("Location: {$URL}");
            exit();
        }

        $rowAI = $resultAI->fetch();
        $AI = str_pad($rowAI['Auto_increment'], 8, '0', STR_PAD_LEFT);
        $partialFail = false;

        //Unlock the activityStudent database table
        try {
            $sql = 'UNLOCK TABLES';
            $result = $connection2->query($sql);
        } catch (PDOException $e) {
        }

        if ($AI == '') {
            //Fail 2
            $URL = $URL.'&return=error2';
            header("Location: {$URL}");
        } else {
            //Write to database
            try {
                $data = array('ibPYPUnitMasterID' => $ibPYPUnitMasterID);
                $sql = 'SELECT * FROM ibPYPUnitMaster WHERE ibPYPUnitMasterID=:ibPYPUnitMasterID';
                $result = $connection2->prepare($sql);
                $result->execute($data);
            } catch (PDOException $e) {
                //Fail 2
                $URL = $URL.'&return=error2';
                header("Location: {$URL}");
                exit();
            }

            if ($result->rowCount() != 1) {
                //Fail 2
                $URL = $URL.'&return=error2';
                header("Location: {$URL}");
            } else {
                $row = $result->fetch();
                try {
                    $data = array('gibbonCourseID' => $gibbonCourseIDTarget, 'gibbonPersonIDCreator' => $_SESSION[$guid]['gibbonPersonID'], 'timestamp' => date('Y-m-d H:i:s'), 'name' => $row['name'], 'active' => $row['active'], 'theme' => $row['theme'], 'centralIdea' => $row['centralIdea'], 'summativeAssessment' => $row['summativeAssessment'], 'relatedConcepts' => $row['relatedConcepts'], 'linesOfInquiry' => $row['linesOfInquiry'], 'teacherQuestions' => $row['teacherQuestions'], 'provocation' => $row['provocation'], 'preAssessment' => $row['preAssessment'], 'formativeAssessment' => $row['formativeAssessment'], 'resources' => $row['resources'], 'action' => $row['action'], 'environments' => $row['environments']);
                    $sql = 'INSERT INTO ibPYPUnitMaster SET gibbonCourseID=:gibbonCourseID, gibbonPersonIDCreator=:gibbonPersonIDCreator, timestamp=:timestamp, name=:name, active=:active, theme=:theme, centralIdea=:centralIdea, summativeAssessment=:summativeAssessment, relatedConcepts=:relatedConcepts, linesOfInquiry=:linesOfInquiry, teacherQuestions=:teacherQuestions, provocation=:provocation, preAssessment=:preAssessment, formativeAssessment=:formativeAssessment, resources=:resources, action=:action, environments=:environments';
                    $result = $connection2->prepare($sql);
                    $result->execute($data);
                } catch (PDOException $e) {
                    //Fail 2
                    $URL = $URL.'&return=error2';
                    header("Location: {$URL}");
                    exit();
                }

                //Copy Smart Blocks
                try {
                    $dataBlocks = array('ibPYPUnitMasterID' => $ibPYPUnitMasterID);
                    $sqlBlocks = 'SELECT * FROM ibPYPUnitMasterSmartBlock WHERE ibPYPUnitMasterID=:ibPYPUnitMasterID';
                    $resultBlocks = $connection2->prepare($sqlBlocks);
                    $resultBlocks->execute($dataBlocks);
                } catch (PDOException $e) {
                    $partialFail = true;
                }
                if ($resultBlocks->rowCount() > 0) {
                    while ($rowBlocks = $resultBlocks->fetch()) {
                        //Write to database
                        try {
                            $dataCopy = array('ibPYPUnitMasterID' => $AI, 'title' => $rowBlocks['title'], 'type' => $rowBlocks['type'], 'length' => $rowBlocks['length'], 'contents' => $rowBlocks['contents'], 'teachersNotes' => $rowBlocks['teachersNotes'], 'sequenceNumber' => $rowBlocks['sequenceNumber']);
                            $sqlCopy = 'INSERT INTO ibPYPUnitMasterSmartBlock SET ibPYPUnitMasterID=:ibPYPUnitMasterID, title=:title, type=:type, length=:length, contents=:contents, teachersNotes=:teachersNotes, sequenceNumber=:sequenceNumber';
                            $resultCopy = $connection2->prepare($sqlCopy);
                            $resultCopy->execute($dataCopy);
                        } catch (PDOException $e) {
                            $partialFail = true;
                        }
                    }
                }

                //Copy Blocks
                try {
                    $dataBlocks = array('ibPYPUnitMasterID' => $ibPYPUnitMasterID);
                    $sqlBlocks = 'SELECT * FROM ibPYPUnitMasterBlock WHERE ibPYPUnitMasterID=:ibPYPUnitMasterID';
                    $resultBlocks = $connection2->prepare($sqlBlocks);
                    $resultBlocks->execute($dataBlocks);
                } catch (PDOException $e) {
                    $partialFail = true;
                }
                if ($resultBlocks->rowCount() > 0) {
                    while ($rowBlocks = $resultBlocks->fetch()) {
                        //Write to database
                        try {
                            $dataCopy = array('ibPYPUnitMasterID' => $AI, 'ibPYPGlossaryID' => $rowBlocks['ibPYPGlossaryID'], 'gibbonOutcomeID' => $rowBlocks['gibbonOutcomeID'], 'content' => $rowBlocks['content'], 'sequenceNumber' => $rowBlocks['sequenceNumber']);
                            $sqlCopy = 'INSERT INTO ibPYPUnitMasterBlock SET ibPYPUnitMasterID=:ibPYPUnitMasterID, ibPYPGlossaryID=:ibPYPGlossaryID, gibbonOutcomeID=:gibbonOutcomeID, content=:content, sequenceNumber=:sequenceNumber';
                            $resultCopy = $connection2->prepare($sqlCopy);
                            $resultCopy->execute($dataCopy);
                        } catch (PDOException $e) {
                            $partialFail = true;
                        }
                    }
                }

                if ($partialFail == true) {
                    //Fail 6
                    $URL = $URL.'&return=error6';
                    header("Location: {$URL}");
                } else {
                    //Success 0
                    $URL = $URL.'&return=success0';
                    header("Location: {$URL}");
                }
            }
        }
    }
}

<?php
//USE ;end TO SEPERATE SQL STATEMENTS. DON'T USE ;end IN ANY OTHER PLACES!

$sql=array() ;
$count=0 ;

//v0.1.00
$sql[$count][0]="0.1.00" ;
$sql[$count][1]="-- First version, nothing to update" ;


//v0.1.01
$count++ ;
$sql[$count][0]="0.1.01" ;
$sql[$count][1]="
UPDATE gibbonAction JOIN gibbonModule ON (gibbonAction.gibbonModuleID=gibbonModule.gibbonModuleID) SET gibbonAction.name='Essential Elements' WHERE gibbonAction.name='Curriculum Glossary' AND gibbonModule.name='IB PYP' ;end
" ;


//v0.1.02
$count++ ;
$sql[$count][0]="0.1.02" ;
$sql[$count][1]="
ALTER TABLE `ibPYPUnitMasterBlock` ADD `sequenceNumber` INT( 4 ) NOT NULL ;end
ALTER TABLE `ibPYPGlossary` CHANGE `type` `type` ENUM( 'Attitude', 'Concept', 'Learner Profile', 'Outcome', 'Transdisciplinary Skill' ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ;end
ALTER TABLE `ibPYPUnitMaster` ADD `action` TEXT NOT NULL AFTER `resources` ;end
UPDATE `gibbonAction` JOIN gibbonModule ON (gibbonAction.gibbonModuleID=gibbonModule.gibbonModuleID) SET `URLList` = 'units_manage.php,units_manage_master_add.php,units_manage_master_edit.php,units_manage_master_delete.php,units_manage_master_deploy.php,units_manage_working_add.php,units_manage_working_edit.php,units_manage_working_delete.php' WHERE `gibbonAction`.`name` ='Units' AND `gibbonModule`.`name` ='IB PYP' ;end
CREATE TABLE `ibPYPUnitWorking` (  `ibPYPUnitWorkingID` int(12) unsigned zerofill NOT NULL AUTO_INCREMENT,  `ibPYPUnitMasterID` int(10) unsigned zerofill NOT NULL,  `gibbonPersonIDCreator` int(10) unsigned zerofill NOT NULL,  `timestamp` datetime NOT NULL,  `name` varchar(50) NOT NULL,  `gibbonSchoolYearID` int(3) unsigned zerofill NOT NULL,  `dateStart` date NOT NULL,  `gibbonCourseClassIDList` text NOT NULL,  `gibbonYearGroupIDList` varchar(255) NOT NULL,  `theme` text NOT NULL,  `centralIdea` text NOT NULL,  `summativeAssessment` text NOT NULL,  `linesOfInquiry` text NOT NULL,  `teacherQuestions` text NOT NULL,  `provocation` text NOT NULL,  `preAssessment` text NOT NULL,  `formativeAssessment` text NOT NULL,  `learningExperiences` text NOT NULL,  `resources` text NOT NULL,  `action` text NOT NULL,  `environments` text NOT NULL,  PRIMARY KEY (`ibPYPUnitWorkingID`)) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;end
CREATE TABLE `ibPYPUnitWorkingBlock` (  `ibPYPUnitWorkingBlockID` int(14) unsigned zerofill NOT NULL AUTO_INCREMENT,  `ibPYPUnitWorkingID` int(12) unsigned zerofill NOT NULL,  `ibPYPGlossaryID` int(6) unsigned zerofill NOT NULL,  `content` text NOT NULL,  `sequenceNumber` int(4) NOT NULL,  PRIMARY KEY (`ibPYPUnitWorkingBlockID`)) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;end
" ;

//v0.2.00
$count++ ;
$sql[$count][0]="0.2.00" ;
$sql[$count][1]="
ALTER TABLE `ibPYPUnitWorking` ADD `assessOutcomes` TEXT NOT NULL ;end
ALTER TABLE `ibPYPUnitWorking` ADD `assessmentImprovements` TEXT NOT NULL ;end
ALTER TABLE `ibPYPUnitWorking` ADD `ideasThemes` TEXT NOT NULL ;end
ALTER TABLE `ibPYPUnitWorking` ADD `learningExperiencesConcepts` TEXT NOT NULL , ADD `learningExperiencesTransSkills` TEXT NOT NULL , ADD `learningExperiencesProfileAttitudes` TEXT NOT NULL ;end
ALTER TABLE `ibPYPUnitWorking` ADD `inquiriesQuestions` TEXT NOT NULL , ADD `questionsProvocations` TEXT NOT NULL , ADD `studentInitAction` TEXT NOT NULL , ADD `teachersNotes` TEXT NOT NULL ;end
UPDATE `gibbonAction` JOIN gibbonModule ON (gibbonAction.gibbonModuleID=gibbonModule.gibbonModuleID) SET `URLList` = 'units_manage.php,units_manage_master_add.php,units_manage_master_edit.php,units_manage_master_delete.php,units_manage_master_deploy.php,units_manage_working_add.php,units_manage_working_edit.php,units_manage_working_delete.php,units_manage_working_copyBack.php' WHERE `gibbonAction`.`name` ='Units' AND `gibbonModule`.`name` ='IB PYP' ;end
ALTER TABLE `ibPYPUnitWorking` DROP `gibbonCourseClassIDList` ;end
ALTER TABLE `ibPYPUnitWorking` ADD `gibbonCourseID` INT( 8 ) UNSIGNED ZEROFILL NOT NULL AFTER `dateStart` ;end
CREATE TABLE `ibPYPUnitWorkingClass` (`ibPYPUnitWorkingClassID` INT( 14 ) UNSIGNED ZEROFILL NOT NULL AUTO_INCREMENT PRIMARY KEY ,`ibPYPUnitWorkingID` INT( 12 ) UNSIGNED ZEROFILL NOT NULL ,`gibbonCourseClassID` INT( 8 ) UNSIGNED ZEROFILL NOT NULL) ENGINE = MYISAM ;end
ALTER TABLE `ibPYPUnitWorking` DROP `dateStart`  ;end
ALTER TABLE `ibPYPUnitWorkingClass` ADD `dateStart` DATE NOT NULL ;end
INSERT INTO `gibbonHook` VALUES(0005, 'IB PYP Unit', 'Unit', 'a:9:{s:9:\"unitTable\";s:16:\"ibPYPUnitWorking\";s:11:\"unitIDField\";s:18:\"ibPYPUnitWorkingID\";s:17:\"unitCourseIDField\";s:14:\"gibbonCourseID\";s:13:\"unitNameField\";s:4:\"name\";s:20:\"unitDescriptionField\";s:5:\"theme\";s:14:\"classLinkTable\";s:21:\"ibPYPUnitWorkingClass\";s:18:\"classLinkJoinField\";s:18:\"ibPYPUnitWorkingID\";s:16:\"classLinkIDField\";s:19:\"gibbonCourseClassID\";s:23:\"classLinkStartDateField\";s:9:\"dateStart\";}');end
" ;

//v0.3.00
$count++ ;
$sql[$count][0]="0.3.00" ;
$sql[$count][1]="
ALTER TABLE  `ibPYPGlossary` CHANGE  `type`  `type` ENUM(  'Attitude',  'Concept',  'Learner Profile',  'Transdisciplinary Skill' ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL;end
ALTER TABLE  `ibPYPUnitMasterBlock` ADD  `gibbonOutcomeID` INT( 8 ) UNSIGNED ZEROFILL NULL DEFAULT NULL AFTER  `ibPYPGlossaryID`;end
ALTER TABLE  `ibPYPUnitMasterBlock` CHANGE  `ibPYPGlossaryID`  `ibPYPGlossaryID` INT( 6 ) UNSIGNED ZEROFILL NULL DEFAULT NULL;end
ALTER TABLE  `ibPYPUnitWorkingBlock` ADD  `gibbonOutcomeID` INT( 8 ) UNSIGNED ZEROFILL NULL DEFAULT NULL AFTER  `ibPYPGlossaryID`;end
ALTER TABLE  `ibPYPUnitWorkingBlock` CHANGE  `ibPYPGlossaryID`  `ibPYPGlossaryID` INT( 6 ) UNSIGNED ZEROFILL NULL DEFAULT NULL;end
" ;

//v0.3.01
$count++ ;
$sql[$count][0]="0.3.01" ;
$sql[$count][1]="
" ;

//v1.0.00
$count++ ;
$sql[$count][0]="1.0.00" ;
$sql[$count][1]="
CREATE TABLE `ibPYPUnitMasterSmartBlock` (  `ibPYPUnitMasterSmartBlockID` int(12) unsigned zerofill NOT NULL AUTO_INCREMENT,  `ibPYPUnitMasterID` int(10) unsigned zerofill NOT NULL,  `title` varchar(100) NOT NULL,  `type` varchar(50) NOT NULL,  `length` varchar(3) NOT NULL,  `contents` text NOT NULL,  `teachersNotes` text NOT NULL,  `sequenceNumber` int(4) NOT NULL,  PRIMARY KEY (`ibPYPUnitMasterSmartBlockID`)) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;end
CREATE TABLE `ibPYPUnitWorkingSmartBlock` (  `ibPYPUnitWorkingSmartBlockID` int(14) unsigned zerofill NOT NULL AUTO_INCREMENT,  `ibPYPUnitWorkingID` int(12) unsigned zerofill NOT NULL,  `gibbonPlannerEntryID` int(14) unsigned zerofill DEFAULT NULL,  `ibPYPUnitMasterSmartBlockID` int(12) unsigned zerofill DEFAULT NULL,  `title` varchar(100) NOT NULL,  `type` varchar(50) NOT NULL,  `length` varchar(3) NOT NULL,  `contents` text NOT NULL,  `teachersNotes` text NOT NULL,  `sequenceNumber` int(4) NOT NULL,  `complete` enum('N','Y') NOT NULL DEFAULT 'N',  PRIMARY KEY (`ibPYPUnitWorkingSmartBlockID`)) ENGINE=MyISAM  DEFAULT CHARSET=utf8;end
UPDATE gibbonHook SET name='IB PYP', options='a:30:{s:9:\"unitTable\";s:15:\"ibPYPUnitMaster\";s:11:\"unitIDField\";s:17:\"ibPYPUnitMasterID\";s:17:\"unitCourseIDField\";s:14:\"gibbonCourseID\";s:13:\"unitNameField\";s:4:\"name\";s:20:\"unitDescriptionField\";s:5:\"theme\";s:19:\"unitSmartBlockTable\";s:25:\"ibPYPUnitMasterSmartBlock\";s:21:\"unitSmartBlockIDField\";s:27:\"ibPYPUnitMasterSmartBlockID\";s:23:\"unitSmartBlockJoinField\";s:17:\"ibPYPUnitMasterID\";s:24:\"unitSmartBlockTitleField\";s:5:\"title\";s:23:\"unitSmartBlockTypeField\";s:4:\"type\";s:25:\"unitSmartBlockLengthField\";s:6:\"length\";s:27:\"unitSmartBlockContentsField\";s:8:\"contents\";s:32:\"unitSmartBlockTeachersNotesField\";s:13:\"teachersNotes\";s:33:\"unitSmartBlockSequenceNumberField\";s:14:\"sequenceNumber\";s:14:\"classLinkTable\";s:16:\"ibPYPUnitWorking\";s:16:\"classLinkIDField\";s:18:\"ibPYPUnitWorkingID\";s:22:\"classLinkJoinFieldUnit\";s:17:\"ibPYPUnitMasterID\";s:23:\"classLinkJoinFieldClass\";s:19:\"gibbonCourseClassID\";s:20:\"classSmartBlockTable\";s:26:\"ibPYPUnitWorkingSmartBlock\";s:22:\"classSmartBlockIDField\";s:28:\"ibPYPUnitWorkingSmartBlockID\";s:24:\"classSmartBlockJoinField\";s:18:\"ibPYPUnitWorkingID\";s:26:\"classSmartBlockPlannerJoin\";s:20:\"gibbonPlannerEntryID\";s:33:\"classSmartBlockUnitBlockJoinField\";s:27:\"ibPYPUnitMasterSmartBlockID\";s:25:\"classSmartBlockTitleField\";s:5:\"title\";s:24:\"classSmartBlockTypeField\";s:4:\"type\";s:26:\"classSmartBlockLengthField\";s:6:\"length\";s:28:\"classSmartBlockContentsField\";s:8:\"contents\";s:33:\"classSmartBlockTeachersNotesField\";s:13:\"teachersNotes\";s:34:\"classSmartBlockSequenceNumberField\";s:14:\"sequenceNumber\";s:28:\"classSmartBlockCompleteField\";s:8:\"complete\";}' WHERE name='IB PYP Unit' AND type='Unit';end
ALTER TABLE ibPYPUnitWorkingClass DROP COLUMN dateStart;end
ALTER TABLE `ibPYPUnitWorking` ADD `gibbonCourseClassID` INT( 8 ) UNSIGNED ZEROFILL NOT NULL AFTER `ibPYPUnitMasterID` ;end
DROP TABLE `ibPYPUnitWorkingClass` ;end
ALTER TABLE ibPYPUnitMaster DROP COLUMN gibbonYearGroupIDList;end
ALTER TABLE `ibPYPUnitMaster` ADD `gibbonCourseID` INT( 8 ) UNSIGNED ZEROFILL NOT NULL AFTER `ibPYPUnitMasterID` ;end
ALTER TABLE `ibPYPUnitMaster` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci ;end
ALTER TABLE `ibPYPUnitWorking` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci ;end
ALTER TABLE `ibPYPUnitMaster` CHANGE `name` `name` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL, CHANGE `active` `active` ENUM('Y','N') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'Y', CHANGE `theme` `theme` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL, CHANGE `centralIdea` `centralIdea` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL, CHANGE `summativeAssessment` `summativeAssessment` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL, CHANGE `linesOfInquiry` `linesOfInquiry` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL, CHANGE `teacherQuestions` `teacherQuestions` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL, CHANGE `provocation` `provocation` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL, CHANGE `preAssessment` `preAssessment` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL, CHANGE `formativeAssessment` `formativeAssessment` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL, CHANGE `learningExperiences` `learningExperiences` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL, CHANGE `resources` `resources` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL, CHANGE `action` `action` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL, CHANGE `environments` `environments` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;end
ALTER TABLE `ibPYPUnitWorking` CHANGE `name` `name` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL, CHANGE `gibbonYearGroupIDList` `gibbonYearGroupIDList` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL, CHANGE `theme` `theme` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL, CHANGE `centralIdea` `centralIdea` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL, CHANGE `summativeAssessment` `summativeAssessment` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL, CHANGE `linesOfInquiry` `linesOfInquiry` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL, CHANGE `teacherQuestions` `teacherQuestions` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL, CHANGE `provocation` `provocation` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL, CHANGE `preAssessment` `preAssessment` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL, CHANGE `formativeAssessment` `formativeAssessment` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL, CHANGE `learningExperiences` `learningExperiences` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL, CHANGE `resources` `resources` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL, CHANGE `action` `action` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL, CHANGE `environments` `environments` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL, CHANGE `assessOutcomes` `assessOutcomes` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL, CHANGE `assessmentImprovements` `assessmentImprovements` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL, CHANGE `ideasThemes` `ideasThemes` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL, CHANGE `learningExperiencesConcepts` `learningExperiencesConcepts` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL, CHANGE `learningExperiencesTransSkills` `learningExperiencesTransSkills` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL, CHANGE `learningExperiencesProfileAttitudes` `learningExperiencesProfileAttitudes` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL, CHANGE `inquiriesQuestions` `inquiriesQuestions` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL, CHANGE `questionsProvocations` `questionsProvocations` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL, CHANGE `studentInitAction` `studentInitAction` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL, CHANGE `teachersNotes` `teachersNotes` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;end
ALTER TABLE ibPYPUnitWorking DROP COLUMN gibbonYearGroupIDList;end
ALTER TABLE ibPYPUnitWorking DROP COLUMN gibbonSchoolYearID;end
" ;

//v1.0.01
$count++ ;
$sql[$count][0]="1.0.01" ;
$sql[$count][1]="
ALTER TABLE `ibPYPUnitMaster` ADD `relatedConcepts` TEXT NULL AFTER `summativeAssessment` ;end
ALTER TABLE `ibPYPUnitWorking` ADD `relatedConcepts` TEXT NULL AFTER `summativeAssessment` ;end
" ;

//v1.0.02
$count++ ;
$sql[$count][0]="1.0.02" ;
$sql[$count][1]="
" ;

//v1.0.03
$count++ ;
$sql[$count][0]="1.0.03" ;
$sql[$count][1]="
ALTER TABLE ibPYPUnitMaster DROP COLUMN learningExperiences;end
ALTER TABLE ibPYPUnitWorking DROP COLUMN learningExperiences;end
" ;

//v1.0.04
$count++ ;
$sql[$count][0]="1.0.04" ;
$sql[$count][1]="
" ;

//v1.1.00
$count++ ;
$sql[$count][0]="1.1.00" ;
$sql[$count][1]="
ALTER TABLE `ibPYPUnitMaster` ADD `assessOutcomes` TEXT NOT NULL AFTER `environments` ,ADD `assessmentImprovements` TEXT NOT NULL AFTER `assessOutcomes` ,ADD `ideasThemes` TEXT NOT NULL AFTER `assessmentImprovements` ,ADD `learningExperiencesConcepts` TEXT NOT NULL AFTER `ideasThemes` ,ADD `learningExperiencesTransSkills` TEXT NOT NULL AFTER `learningExperiencesConcepts` ,ADD `learningExperiencesProfileAttitudes` TEXT NOT NULL AFTER `learningExperiencesTransSkills` ,ADD `inquiriesQuestions` TEXT NOT NULL AFTER `learningExperiencesProfileAttitudes` ,ADD `questionsProvocations` TEXT NOT NULL AFTER `inquiriesQuestions` ,ADD `studentInitAction` TEXT NOT NULL AFTER `questionsProvocations` ,ADD `teachersNotes` TEXT NOT NULL AFTER `studentInitAction` ;end
" ;

//v1.2.00
$count++ ;
$sql[$count][0]="1.2.00" ;
$sql[$count][1]="
INSERT INTO `gibbonSetting` (`gibbonSystemSettingsID` ,`scope` ,`name` ,`nameDisplay` ,`description` ,`value`) VALUES (NULL , 'IB PYP', 'defaultRubric', 'Default Rubric', 'This is the default rubric associated with al new working units.', '');end
ALTER TABLE `ibPYPUnitWorking` ADD `dateStart` DATE NULL DEFAULT NULL AFTER `gibbonCourseID` , ADD `gibbonRubricID` INT( 8 ) UNSIGNED ZEROFILL NULL DEFAULT NULL AFTER `dateStart` ;end
INSERT INTO `gibbonAction` (`gibbonActionID`, `gibbonModuleID`, `name`, `precedence`, `category`, `description`, `URLList`, `entryURL`, `entrySidebar`, `defaultPermissionAdmin`, `defaultPermissionTeacher`, `defaultPermissionStudent`, `defaultPermissionParent`, `defaultPermissionSupport`, `categoryPermissionStaff`, `categoryPermissionStudent`, `categoryPermissionParent`, `categoryPermissionOther`) VALUES (NULL, (SELECT gibbonModuleID FROM gibbonModule WHERE name='IB PYP'), 'Manage Settings', 1, 'Admin', 'Manage settings to control the behaviour of the module.', 'settings_manage.php', 'settings_manage.php', 'Y', 'Y', 'N', 'N', 'N', 'N', 'Y', 'N', 'N', 'N');end
INSERT INTO `gibbonPermission` (`permissionID` ,`gibbonRoleID` ,`gibbonActionID`) VALUES (NULL , '1', (SELECT gibbonActionID FROM gibbonAction JOIN gibbonModule ON (gibbonAction.gibbonModuleID=gibbonModule.gibbonModuleID) WHERE gibbonModule.name='IB PYP' AND gibbonAction.name='Manage Settings'));end
" ;

//v1.2.01
$count++ ;
$sql[$count][0]="1.2.01" ;
$sql[$count][1]="
" ;

//v1.2.02
$count++ ;
$sql[$count][0]="1.2.02" ;
$sql[$count][1]="
" ;

//v1.2.03
$count++ ;
$sql[$count][0]="1.2.03" ;
$sql[$count][1]="
" ;

//v1.2.04
$count++ ;
$sql[$count][0]="1.2.04" ;
$sql[$count][1]="
" ;

//v1.2.05
$count++ ;
$sql[$count][0]="1.2.05" ;
$sql[$count][1]="
" ;

//v1.2.06
$count++ ;
$sql[$count][0]="1.2.06" ;
$sql[$count][1]="
" ;
?>
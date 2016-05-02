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

if (isActionAccessible($guid, $connection2, '/modules/IB PYP/glossary.php') == false) {
    //Acess denied
    echo "<div class='error'>";
    echo 'You do not have access to this action.';
    echo '</div>';
} else {
    echo "<div class='trail'>";
    echo "<div class='trailHead'><a href='".$_SESSION[$guid]['absoluteURL']."'>Home</a> > <a href='".$_SESSION[$guid]['absoluteURL'].'/index.php?q=/modules/'.getModuleName($_GET['q']).'/'.getModuleEntry($_GET['q'], $connection2, $guid)."'>".getModuleName($_GET['q'])."</a> > </div><div class='trailEnd'>Essential Elements</div>";
    echo '</div>';

    if (isset($_GET['return'])) {
        returnProcess($guid, $_GET['return'], null, null);
    }

    $role = getRole($_SESSION[$guid]['gibbonPersonID'], $connection2);

    echo '<p>';
    if ($role == 'Coordinator') {
        echo "Essential Elements allows users to view and edit the school's stock of concepts, transdisciplinary skills, learner profiles and attitudes.";
    } else {
        echo "Essential Elements allows users to view the school's stock of concepts, transdisciplinary skills, learner profiles and attitudes.";
    }
    echo '</p>';

    if ($role != '') {
        try {
            $data = array();
            $sql = 'SELECT * FROM ibPYPGlossary ORDER BY type, category, title';
            $result = $connection2->prepare($sql);
            $result->execute($data);
        } catch (PDOException $e) {
            echo "<div class='error'>".$e->getMessage().'</div>';
        }

        if ($role == 'Coordinator') {
            echo "<div class='linkTop'>";
            echo "<a href='".$_SESSION[$guid]['absoluteURL']."/index.php?q=/modules/IB PYP/glossary_add.php'><img title='New' src='./themes/".$_SESSION[$guid]['gibbonThemeName']."/img/page_new.png'/></a>";
            echo '</div>';
        }
        if ($result->rowCount() < 1) {
            echo "<div class='error'>";
            echo 'There are no glossary terms to display.';
            echo '</div>';
        } else {
            echo "<table cellspacing='0' style='width: 100%'>";
            echo "<tr class='head'>";
            echo '<th>';
            echo 'Type/Category';
            echo '</th>';
            echo '<th>';
            echo 'Title';
            echo '</th>';
            echo '<th>';
            echo 'Content';
            echo '</th>';
            if ($role == 'Coordinator') {
                echo "<th style='width: 75px'>";
                echo 'Actions';
                echo '</th>';
            }
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
                echo '<b>'.$row['type'].'</b><br/>';
                echo '<i>'.$row['category'].'</i>';
                echo '</td>';
                echo '<td>';
                echo $row['title'];
                echo '</td>';
                echo '<td>';
                echo $row['content'];
                echo '</td>';
                if ($role == 'Coordinator') {
                    echo '<td>';
                    echo "<a href='".$_SESSION[$guid]['absoluteURL'].'/index.php?q=/modules/IB PYP/glossary_edit.php&ibPYPGlossaryID='.$row['ibPYPGlossaryID']."'><img title='Edit' src='./themes/".$_SESSION[$guid]['gibbonThemeName']."/img/config.png'/></a> ";
                    echo "<a href='".$_SESSION[$guid]['absoluteURL'].'/index.php?q=/modules/IB PYP/glossary_delete.php&ibPYPGlossaryID='.$row['ibPYPGlossaryID']."'><img title='Delete' src='./themes/".$_SESSION[$guid]['gibbonThemeName']."/img/garbage.png'/></a> ";
                    echo '</td>';
                }
                echo '</tr>';
            }
            echo '</table>';
        }
    } else {
        echo "<div class='error'>";
        echo 'You do not have access to this action.';
        echo '</div>';
    }
}

<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<?php
session_start();
require __DIR__ . '/vendor/autoload.php';

use Kreait\Firebase\Factory;
use Kreait\Firebase\ServiceAccount;
?>
<html>
    <script>
        if (window.history.replaceState) {
            window.history.replaceState(null, null, window.location.href);
        }
    </script>
    <head>
        <meta charset="UTF-8">
        <title></title>
    </head>
    <body>
        <h4>Mission Setting</h4><a href="TreasureHuntManagement.php">Go Back</a>
        <form action="MissionSetting.php" method="POST">
            <table>               
                <tbody>
                    <tr>
                        <td><h4>Set Mission</h4></td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>Mission Type</td>
                        <td>:</td>
                        <td><select name="missionType" onchange="this.form.submit()">
                                <option value="" selected disabled>--select--</option>
                                <option value="01">Shake</option>
                                <option value="02">Tap</option>
                                <option value="03">ScanQRcode</option>
                                <option value="04">Wefie</option>
                            </select></td>
                    </tr>                   
                </tbody>
            </table>
        </form>     
        <?php
        $missiontext = "";
        if (isset($_POST['missionType'])) {
            $missiontype = $_POST['missionType'];
            if ($missiontype == '01') {
                $missiontext = "Shaking";
            } else if ($missiontype == '02') {
                $missiontext = "Tapping";
            } else if ($missiontype == '03') {
                $missiontext = "Scan QR code";
            } else if ($missiontype == '04') {
                $missiontext = "Wefie";
            }
            $_SESSION['missiontype'] = $missiontext;
            ?>
            <form action="MissionSetting.php" method="POST">
                <?php
                if ($missiontype != '03') {
                    echo "<table style='border:1px solid black;'>";
                    echo ' <tbody>';
                    echo ' <tr>';
                    echo '    <td>Mission</td>';
                    echo '    <td>:</td>';
                    echo "<td><label>" . $missiontext . "</label></td>";
                    echo ' </tr>';
                    echo ' <tr>';
                    echo '<td>Mission value</td>';
                    echo '<td>:</td>';
                    echo '<td><input type="text" name="missionvalue" value="" placeholder="eg. 50 for shaking 50 times"/></td>';
                    echo ' </tr>';
                    echo ' </tbody>';
                    echo '</table>';
                } else {
                    echo "<table style='border:1px solid black;'>";
                    echo ' <tbody>';
                    echo ' <tr>';
                    echo '    <td>Mission</td>';
                    echo '    <td>:</td>';
                    echo "<td><label>" . $missiontext . "</label></td>";
                    echo ' </tr>';
                    echo '<tr>';
                    echo '<td>Mission value</td>';
                    echo '<td>:</td>';
                    echo '<td>You need to generate a QR code</td>';
                    echo '</tr>';
                    echo '<tr><td></td><td></td><td>';
                    echo "<a href='QRcodeGenerator.php'>here to generate QR code</a>";
                    echo '</td></tr>';
                    echo ' </tbody>';
                    echo '</table>';
                }
            }
            ?>  

            <input type="submit" value="confirm" name="btnconfirm" />
        </form>

        <?php
        if (isset($_POST['btnconfirm'])) {
            $serviceAccount = ServiceAccount::fromJsonFile(__DIR__ . '/serviceAccountKey.json');
            $firebase = (new Factory)
                    ->withServiceAccount($serviceAccount)
                    ->withDatabaseUri('https://testing-1f32f.firebaseio.com')
                    ->create();
            $database = $firebase->getDatabase();

            $newIndex = 1;

            $existed_index = $database->getReference('Missions')->getChildKeys();
            for ($y = 0; $y < sizeof($existed_index); $y += 1) {
                for ($index = 0; $index < sizeof($existed_index); $index += 1) {
                    $tempindex = explode("_", $existed_index[$index]);

                    if ($tempindex[1] == $newIndex) {
                        $newIndex += 1;
                    }
                }
            }
       

            if ($_POST['missionvalue'] !== null) {
                $mission = $database->getReference("Missions")->getChild("Mission_" . $newIndex)->set([
                    "mission_Type" => $_SESSION['missiontype'],
                    "mission_Value" => $_POST['missionvalue'],
                    "event_mission" => ""
                ]);
                echo "<script>alert('Mission added');</script>";
            }
        }
        ?>
        <style>
            .resulttabletr {
                border: 1px solid black;
                border-collapse: collapse;                   
            }
            .resulttabletd {
                border: 1px solid black;
                border-collapse: collapse;
                overflow: hidden;
                text-overflow: ellipsis;
                white-space: nowrap;
            }
        </style>
        <table style=" border: 1px solid black;
               border-collapse: collapse;table-layout: fixed;width: 750px;word-break: break-all;">
            <thead>
                <tr class="resulttabletr">
                    <th class="resulttabletd" width="100px">Index</th>
                    <th class="resulttabletd" width="150px">Mission Type</th>
                    <th class="resulttabletd" width="150px">Mission Value</th>
                    <th class="resulttabletd" width="200px">Event Mission</th>
                    <th class="resulttabletd"></th>
                </tr>
            </thead>
            <tbody>
                <?php
                $serviceAccount = ServiceAccount::fromJsonFile(__DIR__ . '/serviceAccountKey.json');
                $firebase = (new Factory)
                        ->withServiceAccount($serviceAccount)
                        ->withDatabaseUri('https://testing-1f32f.firebaseio.com')
                        ->create();
                $database = $firebase->getDatabase();
                if (isset($_REQUEST['action'])) {
                    if ($_REQUEST['action'] === "delete") {
                        $missionToRemove = $database->getReference("Missions")->getChild($_REQUEST['index']);
                        $missionToRemove->remove();
                        echo "<script>alert('Mission has been deleted.');</script>";
                    }
                }


                $missions = $database->getReference("Missions")->getChildKeys();
                for ($x = 0; $x < sizeof($missions); $x += 1) {
                    $display_index = explode("_", $missions[$x]);
                    $display_mission = $database->getReference("Missions")->getChild($missions[$x])->getSnapshot()->getValue();
                    echo "<tr class='resulttabletr'>";
                    echo "<td class='resulttabletd'>" . $display_index[1] . "</td>";
                    echo "<td class='resulttabletd'>" . $display_mission['mission_Type'] . "</td>";
                    echo "<td class='resulttabletd'>" . $display_mission['mission_Value'] . "</td>";
                    echo "<td class='resulttabletd'>" . $display_mission['event_mission'] . "</td>";
                    echo '<td class="resulttabletd"><a href="MissionSetting.php?action=delete&index=' . $missions[$x] . '" onclick="return confirm(\'Are you sure you want to remove it?\');" >Delete</a></td>';
                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>


    </body>
</html>

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
        <form action="QRcodeGenerator.php" method="POST">            
            <h4>Generate a QR Code</h4>

            <label>Create a QR code that is available on the event time</label>
            <table style="border: 1px solid black">              
                <tbody>
                    <tr>
                        <td>Acessable Time</td>
                        <td>:</td>
                        <td>From <input type="time" name="startTime" value="" size="4" required="required"/> - <input type="time" name="endTime" value="" size="4" required="required"/></td>
                    </tr>
                    <tr>
                        <td>Accessable Date</td>
                        <td>:</td>
                        <td><input type="text" name="startyear" value="" size="1" maxlength="4" placeholder="2018" pattern="[0-9]{4}" oninvalid="this.setCustomValidity('Please enter numeric.')" required="required"/>/
                            <input type="text" name="startmonth" value="" size="1" maxlength="2" placeholder="01" pattern="[0-9]{2}" oninvalid="this.setCustomValidity('Please enter numeric.')" required="required"/>/
                            <input type="text" name="startday" value="" size="1" maxlength="2" placeholder="01" pattern="[0-9]{2}" oninvalid="this.setCustomValidity('Please enter numeric.')" required="required"/>-
                            <input type="text" name="endyear" value="" size="1" maxlength="4" placeholder="2018" pattern="[0-9]{4}" oninvalid="this.setCustomValidity('Please enter numeric.')" required="required"/>/
                            <input type="text" name="endmonth" value="" size="1" maxlength="2" placeholder="01" pattern="[0-9]{2}" oninvalid="this.setCustomValidity('Please enter numeric.')" required="required"/>/
                            <input type="text" name="endday" value="" size="1" maxlength="2" placeholder="01" pattern="[0-9]{2}" oninvalid="this.setCustomValidity('Please enter numeric.')" required="required"/>
                        </td>
                    </tr>
                    <tr>
                        <td><a href="MissionSetting.php">Go Back</a></td>
                        <td></td>
                        <td><input type="submit" value="generate" name="btngenerate"/></td>
                    </tr>
                </tbody>
            </table>




        </form>
        <?php
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['btngenerate'])) {


            $validDate = false;
            $validmonth = false;

            if ($_POST['startmonth'] > 0 && $_POST['startmonth'] <= 12 && $_POST['endmonth'] > 0 && $_POST['endmonth'] <= 12 && $_POST['startmonth'] >= date("m")) {
                $validmonth = TRUE;
                $startdayinmonth = cal_days_in_month(CAL_GREGORIAN, $_POST['startmonth'], $_POST['startyear']);
                $enddayinmonth = cal_days_in_month(CAL_GREGORIAN, $_POST['endmonth'], $_POST['endyear']);
            }
            if ($_POST['startyear'] >= date("Y") && $_POST['endyear'] >= date("Y") && $_POST['endyear'] == $_POST['startyear']) {
                if ($validmonth == TRUE && $_POST['startmonth'] == $_POST['endmonth']) {
                    if ($_POST['startday'] > 0 && $_POST['startday'] <= $startdayinmonth && $_POST['endday'] > 0 && $_POST['endday'] <= $enddayinmonth && $_POST['endday'] >= $_POST['startday']) {
                        $validDate = TRUE;
                    }
                } else if ($validmonth == TRUE && $_POST['startmonth'] < $_POST['endmonth']) {
                    if ($_POST['startday'] > 0 && $_POST['startday'] <= $startdayinmonth && $_POST['endday'] > 0 && $_POST['endday'] <= $enddayinmonth) {
                        $validDate = TRUE;
                    }
                }
            } else if ($_POST['startyear'] >= date("Y") && $_POST['endyear'] >= date("Y") && $_POST['endyear'] >= $_POST['startyear']) {
                if ($validmonth == TRUE) {
                    if ($_POST['startday'] > 0 && $_POST['startday'] <= $startdayinmonth && $_POST['endday'] > 0 && $_POST['endday'] <= $enddayinmonth) {
                        $validDate = TRUE;
                    }
                }
            }
            if ($validDate === true) {
                $codeID = mt_rand(100000, 999999);
                $codeString = "{'access_time':'" . $_POST['startTime'] . "-" . $_POST['endTime'] . "','access_date':'" . $_POST['startday'] . "/" . $_POST['startmonth'] . "/" . $_POST['startyear'] . "-" . $_POST['endday'] . "/" . $_POST['endmonth'] . "/" . $_POST['endyear'] . "'";
                $codeString = $codeString . ",'ID':'" . (string) $codeID . "'}";
                echo '   <img src="writeQRcode.php?text=' . $codeString . '"/>';
                echo '<br />';
                echo 'saved in QRcode folder';
                $_SERVER['QRcodeID'] = $codeID;
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

                $mission = $database->getReference("Missions")->getChild("Mission_" . $newIndex)->set([
                    "mission_Type" => $_SESSION['missiontype'],
                    "mission_Value" => $_POST['startTime'] . "-" . $_POST['endTime'],
                    "event_mission" => $_POST['startday'] . "/" . $_POST['startmonth'] . "/" . $_POST['startyear'] . "-" . $_POST['endday'] . "/" . $_POST['endmonth'] . "/" . $_POST['endyear'],
                    "QR_ID"=>$codeID
                ]);
            } else {
                echo 'invalid date';
            }
        }
        ?>



    </body>
</html>

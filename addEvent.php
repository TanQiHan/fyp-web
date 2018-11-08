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
    if ( window.history.replaceState ) {
        window.history.replaceState( null, null, window.location.href );
    }
</script>
    <head>
        <meta charset="UTF-8">
        <title></title>
    </head>
    <body>
        <form action="addEvent.php" method="POST">
            <h4>Add New Event</h4>
            <table style="border: 1px solid black">
                <thead>
                    <tr>
                        <th>Event Details</th>
                        <th></th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Event Name</td>
                        <td>:</td>
                        <td><input type="text" name="event_name" value="" required="required" /></td>
                    </tr>
                    <tr>
                        <td>Event Date</td>
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
                        <td>Event Duration Time</td>
                        <td>:</td>
                        <td>From <input type="time" name="startTime" value="" size="4" required="required"/> - <input type="time" name="endTime" value="" size="4" required="required"/></td>
                    </tr>
                    <tr>
                        <td>Location</td>
                        <td>:</td>
                        <td><input type="text" name="event_location" value="" required="required"/></td>
                    </tr>
                    <tr>
                        <td>Description</td>
                        <td>:</td>
                        <td></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td></td>
                        <td><textarea name="event_description" rows="6" cols="50"></textarea></td>
                    </tr>
                    <tr>
                        <td>Event handler</td>
                        <td>:</td>
                        <td></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td></td>
                        <td><table style="border: 1px solid black">                               
                                <tbody>
                                    <tr>
                                        <td>Name</td>
                                        <td>:</td>
                                        <td><input type="text" name="handler_name" value="" required="required" /></td>
                                    </tr>
                                    <tr>
                                        <td>Contact No.</td>
                                        <td>:</td>
                                        <td><input type="text" name="handler_contactNo" value="" required="required" pattern="[0-9]{2,3}-[0-9]{6,8}" title="eg. 012-1234567,03-12345678"/></td>
                                    </tr>
                                    <tr>
                                        <td>Email</td>
                                        <td>:</td>
                                        <td><input type="email" name="handler_email" value="" required="required"/></td>
                                    </tr>                                    
                                </tbody>
                            </table></td>
                    </tr>
                    <tr>
                        <td><a href="TreasureHuntManagement.php">Go Back</a></td>
                        <td></td>
                        <td style="text-align: right"><input type="submit" value="Add" name="btnAdd" /></td>
                    </tr>
                </tbody>
            </table>

        </form>
        <?php
        $serviceAccount = ServiceAccount::fromJsonFile(__DIR__ . '/serviceAccountKey.json');
        $firebase = (new Factory)
                ->withServiceAccount($serviceAccount)
                ->withDatabaseUri('https://testing-1f32f.firebaseio.com')
                ->create();
        $database = $firebase->getDatabase();

        if ($_SERVER['REQUEST_METHOD'] == "POST" and isset($_POST['btnAdd'])) {
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
                    if ($_POST['startday'] > 0 && $_POST['startday'] <= $startdayinmonth && $_POST['endday'] > 0 && $_POST['endday'] <= $enddayinmonth ) {
                        $validDate = TRUE;
                    }
                }
            }

            if ($validDate == TRUE) {
                $event_name = $_POST['event_name'];
                $event_location = $_POST['event_location'];
                if (isset($_POST['event_description'])) {
                    $event_description = htmlspecialchars($_POST['event_description']);
                } else
                    $event_description = "";

                $handler_name = $_POST['handler_name'];
                $handler_contactNo = $_POST['handler_contactNo'];
                $handler_email = $_POST['handler_email'];
                $startTime = htmlspecialchars($_POST['startTime']);
                $endTime = $_POST['endTime'];
                $startyear = $_POST['startyear'];
                $startmonth = $_POST['startmonth'];
                $startday = $_POST['startday'];
                $endyear = $_POST['endyear'];
                $endmonth = $_POST['endmonth'];
                $endday = $_POST['endday'];

                $newEvent = $database->getReference('Events')->getChild($event_name)->set([
                    'event_location' => $event_location,
                    'event_time' => $startTime . "-" . $endTime,
                    'event_date' => $startday . "/" . $startmonth . "/" . $startyear . "-" . $endday . "/" . $endmonth . "/" . $endyear,
                    'event_description' => $event_description,
                    'handler_name' => $handler_name,
                    'handler_contactNo' => $handler_contactNo,
                    'handler_email' => $handler_email
                ]);
                echo "<script>alert('New event is added');window.location.href='TreasureHuntManagement.php';</script>";
            }else {
                echo "<script type='text/javascript'>alert('Event date is invalid')</script>";
            }
        }
        ?>
    </body>
</html>

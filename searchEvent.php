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
        <form action="searchEvent.php" method="POST">
            <h4>Event Searching</h4><a href="TreasureHuntManagement.php">Go Back</a>
            <table style="border: 1px solid black">
                <thead>
                    <tr>
                        <th>Search</th>
                        <th>:</th>
                        <th><input type="text" name="search_eventName" value="" size="40"  placeholder="Search by Event Name(CASE SENTITIVE)"/></th>
                        <th><input type="submit" value="Search" name="btnsearch" /></th>
                    </tr>
                </thead>
                <tbody>                                    

                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                </tbody>
            </table>
            <br />
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
                   border-collapse: collapse;table-layout: fixed;width: 1300px;word-break: break-all;">
                <thead>
                    <tr class="resulttabletr">
                        <th class="resulttabletd" width="100px">Event Name</th>
                        <th class="resulttabletd" width="240px">Description</th>
                        <th class="resulttabletd" width="100px">Time</th>
                        <th class="resulttabletd" width="155px">Date</th>
                        <th class="resulttabletd" width="130px">Location</th>
                        <th class="resulttabletd" width="130px">Handler Name</th>
                        <th class="resulttabletd" width="160px">Hancler Contact No.</th>
                        <th class="resulttabletd" width="170px">Handler Email</th>
                        <th></th><th></th>
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
                    $result = null;
                    $somethingInside = $database->getReference('Events')->getSnapshot()->exists();
                    if ($somethingInside === false) {
                        echo 'No event store in the server.';
                    } else {
                        $eventlist = $database->getReference('Events')->getChildKeys();
                        $count = 0;
                        if ($_SERVER['REQUEST_METHOD'] == "POST" and isset($_POST['btnsearch'])) {

                            if ($_POST['search_eventName'] !== "") {
                                $searchEvent = $_POST['search_eventName'];
                                for ($i = 0; $i < sizeof($eventlist); $i += 1) {
                                    if (stripos($eventlist[$i], $searchEvent) !== false) {
                                        $result[$count] = $eventlist[$i];
                                        $_SESSION['result'][] = $result[$count];
                                        $count += 1;
                                    }
                                }
                                if ($result === null) {
                                    echo 'No record found';
                                } else {


                                    for ($a = 0; $a < sizeof($result); $a += 1) {
                                        echo '<tr class="resulttabletr">';
                                        $getEventDetail = $database->getReference('Events')->getChild($result[$a])->getSnapshot()->getValue();
                                        echo '<td class="resulttabletd">' . $result[$a] . '</td>';
                                        echo '<td class="resulttabletd">' . $getEventDetail['event_description'] . '</td>';
                                        echo '<td class="resulttabletd">' . $getEventDetail['event_time'] . '</td>';
                                        echo '<td class="resulttabletd">' . $getEventDetail['event_date'] . '</td>';
                                        echo '<td class="resulttabletd">' . $getEventDetail['event_location'] . '</td>';
                                        echo '<td class="resulttabletd">' . $getEventDetail['handler_name'] . '</td>';
                                        echo '<td class="resulttabletd">' . $getEventDetail['handler_contactNo'] . '</td>';
                                        echo '<td class="resulttabletd">' . $getEventDetail['handler_email'] . '</td>';
                                        echo '<td class="resulttabletd"><a href="searchEvent.php?action=edit&index=' . $a . '" onclick="return confirm(\'Are you sure you want to edit it?\');" >Edit</a></td>';
                                        echo '<td class="resulttabletd"><a href="searchEvent.php?action=delete&index=' . $a . '" onclick="return confirm(\'Are you sure you want to delete it?\');"  >Delete</a></td>';
                                        echo '</tr>';
                                    }
                                }
                            }
                        }
                    }
                    if (isset($_REQUEST['action'])) {
                        if ($_REQUEST['action'] == "delete") {
                            $eventToDelete = $database->getReference('Events')->getChild($_SESSION['result'][$_REQUEST['index']])->remove();
                            echo 'The particular Event has been deleted.';
                        } elseif ($_REQUEST['action'] == 'edit') {
                            $eventToEdit = $database->getReference('Events')->getChild($_SESSION['result'][$_REQUEST['index']])->getSnapshot()->getValue();
//                                                 
                            $_SESSION['event'] = $eventToEdit;
                            $_SESSION['event']['event_name'] = $_SESSION['result'][$_REQUEST['index']];
                            header('location:EditEventPage.php');
                            flush();
                        }
                    }
                    ?>    

                </tbody>
            </table>

        </form>

    </body>
</html>

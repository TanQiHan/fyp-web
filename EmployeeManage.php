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
        <form action="EmployeeManage.php" method="POST">
            <h4>Employee Role Management</h4><a href="TreasureHuntManagement.php">Go Back</a>      
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
                   border-collapse: collapse;table-layout: fixed;width: 800px;word-break: break-all;"> 
                <thead>
                    <tr class="resulttabletr">
                        <th class="resulttabletd" width="100px">ID</th>
                        <th class="resulttabletd" width="240px">Name</th>
                        <th class="resulttabletd" width="160px">NDIC</th>
                        <th class="resulttabletd" width="155px">role</th>                      
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
                        if ($_REQUEST['action'] === 'promote') {
                            $tempEmp = $database->getReference("Employees")->getChild($_REQUEST['index'])->update([
                                "role" => "admin"
                            ]);
                            echo "<script>alert('Employee is promoted');</script>";
                        } else if ($_REQUEST['action'] === 'demote') {
                            $tempEmp = $database->getReference("Employees")->getChild($_REQUEST['index'])->update([
                                "role" => "employee"
                            ]);
                            echo "<script>alert('Employee is demoted');</script>";
                        } else if ($_REQUEST['action'] === 'remove') {
                            $tempEmp = $database->getReference("Employees")->getChild($_REQUEST['index']);
                            $tempEmp->remove();
                            echo "<script>alert('Employee is removed');</script>";
                        }
                    }


                    $employees_id = $database->getReference("Employees")->getSnapshot()->exists();
                    if ($employees_id === false) {
                        echo 'no records';
                    } else {
                        $employees_id = $database->getReference("Employees")->getChildKeys();
                        for ($z = 0; $z < sizeof($employees_id); $z += 1) {
                            $employee = $database->getReference("Employees")->getChild($employees_id[$z])->getSnapshot()->getValue();
                            echo '<tr class="resulttabletr">';
                            echo ' <td class="resulttabletd">' . $employees_id[$z] . '</td>';
                            echo ' <td class="resulttabletd">' . $employee["employee_name"] . '</td>';
                            echo ' <td class="resulttabletd">' . $employee["NDIC"] . '</td>';
                            echo ' <td class="resulttabletd">' . $employee["role"] . '</td>';
                            if ($employees_id[$z] !== 1800301) {
                                if ($employee["role"] === "employee") {
                                    echo '<td class="resulttabletd"><a href="EmployeeManage.php?action=promote&index=' . $employees_id[$z] . '" onclick="return confirm(\'Are you sure you want to promote it?\');"  >Promote</a>';
                                    echo ' <a href="EmployeeManage.php?action=remove&index=' . $employees_id[$z] . '" onclick="return confirm(\'Are you sure you want to remove it?\');"  >Remove</a></td>';
                                } else {
                                    echo '<td class="resulttabletd"><a href="EmployeeManage.php?action=demote&index=' . $employees_id[$z] . '" onclick="return confirm(\'Are you sure you want to Demote it?\');"  >Demote</a>';
                                    echo ' <a href="EmployeeManage.php?action=remove&index=' . $employees_id[$z] . '" onclick="return confirm(\'Are you sure you want to remove it?\');"  >Remove</a></td>';
                                }
                            } else {
                                echo '<td class="resulttabletd"></td>';
                            }
                            echo ' </tr>';
                        }
                    }
                    ?>

                </tbody>
            </table>

        </form>
        <?php
        ?>
    </body>
</html>

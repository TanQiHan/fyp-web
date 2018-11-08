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
    <head>
        <meta charset="UTF-8">
        <title></title>
    </head>
    <body>
        <form action="TreasureHuntManagement.php" method="POST">
            <h4>Treasure Hunt Management</h4>
            <table style="widows: 50%;text-align: left">               
                <?php
                $serviceAccount = ServiceAccount::fromJsonFile(__DIR__ . '/serviceAccountKey.json');
                $firebase = (new Factory)
                        ->withServiceAccount($serviceAccount)
                        ->withDatabaseUri('https://testing-1f32f.firebaseio.com')
                        ->create();
                $database = $firebase->getDatabase();
                $name = $database->getReference('Employees/' . $_SESSION['username'])->getChild('employee_name')->getSnapshot()->getValue();
                $_SESSION['role'] = $database->getReference("Employees/" . $_SESSION['username'])->getChild("role")->getSnapshot()->getValue();
                ?>                 
                <tbody>                  
                    <tr>
                        <td><?php echo '<p>Hi,' . $name . '</p>'; ?></td>
                        <td><input type="submit" value="log out" name="btnlogout" /></td>
                        <td></td>
                        <?php
                        if ($_SERVER['REQUEST_METHOD'] == "POST" and isset($_POST['btnlogout'])) {
                            session_unset();
                            header('location:Login.php');
                            flush();
                        }
                        ?>
                    </tr>                           
                </tbody>
            </table>
            <table style="border: 1px solid black">              
                <tbody>
                    <?php
                    if ($_SESSION['role'] === "admin") {
                        echo '<tr>';
                        echo ' <td>Employee :</td>';
                        echo ' <td><input type="submit" value="Add Employee" name="btnAddEmployee" /></td>';
                        echo '  <td><input type="submit" value="Employee Manage" name="btnEmployeeManage" /></td>';
                        echo '  </tr>';
                    }
                    ?>  
                    <tr>
                        <td>Event :</td>
                        <td><input type="submit" value="Add Event" name="btnAddEvent"/></td>
                        <td><input type="submit" value="Search Event" name="btnSearchEvent"/></td>
                    </tr>
                    <tr>
                        <td>Mission :</td>
                        <td><input type="submit" value="Mission Setting" name="btnMissionSetting"/></td>
                        <td></td>
                    </tr>
                </tbody>
            </table>

        </form>
        <?php
        if ($_SERVER['REQUEST_METHOD'] == "POST" and isset($_POST['btnAddEvent'])) {

            header('location:addEvent.php');
            flush();
        }
        if ($_SERVER['REQUEST_METHOD'] == "POST" and isset($_POST['btnSearchEvent'])) {

            header('location:searchEvent.php');
            flush();
        }
        if ($_SERVER['REQUEST_METHOD'] == "POST" and isset($_POST['btnAddEmployee'])) {

            header('location:EmployeeRegistration.php');
            flush();
        }
        if ($_SERVER['REQUEST_METHOD'] == "POST" and isset($_POST['btnEmployeeManage'])) {
            header('location:EmployeeManage.php');
            flush();
        }
        if ($_SERVER['REQUEST_METHOD'] == "POST"and isset($_POST['btnMissionSetting'])) {
            header('location:MissionSetting.php');
            fflush();
        }
        ?>
    </body>
</html>

<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<?php
require __DIR__ . '/vendor/autoload.php';

//set_time_limit(0);
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
        <form action="EmployeeRegistration.php" method="POST">
            <table style="border: 1px solid black">
                <thead>
                    <tr>
                        <th><strong>Employee Registration</strong></th>
                        <th></th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Employee ID (known as username)</td>
                        <td>:</td><?php
                        $serviceAccount = ServiceAccount::fromJsonFile(__DIR__ . '/serviceAccountKey.json');
                        $firebase = (new Factory)
                                ->withServiceAccount($serviceAccount)
                                ->withDatabaseUri('https://testing-1f32f.firebaseio.com')
                                ->create();
                        $database = $firebase->getDatabase();
                        $newID = 1800301;
                        $existed_id = $database->getReference('Employees')->getChildKeys();

                        for ($index = 0; $index < sizeof($existed_id); $index += 1) {
                            if ($existed_id[$index] == $newID) {
                                $newID += 1;
                            }
                        }





//                    $password=  my_simple_crypt('123qwe', 'e');
//                        $newPost=$database->getReference('Employees');
//                        $newPost->getChild($newID)->set([
//                            'employee_name'=>'Tan Qi Han','NDIC'=>'961115-14-7049','role'=>'admin','password'=>$password]);



                        echo '<td><input type="text" name="employee_id" value="' . $newID . '" readonly="readonly" disabled="disabled" /></td>';
                        $_SESSION['employee_id'] = $newID;
                        ?>
                    </tr>                 
                    <tr>
                        <td>Name</td>
                        <td>:</td>
                        <td><input type="text" name="employee_name" value="" required="required"/></td>
                    </tr>
                    <tr>
                        <td>NDIC</td>
                        <td>:</td>
                        <td><input type="text" name="NDIC" value="" required="required" pattern="[0-9]{6}-[0-9]{2}-[0-9]{4}" title="eg. 998877-14-1234"/></td>
                    </tr>
                    <tr>
                        <td>Role</td>
                        <td>:</td>
                        <td><input type="text" name="employee_role" value="employee" readonly="readonly" disabled="disabled" /></td>
                    </tr>
                    <tr>
                        <td>Enter your password</td>
                        <td>:</td>
                        <td><input type="password" name="password" value="" required="required" pattern="[a-zA-Z0-9]{6,12}" oninvalid="this.setCustomValidity('any letter and numbers between 6 and 12 characters')"/></td>
                    </tr>
                    <tr>
                        <td>Re-enter your password</td>
                        <td>:</td>
                        <td><input type="password" name="password_reEnter" value="" required="required" /></td>
                    </tr>
                    <tr>
                        <td><a href="TreasureHuntManagement.php">Go Back</a></td>
                        <td></td>
                        <td><input type="submit" value="Register" name="register"/></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>                
                </tbody>
            </table>
        </form>    
        <?php
        if ($_SERVER['REQUEST_METHOD'] == "POST" and isset($_POST['register'])) {
            $same_password = false;

            if ($_POST['password'] == $_POST['password_reEnter']) {
                $same_password = true;
            } else {
                echo 'You must enter same passowrd.';
                $same_password = false;
            }



            if ($same_password == TRUE) {
                $serviceAccount = ServiceAccount::fromJsonFile(__DIR__ . '/serviceAccountKey.json');
                $firebase = (new Factory)
                        ->withServiceAccount($serviceAccount)
                        ->withDatabaseUri('https://testing-1f32f.firebaseio.com')
                        ->create();
                $database = $firebase->getDatabase();

                $encrypted_pass = my_simple_crypt($_POST['password'], 'e');
                $newEmp = $database->getReference('Employees');
                $newEmp->getChild((string) $_SESSION['employee_id'])->set([
                    'employee_name' => $_POST['employee_name'],
                    'NDIC' => $_POST['NDIC'],
                    'role' => 'employee',
                    'password' => $encrypted_pass
                ]);
                header('refresh:0');

                echo "<script>alert('New employee added successful');window.location.href='TreasureHuntManagement.php'</script>";
            }
        }

        function my_simple_crypt($string, $action = 'e') {
            // you may change these values to your own
            $secret_key = 'my_simple_secret_key';
            $secret_iv = 'my_simple_secret_iv';

            $output = false;
            $encrypt_method = "AES-256-CBC";
            $key = hash('sha256', $secret_key);
            $iv = substr(hash('sha256', $secret_iv), 0, 16);

            if ($action == 'e') {
                $output = base64_encode(openssl_encrypt($string, $encrypt_method, $key, 0, $iv));
            } else if ($action == 'd') {
                $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
            }

            return $output;
        }
        ?>
    </body>
</html>

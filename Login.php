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
        <form action="Login.php" method="POST">
            <h2 style="text-align: center">Treasure Hunt Management Login</h2>
            <table style="border: 1px solid black;margin: 0px auto">
                <thead>
                    <tr>
                        <th></th>
                        <th></th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Username</td>
                        <td>:</td>
                        <td><input type="text" name="username" value="" autofocus="" required="required"/></td>
                    </tr>
                    <tr>
                        <td>Password</td>
                        <td>:</td>
                        <td><input type="password" name="password" value="" required="required"/></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td></td>
                        <td><input type="submit" value="Login" name="btnlogin"/></td>
                    </tr>

                </tbody>
            </table>

        </form>
        <?php
        if ($_SERVER['REQUEST_METHOD'] == "POST" and isset($_POST['btnlogin'])) {
            $serviceAccount = ServiceAccount::fromJsonFile(__DIR__ . '/serviceAccountKey.json');
            $firebase = (new Factory)
                    ->withServiceAccount($serviceAccount)
                    ->withDatabaseUri('https://testing-1f32f.firebaseio.com')
                    ->create();
            $database = $firebase->getDatabase();

            $checkEmployee = $database->getReference('Employees')->orderByKey()->equalTo($_POST['username'])->getSnapshot()->getValue();
            if ($checkEmployee == null) {
                echo 'The username you entered does not exist.';
            } else {
                $encrypted_password = $database->getReference('Employees/' . $_POST['username'])->getChild('password')->getSnapshot()->getValue();
                $decrypted_password = my_simple_crypt($encrypted_password, 'd');
                $correctPassword = false;
                
                    if ($_POST['password'] != $decrypted_password) {
                        echo 'Wrong password. Please enter again.';
                        $correctPassword = FALSE;
                    } else {
                        $correctPassword = TRUE;
                    }
               
                if($correctPassword==TRUE){
                  
                    $_SESSION['username']=$_POST['username'];                   
                    header('location:TreasureHuntManagement.php');
                    flush();
                }                
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

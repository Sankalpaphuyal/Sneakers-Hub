<?php
session_start();
require_once '../database.php';
$conn = db_connect();

if(isset($_POST['name']) && isset($_POST['email']) && isset($_POST['password']) && isset($_POST['confirm_password'])){
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $enc_password = hash('sha256', $password);
    $enc_confirm_password = hash('sha256', $confirm_password);

    if($enc_password == $enc_confirm_password){
        $sql = "INSERT INTO users (name, email, password) VALUES ('$name', '$email', '$enc_password')";
        if(mysqli_query($conn, $sql)){
            $_SESSION['success'] = 'You have successfully registered';
            header('Location: ../login.php');
        }else{
            $_SESSION['error'] = 'Something went wrong';
            header('Location: ../register.php');
        }
    }else{
        $_SESSION['error'] = 'The password does not match';
        header('Location: ../register.php');
    }
} else {
    header('Location: ../register.php');
}
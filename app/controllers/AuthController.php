<?php

$username=trim($_POST['username']);
$password=$_POST['password'];

$stmt=$pdo->prepare("
SELECT users.*, roles.role_name
FROM users
JOIN roles
ON users.role_id=roles.role_id
WHERE username=? AND status='Active'
");

$stmt->execute([$username]);

$user=$stmt->fetch(PDO::FETCH_ASSOC);

if($user && password_verify($password,$user['password'])){

    $_SESSION['user']=$user;

    header("Location:index.php");
    exit;
}

$_SESSION['error']="Invalid username or password.";

header("Location:index.php");
exit;

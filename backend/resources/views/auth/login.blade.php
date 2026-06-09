<?php
$isLaravel = true;
$apiLoginUrl = route('login.perform');
$csrfToken = csrf_token();
include base_path('../frontend/public/login.php');
?>

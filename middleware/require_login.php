<?php

require_once __DIR__ . '/../includes/session.php';

if(!isset($_SESSION['user_id'])){
    header("Location: /login.php");
    exit;
}

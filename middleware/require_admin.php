<?php

require_once __DIR__ . '/../includes/session.php';

if(!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin'){
    header("Location: ../login.php");
    exit;
}

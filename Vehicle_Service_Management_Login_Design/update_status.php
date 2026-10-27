<?php
session_start();
if (($_SESSION['user_role'] ?? '') !== 'admin') {
    header('Location: admin-login.php');
    exit;
}
require_once 'db_connect.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: admin-dashboard.php'); exit; }
$id=filter_input(INPUT_POST,'id',FILTER_VALIDATE_INT);$status=$_POST['status']??'';
$allowed=['Scheduled','In Progress','Completed','Delivered'];
if(!$id||!in_array($status,$allowed,true)){http_response_code(400);exit('Invalid appointment ID or status.');}
$stmt=$conn->prepare("UPDATE appointments SET status=? WHERE id=?");$stmt->bind_param('si',$status,$id);$stmt->execute();$stmt->close();header('Location: admin-dashboard.php');exit;

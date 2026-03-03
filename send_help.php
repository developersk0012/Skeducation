<?php
require_once 'config.php';
requireLogin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $subject = $_POST['subject'] ?? '';
    $message = $_POST['message'] ?? '';
    
    sendToTelegram('📞 *HELP REQUEST*', [
        'name' => $_SESSION['user_name'],
        'action' => $subject,
        'type' => $message
    ]);
}

header('Location: dashboard.php');
exit();
?>
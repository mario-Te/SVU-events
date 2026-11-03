<?php
// =====================================
// Initialize System
// =====================================
require_once '../config/db.php';
session_start(); // Default session timeout: 24 minutes (1440 seconds) as per PHP settings

// =====================================
// Auth Check
// =====================================
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

// =====================================
// Delete Event by ID
// =====================================
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    try {
        // Fetch image reference before deleting record
        $stmt = $pdo->prepare("SELECT image FROM events WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => (int)$_GET['id']]);
        $event = $stmt->fetch();

        // Delete record from database
        $stmt = $pdo->prepare("DELETE FROM events WHERE id = :id");
        $stmt->execute([':id' => (int)$_GET['id']]);

        // Optionally delete image file from server
        if ($event && !empty($event['image'])) {
            $file_path = '../assets/img/events/' . $event['image'];
            if (file_exists($file_path)) {
                unlink($file_path);
            }
        }

        // Redirect with success
        header('Location: dashboard.php?msg=deleted');
        exit;
    } catch (PDOException $e) {
        header('Location: dashboard.php?msg=error');
        exit;
    }
} else {
    header('Location: dashboard.php?msg=error');
    exit;
}
?>
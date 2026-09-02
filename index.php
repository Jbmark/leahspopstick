<?php
session_start();

require_once 'config/database.php';

// Handle login form submission requests
if($_SERVER['REQUEST_METHOD'] == "POST"){
    require_once 'app/controllers/AuthController.php';
    exit;
}

// Security Checkpoint: No active session parameters forces fallback to login UI form
if(!isset($_SESSION['user'])){
    include 'app/views/auth/login.php';
    exit;
}

$user = $_SESSION['user'];

/* ==========================================================================
   ROLE-BASED ACCESS CONTROL (RBAC) WORKSPACE ROUTER MATRIX
   ========================================================================== */
switch($user['role_name']){

    case 'Owner':
        include 'app/views/dashboard/owner.php';
        break;

    case 'Bookkeeper':
        // 🌟 STEP 9F RESOLVED: Instantly route Bookkeepers directly into the dynamic financial panel
        require_once "app/controllers/ReportController.php";
        $controller = new ReportController();
        $controller->dashboard();
        exit;

    case 'Office Staff':
        include 'app/views/dashboard/office_staff.php';
        break;

    default:
        // Fallback protection guard if role profiles don't line up cleanly
        echo "Access Restriction Error: Profile parameters unauthorized.";
        exit;
}

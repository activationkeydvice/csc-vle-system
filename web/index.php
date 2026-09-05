<?php
// config.php - Firebase Configuration
session_start();

// For Firebase JS SDK v7.20.0 and later, measurementId is optional
const firebaseConfig = {
  apiKey: "AIzaSyBc3XpH1znblpZIPe2LS1Bzo7SrWhpqzvE",
  authDomain: "anurag-gautam-jansewa-kendra.firebaseapp.com",
  projectId: "anurag-gautam-jansewa-kendra",
  storageBucket: "anurag-gautam-jansewa-kendra.firebasestorage.app",
  messagingSenderId: "461124011252",
  appId: "1:461124011252:web:63ce897bacc55f05250aa1",
  measurementId: "G-DNKGEZ95PR"
};
// Database reference
$db_url = FIREBASE_DATABASE_URL . '/.json';

// Helper functions for Firebase REST API
function firebase_get($path = '') {
    $url = FIREBASE_DATABASE_URL . '/' . $path . '.json';
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $response = curl_exec($ch);
    curl_close($ch);
    return json_decode($response, true);
}

function firebase_post($path, $data) {
    $url = FIREBASE_DATABASE_URL . '/' . $path . '.json';
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $response = curl_exec($ch);
    curl_close($ch);
    return json_decode($response, true);
}

function firebase_patch($path, $data) {
    $url = FIREBASE_DATABASE_URL . '/' . $path . '.json';
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PATCH');
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $response = curl_exec($ch);
    curl_close($ch);
    return json_decode($response, true);
}

function firebase_delete($path) {
    $url = FIREBASE_DATABASE_URL . '/' . $path . '.json';
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $response = curl_exec($ch);
    curl_close($ch);
    return json_decode($response, true);
}

// Authentication functions
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

function isVLE() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'vle';
}

function redirect($url) {
    header("Location: $url");
    exit();
}

// User authentication
function authenticateUser($username, $password) {
    $users = firebase_get('users');
    if ($users) {
        foreach ($users as $id => $user) {
            if ($user['username'] === $username && $user['password'] === md5($password)) {
                $_SESSION['user_id'] = $id;
                $_SESSION['username'] = $user['username'];
                $_SESSION['name'] = $user['name'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['status'] = $user['status'];
                return $user['role'];
            }
        }
    }
    return false;
}

// Get current user data
function getCurrentUser() {
    if (isLoggedIn()) {
        return firebase_get('users/' . $_SESSION['user_id']);
    }
    return null;
}

// Generate unique ID
function generateId() {
    return uniqid() . '_' . rand(1000, 9999);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CSC VLE Services Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        :root {
            --primary: #1a73e8;
            --secondary: #34a853;
            --danger: #ea4335;
            --warning: #fbbc04;
            --dark: #202124;
            --light: #f8f9fa;
        }
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: var(--light);
            min-height: 100vh;
        }
        .login-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 20px;
        }
        .login-card {
            background: white;
            border-radius: 20px;
            padding: 40px;
            width: 100%;
            max-width: 420px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        }
        .login-card .logo {
            text-align: center;
            margin-bottom: 30px;
        }
        .login-card .logo i {
            font-size: 50px;
            color: var(--primary);
            background: #e8f0fe;
            padding: 20px;
            border-radius: 50%;
        }
        .login-card h3 {
            text-align: center;
            font-weight: 600;
            color: var(--dark);
        }
        .login-card p {
            text-align: center;
            color: #5f6368;
            font-size: 14px;
        }
        .form-control {
            border-radius: 10px;
            padding: 12px 15px;
            border: 2px solid #e8eaed;
        }
        .form-control:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 0.2rem rgba(26,115,232,0.25);
        }
        .btn-primary {
            background: var(--primary);
            border: none;
            border-radius: 10px;
            padding: 12px;
            font-weight: 600;
            width: 100%;
        }
        .btn-primary:hover {
            background: #1557b0;
        }
        .dashboard-wrapper {
            display: flex;
            min-height: 100vh;
        }
        .sidebar {
            width: 250px;
            background: var(--dark);
            color: white;
            padding: 20px 0;
            position: fixed;
            height: 100vh;
            overflow-y: auto;
            transition: all 0.3s;
        }
        .sidebar .brand {
            padding: 20px 25px;
            font-size: 20px;
            font-weight: 700;
            border-bottom: 1px solid #3c4043;
            margin-bottom: 20px;
        }
        .sidebar .brand i {
            color: var(--warning);
            margin-right: 10px;
        }
        .sidebar .nav-link {
            color: #9aa0a6;
            padding: 12px 25px;
            border-radius: 0;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
        }
        .sidebar .nav-link:hover, .sidebar .nav-link.active {
            color: white;
            background: #3c4043;
        }
        .sidebar .nav-link i {
            width: 20px;
            text-align: center;
        }
        .main-content {
            margin-left: 250px;
            flex: 1;
            padding: 20px 30px;
            width: 100%;
        }
        .top-bar {
            background: white;
            padding: 15px 25px;
            border-radius: 15px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }
        .top-bar .user-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        .top-bar .user-info .avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--primary);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 25px;
        }
        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 15px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            transition: transform 0.3s;
        }
        .stat-card:hover {
            transform: translateY(-5px);
        }
        .stat-card .stat-icon {
            font-size: 30px;
            color: var(--primary);
            margin-bottom: 10px;
        }
        .stat-card .stat-number {
            font-size: 28px;
            font-weight: 700;
            color: var(--dark);
        }
        .stat-card .stat-label {
            color: #5f6368;
            font-size: 14px;
        }
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            margin-bottom: 25px;
        }
        .card-header {
            background: white;
            border-bottom: 1px solid #e8eaed;
            padding: 15px 20px;
            border-radius: 15px 15px 0 0;
            font-weight: 600;
        }
        .table {
            margin-bottom: 0;
        }
        .table th {
            border-top: none;
            color: #5f6368;
            font-weight: 600;
            font-size: 13px;
            text-transform: uppercase;
        }
        .badge-status {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }
        .badge-active { background: #e6f4ea; color: #1e7e34; }
        .badge-inactive { background: #fce8e6; color: #d93025; }
        .badge-pending { background: #fef7e0; color: #e37400; }
        .badge-approved { background: #e6f4ea; color: #1e7e34; }
        .badge-completed { background: #e8f0fe; color: #1a73e8; }
        @media (max-width: 768px) {
            .sidebar {
                width: 60px;
                padding: 10px 0;
            }
            .sidebar .brand span, .sidebar .nav-link span {
                display: none;
            }
            .sidebar .brand {
                padding: 10px 15px;
                font-size: 16px;
            }
            .sidebar .nav-link {
                padding: 12px 15px;
                justify-content: center;
            }
            .main-content {
                margin-left: 60px;
                padding: 15px;
            }
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }
        @media (max-width: 480px) {
            .stats-grid {
                grid-template-columns: 1fr;
            }
            .login-card {
                padding: 25px;
            }
        }
        .notification-badge {
            position: relative;
        }
        .notification-badge .badge {
            position: absolute;
            top: -8px;
            right: -8px;
            background: var(--danger);
            font-size: 10px;
            padding: 3px 6px;
        }
        .modal-content {
            border-radius: 15px;
        }
        .modal-header {
            border-bottom: 1px solid #e8eaed;
            padding: 20px 25px;
        }
        .modal-body {
            padding: 25px;
        }
        .modal-footer {
            border-top: 1px solid #e8eaed;
            padding: 15px 25px;
        }
        .toast-container {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
        }
        .toast {
            background: white;
            border-radius: 10px;
            padding: 15px 20px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.15);
            margin-bottom: 10px;
            min-width: 300px;
            animation: slideIn 0.3s ease;
        }
        .toast.success { border-left: 4px solid var(--secondary); }
        .toast.error { border-left: 4px solid var(--danger); }
        .toast.warning { border-left: 4px solid var(--warning); }
        .toast.info { border-left: 4px solid var(--primary); }
        @keyframes slideIn {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255,255,255,0.8);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 9998;
        }
        .loading-overlay.show {
            display: flex;
        }
        .spinner {
            width: 50px;
            height: 50px;
            border: 4px solid #e8eaed;
            border-top-color: var(--primary);
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
        }
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        .real-time-indicator {
            display: inline-block;
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: var(--secondary);
            animation: pulse 1.5s ease-in-out infinite;
            margin-right: 8px;
        }
        @keyframes pulse {
            0% { opacity: 0.5; }
            50% { opacity: 1; }
            100% { opacity: 0.5; }
        }
    </style>
</head>
<body>

<?php
// Handle Login
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $role = authenticateUser($username, $password);
    if ($role) {
        if ($role === 'admin') {
            redirect('index.php?page=admin');
        } else {
            redirect('index.php?page=vle');
        }
    } else {
        $login_error = 'Invalid username or password';
    }
}

// Handle Logout
if (isset($_GET['logout'])) {
    session_destroy();
    redirect('index.php');
}

// Handle AJAX requests
if (isset($_GET['action'])) {
    header('Content-Type: application/json');
    
    if (!isLoggedIn()) {
        echo json_encode(['error' => 'Unauthorized']);
        exit();
    }
    
    $action = $_GET['action'];
    $data = json_decode(file_get_contents('php://input'), true) ?? $_POST;
    
    switch ($action) {
        case 'get_stats':
            $users = firebase_get('users');
            $services = firebase_get('services');
            $applications = firebase_get('applications');
            $payments = firebase_get('payments');
            
            $stats = [
                'total_vle' => 0,
                'active_vle' => 0,
                'total_services' => 0,
                'total_applications' => 0,
                'pending_applications' => 0,
                'total_collection' => 0,
                'today_collection' => 0
            ];
            
            if ($users) {
                foreach ($users as $user) {
                    if ($user['role'] === 'vle') {
                        $stats['total_vle']++;
                        if ($user['status'] === 'active') $stats['active_vle']++;
                    }
                }
            }
            
            if ($services) $stats['total_services'] = count($services);
            if ($applications) {
                $stats['total_applications'] = count($applications);
                foreach ($applications as $app) {
                    if ($app['status'] === 'pending' || $app['status'] === 'submitted') {
                        $stats['pending_applications']++;
                    }
                }
            }
            
            if ($payments) {
                foreach ($payments as $payment) {
                    $stats['total_collection'] += floatval($payment['amount'] ?? 0);
                    if (date('Y-m-d', strtotime($payment['createdAt'] ?? 'now')) === date('Y-m-d')) {
                        $stats['today_collection'] += floatval($payment['amount'] ?? 0);
                    }
                }
            }
            
            echo json_encode($stats);
            break;
            
        case 'get_users':
            $users = firebase_get('users');
            echo json_encode($users ?: []);
            break;
            
        case 'get_services':
            $services = firebase_get('services');
            echo json_encode($services ?: []);
            break;
            
        case 'add_user':
            if (!isAdmin()) {
                echo json_encode(['error' => 'Unauthorized']);
                break;
            }
            $userData = [
                'name' => $data['name'],
                'username' => $data['username'],
                'password' => md5($data['password']),
                'role' => $data['role'],
                'status' => $data['status'] ?? 'active',
                'createdAt' => date('Y-m-d H:i:s'),
                'updatedAt' => date('Y-m-d H:i:s')
            ];
            $result = firebase_post('users', $userData);
            echo json_encode(['success' => true, 'id' => $result['name'] ?? null]);
            break;
            
        case 'update_user':
            if (!isAdmin()) {
                echo json_encode(['error' => 'Unauthorized']);
                break;
            }
            $userId = $data['userId'];
            unset($data['userId']);
            if (isset($data['password'])) {
                $data['password'] = md5($data['password']);
            }
            $data['updatedAt'] = date('Y-m-d H:i:s');
            $result = firebase_patch('users/' . $userId, $data);
            echo json_encode(['success' => true]);
            break;
            
        case 'delete_user':
            if (!isAdmin()) {
                echo json_encode(['error' => 'Unauthorized']);
                break;
            }
            $result = firebase_delete('users/' . $data['userId']);
            echo json_encode(['success' => true]);
            break;
            
        case 'add_service':
            if (!isAdmin()) {
                echo json_encode(['error' => 'Unauthorized']);
                break;
            }
            $serviceData = [
                'serviceName' => $data['serviceName'],
                'category' => $data['category'],
                'description' => $data['description'],
                'price' => floatval($data['price']),
                'documents' => $data['documents'] ?? '',
                'processingTime' => $data['processingTime'] ?? '2-3 days',
                'status' => $data['status'] ?? 'active',
                'assignedVLE' => $data['assignedVLE'] ?? 'all',
                'instructions' => $data['instructions'] ?? '',
                'createdAt' => date('Y-m-d H:i:s'),
                'updatedAt' => date('Y-m-d H:i:s')
            ];
            $result = firebase_post('services', $serviceData);
            echo json_encode(['success' => true, 'id' => $result['name'] ?? null]);
            break;
            
        case 'update_service':
            if (!isAdmin()) {
                echo json_encode(['error' => 'Unauthorized']);
                break;
            }
            $serviceId = $data['serviceId'];
            unset($data['serviceId']);
            if (isset($data['price'])) {
                $data['price'] = floatval($data['price']);
            }
            $data['updatedAt'] = date('Y-m-d H:i:s');
            $result = firebase_patch('services/' . $serviceId, $data);
            echo json_encode(['success' => true]);
            break;
            
        case 'delete_service':
            if (!isAdmin()) {
                echo json_encode(['error' => 'Unauthorized']);
                break;
            }
            $result = firebase_delete('services/' . $data['serviceId']);
            echo json_encode(['success' => true]);
            break;
            
        case 'add_application':
            $appData = [
                'customerId' => $data['customerId'],
                'vleId' => $_SESSION['user_id'],
                'serviceId' => $data['serviceId'],
                'status' => 'submitted',
                'paymentStatus' => 'pending',
                'customerName' => $data['customerName'],
                'customerMobile' => $data['customerMobile'],
                'customerAddress' => $data['customerAddress'] ?? '',
                'remarks' => $data['remarks'] ?? '',
                'createdAt' => date('Y-m-d H:i:s'),
                'updatedAt' => date('Y-m-d H:i:s')
            ];
            $result = firebase_post('applications', $appData);
            echo json_encode(['success' => true, 'id' => $result['name'] ?? null]);
            break;
            
        case 'update_application':
            $appId = $data['applicationId'];
            unset($data['applicationId']);
            $data['updatedAt'] = date('Y-m-d H:i:s');
            $result = firebase_patch('applications/' . $appId, $data);
            echo json_encode(['success' => true]);
            break;
            
        case 'add_payment':
            $paymentData = [
                'applicationId' => $data['applicationId'],
                'vleId' => $_SESSION['user_id'],
                'amount' => floatval($data['amount']),
                'paymentMethod' => $data['paymentMethod'],
                'transactionId' => $data['transactionId'] ?? '',
                'status' => $data['status'] ?? 'completed',
                'createdAt' => date('Y-m-d H:i:s')
            ];
            $result = firebase_post('payments', $paymentData);
            echo json_encode(['success' => true, 'id' => $result['name'] ?? null]);
            break;
            
        case 'add_notification':
            if (!isAdmin()) {
                echo json_encode(['error' => 'Unauthorized']);
                break;
            }
            $notifData = [
                'title' => $data['title'],
                'message' => $data['message'],
                'targetUsers' => $data['targetUsers'] ?? 'all',
                'priority' => $data['priority'] ?? 'normal',
                'readBy' => [],
                'createdAt' => date('Y-m-d H:i:s')
            ];
            $result = firebase_post('notifications', $notifData);
            echo json_encode(['success' => true, 'id' => $result['name'] ?? null]);
            break;
            
        case 'get_notifications':
            $notifications = firebase_get('notifications');
            echo json_encode($notifications ?: []);
            break;
            
        case 'get_applications':
            $applications = firebase_get('applications');
            if (isVLE()) {
                $vleId = $_SESSION['user_id'];
                $filtered = [];
                if ($applications) {
                    foreach ($applications as $id => $app) {
                        if ($app['vleId'] === $vleId) {
                            $filtered[$id] = $app;
                        }
                    }
                }
                echo json_encode($filtered);
            } else {
                echo json_encode($applications ?: []);
            }
            break;
            
        case 'get_payments':
            $payments = firebase_get('payments');
            if (isVLE()) {
                $vleId = $_SESSION['user_id'];
                $filtered = [];
                if ($payments) {
                    foreach ($payments as $id => $payment) {
                        if ($payment['vleId'] === $vleId) {
                            $filtered[$id] = $payment;
                        }
                    }
                }
                echo json_encode($filtered);
            } else {
                echo json_encode($payments ?: []);
            }
            break;
            
        case 'get_audit_logs':
            if (!isAdmin()) {
                echo json_encode(['error' => 'Unauthorized']);
                break;
            }
            $logs = firebase_get('auditLogs');
            echo json_encode($logs ?: []);
            break;
            
        case 'add_audit_log':
            $logData = [
                'userId' => $_SESSION['user_id'],
                'username' => $_SESSION['username'],
                'action' => $data['action'],
                'details' => $data['details'] ?? '',
                'timestamp' => date('Y-m-d H:i:s')
            ];
            firebase_post('auditLogs', $logData);
            echo json_encode(['success' => true]);
            break;
            
        default:
            echo json_encode(['error' => 'Invalid action']);
    }
    exit();
}

// Determine page
$page = $_GET['page'] ?? 'login';

// If not logged in, show login
if (!isLoggedIn() && $page !== 'login') {
    redirect('index.php');
}

// If logged in, check role and redirect
if (isLoggedIn() && $page === 'login') {
    if (isAdmin()) {
        redirect('index.php?page=admin');
    } else {
        redirect('index.php?page=vle');
    }
}

// Check access
if (isLoggedIn() && $page === 'admin' && !isAdmin()) {
    redirect('index.php?page=vle');
}
if (isLoggedIn() && $page === 'vle' && !isVLE()) {
    redirect('index.php?page=admin');
}

// Handle page display
if ($page === 'login' || !isLoggedIn()) {
    // Show login page
?>
    <div class="login-container">
        <div class="login-card">
            <div class="logo">
                <i class="fas fa-crown"></i>
                <h3 class="mt-3">CSC VLE Services</h3>
                <p>Management System</p>
            </div>
            
            <?php if (isset($login_error)): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($login_error) ?></div>
            <?php endif; ?>
            
            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">Username</label>
                    <input type="text" name="username" class="form-control" required placeholder="Enter username">
                </div>
                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" required placeholder="Enter password">
                </div>
                <button type="submit" name="login" class="btn btn-primary">
                    <i class="fas fa-sign-in-alt me-2"></i>Login
                </button>
            </form>
            <div class="text-center mt-3">
                <small class="text-muted">Demo: admin/admin123 or vle/vle123</small>
            </div>
        </div>
    </div>
<?php
    exit();
}

// Show dashboard
$currentUser = getCurrentUser();
$isAdmin = isAdmin();
?>
<div class="dashboard-wrapper">
    <!-- Sidebar -->
    <nav class="sidebar">
        <div class="brand">
            <i class="fas fa-crown"></i>
            <span>CSC VLE</span>
        </div>
        
        <?php if ($isAdmin): ?>
            <a href="?page=admin" class="nav-link <?= $page === 'admin' ? 'active' : '' ?>">
                <i class="fas fa-chart-pie"></i><span>Dashboard</span>
            </a>
            <a href="#" class="nav-link" onclick="showSection('vle-management')">
                <i class="fas fa-users-cog"></i><span>VLE Management</span>
            </a>
            <a href="#" class="nav-link" onclick="showSection('service-management')">
                <i class="fas fa-cogs"></i><span>Services</span>
            </a>
            <a href="#" class="nav-link" onclick="showSection('applications')">
                <i class="fas fa-file-alt"></i><span>Applications</span>
            </a>
            <a href="#" class="nav-link" onclick="showSection('payments')">
                <i class="fas fa-money-bill-wave"></i><span>Payments</span>
            </a>
            <a href="#" class="nav-link" onclick="showSection('notifications')">
                <i class="fas fa-bell"></i><span>Notifications</span>
                <span class="badge bg-danger ms-auto" id="notif-count">0</span>
            </a>
            <a href="#" class="nav-link" onclick="showSection('audit-logs')">
                <i class="fas fa-history"></i><span>Audit Logs</span>
            </a>
            <a href="#" class="nav-link" onclick="showSection('settings')">
                <i class="fas fa-cog"></i><span>Settings</span>
            </a>
        <?php else: ?>
            <a href="?page=vle" class="nav-link <?= $page === 'vle' ? 'active' : '' ?>">
                <i class="fas fa-chart-pie"></i><span>Dashboard</span>
            </a>
            <a href="#" class="nav-link" onclick="showSection('services')">
                <i class="fas fa-cogs"></i><span>Services</span>
            </a>
            <a href="#" class="nav-link" onclick="showSection('applications')">
                <i class="fas fa-file-alt"></i><span>My Applications</span>
            </a>
            <a href="#" class="nav-link" onclick="showSection('customers')">
                <i class="fas fa-users"></i><span>Customers</span>
            </a>
            <a href="#" class="nav-link" onclick="showSection('payments')">
                <i class="fas fa-money-bill-wave"></i><span>My Payments</span>
            </a>
            <a href="#" class="nav-link" onclick="showSection('notifications')">
                <i class="fas fa-bell"></i><span>Notifications</span>
                <span class="badge bg-danger ms-auto" id="notif-count">0</span>
            </a>
            <a href="#" class="nav-link" onclick="showSection('profile')">
                <i class="fas fa-user"></i><span>Profile</span>
            </a>
        <?php endif; ?>
        <a href="?logout=1" class="nav-link text-danger">
            <i class="fas fa-sign-out-alt"></i><span>Logout</span>
        </a>
    </nav>
    
    <!-- Main Content -->
    <div class="main-content">
        <!-- Top Bar -->
        <div class="top-bar">
            <div>
                <span class="real-time-indicator"></span>
                <span class="text-muted small">Real-Time Sync Active</span>
            </div>
            <div class="user-info">
                <span class="text-muted"><?= htmlspecialchars($currentUser['name'] ?? 'User') ?></span>
                <span class="badge <?= $isAdmin ? 'bg-primary' : 'bg-success' ?>">
                    <?= $isAdmin ? 'Admin' : 'VLE' ?>
                </span>
                <div class="avatar"><?= substr($currentUser['name'] ?? 'U', 0, 1) ?></div>
            </div>
        </div>
        
        <!-- Dashboard Content -->
        <div id="dashboard-content">
            <!-- Stats for Admin -->
            <?php if ($isAdmin): ?>
                <div class="stats-grid" id="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon"><i class="fas fa-users"></i></div>
                        <div class="stat-number" id="stat-total-vle">0</div>
                        <div class="stat-label">Total VLE</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon"><i class="fas fa-user-check"></i></div>
                        <div class="stat-number" id="stat-active-vle">0</div>
                        <div class="stat-label">Active VLE</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon"><i class="fas fa-cogs"></i></div>
                        <div class="stat-number" id="stat-total-services">0</div>
                        <div class="stat-label">Total Services</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon"><i class="fas fa-file-alt"></i></div>
                        <div class="stat-number" id="stat-total-applications">0</div>
                        <div class="stat-label">Total Applications</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon"><i class="fas fa-clock"></i></div>
                        <div class="stat-number" id="stat-pending-applications">0</div>
                        <div class="stat-label">Pending</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon"><i class="fas fa-rupee-sign"></i></div>
                        <div class="stat-number" id="stat-total-collection">₹0</div>
                        <div class="stat-label">Total Collection</div>
                    </div>
                </div>
            <?php else: ?>
                <div class="stats-grid" id="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon"><i class="fas fa-cogs"></i></div>
                        <div class="stat-number" id="stat-total-services">0</div>
                        <div class="stat-label">Available Services</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon"><i class="fas fa-file-alt"></i></div>
                        <div class="stat-number" id="stat-total-applications">0</div>
                        <div class="stat-label">My Applications</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon"><i class="fas fa-clock"></i></div>
                        <div class="stat-number" id="stat-pending-applications">0</div>
                        <div class="stat-label">Pending</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon"><i class="fas fa-rupee-sign"></i></div>
                        <div class="stat-number" id="stat-total-collection">₹0</div>
                        <div class="stat-label">My Collection</div>
                    </div>
                </div>
            <?php endif; ?>
            
            <!-- Recent Activities -->
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-clock me-2"></i>Recent Applications
                    <button class="btn btn-sm btn-primary float-end" onclick="refreshData()">
                        <i class="fas fa-sync-alt"></i>
                    </button>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Application ID</th>
                                    <th>Customer</th>
                                    <th>Service</th>
                                    <th>Status</th>
                                    <th>Payment</th>
                                    <th>Date</th>
                                    <?php if ($isAdmin): ?>
                                        <th>VLE</th>
                                    <?php endif; ?>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="applications-table">
                                <tr>
                                    <td colspan="8" class="text-center text-muted">Loading...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Section containers for dynamic content -->
        <div id="section-content" style="display: none;"></div>
    </div>
</div>

<!-- Loading Overlay -->
<div class="loading-overlay" id="loadingOverlay">
    <div class="spinner"></div>
</div>

<!-- Toast Container -->
<div class="toast-container" id="toastContainer"></div>

<!-- Modals -->
<!-- Add VLE Modal -->
<div class="modal fade" id="addVLEModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-user-plus me-2"></i>Add New VLE</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="addVLEForm">
                    <div class="mb-3">
                        <label class="form-label">Full Name</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Username</label>
                        <input type="text" name="username" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-control">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                    <input type="hidden" name="role" value="vle">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="addVLE()">Add VLE</button>
            </div>
        </div>
    </div>
</div>

<!-- Add Service Modal -->
<div class="modal fade" id="addServiceModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-plus-circle me-2"></i>Add New Service</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="addServiceForm">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Service Name</label>
                            <input type="text" name="serviceName" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Category</label>
                            <input type="text" name="category" class="form-control" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="2"></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Price (₹)</label>
                            <input type="number" name="price" class="form-control" required step="0.01">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Processing Time</label>
                            <input type="text" name="processingTime" class="form-control" value="2-3 days">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Required Documents</label>
                        <input type="text" name="documents" class="form-control" placeholder="Aadhar, PAN, Photo">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Instructions</label>
                        <textarea name="instructions" class="form-control" rows="2"></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-control">
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Assigned VLE</label>
                            <select name="assignedVLE" class="form-control">
                                <option value="all">All VLE</option>
                            </select>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="addService()">Add Service</button>
            </div>
        </div>
    </div>
</div>

<!-- Add Application Modal -->
<div class="modal fade" id="addApplicationModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-file-medical me-2"></i>New Application</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="addApplicationForm">
                    <div class="mb-3">
                        <label class="form-label">Customer Name</label>
                        <input type="text" name="customerName" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Mobile Number</label>
                        <input type="text" name="customerMobile" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Address</label>
                        <textarea name="customerAddress" class="form-control" rows="2"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Select Service</label>
                        <select name="serviceId" class="form-control" required>
                            <option value="">Select Service...</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Remarks</label>
                        <textarea name="remarks" class="form-control" rows="2"></textarea>
                    </div>
                    <input type="hidden" name="customerId" value="">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="addApplication()">Submit Application</button>
            </div>
        </div>
    </div>
</div>

<script>
// Global variables
let currentSection = 'dashboard';
let allData = {};

// Firebase configuration (for real-time updates)
const firebaseConfig = {
    apiKey: "<?= FIREBASE_API_KEY ?>",
    authDomain: "<?= FIREBASE_AUTH_DOMAIN ?>",
    databaseURL: "<?= FIREBASE_DATABASE_URL ?>",
    projectId: "<?= FIREBASE_PROJECT_ID ?>",
    storageBucket: "<?= FIREBASE_STORAGE_BUCKET ?>",
    messagingSenderId: "<?= FIREBASE_MESSAGING_SENDER_ID ?>",
    appId: "<?= FIREBASE_APP_ID ?>"
};

// Initialize Firebase
firebase.initializeApp(firebaseConfig);
const database = firebase.database();

// Show toast notification
function showToast(message, type = 'info') {
    const container = document.getElementById('toastContainer');
    const toast = document.createElement('div');
    toast.className = `toast ${type}`;
    toast.innerHTML = `
        <div class="d-flex align-items-center">
            <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle'} me-2"></i>
            <span>${message}</span>
            <button type="button" class="btn-close ms-auto" onclick="this.parentElement.parentElement.remove()"></button>
        </div>
    `;
    container.appendChild(toast);
    setTimeout(() => toast.remove(), 5000);
}

// Show loading overlay
function showLoading(show) {
    document.getElementById('loadingOverlay').classList.toggle('show', show);
}

// API call
function apiCall(action, data = {}) {
    return new Promise((resolve, reject) => {
        showLoading(true);
        fetch(`?action=${action}`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        })
        .then(res => res.json())
        .then(result => {
            showLoading(false);
            if (result.error) {
                showToast(result.error, 'error');
                reject(result);
            } else {
                resolve(result);
            }
        })
        .catch(err => {
            showLoading(false);
            showToast('Network error', 'error');
            reject(err);
        });
    });
}

// Load stats
async function loadStats() {
    try {
        const stats = await apiCall('get_stats');
        document.getElementById('stat-total-vle').textContent = stats.total_vle || 0;
        document.getElementById('stat-active-vle').textContent = stats.active_vle || 0;
        document.getElementById('stat-total-services').textContent = stats.total_services || 0;
        document.getElementById('stat-total-applications').textContent = stats.total_applications || 0;
        document.getElementById('stat-pending-applications').textContent = stats.pending_applications || 0;
        document.getElementById('stat-total-collection').textContent = `₹${(stats.total_collection || 0).toFixed(2)}`;
    } catch (e) {
        console.error('Error loading stats:', e);
    }
}

// Load applications
async function loadApplications() {
    try {
        const apps = await apiCall('get_applications');
        const services = await apiCall('get_services');
        const users = await apiCall('get_users');
        
        const tbody = document.getElementById('applications-table');
        if (!apps || Object.keys(apps).length === 0) {
            tbody.innerHTML = `<tr><td colspan="8" class="text-center text-muted">No applications found</td></tr>`;
            return;
        }
        
        let html = '';
        const sorted = Object.entries(apps).sort((a, b) => 
            new Date(b[1].createdAt) - new Date(a[1].createdAt)
        );
        
        sorted.slice(0, 20).forEach(([id, app]) => {
            const serviceName = services?.[app.serviceId]?.serviceName || 'Unknown';
            const vleName = users?.[app.vleId]?.name || 'Unknown';
            const statusClass = {
                'submitted': 'badge-pending',
                'pending': 'badge-pending',
                'approved': 'badge-approved',
                'completed': 'badge-completed',
                'rejected': 'badge-inactive'
            }[app.status] || 'badge-pending';
            
            html += `
                <tr>
                    <td><small>#${id.slice(0, 8)}</small></td>
                    <td>${app.customerName || 'N/A'}</td>
                    <td>${serviceName}</td>
                    <td><span class="badge-status ${statusClass}">${app.status || 'draft'}</span></td>
                    <td><span class="badge-status ${app.paymentStatus === 'completed' ? 'badge-approved' : 'badge-pending'}">${app.paymentStatus || 'pending'}</span></td>
                    <td><small>${new Date(app.createdAt).toLocaleDateString()}</small></td>
                    ${'<?= $isAdmin ? "<td>" + vleName + "</td>" : "" ?>'}
                    <td>
                        <button class="btn btn-sm btn-outline-primary" onclick="viewApplication('${id}')">
                            <i class="fas fa-eye"></i>
                        </button>
                        <?php if ($isAdmin): ?>
                        <button class="btn btn-sm btn-outline-warning" onclick="editApplication('${id}')">
                            <i class="fas fa-edit"></i>
                        </button>
                        <?php endif; ?>
                    </td>
                </tr>
            `;
        });
        tbody.innerHTML = html;
    } catch (e) {
        console.error('Error loading applications:', e);
    }
}

// Load VLE list (for admin)
async function loadVLEList() {
    <?php if ($isAdmin): ?>
    try {
        const users = await apiCall('get_users');
        const container = document.getElementById('vle-list');
        if (!container) return;
        
        if (!users || Object.keys(users).length === 0) {
            container.innerHTML = '<p class="text-muted">No VLE users found</p>';
            return;
        }
        
        let html = '';
        Object.entries(users).forEach(([id, user]) => {
            if (user.role === 'vle') {
                html += `
                    <div class="vle-card">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="vle-name">${user.name || 'Unknown'}</div>
                                <small class="text-muted">@${user.username}</small>
                            </div>
                            <div>
                                <span class="badge-status ${user.status === 'active' ? 'badge-active' : 'badge-inactive'}">${user.status || 'inactive'}</span>
                                <button class="btn btn-sm btn-outline-primary" onclick="editVLE('${id}')">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-danger" onclick="deleteVLE('${id}')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                `;
            }
        });
        container.innerHTML = html;
    } catch (e) {
        console.error('Error loading VLE list:', e);
    }
    <?php endif; ?>
}

// Load services
async function loadServices() {
    try {
        const services = await apiCall('get_services');
        const container = document.getElementById('services-list');
        if (!container) return;
        
        if (!services || Object.keys(services).length === 0) {
            container.innerHTML = '<p class="text-muted">No services available</p>';
            return;
        }
        
        let html = '';
        Object.entries(services).forEach(([id, service]) => {
            html += `
                <div class="service-card">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="service-name">${service.serviceName || 'Unnamed'}</div>
                            <small class="text-muted">${service.category || 'General'}</small>
                            <div class="mt-1">
                                <span class="badge-status ${service.status === 'active' ? 'badge-active' : 'badge-inactive'}">${service.status || 'active'}</span>
                                ${service.documents ? `<span class="badge bg-info text-white ms-1">${service.documents}</span>` : ''}
                            </div>
                        </div>
                        <div>
                            <div class="service-price">₹${service.price || 0}</div>
                            <?php if ($isAdmin): ?>
                            <button class="btn btn-sm btn-outline-primary" onclick="editService('${id}')">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-danger" onclick="deleteService('${id}')">
                                <i class="fas fa-trash"></i>
                            </button>
                            <?php endif; ?>
                        </div>
                    </div>
                    ${service.description ? `<p class="mt-2 mb-0 small">${service.description}</p>` : ''}
                </div>
            `;
        });
        container.innerHTML = html;
    } catch (e) {
        console.error('Error loading services:', e);
    }
}

// Load notifications
async function loadNotifications() {
    try {
        const notifs = await apiCall('get_notifications');
        const container = document.getElementById('notifications-list');
        if (!container) return;
        
        let count = 0;
        if (notifs) {
            count = Object.keys(notifs).length;
        }
        document.getElementById('notif-count').textContent = count;
        
        if (!notifs || Object.keys(notifs).length === 0) {
            container.innerHTML = '<p class="text-muted">No notifications</p>';
            return;
        }
        
        let html = '';
        Object.entries(notifs).sort((a, b) => 
            new Date(b[1].createdAt) - new Date(a[1].createdAt)
        ).forEach(([id, notif]) => {
            html += `
                <div class="list-group-item">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <strong>${notif.title}</strong>
                            <p class="mb-0 small">${notif.message}</p>
                        </div>
                        <small class="text-muted">${new Date(notif.createdAt).toLocaleDateString()}</small>
                    </div>
                    ${notif.priority === 'high' ? '<span class="badge bg-danger">High Priority</span>' : ''}
                </div>
            `;
        });
        container.innerHTML = html;
    } catch (e) {
        console.error('Error loading notifications:', e);
    }
}

// Show section
function showSection(section) {
    currentSection = section;
    document.getElementById('dashboard-content').style.display = 'none';
    const sectionContainer = document.getElementById('section-content');
    sectionContainer.style.display = 'block';
    
    let html = '';
    switch(section) {
        case 'vle-management':
            html = `
                <div class="card">
                    <div class="card-header">
                        <i class="fas fa-users-cog me-2"></i>VLE Management
                        <button class="btn btn-sm btn-primary float-end" onclick="showAddVLEModal()">
                            <i class="fas fa-plus"></i> Add VLE
                        </button>
                    </div>
                    <div class="card-body" id="vle-list">
                        Loading...
                    </div>
                </div>
            `;
            sectionContainer.innerHTML = html;
            loadVLEList();
            break;
            
        case 'service-management':
        case 'services':
            html = `
                <div class="card">
                    <div class="card-header">
                        <i class="fas fa-cogs me-2"></i>${section === 'service-management' ? 'Service Management' : 'Available Services'}
                        ${section === 'service-management' ? `<button class="btn btn-sm btn-primary float-end" onclick="showAddServiceModal()">
                            <i class="fas fa-plus"></i> Add Service
                        </button>` : ''}
                    </div>
                    <div class="card-body" id="services-list">
                        Loading...
                    </div>
                </div>
            `;
            sectionContainer.innerHTML = html;
            loadServices();
            break;
            
        case 'applications':
            html = `
                <div class="card">
                    <div class="card-header">
                        <i class="fas fa-file-alt me-2"></i>Applications
                        <button class="btn btn-sm btn-primary float-end" onclick="showAddApplicationModal()">
                            <i class="fas fa-plus"></i> New Application
                        </button>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Customer</th>
                                        <th>Service</th>
                                        <th>Status</th>
                                        <th>Payment</th>
                                        <th>Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="applications-table">
                                    <tr><td colspan="7" class="text-center text-muted">Loading...</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            `;
            sectionContainer.innerHTML = html;
            loadApplications();
            break;
            
        case 'payments':
            html = `
                <div class="card">
                    <div class="card-header">
                        <i class="fas fa-money-bill-wave me-2"></i>${'<?= $isAdmin ? "Payment Records" : "My Payments" ?>'}
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Transaction ID</th>
                                        <th>Customer</th>
                                        <th>Amount</th>
                                        <th>Method</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody id="payments-table">
                                    <tr><td colspan="6" class="text-center text-muted">Loading...</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            `;
            sectionContainer.innerHTML = html;
            loadPayments();
            break;
            
        case 'notifications':
            html = `
                <div class="card">
                    <div class="card-header">
                        <i class="fas fa-bell me-2"></i>Notifications
                        <?php if ($isAdmin): ?>
                        <button class="btn btn-sm btn-primary float-end" onclick="showSendNotificationModal()">
                            <i class="fas fa-paper-plane"></i> Send
                        </button>
                        <?php endif; ?>
                    </div>
                    <div class="card-body" id="notifications-list">
                        Loading...
                    </div>
                </div>
            `;
            sectionContainer.innerHTML = html;
            loadNotifications();
            break;
            
        case 'customers':
            html = `
                <div class="card">
                    <div class="card-header">
                        <i class="fas fa-users me-2"></i>My Customers
                    </div>
                    <div class="card-body" id="customers-list">
                        <p class="text-muted">Customer management coming soon...</p>
                    </div>
                </div>
            `;
            sectionContainer.innerHTML = html;
            break;
            
        case 'audit-logs':
            html = `
                <div class="card">
                    <div class="card-header">
                        <i class="fas fa-history me-2"></i>Audit Logs
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>User</th>
                                        <th>Action</th>
                                        <th>Details</th>
                                        <th>Timestamp</th>
                                    </tr>
                                </thead>
                                <tbody id="audit-logs-table">
                                    <tr><td colspan="4" class="text-center text-muted">Loading...</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            `;
            sectionContainer.innerHTML = html;
            loadAuditLogs();
            break;
            
        case 'profile':
            html = `
                <div class="card">
                    <div class="card-header">
                        <i class="fas fa-user me-2"></i>My Profile
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Name:</strong> <?= htmlspecialchars($currentUser['name'] ?? 'N/A') ?></p>
                                <p><strong>Username:</strong> <?= htmlspecialchars($currentUser['username'] ?? 'N/A') ?></p>
                                <p><strong>Role:</strong> <?= $isAdmin ? 'Admin' : 'VLE' ?></p>
                                <p><strong>Status:</strong> <?= htmlspecialchars($currentUser['status'] ?? 'active') ?></p>
                                <p><strong>Joined:</strong> <?= htmlspecialchars($currentUser['createdAt'] ?? 'N/A') ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            sectionContainer.innerHTML = html;
            break;
            
        case 'settings':
            html = `
                <div class="card">
                    <div class="card-header">
                        <i class="fas fa-cog me-2"></i>System Settings
                    </div>
                    <div class="card-body">
                        <p class="text-muted">System settings coming soon...</p>
                    </div>
                </div>
            `;
            sectionContainer.innerHTML = html;
            break;
            
        default:
            sectionContainer.innerHTML = '<p>Section not found</p>';
    }
}

// Load payments
async function loadPayments() {
    try {
        const payments = await apiCall('get_payments');
        const tbody = document.getElementById('payments-table');
        if (!tbody) return;
        
        if (!payments || Object.keys(payments).length === 0) {
            tbody.innerHTML = `<tr><td colspan="6" class="text-center text-muted">No payments found</td></tr>`;
            return;
        }
        
        let html = '';
        Object.entries(payments).slice(0, 20).forEach(([id, payment]) => {
            html += `
                <tr>
                    <td><small>#${id.slice(0, 8)}</small></td>
                    <td>${payment.applicationId ? 'Customer' : 'N/A'}</td>
                    <td><strong>₹${payment.amount || 0}</strong></td>
                    <td>${payment.paymentMethod || 'N/A'}</td>
                    <td><span class="badge-status ${payment.status === 'completed' ? 'badge-approved' : 'badge-pending'}">${payment.status || 'pending'}</span></td>
                    <td><small>${new Date(payment.createdAt).toLocaleDateString()}</small></td>
                </tr>
            `;
        });
        tbody.innerHTML = html;
    } catch (e) {
        console.error('Error loading payments:', e);
    }
}

// Load audit logs
async function loadAuditLogs() {
    <?php if ($isAdmin): ?>
    try {
        const logs = await apiCall('get_audit_logs');
        const tbody = document.getElementById('audit-logs-table');
        if (!tbody) return;
        
        if (!logs || Object.keys(logs).length === 0) {
            tbody.innerHTML = `<tr><td colspan="4" class="text-center text-muted">No logs found</td></tr>`;
            return;
        }
        
        let html = '';
        Object.entries(logs).sort((a, b) => 
            new Date(b[1].timestamp) - new Date(a[1].timestamp)
        ).slice(0, 50).forEach(([id, log]) => {
            html += `
                <tr>
                    <td><strong>${log.username || 'Unknown'}</strong></td>
                    <td>${log.action || 'N/A'}</td>
                    <td>${log.details || ''}</td>
                    <td><small>${new Date(log.timestamp).toLocaleString()}</small></td>
                </tr>
            `;
        });
        tbody.innerHTML = html;
    } catch (e) {
        console.error('Error loading audit logs:', e);
    }
    <?php endif; ?>
}

// Add VLE
async function addVLE() {
    const form = document.getElementById('addVLEForm');
    const data = new FormData(form);
    const obj = Object.fromEntries(data);
    
    try {
        const result = await apiCall('add_user', obj);
        if (result.success) {
            showToast('VLE added successfully', 'success');
            bootstrap.Modal.getInstance(document.getElementById('addVLEModal')).hide();
            form.reset();
            loadVLEList();
            loadStats();
            // Log action
            apiCall('add_audit_log', { action: 'add_vle', details: `Added VLE: ${obj.name}` });
        }
    } catch (e) {
        console.error('Error adding VLE:', e);
    }
}

// Edit VLE
async function editVLE(userId) {
    // Load user data and show edit modal
    const users = await apiCall('get_users');
    const user = users[userId];
    if (!user) return;
    
    // Show a simple prompt for now
    const status = prompt('Set status (active/inactive):', user.status || 'active');
    if (status !== null) {
        try {
            await apiCall('update_user', { userId, status });
            showToast('VLE updated successfully', 'success');
            loadVLEList();
            loadStats();
            apiCall('add_audit_log', { action: 'update_vle', details: `Updated VLE: ${user.name}` });
        } catch (e) {
            console.error('Error updating VLE:', e);
        }
    }
}

// Delete VLE
async function deleteVLE(userId) {
    if (!confirm('Are you sure you want to delete this VLE?')) return;
    try {
        await apiCall('delete_user', { userId });
        showToast('VLE deleted successfully', 'success');
        loadVLEList();
        loadStats();
        apiCall('add_audit_log', { action: 'delete_vle', details: `Deleted VLE: ${userId}` });
    } catch (e) {
        console.error('Error deleting VLE:', e);
    }
}

// Add Service
async function addService() {
    const form = document.getElementById('addServiceForm');
    const data = new FormData(form);
    const obj = Object.fromEntries(data);
    
    try {
        const result = await apiCall('add_service', obj);
        if (result.success) {
            showToast('Service added successfully', 'success');
            bootstrap.Modal.getInstance(document.getElementById('addServiceModal')).hide();
            form.reset();
            loadServices();
            loadStats();
            apiCall('add_audit_log', { action: 'add_service', details: `Added service: ${obj.serviceName}` });
        }
    } catch (e) {
        console.error('Error adding service:', e);
    }
}

// Edit Service
async function editService(serviceId) {
    // Simple edit via prompt
    const services = await apiCall('get_services');
    const service = services[serviceId];
    if (!service) return;
    
    const price = prompt('Enter new price:', service.price || '0');
    if (price !== null) {
        try {
            await apiCall('update_service', { 
                serviceId, 
                price: parseFloat(price),
                status: service.status 
            });
            showToast('Service updated successfully', 'success');
            loadServices();
            loadStats();
            apiCall('add_audit_log', { action: 'update_service', details: `Updated service: ${service.serviceName}` });
        } catch (e) {
            console.error('Error updating service:', e);
        }
    }
}

// Delete Service
async function deleteService(serviceId) {
    if (!confirm('Are you sure you want to delete this service?')) return;
    try {
        await apiCall('delete_service', { serviceId });
        showToast('Service deleted successfully', 'success');
        loadServices();
        loadStats();
        apiCall('add_audit_log', { action: 'delete_service', details: `Deleted service: ${serviceId}` });
    } catch (e) {
        console.error('Error deleting service:', e);
    }
}

// Add Application
async function addApplication() {
    const form = document.getElementById('addApplicationForm');
    const data = new FormData(form);
    const obj = Object.fromEntries(data);
    obj.customerId = obj.customerId || 'cust_' + Date.now();
    
    try {
        const result = await apiCall('add_application', obj);
        if (result.success) {
            showToast('Application submitted successfully', 'success');
            bootstrap.Modal.getInstance(document.getElementById('addApplicationModal')).hide();
            form.reset();
            loadApplications();
            loadStats();
            apiCall('add_audit_log', { action: 'add_application', details: `Added application for ${obj.customerName}` });
        }
    } catch (e) {
        console.error('Error adding application:', e);
    }
}

// View Application
function viewApplication(appId) {
    showToast(`Viewing application: ${appId}`, 'info');
    // Could open a detailed view modal
}

// Show modals
function showAddVLEModal() {
    new bootstrap.Modal(document.getElementById('addVLEModal')).show();
}

function showAddServiceModal() {
    // Load VLE list for assignment
    apiCall('get_users').then(users => {
        const select = document.querySelector('#addServiceForm select[name="assignedVLE"]');
        if (select && users) {
            select.innerHTML = '<option value="all">All VLE</option>';
            Object.entries(users).forEach(([id, user]) => {
                if (user.role === 'vle') {
                    select.innerHTML += `<option value="${id}">${user.name || user.username}</option>`;
                }
            });
        }
    });
    new bootstrap.Modal(document.getElementById('addServiceModal')).show();
}

async function showAddApplicationModal() {
    // Load services for dropdown
    const services = await apiCall('get_services');
    const select = document.querySelector('#addApplicationForm select[name="serviceId"]');
    if (select && services) {
        select.innerHTML = '<option value="">Select Service...</option>';
        Object.entries(services).forEach(([id, service]) => {
            if (service.status !== 'inactive') {
                select.innerHTML += `<option value="${id}">${service.serviceName} - ₹${service.price}</option>`;
            }
        });
    }
    new bootstrap.Modal(document.getElementById('addApplicationModal')).show();
}

function showSendNotificationModal() {
    const title = prompt('Enter notification title:');
    if (!title) return;
    const message = prompt('Enter notification message:');
    if (!message) return;
    const priority = confirm('High priority?') ? 'high' : 'normal';
    
    apiCall('add_notification', { title, message, priority })
        .then(result => {
            if (result.success) {
                showToast('Notification sent successfully', 'success');
                loadNotifications();
                apiCall('add_audit_log', { action: 'send_notification', details: `Sent: ${title}` });
            }
        })
        .catch(e => console.error('Error sending notification:', e));
}

// Refresh data
function refreshData() {
    loadStats();
    loadApplications();
    if (currentSection === 'vle-management') loadVLEList();
    if (currentSection === 'service-management' || currentSection === 'services') loadServices();
    if (currentSection === 'notifications') loadNotifications();
    if (currentSection === 'payments') loadPayments();
    if (currentSection === 'audit-logs') loadAuditLogs();
    showToast('Data refreshed', 'info');
}

// Real-time sync with Firebase
function setupRealtimeSync() {
    // Listen for changes in applications
    const appsRef = database.ref('applications');
    appsRef.on('value', (snapshot) => {
        const data = snapshot.val();
        if (data) {
            loadApplications();
            loadStats();
        }
    });
    
    // Listen for changes in services
    const servicesRef = database.ref('services');
    servicesRef.on('value', (snapshot) => {
        const data = snapshot.val();
        if (data) {
            if (currentSection === 'service-management' || currentSection === 'services') {
                loadServices();
            }
            loadStats();
        }
    });
    
    // Listen for changes in users
    const usersRef = database.ref('users');
    usersRef.on('value', (snapshot) => {
        const data = snapshot.val();
        if (data) {
            if (currentSection === 'vle-management') {
                loadVLEList();
            }
            loadStats();
        }
    });
    
    // Listen for notifications
    const notifRef = database.ref('notifications');
    notifRef.on('value', (snapshot) => {
        const data = snapshot.val();
        if (data) {
            if (currentSection === 'notifications') {
                loadNotifications();
            }
            // Update notification count
            const count = Object.keys(data).length;
            document.getElementById('notif-count').textContent = count;
        }
    });
    
    // Listen for payments
    const paymentsRef = database.ref('payments');
    paymentsRef.on('value', (snapshot) => {
        if (currentSection === 'payments') {
            loadPayments();
        }
        loadStats();
    });
}

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    // Load initial data
    loadStats();
    loadApplications();
    
    // Setup real-time sync
    setupRealtimeSync();
    
    // Auto-refresh every 30 seconds
    setInterval(refreshData, 30000);
    
    // Show section if coming from URL hash
    const hash = window.location.hash.slice(1);
    if (hash && hash !== 'dashboard') {
        showSection(hash);
    }
});

// Handle section navigation with hash
window.addEventListener('hashchange', function() {
    const hash = window.location.hash.slice(1);
    if (hash && hash !== 'dashboard') {
        showSection(hash);
    } else {
        document.getElementById('dashboard-content').style.display = 'block';
        document.getElementById('section-content').style.display = 'none';
    }
});
</script>

<!-- Firebase SDK -->
<script src="https://www.gstatic.com/firebasejs/9.6.1/firebase-app-compat.js"></script>
<script src="https://www.gstatic.com/firebasejs/9.6.1/firebase-database-compat.js"></script>

</body>
</html>
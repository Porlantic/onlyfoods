<?php
session_start();
require_once 'config.php';

header('Content-Type: application/json');

// Get inputs
$email = $_POST['login_email'] ?? '';
$password = $_POST['login_password'] ?? '';

// Validate
if (empty($email) || empty($password)) {
    echo json_encode([
        "success" => false,
        "message" => "Email and password are required."
    ]);
    exit();
}

// Query user
$sql = "SELECT * FROM users WHERE email = ?";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    echo json_encode([
        "success" => false,
        "message" => "Database error"
    ]);
    exit();
}

$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

// Check user
if ($result && $result->num_rows > 0) {
    $user = $result->fetch_assoc();

    // ✅ Plain text password check (prototype only)
    if ($password === $user['password']) {

        // Set session
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_role'] = $user['role'];

        // ✅ Role-based redirect
        if ($user['role'] === 'admin') {
            $redirect = "admin.php";
        } elseif ($user['role'] === 'user') {
            $redirect = "user.php";
        } else {
            $redirect = "index.php"; // fallback
        }

        echo json_encode([
            "success" => true,
            "redirect" => $redirect
        ]);
        exit();

    } else {
        echo json_encode([
            "success" => false,
            "message" => "Invalid credentials"
        ]);
        exit();
    }

} else {
    echo json_encode([
        "success" => false,
        "message" => "Invalid credentials"
    ]);
    exit();
}
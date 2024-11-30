<?php
session_start();

function login($username, $password) {
    $conn = getDBConnection();
    $stmt = $conn->prepare("SELECT * FROM Users WHERE username = ? AND password = ?");
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();
    if ($user) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];
        return true;
    }
    return false;
}

function checkRole($role) {
    return isset($_SESSION['role']) && $_SESSION['role'] === $role;
}

function logout() {
    session_destroy();
    header("Location: login.php");
    exit();
}
?>

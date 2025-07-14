<?php
session_start();

function login($username, $password) {
    $conn = getDBConnection();
    $stmt = $conn->prepare("SELECT * FROM Users WHERE username = ? AND password = ?");
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();
    if ($user) {
        $_SESSION['user'] = $user;
        return true;
    }
    return false;
}

function checkRole($role) {
    return isset($_SESSION['user']) && $_SESSION['user']['role'] === $role;
}

function logout() {
    session_destroy();
    header("Location: login.php");
    exit();
}
?>

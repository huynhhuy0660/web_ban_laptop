<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
function registerUser($conn, $username, $email, $password, $fullname) {
    $stmt_check = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
    $stmt_check->bind_param("ss", $username, $email);
    $stmt_check->execute();
    $stmt_check->store_result();
    
    if ($stmt_check->num_rows > 0) {
        $stmt_check->close();
        return ["status" => "error", "message" => "Tên đăng nhập hoặc Email đã được sử dụng."];
    }
    $stmt_check->close();

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $stmt_insert = $conn->prepare("INSERT INTO users (username, email, password, fullname, role) VALUES (?, ?, ?, ?, 0)");
    $stmt_insert->bind_param("ssss", $username, $email, $hashed_password, $fullname);
    
    if ($stmt_insert->execute()) {
        $stmt_insert->close();
        return ["status" => "success", "message" => "Đăng ký thành công! Vui lòng đăng nhập."];
    } else {
        $stmt_insert->close();
        return ["status" => "error", "message" => "Lỗi đăng ký: " . $conn->error];
    }
}
function loginUser($conn, $login_id, $password) {
    $stmt = $conn->prepare("SELECT id, username, fullname, password, role FROM users WHERE username = ? OR email = ?");
    $stmt->bind_param("ss", $login_id, $login_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['full_name'] = $user['fullname']; 
            $_SESSION['role'] = $user['role']; 
            $stmt->close();
            return ["status" => "success", "message" => "Đăng nhập thành công!"];
        } else {
            $stmt->close();
            return ["status" => "error", "message" => "Tên đăng nhập/Email hoặc Mật khẩu không đúng."];
        }
    } else {
        $stmt->close();
        return ["status" => "error", "message" => "Tên đăng nhập/Email hoặc Mật khẩu không đúng."];
    }
}
function forgotPassword($conn, $email) {
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows == 1) {
        $stmt->close();
        return [
            "status" => "success", 
            "message" => "Hướng dẫn khôi phục mật khẩu đã được gửi đến Email của bạn (Giả lập)."
        ];
    } else {
        $stmt->close();
        return [
            "status" => "error", 
            "message" => "Email này không tồn tại trong hệ thống."
        ];
    }
}
function isAuthenticated() {
    return isset($_SESSION['user_id']);
}
?>
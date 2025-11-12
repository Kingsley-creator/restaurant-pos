<?php
// Secure login session check using PDO (PostgreSQL)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once(__DIR__ . '/config.php');

// Function to ensure customer is logged in
function check_login()
{
    if (!isset($_SESSION['customer_id'])) {
        header("Location: login.php");
        exit();
    }
}

// Function to handle login form submission
function login_customer($email, $password)
{
    global $pdo;

    try {
        $stmt = $pdo->prepare("SELECT * FROM rpos_customers WHERE customer_email = :email LIMIT 1");
        $stmt->execute(['email' => $email]);
        $customer = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($customer && password_verify($password, $customer['customer_password'])) {
            $_SESSION['customer_id'] = $customer['customer_id'];
            $_SESSION['customer_name'] = $customer['customer_name'];
            header("Location: index.php");
            exit();
        } else {
            return "Invalid email or password.";
        }
    } catch (PDOException $e) {
        return "Database error: " . htmlspecialchars($e->getMessage());
    }
}
?>

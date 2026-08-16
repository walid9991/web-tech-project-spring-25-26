<?php
session_start();
require_once __DIR__ . '/../includes/db_connect.php';

$error = '';

// Only run login logic on POST
if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if($email && $password){
        $stmt = $mysqli->prepare("SELECT id,name,password_hash,role,is_active FROM users WHERE email=? LIMIT 1");
        $stmt->bind_param("s",$email);
        $stmt->execute();
        $stmt->store_result();

        if($stmt->num_rows === 1){
            $stmt->bind_result($id,$name,$hash,$role,$is_active);
            $stmt->fetch();

            if(!$is_active){
                $error = "Account is inactive.";
            } elseif($password === $hash){
                // Successful login
                $_SESSION['user_id'] = $id;
                $_SESSION['name'] = $name;
                $_SESSION['role'] = $role;

                // Redirect based on role
                switch($role){
                    case 'admin':
                        header("Location: AdminController.php?action=dashboard");
                        exit();
                    case 'member':
                        header("Location: MemberController.php?action=dashboard");
                        exit();
                    case 'client':
                        header("Location: ClientController.php?action=dashboard");
                        exit();
                    case 'team_lead':
                        header("Location: TeamLeadController.php?action=dashboard");
                        exit();
                    default:
                        $error = "Invalid role.";
                }

            } else {
                $error = "Incorrect password.";
            }

        } else {
            $error = "Email not found.";
        }

        $stmt->close();
    } else {
        $error = "Please fill all fields.";
    }
}

// Include the login view
include __DIR__ . '/../views/login.php';
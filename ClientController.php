<?php
require_once __DIR__ . '/../includes/VerifyController.php';
require_once __DIR__ . '/../models/ClientModel.php';

session_start();
if(!isset($_SESSION['user_id']) || $_SESSION['role']!=='client'){
    header("Location: ../views/login.php");
    exit();
}

$client_id = $_SESSION['user_id'];
$model = new ClientModel($mysqli);

// ------------------- Dashboard -------------------
if($_GET['action']=='dashboard'){
    $projects = $model->getAssignedProjects($client_id);
    include __DIR__ . '/../views/client/dashboard.php';
    exit();
}

// ------------------- Profile -------------------
if($_GET['action']=='profile'){
    $profile = $model->getProfile($client_id);
    include __DIR__ . '/../views/client/profile.php';
    exit();
}

if($_GET['action']=='update_profile' && $_SERVER['REQUEST_METHOD']=='POST'){
    $name = $_POST['name'] ?? '';
    $company = $_POST['company_name'] ?? '';
    if($model->updateProfile($client_id,$name,$company)) $msg="Profile updated successfully.";
    else $msg="Failed to update profile.";
    $profile = $model->getProfile($client_id);
    include __DIR__ . '/../views/client/profile.php';
    exit();
}

if($_GET['action']=='change_password' && $_SERVER['REQUEST_METHOD']=='POST'){
    $current = $_POST['current'] ?? '';
    $new = $_POST['new'] ?? '';
    $confirm = $_POST['confirm'] ?? '';
    $profile = $model->getProfile($client_id);

    if($new !== $confirm){
        $msg = "New passwords do not match.";
    } elseif(!password_verify($current,$profile['password_hash'])){
        $msg = "Current password incorrect.";
    } else {
        $hash = password_hash($new,PASSWORD_BCRYPT);
        if($model->changePassword($client_id,$hash)) $msg="Password changed successfully.";
        else $msg="Failed to change password.";
    }
    include __DIR__ . '/../views/client/profile.php';
    exit();
}

// ------------------- Projects -------------------
if($_GET['action']=='projects'){
    $projects = $model->getAssignedProjects($client_id);
    include __DIR__ . '/../views/client/projects.php';
    exit();
}

// ------------------- Milestones -------------------
if($_GET['action']=='milestones'){
    $project_id = intval($_GET['project_id'] ?? 0);
    $milestones = $model->getProjectMilestones($project_id);
    include __DIR__ . '/../views/client/milestones.php';
    exit();
}

if($_GET['action']=='milestone_detail'){
    $milestone_id = intval($_GET['milestone_id'] ?? 0);
    $milestone = $model->getMilestoneDetail($milestone_id);
    $feedback = $model->getFeedback($milestone_id,$client_id);
    include __DIR__ . '/../views/client/milestone_detail.php';
    exit();
}

if($_GET['action']=='submit_feedback' && $_SERVER['REQUEST_METHOD']=='POST'){
    $milestone_id = intval($_POST['milestone_id'] ?? 0);
    $text = $_POST['feedback_text'] ?? '';
    $status = $_POST['approval_status'] ?? 'pending';
    $success = $model->submitFeedback($milestone_id,$client_id,$text,$status);
    header('Content-Type: application/json');
    echo json_encode(['success'=>$success]);
    exit();
}

// ------------------- Task Board (Read-only) -------------------
if($_GET['action']=='task_board'){
    $project_id = intval($_GET['project_id'] ?? 0);
    $tasks = $model->getProjectTasks($project_id);
    include __DIR__ . '/../views/client/task_board.php';
    exit();
}

// ------------------- Project Messages -------------------
if($_GET['action']=='messages'){
    $project_id = intval($_GET['project_id'] ?? 0);
    $messages = $model->getProjectMessages($project_id);
    include __DIR__ . '/../views/client/messages.php';
    exit();
}

if($_GET['action']=='post_message' && $_SERVER['REQUEST_METHOD']=='POST'){
    $project_id = intval($_POST['project_id'] ?? 0);
    $body = $_POST['message'] ?? '';
    $success = $model->postMessage($project_id,$client_id,$body);
    header('Content-Type: application/json');
    echo json_encode(['success'=>$success]);
    exit();
}

// ------------------- Files -------------------
if($_GET['action']=='files'){
    $project_id = intval($_GET['project_id'] ?? 0);
    $files = $model->getProjectFiles($project_id);
    include __DIR__ . '/../views/client/files.php';
    exit();
}

// ------------------- Project Time Summary -------------------
if($_GET['action']=='time_summary'){
    $project_id = intval($_GET['project_id'] ?? 0);
    $total_hours = $model->getProjectTimeSummary($project_id);
    include __DIR__ . '/../views/client/time_summary.php';
    exit();
}
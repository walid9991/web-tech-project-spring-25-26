<?php
require_once __DIR__ . '/../includes/VerifyController.php'; // Already starts session
require_once __DIR__ . '/../models/MemberModel.php';

// Ensure user is logged in and role is 'member'
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'member') {
    header("Location: ../views/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$model = new MemberModel($mysqli);

// ------------------- Dashboard -------------------
if ($_GET['action'] == 'dashboard') {
    $workspaces = $model->getJoinedWorkspaces($user_id);
    include __DIR__ . '/../views/member/dashboard.php';
    exit();
}

// ------------------- Profile -------------------
if ($_GET['action'] == 'profile') {
    $profile = $model->getProfile($user_id);
    include __DIR__ . '/../views/member/profile.php';
    exit();
}

// Update profile
if ($_GET['action'] == 'update_profile' && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $msg = $model->updateProfile($user_id, $name, $phone)
        ? "Profile updated successfully."
        : "Failed to update profile.";
    $profile = $model->getProfile($user_id);
    include __DIR__ . '/../views/member/profile.php';
    exit();
}

// Change password
if ($_GET['action'] == 'change_password' && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $current = $_POST['current'] ?? '';
    $new = $_POST['new'] ?? '';
    $confirm = $_POST['confirm'] ?? '';

    $profile = $model->getProfile($user_id);
    if ($new !== $confirm) {
        $msg = "New passwords do not match.";
    } elseif (!password_verify($current, $profile['password_hash'])) {
        $msg = "Current password incorrect.";
    } else {
        $hash = password_hash($new, PASSWORD_BCRYPT);
        $msg = $model->changePassword($user_id, $hash)
            ? "Password changed successfully."
            : "Failed to change password.";
    }
    include __DIR__ . '/../views/member/profile.php';
    exit();
}

// ------------------- Join Workspace -------------------
if ($_GET['action'] == 'join_workspace' && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $code = $_POST['invite_code'] ?? '';
    $msg = $model->joinWorkspace($user_id, $code)
        ? "Joined workspace successfully."
        : "Failed to join workspace or already joined.";
    $workspaces = $model->getJoinedWorkspaces($user_id);
    include __DIR__ . '/../views/member/dashboard.php';
    exit();
}

// ------------------- Projects -------------------
if ($_GET['action'] == 'projects') {
    $workspace_id = intval($_GET['workspace_id'] ?? 0);
    $projects = $model->getAssignedProjects($user_id, $workspace_id);
    include __DIR__ . '/../views/member/projects.php';
    exit();
}

// ------------------- Task Board -------------------
if ($_GET['action'] == 'task_board') {
    $project_id = intval($_GET['project_id'] ?? 0);
    $tasks = $model->getTasksByProject($project_id, $user_id);
    include __DIR__ . '/../views/member/task_board.php';
    exit();
}

// Update task status (AJAX)
if ($_GET['action'] == 'update_task_status' && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $task_id = intval($_POST['task_id'] ?? 0);
    $status = $_POST['status'] ?? '';
    header('Content-Type: application/json');
    echo json_encode(['success' => $model->updateTaskStatus($task_id, $status)]);
    exit();
}

// Flag/unflag task (AJAX)
if ($_GET['action'] == 'flag_task' && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $task_id = intval($_POST['task_id'] ?? 0);
    $reason = $_POST['reason'] ?? '';
    header('Content-Type: application/json');
    echo json_encode(['success' => $model->flagTaskBlocked($task_id, $reason)]);
    exit();
}

if ($_GET['action'] == 'unflag_task' && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $task_id = intval($_POST['task_id'] ?? 0);
    header('Content-Type: application/json');
    echo json_encode(['success' => $model->unflagTaskBlocked($task_id)]);
    exit();
}

// ------------------- Task Detail -------------------
if ($_GET['action'] == 'task_detail') {
    $task_id = intval($_GET['task_id'] ?? 0);
    $task = $model->getTaskDetail($task_id);
    $attachments = $model->getTaskAttachments($task_id);
    $comments = $model->getTaskComments($task_id);
    include __DIR__ . '/../views/member/task_detail.php';
    exit();
}

// ------------------- Comments -------------------
if ($_GET['action'] == 'add_comment' && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $task_id = intval($_POST['task_id'] ?? 0);
    $body = $_POST['body'] ?? '';
    header('Content-Type: application/json');
    echo json_encode(['success' => $model->addComment($task_id, $user_id, $body, 1)]);
    exit();
}

if ($_GET['action'] == 'delete_comment' && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $comment_id = intval($_POST['comment_id'] ?? 0);
    header('Content-Type: application/json');
    echo json_encode(['success' => $model->deleteComment($comment_id, $user_id)]);
    exit();
}

// ------------------- Time Logs -------------------
if ($_GET['action'] == 'log_time' && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $task_id = intval($_POST['task_id'] ?? 0);
    $hours = floatval($_POST['hours'] ?? 0);
    $note = $_POST['note'] ?? '';
    header('Content-Type: application/json');
    echo json_encode(['success' => $model->logTime($task_id, $user_id, $hours, $note)]);
    exit();
}

// ------------------- Notifications -------------------
if ($_GET['action'] == 'notifications') {
    $notes = $model->getNotifications($user_id);
    include __DIR__ . '/../views/member/notifications.php';
    exit();
}

if ($_GET['action'] == 'mark_notification' && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $note_id = intval($_POST['note_id'] ?? 0);
    header('Content-Type: application/json');
    echo json_encode(['success' => $model->markNotificationRead($note_id)]);
    exit();
}

// ------------------- Logout -------------------
if ($_GET['action'] == 'logout') {
    session_destroy();
    header("Location: ../views/login.php");
    exit();
}
<?php
require_once __DIR__ . '/../includes/VerifyController.php';
require_once __DIR__ . '/../models/TeamLeadModel.php';


if(!isset($_SESSION['user_id']) || $_SESSION['role']!=='team_lead'){
    header("Location: ../views/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$model = new TeamLeadModel($mysqli);

// ------------------- Dashboard -------------------
if($_GET['action']=='dashboard'){
    $projects = $model->getLeadProjects($user_id);
    include __DIR__ . '/../views/team_lead/dashboard.php';
    exit();
}

// ------------------- Profile -------------------
if($_GET['action']=='profile'){
    $profile = $model->getProfile($user_id);
    include __DIR__ . '/../views/team_lead/profile.php';
    exit();
}

if($_GET['action']=='update_profile' && $_SERVER['REQUEST_METHOD']=='POST'){
    $name = $_POST['name'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $success = $model->updateProfile($user_id,$name,$phone);
    $msg = $success ? "Profile updated successfully." : "Failed to update profile.";
    $profile = $model->getProfile($user_id);
    include __DIR__ . '/../views/team_lead/profile.php';
    exit();
}

if($_GET['action']=='change_password' && $_SERVER['REQUEST_METHOD']=='POST'){
    $current = $_POST['current'] ?? '';
    $new = $_POST['new'] ?? '';
    $confirm = $_POST['confirm'] ?? '';

    $profile = $model->getProfile($user_id);

    if($new !== $confirm){
        $msg = "New passwords do not match.";
    } elseif(!password_verify($current,$profile['password_hash'])){
        $msg = "Current password incorrect.";
    } else {
        $hash = password_hash($new,PASSWORD_BCRYPT);
        $success = $model->changePassword($user_id,$hash);
        $msg = $success ? "Password changed successfully." : "Failed to change password.";
    }
    include __DIR__ . '/../views/team_lead/profile.php';
    exit();
}

// ------------------- Projects -------------------
if($_GET['action']=='projects'){
    $projects = $model->getLeadProjects($user_id);
    include __DIR__ . '/../views/team_lead/projects.php';
    exit();
}

// ------------------- Create Project -------------------
if($_GET['action']=='create_project' && $_SERVER['REQUEST_METHOD']=='POST'){
    $workspace_id = intval($_POST['workspace_id']);
    $name = $_POST['name'] ?? '';
    $desc = $_POST['description'] ?? '';
    $client_id = intval($_POST['client_id']);
    $deadline = $_POST['deadline'] ?? '';
    $color = $_POST['color_label'] ?? '';
    $status = $_POST['status'] ?? 'planning';
    $visibility = $_POST['visibility'] ?? 'internal';

    $project_id = $model->createProject($workspace_id,$name,$desc,$client_id,$deadline,$color,$status,$visibility);

    // Assign members
    $member_ids = $_POST['members'] ?? [];
    $model->assignProjectMembers($project_id,$member_ids);

    header("Location: TeamLeadController.php?action=projects");
    exit();
}

// ------------------- Tasks -------------------
if($_GET['action']=='tasks'){
    $project_id = intval($_GET['project_id'] ?? 0);
    $tasks = $model->getProjectTasks($project_id);
    include __DIR__ . '/../views/team_lead/tasks.php';
    exit();
}

if($_GET['action']=='create_task' && $_SERVER['REQUEST_METHOD']=='POST'){
    $project_id = intval($_POST['project_id']);
    $title = $_POST['title'] ?? '';
    $desc = $_POST['description'] ?? '';
    $assigned_to = intval($_POST['assigned_to']);
    $priority = $_POST['priority'] ?? 'medium';
    $due_date = $_POST['due_date'] ?? '';
    $estimated_hours = floatval($_POST['estimated_hours'] ?? 0);
    $milestone_id = intval($_POST['milestone_id'] ?? 0);

    $model->createTask($project_id,$title,$desc,$assigned_to,$priority,$due_date,$estimated_hours,$milestone_id);
    header("Location: TeamLeadController.php?action=tasks&project_id=$project_id");
    exit();
}

if($_GET['action']=='update_task' && $_SERVER['REQUEST_METHOD']=='POST'){
    $task_id = intval($_POST['task_id']);
    $title = $_POST['title'] ?? '';
    $desc = $_POST['description'] ?? '';
    $assigned_to = intval($_POST['assigned_to']);
    $priority = $_POST['priority'] ?? 'medium';
    $due_date = $_POST['due_date'] ?? '';
    $status = $_POST['status'] ?? 'todo';

    $model->updateTask($task_id,$title,$desc,$assigned_to,$priority,$due_date,$status);
    header("Location: TeamLeadController.php?action=tasks&project_id=".intval($_POST['project_id']));
    exit();
}

if($_GET['action']=='delete_task' && $_SERVER['REQUEST_METHOD']=='POST'){
    $task_id = intval($_POST['task_id']);
    $model->deleteTask($task_id);
    header('Content-Type: application/json');
    echo json_encode(['success'=>true]);
    exit();
}

// ------------------- Milestones -------------------
if($_GET['action']=='milestones'){
    $project_id = intval($_GET['project_id'] ?? 0);
    $stmt = $model->getLeadProjects($user_id);
    $milestones = $model->getProjectMilestones($project_id);
    include __DIR__ . '/../views/team_lead/milestones.php';
    exit();
}

if($_GET['action']=='create_milestone' && $_SERVER['REQUEST_METHOD']=='POST'){
    $project_id = intval($_POST['project_id']);
    $title = $_POST['title'] ?? '';
    $desc = $_POST['description'] ?? '';
    $due_date = $_POST['due_date'] ?? '';
    $client_visible = isset($_POST['is_client_visible']) ? 1 : 0;

    $model->createMilestone($project_id,$title,$desc,$due_date,$client_visible);
    header("Location: TeamLeadController.php?action=milestones&project_id=$project_id");
    exit();
}

if($_GET['action']=='complete_milestone' && $_SERVER['REQUEST_METHOD']=='POST'){
    $milestone_id = intval($_POST['milestone_id']);
    $model->markMilestoneCompleted($milestone_id);
    header('Content-Type: application/json');
    echo json_encode(['success'=>true]);
    exit();
}

// ------------------- Project Members -------------------
if($_GET['action']=='project_members'){
    $project_id = intval($_GET['project_id'] ?? 0);
    $members = $model->getProjectMembers($project_id);
    include __DIR__ . '/../views/team_lead/project_members.php';
    exit();
}

// ------------------- Comments -------------------
if($_GET['action']=='comments'){
    $project_id = intval($_GET['project_id'] ?? 0);
    $comments = $model->getProjectComments($project_id);
    include __DIR__ . '/../views/team_lead/comments.php';
    exit();
}

// ------------------- Analytics -------------------
if($_GET['action']=='analytics'){
    $project_id = intval($_GET['project_id'] ?? 0);
    $analytics = $model->getProjectAnalytics($project_id);
    include __DIR__ . '/../views/team_lead/analytics.php';
    exit();
}

// ------------------- Files -------------------
if($_GET['action']=='files'){
    $project_id = intval($_GET['project_id'] ?? 0);
    $files = $model->getProjectFiles($project_id);
    include __DIR__ . '/../views/team_lead/files.php';
    exit();
}

// ------------------- Time Report -------------------
if($_GET['action']=='time_report'){
    $project_id = intval($_GET['project_id'] ?? 0);
    $time_report = $model->getProjectTimeReport($project_id);
    include __DIR__ . '/../views/team_lead/time_report.php';
    exit();
}
if($_GET['action']=='create_workspace' && $_SERVER['REQUEST_METHOD']=='POST'){
    $name = $_POST['name'] ?? '';
    $desc = $_POST['description'] ?? '';
    $id = $model->createWorkspace($name, $desc);
    header("Location: TeamLeadController.php?action=dashboard");
    exit();
}
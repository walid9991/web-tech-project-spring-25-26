<?php
require_once __DIR__ . '/../includes/VerifyController.php';
require_once __DIR__ . '/../models/AdminModel.php';
checkAdmin(); 

$model = new AdminModel($mysqli);

if ($_GET['action']=='dashboard') {
    $totalWorkspaces = $model->getTotalWorkspaces();
    $totalAdmins = $model->getTotalUsersByRole('admin');
    $totalTeamLeads = $model->getTotalUsersByRole('team_lead');
    $totalMembers = $model->getTotalUsersByRole('member');
    $totalClients = $model->getTotalUsersByRole('client');
    $totalProjects = $model->getTotalActiveProjects();
    $tasksToday = $model->getTasksCreatedToday();
    include __DIR__ . '/../views/admin/dashboard.php';
    exit();
}


if ($_GET['action']=='workspaces') {
    $search = $_GET['search'] ?? '';
    $workspaces = $model->getWorkspaces($search);
    include __DIR__ . '/../views/admin/workspaces.php';
    exit();
}

if ($_GET['action']=='workspace_action') {
    header('Content-Type: application/json');
    $type = $_POST['type'] ?? '';
    $id = intval($_POST['id'] ?? 0);
    $response = ['success'=>false,'message'=>'Invalid request'];
    switch($type){
        case 'activate':
            $response=$model->setWorkspaceStatus($id,1)?['success'=>true,'message'=>'Workspace activated']:['success'=>false,'message'=>'Failed to activate'];
            break;
        case 'deactivate':
            $response=$model->setWorkspaceStatus($id,0)?['success'=>true,'message'=>'Workspace deactivated']:['success'=>false,'message'=>'Failed to deactivate'];
            break;
        case 'delete':
            $response=$model->deleteWorkspace($id)?['success'=>true,'message'=>'Workspace deleted']:['success'=>false,'message'=>'Cannot delete workspace with projects'];
            break;
        case 'remove_member':
            $response=$model->removeWorkspaceMember($id)?['success'=>true,'message'=>'Member removed']:['success'=>false,'message'=>'Failed to remove member'];
            break;
    }
    echo json_encode($response);
    exit();
}

if ($_GET['action']=='view_members') {
    $workspace_id=intval($_GET['workspace_id']??0);
    $members=$model->getWorkspaceMembers($workspace_id);
    include __DIR__ . '/../views/admin/workspace_members.php';
    exit();
}


if ($_GET['action']=='users') {
    $search=$_GET['search']??'';
    $users=$model->getUsers($search);
    include __DIR__ . '/../views/admin/users.php';
    exit();
}

if ($_GET['action']=='user_action') {
    header('Content-Type: application/json');
    $type=$_POST['type']??'';
    $id=intval($_POST['id']??0);
    $response=['success'=>false,'message'=>'Invalid request'];
    switch($type){
        case 'activate':
            $response=$model->setUserStatus($id,1)?['success'=>true,'message'=>'User activated']:['success'=>false,'message'=>'Failed to activate'];
            break;
        case 'deactivate':
            $response=$model->setUserStatus($id,0)?['success'=>true,'message'=>'User deactivated']:['success'=>false,'message'=>'Failed to deactivate'];
            break;
        case 'change_role':
            $new_role=$_POST['role']??'';
            $response=$model->changeUserRole($id,$new_role)?['success'=>true,'message'=>'Role updated']:['success'=>false,'message'=>'Failed to update role'];
            break;
        case 'create_admin':
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';  // keep plain text
    $response = $model->createAdminUser($name, $email, $password)
        ? ['success' => true, 'message' => 'Admin created']
        : ['success' => false, 'message' => 'Failed to create admin'];
    break;
    }
    echo json_encode($response);
    exit();
}


if ($_GET['action']=='projects') {
    $workspace_id=intval($_GET['workspace_id']??0);
    $status=$_GET['status']??'';
    $team_lead_id=intval($_GET['team_lead_id']??0);
    $projects=$model->getAllProjects($workspace_id,$status,$team_lead_id);
    include __DIR__ . '/../views/admin/projects.php';
    exit();
}

if ($_GET['action']=='tasks') {
    $status=$_GET['status']??'';
    $priority=$_GET['priority']??'';
    $assigned_to=intval($_GET['assigned_to']??0);
    $tasks=$model->getAllTasks($status,$priority,$assigned_to);
    include __DIR__ . '/../views/admin/tasks.php';
    exit();
}

if ($_GET['action']=='task_action') {
    header('Content-Type: application/json');
    $type=$_POST['type']??'';
    $id=intval($_POST['id']??0);
    $response=['success'=>false,'message'=>'Invalid request'];
    if($type=='delete'){
        $response=$model->deleteTask($id)?['success'=>true,'message'=>'Task deleted']:['success'=>false,'message'=>'Failed to delete task'];
    }
    echo json_encode($response);
    exit();
}

// ------------------- Activity Logs -------------------
if ($_GET['action']=='activity_logs') {
    $date_from=$_GET['date_from']??'';
    $date_to=$_GET['date_to']??'';
    $action_type=$_GET['action_type']??'';
    $logs=$model->getActivityLogs($date_from,$date_to,$action_type);
    include __DIR__ . '/../views/admin/activity_logs.php';
    exit();
}

// ------------------- Support Tickets -------------------
if ($_GET['action']=='support_tickets') {
    $tickets=$model->getSupportTickets();
    include __DIR__ . '/../views/admin/support_tickets.php';
    exit();
}

if ($_GET['action']=='support_ticket_action') {
    header('Content-Type: application/json');
    $ticket_id=intval($_POST['ticket_id']??0);
    $admin_note=$_POST['admin_note']??'';
    $status=$_POST['status']??'';
    $success=false;
    if($ticket_id>0 && $status!='') $success=$model->updateSupportTicket($ticket_id,$admin_note,$status);
    echo json_encode(['success'=>$success]);
    exit();
}


if ($_GET['action']=='analytics') {
    $analytics=$model->getAnalytics();
    include __DIR__ . '/../views/admin/analytics.php';
    exit();
}


if ($_GET['action']=='announcements') {
    $announcements=$model->getAnnouncements();
    include __DIR__ . '/../views/admin/announcements.php';
    exit();
}

if ($_GET['action']=='announcement_action') {
    header('Content-Type: application/json');
    $title=$_POST['title']??'';
    $message=$_POST['message']??'';
    $success=false;
    if($title && $message) $success=$model->createAnnouncement($title,$message);
    echo json_encode(['success'=>$success]);
    exit();
}


if ($_GET['action']=='monthly_report') {
    $month=intval($_GET['month']??date('m'));
    $year=intval($_GET['year']??date('Y'));
    $report=$model->generateMonthlyReport($month,$year);
    include __DIR__ . '/../views/admin/monthly_report.php';
    exit();
}


if ($_GET['action']=='workspace_usage') {
    $usage=$model->getWorkspaceUsage();
    include __DIR__ . '/../views/admin/workspace_usage.php';
    exit();
}


if ($_GET['action']=='plan_limits') {
    $limits=$model->getPlanLimits();
    include __DIR__ . '/../views/admin/plan_limits.php';
    exit();
}

if ($_GET['action']=='plan_limit_action') {
    header('Content-Type: application/json');
    $plan_id=intval($_POST['plan_id']??0);
    $max_projects=intval($_POST['max_projects']??0);
    $success=false;
    if($plan_id>0 && $max_projects>0) $success=$model->updatePlanLimit($plan_id,$max_projects);
    echo json_encode(['success'=>$success]);
    exit();
}


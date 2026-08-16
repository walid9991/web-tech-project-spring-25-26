<?php
require_once __DIR__ . '/../includes/db_connect.php';

class AdminModel {
    private $db;

    public function __construct($mysqli){
        $this->db = $mysqli;
    }

    
    public function getTotalWorkspaces() {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM workspaces");
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();
        $stmt->close();
        return $count;
    }

    public function getTotalUsersByRole($role) {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM users WHERE role=?");
        $stmt->bind_param("s",$role);
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();
        $stmt->close();
        return $count;
    }

    public function getTotalActiveProjects() {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM projects WHERE status='active'");
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();
        $stmt->close();
        return $count;
    }

    public function getTasksCreatedToday() {
        $today = date("Y-m-d");
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM tasks WHERE DATE(created_at)=?");
        $stmt->bind_param("s",$today);
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();
        $stmt->close();
        return $count;
    }

    
    public function getWorkspaces($search="") {
        $search="%$search%";
        $stmt=$this->db->prepare("SELECT * FROM workspaces WHERE name LIKE ?");
        $stmt->bind_param("s",$search);
        $stmt->execute();
        $result=$stmt->get_result();
        $workspaces=$result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $workspaces;
    }

    public function setWorkspaceStatus($workspace_id,$status){
        $stmt=$this->db->prepare("UPDATE workspaces SET is_active=? WHERE id=?");
        $stmt->bind_param("ii",$status,$workspace_id);
        $stmt->execute();
        $affected=$stmt->affected_rows;
        $stmt->close();
        return $affected>0;
    }

    public function deleteWorkspace($workspace_id){
        
        $stmtCheck=$this->db->prepare("SELECT COUNT(*) FROM projects WHERE workspace_id=?");
        $stmtCheck->bind_param("i",$workspace_id);
        $stmtCheck->execute();
        $stmtCheck->bind_result($count);
        $stmtCheck->fetch();
        $stmtCheck->close();
        if($count>0) return false;

        $stmt=$this->db->prepare("DELETE FROM workspaces WHERE id=?");
        $stmt->bind_param("i",$workspace_id);
        $stmt->execute();
        $affected=$stmt->affected_rows;
        $stmt->close();
        return $affected>0;
    }

    public function getWorkspaceMembers($workspace_id){
        $stmt=$this->db->prepare("
            SELECT wm.id,u.name,u.email,wm.workspace_role,wm.joined_at
            FROM workspace_members wm
            JOIN users u ON wm.user_id=u.id
            WHERE wm.workspace_id=?
        ");
        $stmt->bind_param("i",$workspace_id);
        $stmt->execute();
        $result=$stmt->get_result();
        $members=$result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $members;
    }

    public function removeWorkspaceMember($member_id){
        $stmt=$this->db->prepare("DELETE FROM workspace_members WHERE id=?");
        $stmt->bind_param("i",$member_id);
        $stmt->execute();
        $affected=$stmt->affected_rows;
        $stmt->close();
        return $affected>0;
    }

    
    public function getUsers($search=""){
        $search="%$search%";
        $stmt=$this->db->prepare("SELECT id,name,email,role,is_active,created_at FROM users WHERE name LIKE ? OR email LIKE ? ORDER BY created_at DESC");
        $stmt->bind_param("ss",$search,$search);
        $stmt->execute();
        $result=$stmt->get_result();
        $users=$result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $users;
    }

    public function setUserStatus($user_id,$status){
        $stmt=$this->db->prepare("UPDATE users SET is_active=? WHERE id=?");
        $stmt->bind_param("ii",$status,$user_id);
        $stmt->execute();
        $affected=$stmt->affected_rows;
        $stmt->close();
        return $affected>0;
    }

    public function changeUserRole($user_id,$role){
        $stmt=$this->db->prepare("UPDATE users SET role=? WHERE id=?");
        $stmt->bind_param("si",$role,$user_id);
        $stmt->execute();
        $affected=$stmt->affected_rows;
        $stmt->close();
        return $affected>0;
    }

  public function createAdminUser($name,$email,$password){
    $stmt=$this->db->prepare("INSERT INTO users (name,email,password_hash,role,is_active,created_at) VALUES (?,?,?, 'admin',1,NOW())");
    $stmt->bind_param("sss",$name,$email,$password); 
    $stmt->execute();
    $inserted=$stmt->insert_id;
    $stmt->close();
    return $inserted;
}


    public function getAllProjects($workspace_id=0,$status='',$team_lead_id=0){
        $query="SELECT p.*,u.name AS client_name FROM projects p LEFT JOIN users u ON p.client_id=u.id WHERE 1";
        $params=[]; $types='';
        if($workspace_id>0){ $query.=" AND workspace_id=?"; $types.="i"; $params[]=$workspace_id;}
        if($status!=''){ $query.=" AND status=?"; $types.="s"; $params[]=$status;}
        if($team_lead_id>0){ $query.=" AND id IN (SELECT project_id FROM project_members WHERE user_id=?)"; $types.="i"; $params[]=$team_lead_id;}
        $query.=" ORDER BY created_at DESC";

        $stmt=$this->db->prepare($query);
        if(!empty($params)) $stmt->bind_param($types,...$params);
        $stmt->execute();
        $result=$stmt->get_result();
        $projects=$result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $projects;
    }

    public function getAllTasks($status='',$priority='',$assigned_to=0){
        $query="SELECT t.*,p.name AS project_name,u.name AS assignee_name FROM tasks t LEFT JOIN projects p ON t.project_id=p.id LEFT JOIN users u ON t.assigned_to=u.id WHERE 1";
        $params=[]; $types='';
        if($status!=''){ $query.=" AND t.status=?"; $types.="s"; $params[]=$status;}
        if($priority!=''){ $query.=" AND t.priority=?"; $types.="s"; $params[]=$priority;}
        if($assigned_to>0){ $query.=" AND t.assigned_to=?"; $types.="i"; $params[]=$assigned_to;}
        $query.=" ORDER BY t.created_at DESC";
        $stmt=$this->db->prepare($query);
        if(!empty($params)) $stmt->bind_param($types,...$params);
        $stmt->execute();
        $result=$stmt->get_result();
        $tasks=$result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $tasks;
    }

    public function deleteTask($task_id){
        $stmt=$this->db->prepare("DELETE FROM tasks WHERE id=?");
        $stmt->bind_param("i",$task_id);
        $stmt->execute();
        $affected=$stmt->affected_rows;
        $stmt->close();
        return $affected>0;
    }

    
    public function getActivityLogs($date_from='',$date_to='',$action_type=''){
        $query="SELECT a.*,w.name AS workspace_name,u.name AS user_name FROM activity_logs a LEFT JOIN workspaces w ON a.workspace_id=w.id LEFT JOIN users u ON a.user_id=u.id WHERE 1";
        $params=[]; $types='';
        if($date_from!=''){ $query.=" AND DATE(a.created_at)>=?"; $types.="s"; $params[]=$date_from;}
        if($date_to!=''){ $query.=" AND DATE(a.created_at)<=?"; $types.="s"; $params[]=$date_to;}
        if($action_type!=''){ $query.=" AND a.action_type=?"; $types.="s"; $params[]=$action_type;}
        $query.=" ORDER BY a.created_at DESC";
        $stmt=$this->db->prepare($query);
        if(!empty($params)) $stmt->bind_param($types,...$params);
        $stmt->execute();
        $result=$stmt->get_result();
        $logs=$result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $logs;
    }

    
    public function getSupportTickets(){
        $stmt=$this->db->prepare("SELECT * FROM support_tickets ORDER BY created_at DESC");
        $stmt->execute();
        $result=$stmt->get_result();
        $tickets=$result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $tickets;
    }

    public function updateSupportTicket($ticket_id,$admin_note,$status){
        $stmt=$this->db->prepare("UPDATE support_tickets SET admin_note=?,status=? WHERE id=?");
        $stmt->bind_param("ssi",$admin_note,$status,$ticket_id);
        $stmt->execute();
        $affected=$stmt->affected_rows;
        $stmt->close();
        return $affected>0;
    }

    
    public function getAnalytics(){
        $analytics=[];
        $stmt=$this->db->prepare("SELECT COUNT(*) FROM users WHERE is_active=1");
        $stmt->execute();
        $stmt->bind_result($active_users);
        $stmt->fetch();
        $stmt->close();
        $analytics['active_users']=$active_users;

        $stmt=$this->db->prepare("SELECT COUNT(*) FROM tasks");
        $stmt->execute();
        $stmt->bind_result($total_tasks);
        $stmt->fetch();
        $stmt->close();
        $analytics['total_tasks']=$total_tasks;

        $stmt=$this->db->prepare("SELECT COUNT(*) FROM tasks WHERE status='done'");
        $stmt->execute();
        $stmt->bind_result($tasks_done);
        $stmt->fetch();
        $stmt->close();
        $analytics['tasks_done']=$tasks_done;

        $stmt=$this->db->prepare("SELECT COUNT(*) FROM workspaces");
        $stmt->execute();
        $stmt->bind_result($workspace_count);
        $stmt->fetch();
        $stmt->close();
        $analytics['workspace_count']=$workspace_count;

        return $analytics;
    }

    
    public function createAnnouncement($title,$message){
        $stmt=$this->db->prepare("INSERT INTO system_announcements (title,message,created_at) VALUES (?,?,NOW())");
        $stmt->bind_param("ss",$title,$message);
        $stmt->execute();
        $inserted=$stmt->insert_id;
        $stmt->close();
        return $inserted;
    }

    public function getAnnouncements(){
        $stmt=$this->db->prepare("SELECT * FROM system_announcements ORDER BY created_at DESC");
        $stmt->execute();
        $result=$stmt->get_result();
        $announcements=$result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $announcements;
    }

    
    public function generateMonthlyReport($month,$year){
        $report=[];

        $stmt=$this->db->prepare("SELECT COUNT(*) FROM tasks WHERE MONTH(created_at)=? AND YEAR(created_at)=?");
        $stmt->bind_param("ii",$month,$year);
        $stmt->execute();
        $stmt->bind_result($tasks_created);
        $stmt->fetch();
        $stmt->close();
        $report['tasks_created']=$tasks_created;

        $stmt=$this->db->prepare("SELECT COUNT(*) FROM tasks WHERE MONTH(created_at)=? AND YEAR(created_at)=? AND status='done'");
        $stmt->bind_param("ii",$month,$year);
        $stmt->execute();
        $stmt->bind_result($tasks_completed);
        $stmt->fetch();
        $stmt->close();
        $report['tasks_completed']=$tasks_completed;

        $stmt=$this->db->prepare("SELECT COUNT(*) FROM users WHERE MONTH(created_at)=? AND YEAR(created_at)=?");
        $stmt->bind_param("ii",$month,$year);
        $stmt->execute();
        $stmt->bind_result($users_created);
        $stmt->fetch();
        $stmt->close();
        $report['users_created']=$users_created;

        $stmt=$this->db->prepare("SELECT COUNT(*) FROM workspaces WHERE MONTH(created_at)=? AND YEAR(created_at)=?");
        $stmt->bind_param("ii",$month,$year);
        $stmt->execute();
        $stmt->bind_result($workspaces_created);
        $stmt->fetch();
        $stmt->close();
        $report['workspaces_created']=$workspaces_created;

        return $report;
    }

    
    public function getWorkspaceUsage(){
        $stmt=$this->db->prepare("
            SELECT w.id,w.name,
                (SELECT COUNT(*) FROM tasks t WHERE t.project_id IN (SELECT id FROM projects WHERE workspace_id=w.id)) AS task_count,
                (SELECT COUNT(*) FROM workspace_members wm WHERE wm.workspace_id=w.id) AS active_members
            FROM workspaces w
        ");
        $stmt->execute();
        $result=$stmt->get_result();
        $usage=[];
        while($row=$result->fetch_assoc()){
            $stmt2=$this->db->prepare("SELECT file_path FROM task_attachments WHERE task_id IN (SELECT id FROM tasks WHERE project_id IN (SELECT id FROM projects WHERE workspace_id=?))");
            $stmt2->bind_param("i",$row['id']);
            $stmt2->execute();
            $res2=$stmt2->get_result();
            $storage=0;
            while($file=$res2->fetch_assoc()){ if(file_exists($file['file_path'])) $storage+=filesize($file['file_path']); }
            $stmt2->close();
            $row['storage_used_bytes']=$storage;
            $usage[]=$row;
        }
        $stmt->close();
        return $usage;
    }

    
    public function getPlanLimits(){
        $stmt=$this->db->prepare("SELECT * FROM plan_limits");
        $stmt->execute();
        $result=$stmt->get_result();
        $limits=$result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $limits;
    }

    public function updatePlanLimit($plan_id,$max_projects){
        $stmt=$this->db->prepare("UPDATE plan_limits SET max_projects=? WHERE id=?");
        $stmt->bind_param("ii",$max_projects,$plan_id);
        $stmt->execute();
        $affected=$stmt->affected_rows;
        $stmt->close();
        return $affected>0;
    }
    
}
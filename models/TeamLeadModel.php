<?php
require_once __DIR__ . '/../includes/db_connect.php';

class TeamLeadModel {
    private $db;

    public function __construct($mysqli){
        $this->db = $mysqli;
    }

    // ------------------- Profile -------------------
    public function getProfile($user_id){
        $stmt = $this->db->prepare("SELECT id,name,email,phone,role,profile_pic,created_at FROM users WHERE id=?");
        $stmt->bind_param("i",$user_id);
        $stmt->execute();
        $result=$stmt->get_result();
        $profile = $result->fetch_assoc();
        $stmt->close();
        return $profile;
    }

    public function updateProfile($user_id,$name,$phone){
        $stmt = $this->db->prepare("UPDATE users SET name=?, phone=? WHERE id=?");
        $stmt->bind_param("ssi",$name,$phone,$user_id);
        $stmt->execute();
        $affected = $stmt->affected_rows;
        $stmt->close();
        return $affected>0;
    }

    public function changePassword($user_id,$new_hash){
        $stmt = $this->db->prepare("UPDATE users SET password_hash=? WHERE id=?");
        $stmt->bind_param("si",$new_hash,$user_id);
        $stmt->execute();
        $affected = $stmt->affected_rows;
        $stmt->close();
        return $affected>0;
    }

    public function updateProfilePic($user_id,$file_path){
        $stmt = $this->db->prepare("UPDATE users SET profile_pic=? WHERE id=?");
        $stmt->bind_param("si",$file_path,$user_id);
        $stmt->execute();
        $affected = $stmt->affected_rows;
        $stmt->close();
        return $affected>0;
    }

    // ------------------- Projects -------------------
    public function getLeadProjects($user_id){
        $stmt = $this->db->prepare("
            SELECT p.*, u.name AS client_name
            FROM projects p
            JOIN project_members pm ON p.id = pm.project_id
            LEFT JOIN users u ON p.client_id = u.id
            WHERE pm.user_id=?
            GROUP BY p.id
            ORDER BY p.created_at DESC
        ");
        $stmt->bind_param("i",$user_id);
        $stmt->execute();
        $result=$stmt->get_result();
        $projects = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $projects;
    }

    public function createProject($workspace_id,$name,$description,$client_id,$deadline,$color_label,$status='planning',$visibility='internal'){
        $stmt = $this->db->prepare("
            INSERT INTO projects (workspace_id,name,description,client_id,deadline,color_label,status,visibility,created_at)
            VALUES (?,?,?,?,?,?,?,?,NOW())
        ");
        $stmt->bind_param("ississss",$workspace_id,$name,$description,$client_id,$deadline,$color_label,$status,$visibility);
        $stmt->execute();
        $inserted=$stmt->insert_id;
        $stmt->close();
        return $inserted;
    }

    public function assignProjectMembers($project_id,$user_ids){
        foreach($user_ids as $uid){
            $stmt = $this->db->prepare("INSERT INTO project_members (project_id,user_id,assigned_at) VALUES (?,?,NOW())");
            $stmt->bind_param("ii",$project_id,$uid);
            $stmt->execute();
            $stmt->close();
        }
        return true;
    }

    public function getProjectTasks($project_id){
        $stmt = $this->db->prepare("
            SELECT t.*, u.name AS assigned_name
            FROM tasks t
            LEFT JOIN users u ON t.assigned_to = u.id
            WHERE t.project_id=?
            ORDER BY FIELD(t.status,'todo','in_progress','review','done')
        ");
        $stmt->bind_param("i",$project_id);
        $stmt->execute();
        $result=$stmt->get_result();
        $tasks = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $tasks;
    }

    public function createTask($project_id,$title,$description,$assigned_to,$priority,$due_date,$estimated_hours,$milestone_id=NULL){
        $stmt = $this->db->prepare("
            INSERT INTO tasks (project_id,title,description,assigned_to,priority,due_date,estimated_hours,milestone_id,created_at)
            VALUES (?,?,?,?,?,?,?,?,NOW())
        ");
        $stmt->bind_param("issssdisi",$project_id,$title,$description,$assigned_to,$priority,$due_date,$estimated_hours,$milestone_id);
        $stmt->execute();
        $inserted = $stmt->insert_id;
        $stmt->close();
        return $inserted;
    }

    public function updateTask($task_id,$title,$description,$assigned_to,$priority,$due_date,$status){
        $stmt = $this->db->prepare("
            UPDATE tasks SET title=?, description=?, assigned_to=?, priority=?, due_date=?, status=?
            WHERE id=?
        ");
        $stmt->bind_param("ssisssi",$title,$description,$assigned_to,$priority,$due_date,$status,$task_id);
        $stmt->execute();
        $affected=$stmt->affected_rows;
        $stmt->close();
        return $affected>0;
    }

    public function deleteTask($task_id){
        $stmt = $this->db->prepare("DELETE FROM tasks WHERE id=?");
        $stmt->bind_param("i",$task_id);
        $stmt->execute();
        $affected=$stmt->affected_rows;
        $stmt->close();
        return $affected>0;
    }

    public function createMilestone($project_id,$title,$description,$due_date,$is_client_visible=0){
        $stmt = $this->db->prepare("
            INSERT INTO milestones (project_id,title,description,due_date,status,is_client_visible,created_at)
            VALUES (?,?,?,?, 'pending',?,NOW())
        ");
        $stmt->bind_param("issii",$project_id,$title,$description,$due_date,$is_client_visible);
        $stmt->execute();
        $inserted=$stmt->insert_id;
        $stmt->close();
        return $inserted;
    }

    public function markMilestoneCompleted($milestone_id){
        $stmt = $this->db->prepare("UPDATE milestones SET status='completed' WHERE id=?");
        $stmt->bind_param("i",$milestone_id);
        $stmt->execute();
        $affected=$stmt->affected_rows;
        $stmt->close();
        return $affected>0;
    }

    public function getProjectMembers($project_id){
        $stmt = $this->db->prepare("
            SELECT u.id,u.name,u.email,pm.assigned_at
            FROM project_members pm
            JOIN users u ON pm.user_id=u.id
            WHERE pm.project_id=?
        ");
        $stmt->bind_param("i",$project_id);
        $stmt->execute();
        $result=$stmt->get_result();
        $members=$result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $members;
    }

    public function getProjectComments($project_id){
        $stmt = $this->db->prepare("SELECT * FROM comments WHERE project_id=? ORDER BY created_at ASC");
        $stmt->bind_param("i",$project_id);
        $stmt->execute();
        $result=$stmt->get_result();
        $comments=$result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $comments;
    }

    public function getProjectAnalytics($project_id){
        $stmt = $this->db->prepare("
            SELECT COUNT(*) AS total_tasks,
                   SUM(CASE WHEN status='done' THEN 1 ELSE 0 END) AS completed_tasks
            FROM tasks
            WHERE project_id=?
        ");
        $stmt->bind_param("i",$project_id);
        $stmt->execute();
        $stmt->bind_result($total,$completed);
        $stmt->fetch();
        $stmt->close();
        return ['total_tasks'=>$total,'completed_tasks'=>$completed];
    }

    public function getProjectFiles($project_id){
        $stmt = $this->db->prepare("
            SELECT ta.* FROM task_attachments ta
            JOIN tasks t ON ta.task_id = t.id
            WHERE t.project_id=?
        ");
        $stmt->bind_param("i",$project_id);
        $stmt->execute();
        $result=$stmt->get_result();
        $files=$result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $files;
    }

    public function getProjectTimeReport($project_id){
        $stmt = $this->db->prepare("
            SELECT u.name AS member_name, SUM(tl.hours_logged) AS total_hours
            FROM time_logs tl
            JOIN users u ON tl.user_id = u.id
            WHERE tl.task_id IN (SELECT id FROM tasks WHERE project_id=?)
            GROUP BY u.id
        ");
        $stmt->bind_param("i",$project_id);
        $stmt->execute();
        $result=$stmt->get_result();
        $time_report=$result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $time_report;
    }
    public function getMilestones($project_id) {
    $stmt = $this->db->prepare("SELECT * FROM milestones WHERE project_id=? ORDER BY due_date ASC");
    $stmt->bind_param("i", $project_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $milestones = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    return $milestones;
}
}
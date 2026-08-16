<?php
require_once __DIR__ . '/../includes/db_connect.php';

class ClientModel {
    private $db;

    public function __construct($mysqli){
        $this->db = $mysqli;
    }

    // ------------------- Profile -------------------
    public function getProfile($client_id){
        $stmt = $this->db->prepare("SELECT id,name,email,company_name,role,profile_pic,created_at FROM users WHERE id=?");
        $stmt->bind_param("i",$client_id);
        $stmt->execute();
        $result=$stmt->get_result();
        $profile = $result->fetch_assoc();
        $stmt->close();
        return $profile;
    }

    public function updateProfile($client_id,$name,$company){
        $stmt = $this->db->prepare("UPDATE users SET name=?, company_name=? WHERE id=?");
        $stmt->bind_param("ssi",$name,$company,$client_id);
        $stmt->execute();
        $affected=$stmt->affected_rows;
        $stmt->close();
        return $affected>0;
    }

    public function changePassword($client_id,$new_hash){
        $stmt = $this->db->prepare("UPDATE users SET password_hash=? WHERE id=?");
        $stmt->bind_param("si",$new_hash,$client_id);
        $stmt->execute();
        $affected=$stmt->affected_rows;
        $stmt->close();
        return $affected>0;
    }

    public function updateProfilePic($client_id,$file_path){
        $stmt = $this->db->prepare("UPDATE users SET profile_pic=? WHERE id=?");
        $stmt->bind_param("si",$file_path,$client_id);
        $stmt->execute();
        $affected=$stmt->affected_rows;
        $stmt->close();
        return $affected>0;
    }

    // ------------------- Projects -------------------
    public function getAssignedProjects($client_id){
        $stmt = $this->db->prepare("SELECT * FROM projects WHERE client_id=? ORDER BY created_at DESC");
        $stmt->bind_param("i",$client_id);
        $stmt->execute();
        $result=$stmt->get_result();
        $projects = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $projects;
    }

    public function getProjectMilestones($project_id){
        $stmt = $this->db->prepare("SELECT * FROM milestones WHERE project_id=? AND is_client_visible=1 ORDER BY due_date ASC");
        $stmt->bind_param("i",$project_id);
        $stmt->execute();
        $result=$stmt->get_result();
        $milestones = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $milestones;
    }

    public function getMilestoneDetail($milestone_id){
        $stmt = $this->db->prepare("SELECT * FROM milestones WHERE id=?");
        $stmt->bind_param("i",$milestone_id);
        $stmt->execute();
        $result=$stmt->get_result();
        $milestone = $result->fetch_assoc();
        $stmt->close();
        return $milestone;
    }

    // ------------------- Client Feedback -------------------
    public function getFeedback($milestone_id,$client_id){
        $stmt = $this->db->prepare("SELECT * FROM client_feedback WHERE milestone_id=? AND client_id=?");
        $stmt->bind_param("ii",$milestone_id,$client_id);
        $stmt->execute();
        $result=$stmt->get_result();
        $feedback = $result->fetch_assoc();
        $stmt->close();
        return $feedback;
    }

    public function submitFeedback($milestone_id,$client_id,$text,$status){
        $stmt = $this->db->prepare("
            INSERT INTO client_feedback (milestone_id, client_id, feedback_text, approval_status, created_at)
            VALUES (?,?,?,?,NOW())
            ON DUPLICATE KEY UPDATE feedback_text=?, approval_status=?
        ");
        $stmt->bind_param("iissss",$milestone_id,$client_id,$text,$status,$text,$status);
        $stmt->execute();
        $affected=$stmt->affected_rows;
        $stmt->close();
        return $affected>0;
    }

    // ------------------- Task Board (Read-only) -------------------
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

    // ------------------- Messages -------------------
    public function getProjectMessages($project_id){
        $stmt = $this->db->prepare("SELECT * FROM project_messages WHERE project_id=? ORDER BY created_at ASC");
        $stmt->bind_param("i",$project_id);
        $stmt->execute();
        $result=$stmt->get_result();
        $messages = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $messages;
    }

    public function postMessage($project_id,$client_id,$body){
        $stmt = $this->db->prepare("
            INSERT INTO project_messages (project_id,user_id,message,created_at)
            VALUES (?,?,?,NOW())
        ");
        $stmt->bind_param("iis",$project_id,$client_id,$body);
        $stmt->execute();
        $inserted=$stmt->insert_id;
        $stmt->close();
        return $inserted>0;
    }

    // ------------------- Project Files -------------------
    public function getProjectFiles($project_id){
        $stmt = $this->db->prepare("
            SELECT ta.* FROM task_attachments ta
            JOIN tasks t ON ta.task_id = t.id
            WHERE t.project_id=? 
        ");
        $stmt->bind_param("i",$project_id);
        $stmt->execute();
        $result=$stmt->get_result();
        $files = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $files;
    }

    // ------------------- Project Time Summary -------------------
    public function getProjectTimeSummary($project_id){
        $stmt = $this->db->prepare("
            SELECT SUM(hours_logged) AS total_hours FROM time_logs
            WHERE task_id IN (SELECT id FROM tasks WHERE project_id=?)
        ");
        $stmt->bind_param("i",$project_id);
        $stmt->execute();
        $stmt->bind_result($total_hours);
        $stmt->fetch();
        $stmt->close();
        return $total_hours ?? 0;
    }
}
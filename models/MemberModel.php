<?php
require_once __DIR__ . '/../includes/db_connect.php';

class MemberModel {
    private $db;

    public function __construct($mysqli) {
        $this->db = $mysqli;
    }

    // ------------------- Profile -------------------
    public function getProfile($user_id){
        $stmt = $this->db->prepare("SELECT id,name,email,phone,role,profile_pic,password_hash,created_at FROM users WHERE id=?");
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

    // ------------------- Workspaces -------------------
    public function getJoinedWorkspaces($user_id){
        $stmt = $this->db->prepare("
            SELECT w.* FROM workspaces w
            JOIN workspace_members wm ON w.id=wm.workspace_id
            WHERE wm.user_id=?
        ");
        $stmt->bind_param("i",$user_id);
        $stmt->execute();
        $result=$stmt->get_result();
        $workspaces=$result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $workspaces;
    }

    public function joinWorkspace($user_id,$invite_code){
        // Find workspace
        $stmt = $this->db->prepare("SELECT id FROM workspaces WHERE invite_code=? AND is_active=1");
        $stmt->bind_param("s",$invite_code);
        $stmt->execute();
        $result=$stmt->get_result();
        if($workspace=$result->fetch_assoc()){
            $workspace_id = $workspace['id'];
            $stmt->close();
            // Check already joined
            $stmtCheck=$this->db->prepare("SELECT id FROM workspace_members WHERE workspace_id=? AND user_id=?");
            $stmtCheck->bind_param("ii",$workspace_id,$user_id);
            $stmtCheck->execute();
            $stmtCheck->store_result();
            if($stmtCheck->num_rows>0){ $stmtCheck->close(); return false; }
            $stmtCheck->close();
            // Join
            $stmtJoin = $this->db->prepare("INSERT INTO workspace_members (workspace_id,user_id,workspace_role,joined_at) VALUES (?,?, 'member', NOW())");
            $stmtJoin->bind_param("ii",$workspace_id,$user_id);
            $stmtJoin->execute();
            $inserted = $stmtJoin->insert_id;
            $stmtJoin->close();
            return $inserted>0;
        } else {
            $stmt->close();
            return false;
        }
    }

    // ------------------- Projects -------------------
    public function getAssignedProjects($user_id,$workspace_id){
        $stmt = $this->db->prepare("
            SELECT p.* FROM projects p
            JOIN project_members pm ON p.id=pm.project_id
            WHERE pm.user_id=? AND p.workspace_id=?
        ");
        $stmt->bind_param("ii",$user_id,$workspace_id);
        $stmt->execute();
        $result=$stmt->get_result();
        $projects=$result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $projects;
    }

    // ------------------- Tasks -------------------
    public function getTasksByProject($project_id,$user_id){
        $stmt = $this->db->prepare("
            SELECT * FROM tasks
            WHERE project_id=? AND assigned_to=?
            ORDER BY FIELD(status,'todo','in_progress','review','done')
        ");
        $stmt->bind_param("ii",$project_id,$user_id);
        $stmt->execute();
        $result=$stmt->get_result();
        $tasks=$result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $tasks;
    }

    public function updateTaskStatus($task_id,$status){
        $stmt = $this->db->prepare("UPDATE tasks SET status=? WHERE id=?");
        $stmt->bind_param("si",$status,$task_id);
        $stmt->execute();
        $affected=$stmt->affected_rows;
        $stmt->close();
        return $affected>0;
    }

    public function flagTaskBlocked($task_id,$reason){
        $stmt = $this->db->prepare("UPDATE tasks SET description=CONCAT(description,'\n[BLOCKED]: ',?) WHERE id=?");
        $stmt->bind_param("si",$reason,$task_id);
        $stmt->execute();
        $affected=$stmt->affected_rows;
        $stmt->close();
        return $affected>0;
    }

    public function unflagTaskBlocked($task_id){
        $stmt = $this->db->prepare("
            UPDATE tasks 
            SET description=REPLACE(description,SUBSTRING_INDEX(description,'[BLOCKED]:','-1'),'') 
            WHERE id=?
        ");
        $stmt->bind_param("i",$task_id);
        $stmt->execute();
        $affected=$stmt->affected_rows;
        $stmt->close();
        return $affected>0;
    }

    public function getTaskDetail($task_id){
        $stmt = $this->db->prepare("SELECT * FROM tasks WHERE id=?");
        $stmt->bind_param("i",$task_id);
        $stmt->execute();
        $result=$stmt->get_result();
        $task=$result->fetch_assoc();
        $stmt->close();
        return $task;
    }

    public function getTaskAttachments($task_id){
        $stmt = $this->db->prepare("SELECT * FROM task_attachments WHERE task_id=?");
        $stmt->bind_param("i",$task_id);
        $stmt->execute();
        $result=$stmt->get_result();
        $files=$result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $files;
    }

    public function uploadTaskAttachment($task_id,$user_id,$file_path,$file_name){
        $stmt = $this->db->prepare("
            INSERT INTO task_attachments (task_id, uploaded_by, file_path, file_name, uploaded_at)
            VALUES (?,?,?,?,NOW())
        ");
        $stmt->bind_param("iiss",$task_id,$user_id,$file_path,$file_name);
        $stmt->execute();
        $inserted=$stmt->insert_id;
        $stmt->close();
        return $inserted>0;
    }

    public function getTaskComments($task_id){
        $stmt = $this->db->prepare("SELECT * FROM comments WHERE task_id=? ORDER BY created_at ASC");
        $stmt->bind_param("i",$task_id);
        $stmt->execute();
        $result=$stmt->get_result();
        $comments=$result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $comments;
    }

    public function addComment($task_id,$user_id,$body,$is_internal=1){
        $stmt = $this->db->prepare("INSERT INTO comments (task_id,user_id,body,is_internal,created_at) VALUES (?,?,?,?,NOW())");
        $stmt->bind_param("iisi",$task_id,$user_id,$body,$is_internal);
        $stmt->execute();
        $inserted=$stmt->insert_id;
        $stmt->close();
        return $inserted>0;
    }

    public function deleteComment($comment_id,$user_id){
        $stmt = $this->db->prepare("DELETE FROM comments WHERE id=? AND user_id=?");
        $stmt->bind_param("ii",$comment_id,$user_id);
        $stmt->execute();
        $affected=$stmt->affected_rows;
        $stmt->close();
        return $affected>0;
    }

    public function logTime($task_id,$user_id,$hours,$note){
        $stmt = $this->db->prepare("INSERT INTO time_logs (task_id,user_id,hours_logged,note,logged_at) VALUES (?,?,?,?,NOW())");
        $stmt->bind_param("iids",$task_id,$user_id,$hours,$note);
        $stmt->execute();
        $inserted=$stmt->insert_id;
        $stmt->close();
        return $inserted>0;
    }

    public function getTimeLogs($task_id,$user_id){
        $stmt = $this->db->prepare("SELECT * FROM time_logs WHERE task_id=? AND user_id=? ORDER BY logged_at DESC");
        $stmt->bind_param("ii",$task_id,$user_id);
        $stmt->execute();
        $result=$stmt->get_result();
        $logs=$result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $logs;
    }

    public function getNotifications($user_id){
        $stmt = $this->db->prepare("SELECT * FROM notifications WHERE user_id=? ORDER BY created_at DESC");
        $stmt->bind_param("i",$user_id);
        $stmt->execute();
        $result=$stmt->get_result();
        $notes=$result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $notes;
    }

    public function markNotificationRead($note_id){
        $stmt = $this->db->prepare("UPDATE notifications SET is_read=1 WHERE id=?");
        $stmt->bind_param("i",$note_id);
        $stmt->execute();
        $affected=$stmt->affected_rows;
        $stmt->close();
        return $affected>0;
    }

    // ------------------- Fixed Workspace creation -------------------
    public function createWorkspace($user_id, $name, $description, $plan='free') {
        $stmt = $this->db->prepare("
            INSERT INTO workspaces (name, description, owner_id, plan, is_active, created_at) 
            VALUES (?, ?, ?, ?, 1, NOW())
        ");
        $stmt->bind_param("ssis", $name, $description, $user_id, $plan);
        $stmt->execute();
        $inserted = $stmt->insert_id;
        $stmt->close();
        return $inserted;
    }
}
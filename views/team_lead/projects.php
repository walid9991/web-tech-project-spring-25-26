<?php require_once __DIR__ . '/../../includes/header.php'; ?>

<h1>Your Projects</h1>

<ul>
<?php foreach($projects as $p): ?>
    <li>
        <?= htmlspecialchars($p['name']) ?> -
        Status: <?= $p['status'] ?> -
        <a href="TeamLeadController.php?action=tasks&project_id=<?= $p['id'] ?>">Tasks</a> |
        <a href="TeamLeadController.php?action=milestones&project_id=<?= $p['id'] ?>">Milestones</a> |
        <a href="TeamLeadController.php?action=project_members&project_id=<?= $p['id'] ?>">Team Members</a> |
        <a href="TeamLeadController.php?action=analytics&project_id=<?= $p['id'] ?>">Analytics</a>
    </li>
<?php endforeach; ?>
</ul>

<h2>Create New Project</h2>
<form method="post" action="TeamLeadController.php?action=create_project">
    <label>Workspace ID:</label><br>
    <input type="number" name="workspace_id" required><br><br>
    <label>Project Name:</label><br>
    <input type="text" name="name" required><br><br>
    <label>Description:</label><br>
    <textarea name="description"></textarea><br><br>
    <label>Client ID:</label><br>
    <input type="number" name="client_id" required><br><br>
    <label>Deadline:</label><br>
    <input type="date" name="deadline"><br><br>
    <label>Color Label:</label><br>
    <input type="text" name="color_label"><br><br>
    <label>Status:</label><br>
    <select name="status">
        <option value="planning">Planning</option>
        <option value="active">Active</option>
        <option value="on_hold">On Hold</option>
        <option value="completed">Completed</option>
    </select><br><br>
    <label>Visibility:</label><br>
    <select name="visibility">
        <option value="internal">Internal</option>
        <option value="client_visible">Client Visible</option>
    </select><br><br>
    <label>Assign Members (comma-separated user IDs):</label><br>
    <input type="text" name="members"><br><br>
    <button type="submit">Create Project</button>
</form>
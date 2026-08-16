<?php require_once __DIR__ . '/../../includes/header.php'; ?>

<style>
.dashboard-container {
    display: flex;
    flex-direction: column;
    gap: 30px;
    max-width: 1200px;
    margin: 20px auto;
    font-family: Poppins, sans-serif;
}
.dashboard-container h2 {
    border-bottom: 2px solid #007BFF;
    padding-bottom: 5px;
    color: #007BFF;
}
.projects-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
    gap: 20px;
}
.project-card {
    border: 1px solid #ccc;
    border-radius: 10px;
    padding: 15px;
    box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    background-color: #fff;
    transition: transform 0.2s, box-shadow 0.2s;
}
.project-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}
.project-links a {
    display: inline-block;
    margin-right: 10px;
    text-decoration: none;
    color: #007BFF;
    font-weight: 500;
}
.project-links a:hover {
    text-decoration: underline;
}
.workspace-form input[type="text"],
.workspace-form textarea {
    width: 100%;
    padding: 8px 10px;
    margin-bottom: 10px;
    border-radius: 5px;
    border: 1px solid #ccc;
}
.workspace-form button {
    padding: 10px 20px;
    border: none;
    border-radius: 5px;
    background-color: #007BFF;
    color: #fff;
    font-weight: bold;
    cursor: pointer;
}
.workspace-form button:hover {
    background-color: #0056b3;
}
.sub-section {
    margin-top: 10px;
    font-size: 0.9rem;
}
.sub-section h4 {
    margin: 5px 0;
    font-size: 0.95rem;
    color: #555;
}
.sub-section ul {
    padding-left: 15px;
    margin: 0;
}
.sub-section ul li {
    margin-bottom: 3px;
}
</style>

<div class="dashboard-container">

<h1>Team Lead Dashboard</h1>
<p>Welcome, <strong><?= htmlspecialchars($_SESSION['name']) ?></strong>!</p>

<section>
<h2>Your Active Projects</h2>
<?php if (!empty($projects) && is_array($projects)) : ?>
    <div class="projects-grid">
    <?php foreach($projects as $p): ?>
        <div class="project-card">
            <h3><?= htmlspecialchars($p['name']) ?></h3>
            <p><strong>Client:</strong> <?= htmlspecialchars($p['client_name'] ?? '-') ?></p>
            <p><strong>Status:</strong> <?= htmlspecialchars($p['status']) ?></p>
            <div class="project-links">
                <a href="TeamLeadController.php?action=tasks&project_id=<?= $p['id'] ?>">Tasks</a>
                <a href="TeamLeadController.php?action=project_members&project_id=<?= $p['id'] ?>">Team Members</a>
                <a href="TeamLeadController.php?action=analytics&project_id=<?= $p['id'] ?>">Analytics</a>
                <a href="TeamLeadController.php?action=files&project_id=<?= $p['id'] ?>">Files</a>
                <a href="TeamLeadController.php?action=time_report&project_id=<?= $p['id'] ?>">Time Report</a>
            </div>

            <!-- Top Tasks -->
            <?php if (!empty($p['tasks']) && is_array($p['tasks'])): ?>
            <div class="sub-section">
                <h4>Top Tasks</h4>
                <ul>
                <?php foreach(array_slice($p['tasks'],0,3) as $task): ?>
                    <li><?= htmlspecialchars($task['title']) ?> (<?= htmlspecialchars($task['status']) ?>, due <?= htmlspecialchars($task['due_date']) ?>)</li>
                <?php endforeach; ?>
                </ul>
            </div>
            <?php endif; ?>

            <!-- Unread notifications count -->
            <?php if (!empty($notifications) && is_array($notifications)): ?>
            <div class="sub-section">
                <strong>Unread Notifications:</strong>
                <?= count(array_filter($notifications, fn($n) => $n['is_read']==0)) ?>
            </div>
            <?php endif; ?>

        </div>
    <?php endforeach; ?>
    </div>
<?php else: ?>
    <p>No projects assigned.</p>
<?php endif; ?>
</section>

<section>
<h2>Create New Workspace</h2>
<form class="workspace-form" method="post" action="TeamLeadController.php?action=create_workspace">
    <input type="text" name="name" placeholder="Workspace Name" required>
    <textarea name="description" placeholder="Description"></textarea>
    <button type="submit">Create Workspace</button>
</form>
</section>
</div>
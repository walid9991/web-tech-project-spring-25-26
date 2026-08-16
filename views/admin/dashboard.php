<?php require_once __DIR__ . '/../../includes/header.php'; ?>

<style>
.dashboard-container {
    max-width: 1200px;
    margin: 20px auto;
    font-family: Poppins, sans-serif;
    display: flex;
    flex-direction: column;
    gap: 30px;
}


.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    gap: 20px;
}
.stat-card {
    background-color: #fff;
    border: 1px solid #ccc;
    border-radius: 10px;
    padding: 15px;
    text-align: center;
    box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    transition: transform 0.2s, box-shadow 0.2s;
}
.stat-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}
.stat-card h3 {
    margin: 0;
    font-size: 1.2rem;
    color: #007BFF;
}
.stat-card p {
    margin: 5px 0 0 0;
    font-size: 1rem;
    color: #333;
}


.admin-nav {
    display: flex;
    flex-wrap: wrap;
    gap: 15px;
}
.admin-nav a {
    flex: 1 1 180px;
    padding: 10px 15px;
    text-align: center;
    background-color: #007BFF;
    color: #fff;
    font-weight: 500;
    border-radius: 8px;
    text-decoration: none;
    transition: background-color 0.2s;
}
.admin-nav a:hover {
    background-color: #0056b3;
}
</style>

<div class="dashboard-container">

<h1>Admin Dashboard</h1>


<div class="stats-grid">
    <div class="stat-card">
        <h3><?= $totalWorkspaces ?></h3>
        <p>Total Workspaces</p>
    </div>
    <div class="stat-card">
        <h3><?= $totalAdmins ?></h3>
        <p>Total Admins</p>
    </div>
    <div class="stat-card">
        <h3><?= $totalTeamLeads ?></h3>
        <p>Total Team Leads</p>
    </div>
    <div class="stat-card">
        <h3><?= $totalMembers ?></h3>
        <p>Total Members</p>
    </div>
    <div class="stat-card">
        <h3><?= $totalClients ?></h3>
        <p>Total Clients</p>
    </div>
    <div class="stat-card">
        <h3><?= $totalProjects ?></h3>
        <p>Total Active Projects</p>
    </div>
    <div class="stat-card">
        <h3><?= $tasksToday ?></h3>
        <p>Tasks Created Today</p>
    </div>
</div>


<div class="admin-nav">
    <a href="AdminController.php?action=workspaces">Manage Workspaces</a>
    <a href="AdminController.php?action=users">Manage Users</a>
    <a href="AdminController.php?action=projects">Projects</a>
    <a href="AdminController.php?action=tasks">Tasks</a>
    <a href="AdminController.php?action=activity_logs">Activity Logs</a>
    <a href="AdminController.php?action=support_tickets">Support Tickets</a>
    <a href="AdminController.php?action=analytics">Analytics</a>
    <a href="AdminController.php?action=announcements">Announcements</a>
    <a href="AdminController.php?action=monthly_report">Monthly Report</a>
    <a href="AdminController.php?action=workspace_usage">Workspace Usage</a>
    <a href="AdminController.php?action=plan_limits">Plan Limits</a>
</div>

</div>
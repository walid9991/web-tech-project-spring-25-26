<?php require_once __DIR__ . '/../../includes/header.php'; ?>

<style>
.dashboard-container {
    display: flex;
    flex-direction: column;
    gap: 30px;
    max-width: 800px;
    margin: 20px auto;
    font-family: Poppins, sans-serif;
}
.dashboard-container h2 {
    border-bottom: 2px solid #007BFF;
    padding-bottom: 5px;
    color: #007BFF;
}
.workspaces ul {
    list-style: none;
    padding-left: 0;
}
.workspaces ul li {
    margin-bottom: 8px;
}
.workspaces a {
    margin-left: 10px;
    text-decoration: none;
    color: #007BFF;
}
.workspaces a:hover {
    text-decoration: underline;
}
.workspace-form input[type="text"] {
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
.profile a {
    display: inline-block;
    margin-top: 5px;
    color: #007BFF;
    text-decoration: none;
}
.profile a:hover {
    text-decoration: underline;
}
</style>

<div class="dashboard-container">

<h1>Member Dashboard</h1>

<!-- Welcome & Profile -->
<p>Welcome, <strong><?= htmlspecialchars($_SESSION['name'] ?? 'Member') ?></strong>!</p>

<div class="profile">
    <h2>Your Profile</h2>
    <p>Name: <?= htmlspecialchars($_SESSION['name'] ?? '-') ?></p>
    <a href="MemberController.php?action=profile">Edit Profile</a>
</div>

<!-- Workspaces -->
<div class="workspaces">
    <h2>Your Workspaces</h2>
    <form class="workspace-form" method="post" action="MemberController.php?action=join_workspace">
        <input type="text" name="invite_code" placeholder="Enter Invite Code" required>
        <button type="submit">Join Workspace</button>
    </form>

    <ul>
    <?php if (!empty($workspaces) && is_array($workspaces)) : ?>
        <?php foreach ($workspaces as $w) : ?>
            <li>
                <?= htmlspecialchars($w['name']) ?>
                <a href="MemberController.php?action=projects&workspace_id=<?= $w['id'] ?>">View Projects</a>
            </li>
        <?php endforeach; ?>
    <?php else: ?>
        <li>No workspaces joined yet.</li>
    <?php endif; ?>
    </ul>
</div>
</div>
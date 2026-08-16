<?php require_once __DIR__ . '/../../includes/header.php'; ?>

<h1>User Management</h1>

<form method="get" action="AdminController.php">
    <input type="hidden" name="action" value="users">
    <input type="text" name="search" placeholder="Search user" value="<?= htmlspecialchars($search) ?>">
    <button type="submit">Search</button>
</form>

<button onclick="showCreateAdminForm()">Create New Admin</button>

<table border="1" cellpadding="5">
<tr>
    <th>ID</th>
    <th>Name</th>
    <th>Email</th>
    <th>Role</th>
    <th>Status</th>
    <th>Actions</th>
</tr>
<?php foreach($users as $u): ?>
<tr>
    <td><?= $u['id'] ?></td>
    <td><?= htmlspecialchars($u['name']) ?></td>
    <td><?= htmlspecialchars($u['email']) ?></td>
    <td>
        <select onchange="changeRole(<?= $u['id'] ?>,this.value)">
            <option value="member" <?= $u['role']=='member'?'selected':'' ?>>Member</option>
            <option value="team_lead" <?= $u['role']=='team_lead'?'selected':'' ?>>Team Lead</option>
            <option value="client" <?= $u['role']=='client'?'selected':'' ?>>Client</option>
            <option value="admin" <?= $u['role']=='admin'?'selected':'' ?>>Admin</option>
        </select>
    </td>
    <td><?= $u['is_active']?'Active':'Inactive' ?></td>
    <td>
        <button onclick="userAction(<?= $u['id'] ?>,'activate')">Activate</button>
        <button onclick="userAction(<?= $u['id'] ?>,'deactivate')">Deactivate</button>
    </td>
</tr>
<?php endforeach; ?>
</table>

<div id="createAdminForm" style="display:none">
    <h3>Create Admin User</h3>
    <input type="text" id="adminName" placeholder="Name">
    <input type="email" id="adminEmail" placeholder="Email">
    <input type="password" id="adminPassword" placeholder="Password">
    <button onclick="createAdmin()">Create</button>
</div>

<script>
function userAction(id,type){
    var xhr=new XMLHttpRequest();
    xhr.open('POST','AdminController.php?action=user_action',true);
    xhr.setRequestHeader('Content-type','application/x-www-form-urlencoded');
    xhr.onload=function(){
        var res=JSON.parse(this.responseText);
        alert(res.message);
        if(res.success) location.reload();
    };
    xhr.send('type='+type+'&id='+id);
}

function changeRole(id,role){
    var xhr=new XMLHttpRequest();
    xhr.open('POST','AdminController.php?action=user_action',true);
    xhr.setRequestHeader('Content-type','application/x-www-form-urlencoded');
    xhr.onload=function(){
        var res=JSON.parse(this.responseText);
        alert(res.message);
        if(res.success) location.reload();
    };
    xhr.send('type=change_role&id='+id+'&role='+role);
}

function showCreateAdminForm(){
    document.getElementById('createAdminForm').style.display='block';
}

function createAdmin(){
    var name=document.getElementById('adminName').value;
    var email=document.getElementById('adminEmail').value;
    var password=document.getElementById('adminPassword').value;
    var xhr=new XMLHttpRequest();
    xhr.open('POST','AdminController.php?action=user_action',true);
    xhr.setRequestHeader('Content-type','application/x-www-form-urlencoded');
    xhr.onload=function(){
        var res=JSON.parse(this.responseText);
        alert(res.message);
        if(res.success) location.reload();
    };
    xhr.send('type=create_admin&name='+encodeURIComponent(name)+'&email='+encodeURIComponent(email)+'&password='+encodeURIComponent(password));
}
</script>
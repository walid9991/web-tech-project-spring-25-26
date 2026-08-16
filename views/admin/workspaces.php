<?php require_once __DIR__ . '/../../includes/header.php'; ?>

<h1>Manage Workspaces</h1>

<form method="get" action="AdminController.php">
    <input type="hidden" name="action" value="workspaces">
    <input type="text" name="search" placeholder="Search workspace" value="<?= htmlspecialchars($search) ?>">
    <button type="submit">Search</button>
</form>

<table border="1" cellpadding="5">
<tr>
    <th>ID</th>
    <th>Name</th>
    <th>Owner ID</th>
    <th>Status</th>
    <th>Actions</th>
    <th>Members</th>
</tr>
<?php foreach($workspaces as $ws): ?>
<tr>
    <td><?= $ws['id'] ?></td>
    <td><?= htmlspecialchars($ws['name']) ?></td>
    <td><?= $ws['owner_id'] ?></td>
    <td><?= $ws['is_active']?'Active':'Inactive' ?></td>
    <td>
        <button onclick="workspaceAction(<?= $ws['id'] ?>,'activate')">Activate</button>
        <button onclick="workspaceAction(<?= $ws['id'] ?>,'deactivate')">Deactivate</button>
        <button onclick="workspaceAction(<?= $ws['id'] ?>,'delete')">Delete</button>
    </td>
    <td>
        <a href="AdminController.php?action=view_members&workspace_id=<?= $ws['id'] ?>">View Members</a>
    </td>
</tr>
<?php endforeach; ?>
</table>

<script>
function workspaceAction(id,type){
    if(type==='delete' && !confirm('Are you sure to delete this workspace?')) return;
    var xhr=new XMLHttpRequest();
    xhr.open('POST','AdminController.php?action=workspace_action',true);
    xhr.setRequestHeader('Content-type','application/x-www-form-urlencoded');
    xhr.onload=function(){
        var res=JSON.parse(this.responseText);
        alert(res.message);
        if(res.success) location.reload();
    };
    xhr.send('type='+type+'&id='+id);
}
</script>
<?php require_once __DIR__ . '/../../includes/header.php'; ?>

<h1>Support Tickets</h1>

<table border="1" cellpadding="5">
<tr>
    <th>ID</th><th>User ID</th><th>Issue</th><th>Status</th><th>Admin Note</th><th>Action</th>
</tr>
<?php foreach($tickets as $t): ?>
<tr>
    <td><?= $t['id'] ?></td>
    <td><?= $t['user_id'] ?></td>
    <td><?= htmlspecialchars($t['issue_text']) ?></td>
    <td><?= $t['status'] ?></td>
    <td><?= htmlspecialchars($t['admin_note']) ?></td>
    <td>
        <input type="text" id="note_<?= $t['id'] ?>" placeholder="Add note">
        <select id="status_<?= $t['id'] ?>">
            <option value="pending" <?= $t['status']=='pending'?'selected':'' ?>>Pending</option>
            <option value="resolved" <?= $t['status']=='resolved'?'selected':'' ?>>Resolved</option>
        </select>
        <button onclick="updateTicket(<?= $t['id'] ?>)">Update</button>
    </td>
</tr>
<?php endforeach; ?>
</table>

<script>
function updateTicket(id){
    var note = document.getElementById('note_'+id).value;
    var status = document.getElementById('status_'+id).value;

    var xhr = new XMLHttpRequest();
    xhr.open('POST','AdminController.php?action=support_ticket_action',true);
    xhr.setRequestHeader('Content-type','application/x-www-form-urlencoded');
    xhr.onload = function(){
        var res=JSON.parse(this.responseText);
        alert(res.message);
        if(res.success) location.reload();
    };
    xhr.send('ticket_id='+id+'&admin_note='+encodeURIComponent(note)+'&status='+status);
}
</script>
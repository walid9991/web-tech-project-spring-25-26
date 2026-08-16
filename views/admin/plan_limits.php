<?php require_once __DIR__ . '/../../includes/header.php'; ?>

<h1>Platform Plan Limits</h1>

<table border="1" cellpadding="5">
<tr>
    <th>ID</th>
    <th>Plan Type</th>
    <th>Max Projects</th>
    <th>Action</th>
</tr>
<?php foreach($limits as $l): ?>
<tr>
    <td><?= $l['id'] ?></td>
    <td><?= $l['plan_type'] ?></td>
    <td><input type="number" id="max_<?= $l['id'] ?>" value="<?= $l['max_projects'] ?>"></td>
    <td><button onclick="updatePlan(<?= $l['id'] ?>)">Update</button></td>
</tr>
<?php endforeach; ?>
</table>

<script>
function updatePlan(id){
    var max_projects=document.getElementById('max_'+id).value;
    var xhr=new XMLHttpRequest();
    xhr.open('POST','AdminController.php?action=plan_limit_action',true);
    xhr.setRequestHeader('Content-type','application/x-www-form-urlencoded');
    xhr.onload=function(){
        var res=JSON.parse(this.responseText);
        alert(res.message);
        if(res.success) location.reload();
    };
    xhr.send('plan_id='+id+'&max_projects='+max_projects);
}
</script>
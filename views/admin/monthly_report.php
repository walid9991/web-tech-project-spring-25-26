<?php require_once __DIR__ . '/../../includes/header.php'; ?>

<h1>Monthly Platform Usage Report</h1>

<form method="get" action="AdminController.php">
    <input type="hidden" name="action" value="monthly_report">
    <select name="month">
        <?php for($m=1;$m<=12;$m++): ?>
            <option value="<?= $m ?>" <?= $m==$month?'selected':'' ?>><?= $m ?></option>
        <?php endfor; ?>
    </select>
    <input type="number" name="year" value="<?= $year ?>" min="2000" max="<?= date('Y') ?>">
    <button type="submit">Generate</button>
</form>

<table border="1" cellpadding="5">
<tr><th>Metric</th><th>Count</th></tr>
<tr><td>Tasks Created</td><td><?= $report['tasks_created'] ?></td></tr>
<tr><td>Tasks Completed</td><td><?= $report['tasks_completed'] ?></td></tr>
<tr><td>New Users</td><td><?= $report['users_created'] ?></td></tr>
<tr><td>New Workspaces</td><td><?= $report['workspaces_created'] ?></td></tr>
</table>

<button onclick="exportCSV()">Export CSV</button>

<script>
function exportCSV(){
    var csvContent="data:text/csv;charset=utf-8,Metric,Count\n";
    csvContent+="Tasks Created,<?= $report['tasks_created'] ?>\n";
    csvContent+="Tasks Completed,<?= $report['tasks_completed'] ?>\n";
    csvContent+="New Users,<?= $report['users_created'] ?>\n";
    csvContent+="New Workspaces,<?= $report['workspaces_created'] ?>\n";

    var encodedUri=encodeURI(csvContent);
    var link=document.createElement("a");
    link.setAttribute("href",encodedUri);
    link.setAttribute("download","monthly_report_<?= $month ?>_<?= $year ?>.csv");
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}
</script>
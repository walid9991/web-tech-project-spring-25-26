<?php require_once __DIR__ . '/../../includes/header.php'; ?>

<h1>Milestone Detail</h1>

<h3><?= htmlspecialchars($milestone['title']) ?></h3>
<p><?= htmlspecialchars($milestone['description']) ?></p>
<p>Due Date: <?= $milestone['due_date'] ?></p>
<p>Status: <?= $milestone['status'] ?></p>

<h2>Your Feedback</h2>

<?php if($feedback): ?>
    <p>Previous Feedback: <?= htmlspecialchars($feedback['feedback_text']) ?></p>
    <p>Approval Status: <?= $feedback['approval_status'] ?></p>
<?php endif; ?>

<textarea id="feedbackText" placeholder="Enter feedback"><?= htmlspecialchars($feedback['feedback_text'] ?? '') ?></textarea>
<select id="approvalStatus">
    <option value="pending" <?= ($feedback['approval_status']??'')=='pending'?'selected':'' ?>>Pending</option>
    <option value="approved" <?= ($feedback['approval_status']??'')=='approved'?'selected':'' ?>>Approved</option>
    <option value="revision_requested" <?= ($feedback['approval_status']??'')=='revision_requested'?'selected':'' ?>>Revision Requested</option>
</select>
<button onclick="submitFeedback(<?= $milestone['id'] ?>)">Submit Feedback</button>

<script>
function submitFeedback(milestone_id){
    var text = document.getElementById('feedbackText').value;
    var status = document.getElementById('approvalStatus').value;

    var xhr = new XMLHttpRequest();
    xhr.open('POST','ClientController.php?action=submit_feedback',true);
    xhr.setRequestHeader('Content-type','application/x-www-form-urlencoded');
    xhr.onload = function(){
        var res = JSON.parse(this.responseText);
        alert(res.success ? 'Feedback submitted' : 'Failed to submit');
        if(res.success) location.reload();
    };
    xhr.send('milestone_id='+milestone_id+'&feedback_text='+encodeURIComponent(text)+'&approval_status='+status);
}
</script>
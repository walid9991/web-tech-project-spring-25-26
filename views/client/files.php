<?php require_once __DIR__ . '/../../includes/header.php'; ?>

<h1>Project Files</h1>

<ul>
<?php foreach($files as $f): ?>
    <li>
        <a href="<?= htmlspecialchars($f['file_path']) ?>" target="_blank"><?= htmlspecialchars($f['file_name']) ?></a>
        (Uploaded by User ID: <?= $f['uploaded_by'] ?>)
    </li>
<?php endforeach; ?>
</ul>

<p>All files listed here are shared and client-visible only.</p>
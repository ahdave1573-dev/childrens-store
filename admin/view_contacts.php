<?php
require_once __DIR__ . '/../middleware/require_admin.php';
require_once __DIR__ . '/../config/database.php';

// Ensure table exists just in case
$conn->query("CREATE TABLE IF NOT EXISTS `contacts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `status` enum('Unread','Read') DEFAULT 'Unread',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM contacts WHERE id=$id");
    header("Location: view_contacts.php");
    exit;
}

if (isset($_GET['toggle_status'])) {
    $id = intval($_GET['toggle_status']);
    $res = $conn->query("SELECT status FROM contacts WHERE id=$id");
    if ($res->num_rows > 0) {
        $row = $res->fetch_assoc();
        $new_status = ($row['status'] == 'Unread') ? 'Read' : 'Unread';
        $conn->query("UPDATE contacts SET status='$new_status' WHERE id=$id");
    }
    header("Location: view_contacts.php");
    exit;
}

$contacts = mysqli_query($conn, "SELECT * FROM contacts ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<title>Contacts | Admin</title>
<link rel="stylesheet" href="assets/dashboard.css">
<script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
</head>
<body>

<?php $page = 'view_contacts'; include 'sidebar.php'; ?>

<div class="main">
    <div class="topbar">
        <h1>Contact Messages</h1>
        <span><?php echo date("d M Y"); ?></span>
    </div>

    <div class="panel">
        <h3>Inbox</h3>
        <?php if ($contacts->num_rows > 0): ?>
        <style>
            .contacts-table td {
                vertical-align: top;
                padding-top: 1.25rem;
            }
        </style>
        <div style="overflow-x: auto; width: 100%;">
            <table class="contacts-table">
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Message</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th>Action</th>
                </tr>
                <?php while($c = mysqli_fetch_assoc($contacts)): ?>
                <tr>
                    <td>#<?php echo $c['id']; ?></td>
                    <td><strong><?php echo htmlspecialchars($c['name']); ?></strong></td>
                    <td><a href="mailto:<?php echo htmlspecialchars($c['email']); ?>"><?php echo htmlspecialchars($c['email']); ?></a></td>
                    <td>
                        <div style="width: 220px; word-break: break-all; overflow-wrap: break-word; white-space: normal;">
                            <?php 
                            $msg = htmlspecialchars($c['message']);
                            if (strlen($msg) > 150): 
                                $short = substr($msg, 0, 150);
                            ?>
                                <span class="msg-short"><?php echo nl2br($short); ?>...</span>
                                <span class="msg-full" style="display: none;"><?php echo nl2br($msg); ?></span>
                                <a href="javascript:void(0);" onclick="toggleMessage(this)" style="color: #6366f1; font-weight: 600; display: block; margin-top: 6px; font-size: 0.85rem; text-decoration: none;">Read More</a>
                            <?php else: ?>
                                <?php echo nl2br($msg); ?>
                            <?php endif; ?>
                        </div>
                    </td>
                    <td>
                        <?php if ($c['status'] == 'Unread'): ?>
                            <span style="background:#ef4444; color:white; padding:4px 8px; border-radius:4px; font-size:0.8rem; font-weight:bold; display: inline-block;">Unread</span>
                        <?php else: ?>
                            <span style="background:#10b981; color:white; padding:4px 8px; border-radius:4px; font-size:0.8rem; font-weight:bold; display: inline-block;">Read</span>
                        <?php endif; ?>
                    </td>
                    <td style="white-space: nowrap;"><?php echo date('d M Y, h:i A', strtotime($c['created_at'])); ?></td>
                    <td style="white-space: nowrap;">
                        <a href="view_contacts.php?toggle_status=<?php echo $c['id']; ?>" style="color:var(--primary); font-weight:600; margin-right:12px; display: inline-flex; align-items: center; gap: 4px; vertical-align: middle;">
                            <ion-icon name="checkmark-done-outline" style="font-size: 1.2rem;"></ion-icon> <?php echo ($c['status'] == 'Unread') ? 'Mark Read' : 'Mark Unread'; ?>
                        </a>
                        <a href="view_contacts.php?delete=<?php echo $c['id']; ?>" onclick="return confirm('Delete this message?');" style="color:red; font-weight:600; display: inline-flex; align-items: center; vertical-align: middle;" title="Delete">
                            <ion-icon name="trash-outline" style="font-size: 1.2rem;"></ion-icon>
                        </a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </table>
        </div>
        <?php else: ?>
            <p style="color:var(--text-muted); text-align:center; padding: 2rem;">No messages yet.</p>
        <?php endif; ?>
    </div>
</div>

<script>
function toggleMessage(btn) {
    var td = btn.parentNode;
    var shortSpan = td.querySelector('.msg-short');
    var fullSpan = td.querySelector('.msg-full');
    if (fullSpan.style.display === 'none') {
        fullSpan.style.display = 'inline';
        shortSpan.style.display = 'none';
        btn.innerText = 'Read Less';
    } else {
        fullSpan.style.display = 'none';
        shortSpan.style.display = 'inline';
        btn.innerText = 'Read More';
    }
}
</script>

</body>
</html>

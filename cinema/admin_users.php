<?php require_once 'config.php'; ?>

<div class="section-header">
    <h2>User List</h2>
</div>

<div class="bookings-table-container">

    <table class="bookings-table">

        <thead>
            <tr>
                <th>User ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Role</th>
                <th>Status</th>
                <th>Date Created</th>
                <th>Action</th>
            </tr>
        </thead>

        <tbody>

        <?php
        // ONLY USERS (admins hidden)
        $result = $conn->query("
            SELECT * 
            FROM users 
            WHERE role = 'user'
            ORDER BY user_id DESC
        ");

        if ($result && $result->num_rows > 0) {

            while ($user = $result->fetch_assoc()) {
        ?>

            <tr>
                <td><?= $user['user_id'] ?></td>
                <td><?= htmlspecialchars($user['name']) ?></td>
                <td><?= htmlspecialchars($user['email']) ?></td>
                <td><?= htmlspecialchars($user['role']) ?></td>

                <!-- STATUS COLUMN -->
                <td>
                    <?php
                        // fallback if status column doesn't exist yet
                        echo isset($user['status']) && $user['status'] 
                            ? htmlspecialchars($user['status']) 
                            : 'active';
                    ?>
                </td>

                <td>
                    <?= !empty($user['created_at']) 
                        ? date('M d, Y', strtotime($user['created_at'])) 
                        : 'N/A' ?>
                </td>

                <td>
                    <button class="btn btn-edit btn-sm" onclick="editUser(<?= $user['user_id'] ?>)">
                        Edit
                    </button>

                    <button class="btn btn-delete btn-sm" onclick="deleteUser(<?= $user['user_id'] ?>)">
                        Delete
                    </button>
                </td>
            </tr>

        <?php
            }

        } else {
        ?>

            <tr>
                <td colspan="7" style="text-align:center; padding:20px; color:#aaa;">
                    No users yet
                </td>
            </tr>

        <?php } ?>

        </tbody>

    </table>

</div>

<script>

function editUser(id) {
    alert("Edit user ID: " + id + " (you can connect modal later)");
}

function deleteUser(id) {
    if (!confirm("Delete this user?")) return;

    fetch('delete_user.php?id=' + id)
    .then(res => res.json())
    .then(res => {
        if (res.success) location.reload();
        else alert(res.error || "Failed to delete user");
    });
}

</script>
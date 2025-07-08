<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Manage Faculty Users</title>
<style>
    .user-item {
        margin-bottom: 10px;
        border: 1px solid #ccc;
        padding: 10px;
    }
    .actions {
        margin-top: 5px;
    }
    .pagination {
        margin-top: 10px;
    }
    .pagination a {
        margin-right: 5px;
    }
    .pagination a.active {
        font-weight: bold;
    }
</style>
</head>
<body>
<div id="faculty-users">
    <!-- User items will be populated here -->
   
</div>

<script>
function deleteUser(userId) {
    if (confirm("Are you sure you want to delete this user?")) {
        document.getElementById('delete-form').faculty_id.value = userId;
        document.getElementById('delete-form').submit();
    }
}
</script>

<!-- Hidden form for delete action -->
<form id="delete-form" action="deletefaculty.php" method="post" style="display: none;">
    <input type="hidden" name="faculty_id" value="">
</form>

</body>
</html>

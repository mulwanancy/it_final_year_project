<?php
session_start();
include("../config/connect.php");

// ----- SIMULATE ROLE FOR TESTING -----
// Switch role via URL: ?role=admin or ?role=nurse
if(isset($_GET['role']) && in_array($_GET['role'], ['admin','nurse'])){
    $_SESSION['role'] = $_GET['role'];
}
$role = $_SESSION['role'] ?? 'nurse';

// Handle Add Nurse (Admin only)
if($role == 'admin' && isset($_POST['add_nurse'])){
    $nurse_number = $_POST['nurse_number'];
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $contact = $_POST['contact'];
    $email = $_POST['email'];
    $hire_date = $_POST['hire_date'];
    $basic_salary = $_POST['basic_salary'];
    $status = $_POST['status'];
    $department = $_POST['department'];
    $shift_name = $_POST['shift_name'];

    $stmt = $conn->prepare("INSERT INTO nurses (nurse_number, first_name, last_name, contact, email, hire_date, basic_salary, status, department, shift_name) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssdsss", $nurse_number, $first_name, $last_name, $contact, $email, $hire_date, $basic_salary, $status, $department, $shift_name);
    $stmt->execute();
    $stmt->close();
}

// Handle Delete Nurse (Admin only)
if($role == 'admin' && isset($_GET['delete'])){
    $id = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM nurses WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
}

// Handle Search
$search_query = "";
if(isset($_GET['search'])){
    $search_query = $_GET['search'];
    $search_query_safe = $conn->real_escape_string($search_query);
    $result = $conn->query("SELECT * FROM nurses WHERE nurse_number LIKE '%$search_query_safe%' OR first_name LIKE '%$search_query_safe%' OR last_name LIKE '%$search_query_safe%' OR department LIKE '%$search_query_safe%' OR shift_name LIKE '%$search_query_safe%'");
} else {
    $result = $conn->query("SELECT * FROM nurses");
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Nurses Management</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background-color: #f4f6f8; color: #333; }
        h2 { color: #0b3d91; }
        form input, form select, form button { padding: 8px; margin: 5px 0; width: 200px; }
        form button { width: auto; background-color: #0b3d91; color: white; border: none; cursor: pointer; padding: 8px 15px; }
        form button:hover { background-color: #062964; }
        .search-bar { margin: 15px 0; }
        .search-bar input { width: 300px; padding: 8px; }
        .search-bar button { padding: 8px 15px; background-color: #0b3d91; color: white; border: none; cursor: pointer; }
        .search-bar button:hover { background-color: #062964; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        table, th, td { border: 1px solid #0b3d91; }
        th, td { padding: 10px; text-align: left; }
        th { background-color: #0b3d91; color: white; }
        tr:nth-child(even) { background-color: #e6f2ff; }
        tr:hover { background-color: #cce0ff; }
        .actions a { margin-right: 8px; text-decoration: none; font-weight: bold; }
        .actions a.edit { color: #006600; }
        .actions a.delete { color: #cc0000; }
    </style>
</head>
<body>
    <h2>Nurses Management</h2>

    <!-- Admin Add Nurse Form -->
    <?php if($role == 'admin'): ?>
    <h3>Add Nurse</h3>
    <form method="POST" action="">
        <input type="text" name="nurse_number" placeholder="Nurse Number" required>
        <input type="text" name="first_name" placeholder="First Name" required>
        <input type="text" name="last_name" placeholder="Last Name" required>
        <input type="text" name="contact" placeholder="Contact">
        <input type="email" name="email" placeholder="Email">
        <input type="date" name="hire_date" placeholder="Hire Date">
        <input type="number" name="basic_salary" value="50000" placeholder="Basic Salary">
        <select name="status">
            <option value="Active">Active</option>
            <option value="On Leave">On Leave</option>
            <option value="Resigned">Resigned</option>
        </select>
        <input type="text" name="department" placeholder="Department" value="Maternity">
        <select name="shift_name">
            <option value="A">Shift A (6AM - 6PM)</option>
            <option value="B">Shift B (6PM - 6AM)</option>
            <option value="C">Shift C (6AM - 6PM)</option>
        </select>
        <button type="submit" name="add_nurse">Add Nurse</button>
    </form>
    <?php endif; ?>

    <!-- Search Bar -->
    <div class="search-bar">
        <form method="GET">
            <input type="text" name="search" placeholder="Search by Nurse Number, Name, Department, Shift" value="<?php echo htmlspecialchars($search_query); ?>">
            <button type="submit">Search</button>
        </form>
    </div>

    <!-- Nurses Table -->
    <table>
        <tr>
            <th>ID</th>
            <th>Nurse Number</th>
            <th>First Name</th>
            <th>Last Name</th>
            <th>Contact</th>
            <th>Email</th>
            <th>Hire Date</th>
            <th>Basic Salary</th>
            <th>Status</th>
            <th>Department</th>
            <th>Shift</th>
            <?php if($role == 'admin'): ?>
            <th>Actions</th>
            <?php endif; ?>
        </tr>
        <?php while($row = $result->fetch_assoc()) { ?>
        <tr>
            <td><?php echo $row['id']; ?></td>
            <td><?php echo $row['nurse_number']; ?></td>
            <td><?php echo $row['first_name']; ?></td>
            <td><?php echo $row['last_name']; ?></td>
            <td><?php echo $row['contact']; ?></td>
            <td><?php echo $row['email']; ?></td>
            <td><?php echo $row['hire_date']; ?></td>
            <td><?php echo $row['basic_salary']; ?></td>
            <td><?php echo $row['status']; ?></td>
            <td><?php echo $row['department']; ?></td>
            <td><?php echo $row['shift_name']; ?></td>
            <?php if($role == 'admin'): ?>
            <td class="actions">
                <a class="edit" href="edit_nurse.php?id=<?php echo $row['id']; ?>">Edit</a>
                <a class="delete" href="?delete=<?php echo $row['id']; ?>" onclick="return confirm('Delete this nurse?')">Delete</a>
            </td>
            <?php endif; ?>
        </tr>
        <?php } ?>
    </table>

    <p>
        <!-- Role switch for testing -->
        <a href="?role=admin">View as Admin</a> | 
        <a href="?role=nurse">View as Nurse</a>
    </p>
</body>
</html>

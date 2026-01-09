<?php
session_start();
include("../config/connect.php");

// ----- SIMULATE ROLE FOR TESTING -----
// Switch role via URL: ?role=admin or ?role=nurse
if(isset($_GET['role']) && in_array($_GET['role'], ['admin','nurse'])){
    $_SESSION['role'] = $_GET['role'];
}
$role = $_SESSION['role'] ?? 'nurse';

// ----------- Ensure we have logged-in nurse_number (try session, then DB) ----------
$nurse_number_session = $_SESSION['nurse_number'] ?? null;
if (!$nurse_number_session && isset($_SESSION['user_id'])) {
    $uid = $_SESSION['user_id'];
    $tmp = $conn->query("SELECT nurse_number FROM nurses WHERE id='$uid' LIMIT 1");
    if ($tmp && $tmp->num_rows > 0) {
        $nn = $tmp->fetch_assoc();
        $nurse_number_session = $nn['nurse_number'];
        // store to session for later use
        $_SESSION['nurse_number'] = $nurse_number_session;
    }
}

// ------------------ Admin: Add Payroll (do not insert into total_salary - it's generated) ------------------
if ($role == 'admin' && isset($_POST['add_payroll'])) {
    $nurse_number = trim($_POST['nurse_number']);
    $month = trim($_POST['month']); // expected "YYYY-MM" from <input type="month"> or "December 2025"
    $basic_salary = floatval($_POST['basic_salary']);
    $allowances = floatval($_POST['allowances']);
    $deductions = floatval($_POST['deductions']);

    // Prepared insert (do NOT include total_salary column because it is STORED GENERATED in DB)
    $stmt = $conn->prepare("INSERT INTO payroll (nurse_number, month, basic_salary, allowances, deductions) VALUES (?, ?, ?, ?, ?)");
    if ($stmt) {
        $stmt->bind_param("ssddd", $nurse_number, $month, $basic_salary, $allowances, $deductions);
        $stmt->execute();
        $stmt->close();
    } else {
        // debug (optional): uncomment to show error during testing
        // echo "Prepare failed: (" . $conn->errno . ") " . $conn->error;
    }
}

// ------------------ Helper: format month display ------------------
function format_month_display($raw) {
    $r = trim((string)$raw);
    if ($r === '') return '';
    // If format is YYYY-MM
    if (preg_match('/^\d{4}-\d{2}$/', $r)) {
        $dt = DateTime::createFromFormat('Y-m', $r);
        if ($dt) return $dt->format('F Y');
    }
    // If format is YYYY (year only)
    if (preg_match('/^\d{4}$/', $r)) {
        return $r; // can't show month name, show year
    }
    // If it already contains a month name like "December 2025"
    $ts = strtotime($r);
    if ($ts !== false) {
        return date("F Y", $ts);
    }
    // fallback: return raw
    return $r;
}

// ------------------ Build query (JOIN nurses + payroll) ------------------
$search_query = "";
$where = "";
$search_safe = "";

if(isset($_GET['search'])){
    $search_query = trim($_GET['search']);
    $search_safe = $conn->real_escape_string($search_query);
}

$base_select = "
    SELECT 
        p.id,
        n.first_name,
        n.last_name,
        p.nurse_number,
        p.month,
        p.basic_salary,
        p.allowances,
        p.deductions,
        p.total_salary
    FROM nurses n
    LEFT JOIN payroll p ON n.nurse_number = p.nurse_number
";

// build where/filter depending on role & search
if ($role == 'nurse') {
    // restrict to logged-in nurse
    if ($nurse_number_session) {
        $where = " WHERE n.nurse_number = '" . $conn->real_escape_string($nurse_number_session) . "' ";
        if ($search_safe !== '') {
            // further filter if search term provided (search by nurse number or name)
            $where .= " AND (n.nurse_number LIKE '%$search_safe%' OR n.first_name LIKE '%$search_safe%' OR n.last_name LIKE '%$search_safe%') ";
        }
    } else {
        // no nurse_number available -> return no rows
        $where = " WHERE 1=0 ";
    }
} else {
    // admin - can search
    if ($search_safe !== '') {
        $where = " WHERE n.nurse_number LIKE '%$search_safe%' OR n.first_name LIKE '%$search_safe%' OR n.last_name LIKE '%$search_safe%' ";
    } else {
        $where = ""; // no restriction
    }
}

// final query (order by payroll id DESC so latest appears first)
$query = $base_select . $where . " ORDER BY p.id DESC";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Payroll - NurseShift Payroll</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; background-color: #f4f6f9; color: #333; }

        /* Navbar */
        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #0b3d91;
            padding: 20px 40px;
            min-height: 90px;
        }
        .navbar .logo-container { display: flex; align-items: center; }
        .navbar .logo-container img { height: 80px; margin-right: 10px; }
        .navbar .logo-text { display: flex; flex-direction: column; }
        .navbar .logo { font-size: 24px; font-weight: bold; color: #ffcc00; }
        .navbar .tagline { font-size: 14px; font-style: italic; color: #ffcc00; margin-top: 2px; }
        .navbar ul { list-style: none; display: flex; gap: 20px; margin: 0; padding: 0; margin-left:auto; }
        .navbar ul li a { color: #fff; text-decoration: none; font-size: 16px; }
        .navbar ul li a:hover { color: #ffcc00; }

        h2 { color: #0b3d91; margin:20px 40px 0 40px; }

        form input, form button { padding: 8px; margin: 5px 0; width: 200px; }
        form button { width: auto; background-color: #0b3d91; color: white; border: none; cursor: pointer; padding: 8px 15px; }
        form button:hover { background-color: #062964; }

        .search-bar { margin: 15px 40px; }
        .search-bar input { width: 300px; padding: 8px; }
        .search-bar button { padding: 8px 15px; background-color: #0b3d91; color: white; border: none; cursor: pointer; }
        .search-bar button:hover { background-color: #062964; }

        table { width: 90%; margin: 20px 40px; border-collapse: collapse; }
        table, th, td { border: 1px solid #0b3d91; }
        th, td { padding: 10px; text-align: left; }
        th { background-color: #0b3d91; color: white; }
        tr:nth-child(even) { background-color: #e6f2ff; }
        tr:hover { background-color: #cce0ff; }
        .actions a { margin-right: 8px; text-decoration: none; font-weight: bold; }
        .actions a.edit { color: #006600; }
        .actions a.delete { color: #cc0000; }

        .download-btn { margin: 0 40px 20px 40px; padding: 10px 15px; background: #0b3d91; color: #fff; border: none; cursor: pointer; }
        .download-btn:hover { background: #062964; }
    </style>
</head>
<body>

<!-- Navbar -->
<div class="navbar">
    <div class="logo-container">
        <img src="http://localhost/nurseshift_payroll/public/images/logo.jpg" alt="NurseShift Logo">
        <div class="logo-text">
            <div class="logo">NurseShift Payroll</div>
            <div class="tagline">Empowering maternity healthcare teams with seamless management.</div>
        </div>
    </div>
    <ul>
        <li><a href="dashboard.php">Dashboard</a></li>
        <li><a href="payroll.php">Payroll</a></li>
        <li><a href="logout.php">Logout</a></li>
    </ul>
</div>

<h2>Payroll Management</h2>

<!-- Admin Add Form -->
<?php if($role == 'admin'): ?>
<h3 style="margin-left:40px;">Add Payroll</h3>
<form method="POST" action="" style="margin-left:40px;">
    Nurse Number: <input type="text" name="nurse_number" required><br>
    <!-- Input type month allows YYYY-MM format in many browsers -->
    Month: <input type="month" name="month" required><br>
    Basic Salary: <input type="number" step="0.01" name="basic_salary" required><br>
    Allowances: <input type="number" step="0.01" name="allowances" value="0"><br>
    Deductions: <input type="number" step="0.01" name="deductions" value="0"><br>
    <button type="submit" name="add_payroll">Add Payroll</button>
</form>
<?php endif; ?>

<!-- Search -->
<div class="search-bar">
    <form method="GET">
        <input type="text" name="search" placeholder="Search by Nurse Number or Name" value="<?php echo htmlspecialchars($search_query); ?>">
        <button type="submit">Search</button>
    </form>
</div>

<!-- Payroll Table -->
<table>
    <tr>
        <th>ID</th>
        <th>Nurse Number</th>
        <th>Name</th>
        <th>Month</th>
        <th>Basic Salary</th>
        <th>Allowances</th>
        <th>Deductions</th>
        <th>Total Salary</th>
        <?php if($role=='admin') echo "<th>Actions</th>"; ?>
    </tr>

    <?php if ($result && $result->num_rows > 0): ?>
        <?php while($row = $result->fetch_assoc()) { ?>
        <tr>
            <td><?php echo htmlspecialchars($row['id']); ?></td>
            <td><?php echo htmlspecialchars($row['nurse_number']); ?></td>
            <td><?php echo htmlspecialchars($row['first_name'] . ' ' . $row['last_name']); ?></td>
            <td>
                <?php
                    // format month intelligently
                    echo htmlspecialchars(format_month_display($row['month']));
                ?>
            </td>
            <td><?php echo number_format((float)$row['basic_salary'], 2); ?></td>
            <td><?php echo number_format((float)$row['allowances'], 2); ?></td>
            <td><?php echo number_format((float)$row['deductions'], 2); ?></td>
            <td><?php echo number_format((float)$row['total_salary'], 2); ?></td>
            <?php if($role=='admin'): ?>
            <td class="actions">
                <a class="edit" href="edit_payroll.php?id=<?php echo $row['id']; ?>">Edit</a>
                <a class="delete" href="delete_payroll.php?id=<?php echo $row['id']; ?>" onclick="return confirm('Delete this payroll?')">Delete</a>
            </td>
            <?php endif; ?>
        </tr>
        <?php } ?>
    <?php else: ?>
        <tr>
            <td colspan="<?php echo ($role=='admin') ? 9 : 8; ?>" style="text-align:center; color:#cc0000;">
                No payroll records found.
            </td>
        </tr>
    <?php endif; ?>
</table>

<!-- Download Payroll -->
<form method="GET" action="download_payroll.php" target="_blank" style="margin-left:40px;">
    <button type="submit" class="download-btn">Download Payroll</button>
</form>

</body>
</html>

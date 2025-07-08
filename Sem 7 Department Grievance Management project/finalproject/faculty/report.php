<?php include("header_sidenavbar.php");?>
</br>
</br>
</br>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
  
    <title>All Complaints</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f9;
        }
        .container {
            max-width: 1200px;
            margin: 40px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }
        h1 {
            text-align: center;
            margin-bottom: 20px;
            font-size: 28px;
            color: #333;
        }
        .filter-form {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            gap: 10px;
        }
        .filter-form label {
            font-weight: bold;
            color: #555;
        }
        .filter-form input[type="date"], .filter-form select {
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            width: 100%;
            max-width: 180px;
            box-sizing: border-box;
        }
        .filter-form button[type="submit"] {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            background-color: #4CAF50;
            color: #fff;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s;
        }
        .filter-form button[type="submit"]:hover {
            background-color: #45a049;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            font-size: 16px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
            color: #333;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        @media screen and (max-width: 600px) {
            .filter-form {
                flex-direction: column;
                align-items: flex-start;
            }
            .filter-form input[type="date"], .filter-form select, .filter-form button[type="submit"] {
                max-width: 100%;
                margin-bottom: 10px;
            }
        }
    </style>
</head>
<body>

<div class="container">
    <h1>All Complaints</h1>
    
    <!-- Filter Form -->
    <form class="filter-form" action="" method="get">
        <label for="dueDateFilter">Due Date:</label>
        <input type="date" id="dueDateFilter" name="due_date">
        
        <label for="endDateFilter">End Date:</label>
        <input type="date" id="endDateFilter" name="end_date">
        
        <label for="statusFilter">Status:</label>
        <select id="statusFilter" name="status">
            <option value="">Select Status</option>
            <option value="Pending">Pending</option>
            <option value="Processing">Processing</option>
            <option value="Resolved">Resolved</option>
        </select>

        <?php
        // Fetch categories from the category table
        include '../php/config.php';
        $sql = "SELECT complaint_category_id, category_description FROM complaint_category";
        $result = $con->query($sql);

        if ($result && $result->num_rows > 0) {
            echo "<label for='categoryFilter'>Category:</label>";
            echo "<select id='categoryFilter' name='category'>";
            echo "<option value=''>Select Category</option>";
            while ($row = $result->fetch_assoc()) {
                echo "<option value='{$row['complaint_category_id']}'>{$row['category_description']}</option>";
            }
            echo "</select>";
        }
        ?>
    
        <button type="submit">Filter</button>
    </form>

    <table>
        <thead>
            <tr>
                <th>Complaint ID</th>
                <th>Due Date</th>
                <th>End Date</th>
                <th>Status</th>
                <th>Complaint Category</th>
            </tr>
        </thead>
        <tbody>
        <?php
        include '../php/config.php';

        if ($con) {
            $whereClause = '';

            if (isset($_GET['due_date']) && !empty($_GET['due_date'])) {
                $dueDate = $_GET['due_date'];
                $whereClause .= "Due_Date = '$dueDate'";
            }

            if (isset($_GET['end_date']) && !empty($_GET['end_date'])) {
                $endDate = $_GET['end_date'];
                $whereClause .= ($whereClause ? ' AND ' : '') . "End_Date = '$endDate'";
            }

            if (isset($_GET['status']) && !empty($_GET['status'])) {
                $status = $_GET['status'];
                $whereClause .= ($whereClause ? ' AND ' : '') . "Status = '$status'";
            }

            if (isset($_GET['category']) && !empty($_GET['category'])) {
                $category = $_GET['category'];
                $whereClause .= ($whereClause ? ' AND ' : '') . "c.Complaint_Cat_ID = '$category'";
            }

            $sql = "SELECT c.Comp_ID, c.Due_Date, c.End_Date, c.Status, cc.category_description AS `Complaint Category` 
                    FROM complaints c 
                    LEFT JOIN complaint_category cc ON c.Complaint_Cat_ID = cc.complaint_category_id";
            if ($whereClause) {
                $sql .= " WHERE $whereClause";
            }

            $result = $con->query($sql);

            if ($result && $result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>{$row['Comp_ID']}</td>";
                    echo "<td>{$row['Due_Date']}</td>";
                    echo "<td>{$row['End_Date']}</td>";
                    echo "<td>{$row['Status']}</td>";
                    echo "<td>{$row['Complaint Category']}</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='5'>No complaints found</td></tr>";
            }

            $result->close();
        } else {
            echo "<tr><td colspan='5'>Failed to connect to the database.</td></tr>";
        }

        $con->close();
        ?>
        </tbody>
    </table>
</div>
</body>
</html>

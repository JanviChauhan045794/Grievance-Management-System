<?php
// Start output buffering to prevent "headers already sent" errors
ob_start();

session_start();
require '../vendor/autoload.php'; // Include PHPSpreadsheet library

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Include database configuration
include("../php/config.php");

// Check if the faculty ID exists in the session
if (!isset($_SESSION['faculty_id'])) {
    die("Error: Faculty ID not found in session.");
}
$faculty_id = $_SESSION['faculty_id'];

// Initialize filters array and parameter bindings
$filters = [];
$params = [];
$types = "i";  // Initial type for faculty ID

// 1. Filter by Due Date (e.g., "Due Date >= 2024-12-20")
if (!empty($_GET['Due_Date'])) {
    $filters[] = "c.Due_Date >= ?";
    $params[] = $_GET['Due_Date'];
    $types .= "s";  // Adding string type for Due Date
}

// 2. Filter by End Date (e.g., "End Date <= 2024-12-30")
if (!empty($_GET['End_Date'])) {
    $filters[] = "c.End_Date <= ?";
    $params[] = $_GET['End_Date'];
    $types .= "s";  // Adding string type for End Date
}

// 3. Filter by Status (e.g., "Status = 'processing'")
if (!empty($_GET['status'])) {
    $filters[] = "c.status = ?";
    $params[] = $_GET['status'];
    $types .= "s";  // Adding string type for Status
}

// 4. Filter by Complaint Category (e.g., "category_description = 'Technical Issues'")
if (!empty($_GET['category_description'])) {
    $filters[] = "cc.category_description = ?";
    $params[] = $_GET['category_description'];
    $types .= "s";  // Adding string type for category_description
}

// Construct the SQL query with the dynamic WHERE clause
$filterSQL = $filters ? " AND " . implode(" AND ", $filters) : "";

// Construct the SQL query to fetch complaints data
$sqlQuery = "
    SELECT c.Comp_ID, c.complaint_datetime, c.Due_Date, c.End_Date, c.Status, 
           cc.category_description AS Complaint_Category, 
           CONCAT(u.firstname, ' ', u.lastname) AS Faculty_Name
    FROM complaints c
    JOIN complaint_category cc ON c.Complaint_Cat_ID = cc.complaint_category_id
    JOIN faculty f ON c.Faculty_ID = f.faculty_id
    JOIN users u ON f.user_id = u.user_id
    WHERE c.Faculty_ID = ? 
    $filterSQL
    ORDER BY c.complaint_datetime DESC";

// Merge faculty_id and additional parameters for dynamic binding
$params = array_merge([$faculty_id], $params);

// Prepare the statement
$stmt = $con->prepare($sqlQuery);

// Bind parameters dynamically
$stmt->bind_param($types, ...$params);

// Execute the query
$stmt->execute();
$result = $stmt->get_result();

// Handle SQL errors
if (!$result) {
    error_log("SQL Error: " . $stmt->error);
    die("Error fetching data.");
}

// Check if there are rows to export
if ($result->num_rows === 0) {
    die("No data found to export.");
}

// Create a new spreadsheet
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Set header row for the spreadsheet
$headers = ['Complaint ID', 'Complaint Date', 'Due Date', 'End Date', 'Status', 'Complaint Category', 'Faculty Name'];
$sheet->fromArray($headers, NULL, 'A1');

// Style the header row
$headerStyle = [
    'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
    'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '4CAF50']],
    'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
];
$sheet->getStyle('A1:G1')->applyFromArray($headerStyle); // Adjusted for 7 columns

// Populate data rows
$rowIndex = 2; // Start from the second row
while ($row = $result->fetch_assoc()) {
    $sheet->fromArray(array_values($row), NULL, 'A' . $rowIndex);
    $rowIndex++;
}

// Add a timestamp at the bottom
$sheet->setCellValue('A' . $rowIndex, 'Exported on: ' . date('Y-m-d H:i:s'));
$sheet->mergeCells("A$rowIndex:G$rowIndex"); // Merge cells for timestamp
$sheet->getStyle("A$rowIndex")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

// Auto-adjust column widths
foreach (range('A', 'G') as $column) { // Adjusted for 7 columns
    $sheet->getColumnDimension($column)->setAutoSize(true);
}

// Set the filename for download
$filename = "Filtered_Complaints_" . date('Y-m-d_H-i-s') . ".xlsx";

// Clean any previous output that might have been sent
ob_end_clean(); // This will discard any previous output in the buffer

// Send headers to prompt download
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header("Content-Disposition: attachment; filename=\"$filename\"");

// Write the spreadsheet to output
$writer = new Xlsx($spreadsheet);
$writer->save('php://output');

// End the script after output
exit;
?>

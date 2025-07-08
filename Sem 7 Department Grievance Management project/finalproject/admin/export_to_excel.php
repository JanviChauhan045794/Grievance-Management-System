<?php
require_once '../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

include '../php/config.php';

// Create a new spreadsheet
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Add title to the spreadsheet
$sheet->setCellValue('A1', 'Filtered Complaints Report');
$sheet->mergeCells('A1:G1');
$sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
$sheet->getStyle('A1')->getAlignment()->setHorizontal('center');

// Add headers
$headers = [
    'Complaint ID',
    'Complaint Datetime',
    'Due Date',
    'End Date',
    'Status',
    'Complaint Category',
    'Assigned Faculty'
];
$sheet->fromArray($headers, null, 'A2');

// Style the header row
$headerStyle = [
    'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4CAF50']],
    'alignment' => ['horizontal' => 'center'],
    'borders' => ['bottom' => ['borderStyle' => Border::BORDER_THIN]]
];
$sheet->getStyle('A2:G2')->applyFromArray($headerStyle);

// Prepare filters from GET parameters
$whereClause = [];
$debugMessages = [];

// Debugging: Log incoming GET parameters
$debugMessages[] = "GET Parameters: " . json_encode($_GET);

// Add filters dynamically based on GET parameters
if (!empty($_GET['due_date'])) {
    $dueDate = $con->real_escape_string($_GET['due_date']);
    $whereClause[] = "c.Due_Date >= '$dueDate'";
    $debugMessages[] = "Filtering by Due Date: $dueDate";
}

if (!empty($_GET['end_date'])) {
    $endDate = $con->real_escape_string($_GET['end_date']);
    $whereClause[] = "c.End_Date <= '$endDate'";
    $debugMessages[] = "Filtering by End Date: $endDate";
}

if (!empty($_GET['status'])) {
    $status = $con->real_escape_string($_GET['status']);
    $whereClause[] = "c.Status = '$status'";
    $debugMessages[] = "Filtering by Status: $status";
}

if (!empty($_GET['category'])) {
    $category = (int) $_GET['category'];
    $whereClause[] = "c.Complaint_Cat_ID = $category";
    $debugMessages[] = "Filtering by Category ID: $category";
}

if (!empty($_GET['faculty'])) {
    $faculty = (int) $_GET['faculty'];
    $whereClause[] = "c.Faculty_ID = $faculty";
    $debugMessages[] = "Filtering by Faculty ID: $faculty";
}

// Base SQL query (without filters)
$query = "SELECT c.Comp_ID, 
                 c.complaint_datetime, 
                 c.Due_Date, 
                 c.End_Date, 
                 c.Status, 
                 cc.category_description AS Complaint_Category, 
                 CONCAT(u.firstname, ' ', u.lastname) AS Faculty_Name
          FROM complaints c
          JOIN complaint_category cc ON c.Complaint_Cat_ID = cc.complaint_category_id
          JOIN faculty f ON c.Faculty_ID = f.faculty_id
          JOIN users u ON f.user_id = u.user_id";

// Add the filters to the SQL query
if (!empty($whereClause)) {
    $query .= " WHERE " . implode(' AND ', $whereClause);
}

// Ensure that the data is ordered by the complaint date
$query .= " ORDER BY c.complaint_datetime DESC";

// Debugging: Log the final SQL query
$debugMessages[] = "SQL Query: " . $query;

// Run the query
$result = $con->query($query);

// Check for query errors
if (!$result) {
    die("Query failed: " . $con->error);
}

$rowIndex = 3; // Start row for data

// Collect debug messages for output
$debugMessages[] = "Number of rows returned: " . $result->num_rows;

// If no rows match the filters
if ($result->num_rows > 0) {
    // Loop through the rows and populate the spreadsheet
    while ($row = $result->fetch_assoc()) {
        $sheet->fromArray([
            $row['Comp_ID'],
            $row['complaint_datetime'],
            $row['Due_Date'],
            $row['End_Date'],
            $row['Status'],
            $row['Complaint_Category'],
            $row['Faculty_Name']
        ], null, "A$rowIndex");
        $rowIndex++;
    }
} else {
    // If no rows match, show this message
    $sheet->setCellValue("A3", "No data found matching the filters.");
    $sheet->mergeCells("A3:G3");
    $sheet->getStyle("A3")->getAlignment()->setHorizontal('center');
}

// Add borders around all data cells
$sheet->getStyle("A2:G" . max(2, $rowIndex - 1))->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

// Add export date and time
$dateTime = date('Y-m-d_H-i-s');
$sheet->setCellValue("A$rowIndex", "Exported on: " . date('Y-m-d H:i:s'));
$sheet->mergeCells("A$rowIndex:G$rowIndex");

// Output the file
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header("Content-Disposition: attachment;filename=complaints_report_$dateTime.xlsx");
header('Cache-Control: max-age=0');

// Clear the output buffer and send the file
ob_clean();
flush();

// Write the file to output
$writer = new Xlsx($spreadsheet);
$writer->save('php://output');

// Log debug messages to error log for review
error_log(implode("\n", $debugMessages));

exit;
?>

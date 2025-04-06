<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Job Cards</title>
    <link rel="stylesheet" href="css.css/style.css">
</head>
<body>
    <h1>Job Cards</h1>
    <table border="1">
        <thead>
            <tr>
                <th>Job No</th>
                <th>Date</th>
                <th>Customer Name</th>
                <th>Technician Name</th>
                <th>Date Started</th>
                <th>Date Finished</th>
                <th>Machine Serial Number</th>
                <th>Job Description</th>
                <th>Spare Part</th>
                <th>Quantity</th>
                <th>Unit Cost</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Database connection
            $conn = new mysqli('localhost', 'root', '', 'pekar');

            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }

            // Fetch job cards from the database
            $sql = "SELECT jobNo, date, customer_name, technician_name, date_started, date_finished FROM card ORDER BY jobNo";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    $jobNo = $row["jobNo"];
                    echo "<tr>";
                    echo "<td>" . $row["jobNo"] . "</td>";
                    echo "<td>" . $row["date"] . "</td>";
                    echo "<td>" . $row["customer_name"] . "</td>";
                    echo "<td>" . $row["technician_name"] . "</td>";
                    echo "<td>" . $row["date_started"] . "</td>";
                    echo "<td>" . $row["date_finished"] . "</td>";

                    // Fetch items for the current job card
                    $item_sql = "SELECT machine_serial_number, job_description FROM card_item WHERE jobNo = '$jobNo'";
                    $item_result = $conn->query($item_sql);
                    if ($item_result->num_rows > 0) {
                        while($item_row = $item_result->fetch_assoc()) {
                            echo "<td>" . $item_row["machine_serial_number"] . "</td>";
                            echo "<td>" . $item_row["job_description"] . "</td>";
                        }
                    } else {
                        echo "<td colspan='2'>No items found</td>";
                    }

                    // Fetch spares for the current job card
                    $spare_sql = "SELECT spare_part, quantity, unit_cost, total FROM card_spare WHERE jobNo = '$jobNo'";
                    $spare_result = $conn->query($spare_sql);
                    if ($spare_result->num_rows > 0) {
                        while($spare_row = $spare_result->fetch_assoc()) {
                            echo "<td>" . $spare_row["spare_part"] . "</td>";
                            echo "<td>" . $spare_row["quantity"] . "</td>";
                            echo "<td>" . $spare_row["unit_cost"] . "</td>";
                            echo "<td>" . $spare_row["total"] . "</td>";
                        }
                    } else {
                        echo "<td colspan='4'>No spares found</td>";
                    }

                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='12'>No job cards found</td></tr>";
            }

            $conn->close();
            ?>
        </tbody>
    </table>
</body>
</html>
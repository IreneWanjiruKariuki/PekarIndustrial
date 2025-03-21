<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Delivery Notes</title>
    <link rel="stylesheet" href="css.css/style.css">
</head>
<body>
    <h1>Delivery Notes</h1>
    <table border="1">
        <thead>
            <tr>
                <th>Delivery No</th>
                <th>Deliver To</th>
                <th>LPO/Invoice No</th>
                <th>Dated</th>
                <th>Delivery Date</th>
                <th>Delivered By</th>
                <th>Item</th>
                <th>Description</th>
                <th>Unit</th>
                <th>Quantity</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Database connection
            $conn = new mysqli('localhost', 'root', '', 'pekar');

            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }

            // Fetch delivery notes from the database
            $sql = "SELECT delivery_no, deliver_to, lpo_no, dated, delivery_date, delivered_by, item, description, unit, quantity FROM note";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                // Output data of each row
                while($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $row["delivery_no"] . "</td>";
                    echo "<td>" . $row["deliver_to"] . "</td>";
                    echo "<td>" . $row["lpo_no"] . "</td>";
                    echo "<td>" . $row["dated"] . "</td>";
                    echo "<td>" . $row["delivery_date"] . "</td>";
                    echo "<td>" . $row["delivered_by"] . "</td>";
                    echo "<td>" . $row["item"] . "</td>";
                    echo "<td>" . $row["description"] . "</td>";
                    echo "<td>" . $row["unit"] . "</td>";
                    echo "<td>" . $row["quantity"] . "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='10'>No delivery notes found</td></tr>";
            }

            $conn->close();
            ?>
        </tbody>
    </table>
</body>
</html>
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

            // Fetch delivery notes and their items from the database
            $sql = "SELECT n.delivery_no, n.deliver_to, n.lpo_no, n.dated, n.delivery_date, n.delivered_by, 
                           ni.item, ni.description, ni.unit, ni.quantity
                    FROM note n
                    LEFT JOIN note_item ni ON n.delivery_no = ni.delivery_no
                    ORDER BY n.delivery_no, ni.item";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                $current_delivery_no = null;
                while($row = $result->fetch_assoc()) {
                    if ($current_delivery_no !== $row["delivery_no"]) {
                        if ($current_delivery_no !== null) {
                            echo "</tr>";
                        }
                        $current_delivery_no = $row["delivery_no"];
                        echo "<tr>";
                        echo "<td>" . $row["delivery_no"] . "</td>";
                        echo "<td>" . $row["deliver_to"] . "</td>";
                        echo "<td>" . $row["lpo_no"] . "</td>";
                        echo "<td>" . $row["dated"] . "</td>";
                        echo "<td>" . $row["delivery_date"] . "</td>";
                        echo "<td>" . $row["delivered_by"] . "</td>";
                    } else {
                        echo "<tr><td colspan='6'></td>";
                    }
                    echo "<td>" . $row["item"] . "</td>";
                    echo "<td>" . $row["description"] . "</td>";
                    echo "<td>" . $row["unit"] . "</td>";
                    echo "<td>" . $row["quantity"] . "</td>";
                    echo "</tr>";
                }
                // Close the last delivery note row
                echo "</tr>";
            } else {
                echo "<tr><td colspan='10'>No delivery notes found</td></tr>";
            }

            $conn->close();
            ?>
        </tbody>
    </table>
</body>
</html>
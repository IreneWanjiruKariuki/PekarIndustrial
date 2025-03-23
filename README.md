<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Invoices</title>
    <link rel="stylesheet" href="css.css/style.css">
</head>
<body>
    <h1>Invoices</h1>
    <table border="1">
        <thead>
            <tr>
                <th>Name</th>
                <th>Invoice No</th>
                <th>Address</th>
                <th>LPO No</th>
                <th>Contact</th>
                <th>Delivery No</th>
                <th>Tel</th>
                <th>Date</th>
                <th>Item Code</th>
                <th>Description</th>
                <th>Quantity</th>
                <th>Unit Price</th>
                <th>Total Cost</th>
                <th>Total</th>
                <th>VAT</th>
                <th>Grand Total</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Database connection
            $conn = new mysqli('localhost', 'root', '', 'pekar');

            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }

            // Fetch invoices from the database
            $sql = "SELECT name, invoice_no, address, lpo_no, contact, delivery_no, tel, date,  total, vat, grand_total FROM invoice";
            $sql2 = "SELECT item_code, description, quantity, unit_price, total_cost FROM invoice_item";
            $result = $conn->query($sql);
            $result2 = $conn->query($sql2);

            if ($result->num_rows > 0) {
                // Output data of each row
                while($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $row["name"] . "</td>";
                    echo "<td>" . $row["invoice_no"] . "</td>";
                    echo "<td>" . $row["address"] . "</td>";
                    echo "<td>" . $row["lpo_no"] . "</td>";
                    echo "<td>" . $row["contact"] . "</td>";
                    echo "<td>" . $row["delivery_no"] . "</td>";
                    echo "<td>" . $row["tel"] . "</td>";
                    echo "<td>" . $row["date"] . "</td>";
                    echo "<td>" . $row["item_code"] . "</td>";
                    echo "<td>" . $row["description"] . "</td>";
                    echo "<td>" . $row["quantity"] . "</td>";
                    echo "<td>" . $row["unit_price"] . "</td>";
                    echo "<td>" . $row["total_cost"] . "</td>";
                    echo "<td>" . $row["total"] . "</td>";
                    echo "<td>" . $row["vat"] . "</td>";
                    echo "<td>" . $row["grand_total"] . "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='16'>No invoices found</td></tr>";
            }

            $conn->close();
            ?>
        </tbody>
    </table>
</body>
</html>
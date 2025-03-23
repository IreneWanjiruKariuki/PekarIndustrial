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

            // Fetch invoices and their items from the database
            $sql = "SELECT i.name, i.invoice_no, i.address, i.lpo_no, i.contact, i.delivery_no, i.tel, i.date, 
                           ii.item_code, ii.description, ii.quantity, ii.unit_price, ii.total_cost, 
                           i.total, i.vat, i.grand_total
                    FROM invoice i
                    LEFT JOIN invoice_item ii ON i.invoice_no = ii.invoice_no
                    ORDER BY i.invoice_no, ii.item_code";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                $current_invoice_no = null;
                while($row = $result->fetch_assoc()) {
                    if ($current_invoice_no !== $row["invoice_no"]) {
                        if ($current_invoice_no !== null) {
                            // Close the previous invoice row
                            echo "<td>" . $total . "</td>";
                            echo "<td>" . $vat . "</td>";
                            echo "<td>" . $grand_total . "</td>";
                            echo "</tr>";
                        }
                        $current_invoice_no = $row["invoice_no"];
                        $total = $row["total"];
                        $vat = $row["vat"];
                        $grand_total = $row["grand_total"];
                        echo "<tr>";
                        echo "<td>" . $row["name"] . "</td>";
                        echo "<td>" . $row["invoice_no"] . "</td>";
                        echo "<td>" . $row["address"] . "</td>";
                        echo "<td>" . $row["lpo_no"] . "</td>";
                        echo "<td>" . $row["contact"] . "</td>";
                        echo "<td>" . $row["delivery_no"] . "</td>";
                        echo "<td>" . $row["tel"] . "</td>";
                        echo "<td>" . $row["date"] . "</td>";
                    } else {
                        echo "<tr><td colspan='8'></td>";
                    }
                    echo "<td>" . $row["item_code"] . "</td>";
                    echo "<td>" . $row["description"] . "</td>";
                    echo "<td>" . $row["quantity"] . "</td>";
                    echo "<td>" . $row["unit_price"] . "</td>";
                    echo "<td>" . $row["total_cost"] . "</td>";
                }
                // Close the last invoice row
                echo "<td>" . $total . "</td>";
                echo "<td>" . $vat . "</td>";
                echo "<td>" . $grand_total . "</td>";
                echo "</tr>";
            } else {
                echo "<tr><td colspan='16'>No invoices found</td></tr>";
            }

            $conn->close();
            ?>
        </tbody>
    </table>
</body>
</html>
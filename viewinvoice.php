<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Invoices</title>
    <link rel="stylesheet" href="css.css/style.css">
</head>
<body background="images/img5.jpg">
<nav>
    
    <a href="./">HOME</a>
    <a href="viewcard.php">VIEW JOB CARDS</a>
    <a href="viewnote.php">VIEW DELIVERY NOTES</a>
    <a href="viewinvoice.php">VIEW INVOICES</a>
    <div class="search-container">
        <input type="text" placeholder="Search..." id="search">
        <button type="submit">🔍</button>
    </div>
</nav>
<div class="cont">
        <img src="images/image.png" width="1255" height="150" class="d-inline-block align-top" alt="Logo">
    </div>
    <h1>Invoices</h1>
    <table >
        <thead>
            <tr>
                <th>Invoice No</th>
                <th>LPO No</th>
                <th>Name</th>
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

            // Fetch the required columns from the invoice table and order by invoice_no in descending order
            $sql = "SELECT invoice_no, lpo_no, name, total, vat, grand_total FROM invoice ORDER BY invoice_no DESC";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $row["invoice_no"] . "</td>";
                    echo "<td>" . $row["lpo_no"] . "</td>";
                    echo "<td>" . $row["name"] . "</td>";
                    echo "<td>" . $row["total"] . "</td>";
                    echo "<td>" . $row["vat"] . "</td>";
                    echo "<td>" . $row["grand_total"] . "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='6'>No invoices found</td></tr>";
            }

            $conn->close();
            ?>
        </tbody>
    </table>
</body>
</html>
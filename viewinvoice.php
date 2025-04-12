<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Invoices</title>
    <link rel="stylesheet" href="css.css/style.css">
    <style>
        table {
            width: 90%; /* Optional: Makes the table take full width */
            border-collapse: collapse; /* Ensures no double borders */
        }

        table th, table td {
            padding: 8px; /* Adds padding for better spacing */
            text-align: left; /* Aligns text to the left */
        }

        /* Set specific column widths */
        table th:nth-child(1), table td:nth-child(1) {
            width: 11%; /* Adjust Job No column width */
        }

        table th:nth-child(2), table td:nth-child(2) {
            width: 7%; /* Adjust Customer Name column width */
        }

        table th:nth-child(3), table td:nth-child(3) {
            width: 28%; /* Adjust LPO No column width */
        }
        table th:nth-child(4), table td:nth-child(4) {
            width: 10%; /* Adjust Date column width */
        }
        table th:nth-child(5), table td:nth-child(5) {
            width: 9%; /* Adjust Date column width */
        }
        table th:nth-child(6), table td:nth-child(6) {
            width: 13%; /* Adjust Date column width */
        }
        td{
    border: 1px solid #373737;
    padding: 5px;
    text-align: left;
}
th{
    background-color:white;
    color:#373737;
    border: 1px solid #373737;
    padding: 5px;
    text-align: left;
}
tr:nth-child(odd){
    background-color:rgba(224, 224, 226, 0.68);
}
tr:hover{
    color:#373737;
    background-color: azure;
    text-decoration: none;
}
    </style>
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
        <img src="images/image.png" width="1250" height="150" class="d-inline-block align-top" alt="Logo">
    </div>
    
    <h1>Invoices</h1>
    <table style="margin-bottom: 20px;background-color: white;">
        <thead>
            <tr>
                <th>INVOICE NO</th>
                <th>LPO NO</th>
                <th>NAME</th>
                <th>TOTAL</th>
                <th>VAT</th>
                <th>GRAND TOTAL</th>
                <th>ACTION</th>
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
                    echo "<td>";
                echo "<button onclick=\"window.location.href='editinvoice.php?invoice_no=" . $row["invoice_no"] . "'\">Edit</button>";
                echo " ";
                echo "<button onclick=\"window.location.href='downloadinvoice.php?invoice_no=" . $row["invoice_no"] . "'\">Download</button>";
                echo " ";
                echo "<button onclick=\"if(confirm('Are you sure you want to delete this invoice?')) window.location.href='deleteinvoice.php?invoice_no=" . $row["invoice_no"] . "'\">Delete</button>";
                echo "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='6'>No invoices found</td></tr>";
            }

            $conn->close();
            ?>
        </tbody>
    </table>
    </div>
    <footer style="margin-top: 20px;">
        <div class="footer-container">
            <!-- Section 1 -->
            <div class="footer-section">
                <h3>Pekar industrial & construction LTD</h3>
                <ul>
                    <li><h4>Location: Kasarani Mwiki Road</h4></li>
                    <li><h4>P.O Box 4384-00200 City Square Nairobi</h4></li>
                </ul>
            </div>
            <div class="footer-section">
                <h3>contact info</h3>
                <ul>
                    <li><h4>Email: pekar.industrial@gmail.com</h4></li>
                    <li><h4>Cell Phone: 0721301274/0722301274</h4></li>
                </ul>
            </div>
            <div class="footer-bottom">
                © 2025 Your Company | All rights reserved.
            </div>
        </div>
    </footer>
</body>
</html>
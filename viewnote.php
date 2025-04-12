<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Delivery Notes</title>
    <link rel="stylesheet" href="css.css/style.css">
    <style>
        table {
            width: 75%; /* Optional: Makes the table take full width */
            border-collapse: collapse; /* Ensures no double borders */
        }

        table th, table td {
            padding: 8px; /* Adds padding for better spacing */
            text-align: left; /* Aligns text to the left */
        }

        /* Set specific column widths */
        table th:nth-child(1), table td:nth-child(1) {
            width: 14%; /* Adjust Job No column width */
        }

        table th:nth-child(2), table td:nth-child(2) {
            width: 17%; /* Adjust Customer Name column width */
        }

        table th:nth-child(3), table td:nth-child(3) {
            width: 30%; /* Adjust LPO No column width */
        }
        table th:nth-child(4), table td:nth-child(4) {
            width: 17%; /* Adjust Date column width */
        }
        table th:nth-child(5), table td:nth-child(5) {
            width: 30%; /* Adjust Date column width */
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
        <img src="images/image.png" width="1255" height="150" class="d-inline-block align-top" alt="Logo">
    </div>

    <h1>Delivery Notes</h1>
    <table style=" margin-bottom: 20px;background-color: white;">
        <thead>
            <tr>
                <th>DELIVERY NO</th>
                <th>LPO/INVOICE NO</th>
                <th>DELIVER TO</th>
                <th>DELIVERY DATE</th>
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

            // Fetch delivery notes with only the required columns
            $sql = "SELECT delivery_no, lpo_no, deliver_to, delivery_date FROM note ORDER BY delivery_no DESC";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $row["delivery_no"] . "</td>";
                    echo "<td>" . $row["lpo_no"] . "</td>";
                    echo "<td>" . $row["deliver_to"] . "</td>";
                    echo "<td>" . $row["delivery_date"] . "</td>";
                    echo "<td>";
                echo "<button onclick=\"window.location.href='editnote.php?delivery_no=" . $row["delivery_no"] . "'\">Edit</button>";
                echo " ";
                echo "<button onclick=\"window.location.href='downloadnote.php?delivery_no=" . $row["delivery_no"] . "'\">Download</button>";
                echo " ";
                echo "<button onclick=\"if(confirm('Are you sure you want to delete this delivery note?')) window.location.href='deletenote.php?delivery_no=" . $row["delivery_no"] . "'\">Delete</button>";
                echo "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='4'>No delivery notes found</td></tr>";
            }

            $conn->close();
            ?>
        </tbody>
    </table>
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
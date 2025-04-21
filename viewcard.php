<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Job Cards</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <link rel="stylesheet" href="css.css/style.css">
    <style>
        table {
            width: 70%; /* Optional: Makes the table take full width */
            border-collapse: collapse; /* Ensures no double borders */
        }

        table th, table td {
            padding: 8px; /* Adds padding for better spacing */
            text-align: left; /* Aligns text to the left */
        }

        /* Set specific column widths */
        table th:nth-child(1), table td:nth-child(1) {
            width: 8%; /* Adjust Job No column width */
        }

        table th:nth-child(2), table td:nth-child(2) {
            width: 30%; /* Adjust Customer Name column width */
        }

        table th:nth-child(3), table td:nth-child(3) {
            width: 11%; /* Adjust LPO No column width */
        }
        table th:nth-child(4), table td:nth-child(4) {
            width: 8%; /* Adjust Date column width */
        }
        table th:nth-child(5), table td:nth-child(4) {
            width: 20%; /* Adjust Date column width */
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

    <?php
    if(isset($_GET["DelId"])){
        $DelId=$_GET["DelId"]; 
    
        // Delete related rows from card_item
    $del_items = "DELETE FROM card_item WHERE jobNo='$DelId'";
    $conn->query($del_items);

    // Delete related rows from card_spares
    $del_spares = "DELETE FROM card_spares WHERE jobNo='$DelId'";
    $conn->query($del_spares);
        // sql to delete a record
        $del_card = "DELETE FROM card WHERE jobNo='$DelId' LIMIT 1";
    
        if ($conn->query($del_card) === TRUE) {
            header("Location:viewcard.php");
            exit();
        } else {
            echo "Error deleting record: " . $conn->error;
        }
    }
    ?>
    <h1>Job Cards</h1>
    <table style="margin-bottom: 20px;background-color: white;">
        <thead>
            <tr>
                <th>JOB NO</th>
                <th>CUSTOMER NAME</th>
                <th>LPO NO</th>
                <th>DATE</th>
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

            // Fetch job cards with only the required columns
            
            $sql="SELECT jobNo, customer_name, lpo_no, date FROM card ORDER BY jobNo DESC";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $row["jobNo"] . "</td>";
                    echo "<td>" . $row["customer_name"] . "</td>";
                    echo "<td>" . $row["lpo_no"] . "</td>";
                    echo "<td>" . $row["date"] . "</td>";
                    echo "<td>";
                    echo "<button onclick=\"window.location.href='editcard.php?jobNo=" . $row["jobNo"] . "'\">Edit</button>";
                    echo " ";
                    echo "<button onclick=\"generatePDF('{$row['jobNo']}', '{$row['customer_name']}', '{$row['lpo_no']}', '{$row['date']}')\">Download</button>";
                    echo " ";
                    echo "<button onclick=\"if(confirm('Are you sure you want to delete this job card?')) window.location.href='viewcard.php?DelId=" . $row["jobNo"] . "'\">Delete</button>";
                    echo "</td>";
                    echo "</tr>";
                }
                
            } else {
                echo "<tr><td colspan='5'>No job cards found</td></tr>";
            }

            $conn->close();
            ?>
            
        </tbody>
    </table>
    <script>
    async function generatePDF(jobNo, customerName, lpoNo, date) {
        const { jsPDF } = window.jspdf;
        const doc = new jsPDF();

        // Title
        doc.setFontSize(18);
        doc.text("Job Card", 20, 20);

        // Add Job Info
        doc.setFontSize(12);
        doc.text(`Job No: ${jobNo}`, 20, 40);
        doc.text(`Customer Name: ${customerName}`, 20, 50);
        doc.text(`LPO No: ${lpoNo}`, 20, 60);
        doc.text(`Date: ${date}`, 20, 70);

        // Optional footer
        doc.setFontSize(10);
        doc.text("Generated by Pekar Industrial & Construction LTD", 20, 280);

        // Save the PDF
        doc.save(`JobCard_${jobNo}.pdf`);
    }
</script>

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
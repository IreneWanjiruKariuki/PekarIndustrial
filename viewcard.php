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
            $sql = "SELECT jobNo, customer_name, lpo_no, date FROM card ORDER BY jobNo DESC";
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
                echo "<button onclick=\"generatePDF('" . $row["jobNo"] . "', '" . $row["customer_name"] . "', '" . $row["lpo_no"] . "', '" . $row["date"] . "')\">Download</button>";
                echo " ";
                echo "<button onclick=\"if(confirm('Are you sure you want to delete this job card?')) window.location.href='deletecard.php?jobNo=" . $row["jobNo"] . "'\">Delete</button>";
                echo "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='4'>No job cards found</td></tr>";
            }

            $conn->close();
            ?>
            
        </tbody>
    </table>
    <!-- Static HTML Elements for Generate PDF -->
    <div id="jobDetails" style="display: none;">
        <p id="jobNumber"></p>
        <p id="date"></p>
        <p id="customerName"></p>
        <p id="technicianName"></p>
        <p id="lpo"></p>
        <p id="dateStarted"></p>
        <p id="dateFinished"></p>
    </div>
</div>
    <script>
            async function generatePDF(jobNo) {
            const { total } = calculateTotals();

            const { jsPDF } = window.jspdf;
            const doc = new jsPDF();

            // Getting input values
            const date = document.getElementById('date').value;
            const jobNumber = document.getElementById('jobNumber').value;
            const customerName = document.getElementById('customerName').value;
            const technicianName = document.getElementById('technicianName').value;
            const lpo = document.getElementById('lpo').value;
            const dateStarted = document.getElementById('dateStarted').value;
            const dateFinished = document.getElementById('dateFinished').value;

            // Ensure jsPDF is properly loaded
            if (!jsPDF) {
                alert("jsPDF failed to load. Please check your internet connection.");
                return;
            }

            // Add Image at the top left corner
            const img = new Image();
            img.src = 'images/image.png'; // Replace with your image path
            doc.addImage(img, 'PNG', 10, 10, 50, 35);
            doc.setFont("helvetica", "normal");
            doc.setFontSize(10);
            doc.setTextColor(128, 128, 128);
            doc.text("Plumbing works, Mechanical & Electrical plant installations HVAC, Infra-Red thermography and other maintenance solutions and General Contractors", 70, 15, { maxWidth: 140 });
            doc.text("Location: Kasarani Mwiki Road", 70, 25);
            doc.text("P.O BOX 4384-00200 City Square Nairobi", 70, 31);
            doc.text("Email: pekar.industrial@gmail.com", 70, 37);
            doc.text("Cell Phone: 0722301274/0721301274", 70, 43);
            // Adding content to PDF
            doc.setFont("helvetica", "bold");
            doc.setFontSize(18);
            doc.setTextColor(0, 0, 0);
            doc.setLineWidth(0.7);
            doc.rect(15, 50, 180, 10);
            doc.text("JOB CARD/EQUIPMENT HANDOVER", 55, 57);
            doc.setFont("helvetica", "normal");

            // Adding form details
            doc.setFontSize(11);
            doc.setLineWidth(0.1);
    
            // Adding Date and Job Number
            doc.setTextColor(50, 50, 50);
            doc.text(`DATE:`, 20, 65);
            doc.setTextColor(0, 0, 0);
            doc.text(`${document.getElementById('date').value}`, 35, 65);
            doc.setTextColor(50, 50, 50);
            doc.text(`JOB NO:`, 140, 65);
            doc.setTextColor(0, 0, 0);
            doc.text(`${document.getElementById('jobNumber').value}`, 160, 65);
    
            // Customer and Technician Info
            doc.rect(20, 69, 180, 7);
            doc.setTextColor(50, 50, 50);
            doc.text(`CUSTOMER:`, 22, 73.5);
            doc.setTextColor(0, 0, 0);
            doc.text(`${document.getElementById('customerName').value}`, 50, 73.5);
    
            doc.rect(20, 76, 90, 8);
            doc.rect(20, 76, 40, 8);
            doc.setTextColor(50, 50, 50);
            doc.text("TECHNICIAN :", 22, 80.5, { maxWidth: 40 });
            doc.setTextColor(0, 0, 0);
            doc.text(`${document.getElementById('technicianName').value}`, 62, 80.5);
    
            doc.rect(110, 76, 90, 8);
            doc.rect(110, 76, 40, 8);
            doc.setTextColor(50, 50, 50);
            doc.text("LPO/REF:", 112, 80.5, { maxWidth: 50 });
            doc.setTextColor(0, 0, 0);
            doc.text(`${document.getElementById('lpo').value}`, 152, 80.5);
    
            doc.rect(20, 84, 90, 8);
            doc.rect(20, 84, 40, 8);
            doc.setTextColor(50, 50, 50);
            doc.text("JOB STARTED:", 22, 88.5, { maxWidth: 40 });
            doc.setTextColor(0, 0, 0);
            doc.text(`${document.getElementById('dateStarted').value}`, 62, 88.5);
    
            doc.rect(110, 84, 90, 8);
            doc.rect(110, 84, 40, 8);
            doc.setTextColor(50, 50, 50);
            doc.text("JOB FINISHED:", 112, 88.5, { maxWidth: 40 });
            doc.setTextColor(0, 0, 0);
            doc.text(`${document.getElementById('dateFinished').value}`, 152, 88.5);
    
            // Item Description
            doc.setFont("helvetica", "bold");
            doc.setTextColor(128, 128, 128);

            const itemHeader = (doc,yPos) =>{
            doc.text("ITEM/MACHINE SERIAL NO", 22, yPos, { maxWidth: 70 });
            doc.text("JOB DESCRIPTION/INSTRUCTION", 85, yPos);
            };

            let yPos = 100;
            itemHeader(doc, yPos);
            yPos += 5;
    
            // Job Description Content
            doc.setFont("helvetica", "normal");
            doc.setTextColor(0, 0, 0);
            //let yPos = 100;
            const itemDescriptions = document.querySelectorAll('.item-description');
            itemDescriptions.forEach((item, index)=>{

                if (yPos > 270) {
                    doc.addPage();
                    yPos = 20;
                    itemHeader(doc, yPos);
                    yPos += 5;
                }
                const machineSerialNumber = item.querySelector(`.machine-serial-number`).value;
                const jobDescription = item.querySelector(`.job-description`).value;
                const jobDescriptionLines = doc.splitTextToSize(jobDescription, 110);
                const lineHeight = 4.5;
                const itemHeight = jobDescriptionLines.length * lineHeight;
                doc.text(`${machineSerialNumber}`, 22, yPos + lineHeight);
                doc.text(`${jobDescription}`, 85, yPos + lineHeight, { maxWidth: 110 });
                yPos += itemHeight + 1;
            })
            
            // Spare Parts Table
            yPos += 2;

            const sparesHeader = (doc, yPos) =>{
            doc.setTextColor(128, 128, 128);
            doc.setFont("helvetica", "bold");
            doc.text("SPARES USED", 20, yPos + 7);
            doc.text("QTY", 104, yPos + 7);
            doc.text("UNIT COST", 130, yPos + 7);
            doc.text("TOTAL", 165, yPos + 7);
            doc.setTextColor(0, 0, 0);
            doc.setFont("helvetica", "normal");
            };
            
            sparesHeader(doc, yPos);
            yPos += 6.5;
            const spareParts = document.querySelectorAll('.spare-part');
            spareParts.forEach((part, index) => {

                if (yPos > 270) {
                    doc.addPage();
                    yPos = 20;
                    sparesHeader(doc, yPos);
                    yPos += 5;
                }
                const sparePartName = part.querySelector('.spare-part-name').value;
                const quantity = part.querySelector('.spare-part-quantity').value;
                const unitCost = part.querySelector('.spare-part-unit-cost').value;
                const totalCost = (parseFloat(unitCost) * parseFloat(quantity)).toFixed(2);
                

        
        doc.text(sparePartName, 20, yPos + 7);
        
        doc.text(quantity, 105, yPos + 7);
        
        doc.text(unitCost, 130, yPos + 7);
        
        doc.text(totalCost, 165, yPos + 7);

        yPos += 6.5;
    });

    if (yPos > 270) {
        doc.addPage();
        yPos = 20;
    }
    doc.setFont("helvetica", "bold");
    doc.text("Installation, testing, and commissioning done and system left in good working condition.", 20, yPos + 10, { maxWidth: 180 });
    if (yPos > 270) {
        doc.addPage();
        yPos = 20;
    }
    doc.setFont("helvetica", "normal");
    doc.text("Technician's Name and Signature: ..........................................................", 20, yPos + 17.5);

    if (yPos > 270) {
        doc.addPage();
        yPos = 20;
    }
    doc.setFont("helvetica", "bold");
    doc.text("Confirmed by client representative.", 20, yPos + 25, { maxWidth: 180 });
    if (yPos > 270) {
        doc.addPage();
        yPos = 20;
    }
    doc.setFont("helvetica", "normal");
    doc.text("Client's Name and Signature: ...............................................................", 20, yPos + 32.5);

    
            // Saving the PDF
            doc.save(`job_card_${jobNo}.pdf`);
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
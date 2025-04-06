<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create job card</title>
    <link rel="stylesheet" href="css.css/style.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script>
        function generateJobNumber() {
            let lastJobNumber = localStorage.getItem('lastJobNumber');
            if (!lastJobNumber) {
                lastJobNumber = 0;
            }
            const newJobNumber = parseInt(lastJobNumber) + 1;
            localStorage.setItem('lastJobNumber', newJobNumber);
            return `JOB-${newJobNumber}`;
        }

        function resetJobNumber() {
            localStorage.removeItem('lastJobNumber');
            alert("Job number has been reset.");
            document.getElementById('jobNumber').value = generateJobNumber(); // Update the job number after reset
        }

        window.onload = function() {
            document.getElementById('jobNumber').value = generateJobNumber(); // Set the job number when the page loads
        }
    </script>
</head>
<body background="images/img5.jpg">
    <nav>
    
        <a href="home.html">HOME</a>
        <a href="viewcard.php">VIEW JOB CARDS</a>
        <a href="viewnote.php">VIEW DELIVERY NOTES</a>
        <a href="viewinvoice.php">VIEW INVOICES</a>
        <a href="javascript:void(0);" class="icon" onclick="toggleNavbar()">
            &#9776;
        </a>
    </nav>

    <?php
require_once("db_connect.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    ob_start();

    $jobNumber = mysqli_real_escape_string($conn, $_POST['jobNumber']);
    $date = mysqli_real_escape_string($conn, $_POST['date']);
    $customerName = mysqli_real_escape_string($conn, $_POST['customerName']);
    $technicianName = mysqli_real_escape_string($conn, $_POST['technicianName']);
    $lpo = mysqli_real_escape_string($conn, $_POST['lpo']);
    $dateStarted = mysqli_real_escape_string($conn, $_POST['dateStarted']);
    $dateFinished = mysqli_real_escape_string($conn, $_POST['dateFinished']);
    $machineSerialNumbers = $_POST['machineSerialNumbers'];
    $jobDescriptions = $_POST['jobDescriptions'];
    $spareParts = $_POST['spareParts'];
    $quantities = $_POST['quantities'];
    $unitCosts = $_POST['unitCosts'];

    // Step 1: Insert the main job card details into the card table
    $stmt = $conn->prepare("INSERT INTO card (jobNo, date, customer_name, technician_name, date_started, date_finished) VALUES (?, ?, ?, ?, ?, ?)");
    if (!$stmt) {
        echo "Error preparing statement for card: " . $conn->error;
        exit();
    }
    $stmt->bind_param("ssssss", $jobNumber, $date, $customerName, $technicianName, $dateStarted, $dateFinished);
    if (!$stmt->execute()) {
        echo "Error executing card statement: " . $stmt->error;
        exit();
    }
    $stmt->close();

    // Step 2: Insert each item associated with the job card into the card_item table
    $stmt = $conn->prepare("INSERT INTO card_item (jobNo, machine_serial_number, job_description) VALUES (?, ?, ?)");
    if (!$stmt) {
        echo "Error preparing statement for card items: " . $conn->error;
        exit();
    }

    for ($i = 0; $i < count($machineSerialNumbers); $i++) {
        $machineSerialNumber = mysqli_real_escape_string($conn, $machineSerialNumbers[$i]);
        $jobDescription = mysqli_real_escape_string($conn, $jobDescriptions[$i]);

        $stmt->bind_param("sss", $jobNumber, $machineSerialNumber, $jobDescription);
        if (!$stmt->execute()) {
            echo "Error executing statement for card items: " . $stmt->error;
            exit();
        }
    }
    $stmt->close();

    // Step 3: Insert each spare part associated with the job card into the card_spares table
    $stmt = $conn->prepare("INSERT INTO card_spare (jobNo, spare_part, quantity, unit_cost, total) VALUES (?, ?, ?, ?, ?)");
    if (!$stmt) {
        echo "Error preparing statement for card spares: " . $conn->error;
        exit();
    }

    for ($i = 0; $i < count($spareParts); $i++) {
        $sparePart = mysqli_real_escape_string($conn, $spareParts[$i]);
        $quantity = mysqli_real_escape_string($conn, $quantities[$i]);
        $unitCost = mysqli_real_escape_string($conn, $unitCosts[$i]);
        $total = $quantity * $unitCost;

        $stmt->bind_param("ssidd", $jobNumber, $sparePart, $quantity, $unitCost, $total);
        if (!$stmt->execute()) {
            echo "Error executing statement for card spares: " . $stmt->error;
            exit();
        }
    }
    $stmt->close();

    echo "Job Card saved successfully!";
    header("Location: viewcard.php");
    ob_end_flush();
    exit();
}

$conn->close();
?>

    <div class="cont">
        <img src="images/image.png" width="1255" height="150" class="d-inline-block align-top" alt="Logo">
    </div>

    
    <form id="jobCardForm" method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" style="background-color: white; width: 60%; padding: 20px;">
        
        <h2 style="text-align: center;">JOB CARD/EQUIPMENTMENT HANDOVER</h2>
        
        <label for="jobNumber">JOB NUMBER:</label>
        <input type="text" id="jobNumber" name="jobNumber" required><br><br>

        <label for="date">DATE:</label>
        <input type="date" id="date" name="date" required><br><br>

        <label for="customerName">CUSTOMER:</label>
        <input type="text" id="customerName" name="customerName" required><br><br>

        <label for="technicianName">TECHNICIAN NAME:</label>
        <input type="text" id="technicianName" name="technicianName" required><br><br>

        <label for="lpo">LPO/REF:</label>
        <input type="text" id="lpo" name="lpo" required><br><br>

        <label for="dateStarted">TIME JOB STARTED:</label>
        <input type="date" id="dateStarted" name="dateStarted" required><br><br>

        <label for="dateFinished">TIME JOB FINISHED:</label>
        <input type="date" id="dateFinished" name="dateFinished" required><br><br>
        
        <h3>Item Description</h3>
        <div id="itemDescriptionContainer">
            <div class="item-description">
                <label for="machineSerialNumber1">ITEM/MACHINE SERIAL NUMBER:</label>
                <input type="text" id="machineSerialNumber1" name="machineSerialNumbers[]" class= "machine-serial-number" required>
                <label for="jobDescription1">JOB DESCRIPTION/INSTRUCTION:</label>
                <textarea id="jobDescription1" name="jobDescriptions[]" class="job-description" required></textarea>
            </div>
        </div>
        <button type="button" onclick="addItem()">Add item/machine</button><br><br>
        <h3>Spare Parts Used</h3>
        <div id="sparePartsContainer">
            <div class="spare-part">
                <label for="sparePart1">SPARES USED:</label>
                <input type="text" id="sparePart1" name="spareParts[]" class="spare-part-name" required>
                <label for="quantity1">QUANTITY:</label>
                <input type="text" id="quantity1" name="quantities[]" class="spare-part-quantity" required>
                <label for="unitCost1">UNIT COST:</label>
                <input type="text" id="unitCost1" name="unitCosts[]" class="spare-part-unit-cost" required>
                
            </div>
        </div>
        <button type="button" onclick="addSparePart()">Add Spare Part</button><br><br>

        <button type="button" onclick="generatePDF()">Download as PDF</button>

        <button type="button" onclick="resetJobNumber()">Reset Job Number</button>

        <input type="submit" value="Save Job Card">
    </form>

    <script>
        let sparePartCount = 1;
        let itemCount = 1;

        function addItem() {
            itemCount++;
            const container = document.getElementById('itemDescriptionContainer');
            const newItem = document.createElement('div');
            newItem.classList.add('item-description');
            newItem.innerHTML = `
                <label for="machineSerialNumber${itemCount}">ITEM/MACHINE SERIAL NUMBER:</label>
                <input type="text" id="machineSerialNumber${itemCount}" name="machineSerialNumbers[]" class="machine-serial-number" required>
                <label for="jobDescription${itemCount}">JOB DESCRIPTION/INSTRUCTION:</label>
                <textarea id="jobDescription${itemCount}" name="jobDescriptions[]" class="job-description" required></textarea>
            `;
            container.appendChild(newItem);
        }

        function calculateTotals() {
            let total = 0;
            const items = document.querySelectorAll('.spare-part');
            items.forEach((sparePart, index) => {
                const unitPrice = parseFloat(sparePart.querySelector('.spare-part-unit-cost').value) || 0;
                const quantity = parseFloat(sparePart.querySelector('.spare-part-quantity').value) || 0;
                const totalCost = unitPrice * quantity;
                total += totalCost;
            });

            return { total };
        }

        function addSparePart() { 
            sparePartCount++;
            const container = document.getElementById('sparePartsContainer');
            const newSparePart = document.createElement('div');
            newSparePart.classList.add('spare-part');
            newSparePart.innerHTML = `
                <label for="sparePart${sparePartCount}">SPARES USED:</label>
                <input type="text" id="sparePart${sparePartCount}" name="spareParts[]" class="spare-part-name" required>
                <label for="quantity${sparePartCount}">QUANTITY:</label>
                <input type="text" id="quantity${sparePartCount}" name="quantities[]" class="spare-part-quantity" required>
                <label for="unitCost${sparePartCount}">UNIT COST:</label>
                <input type="text" id="unitCost${sparePartCount}" name="unitCosts[]" class="spare-part-unit-cost" required>
                
            `;
            container.appendChild(newSparePart);
        }

        async function generatePDF() {
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
            doc.save("job_card.pdf");
        }
    </script>
    
    <footer>
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
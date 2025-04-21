<!-- filepath: c:\Users\kariu\OneDrive\Documents\Irene\htdocs\pekar\PekarIndustrial\editcard.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Job Card</title>
    <link rel="stylesheet" href="css.css/style.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
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
<div class="cont">
        <img src="images/image.png" width="1255" height="150" class="d-inline-block align-top" alt="Logo">
    </div>
    <?php
    // Database connection
    $conn = new mysqli('localhost', 'root', '', 'pekar');

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Check if jobNo is passed in the URL
    if (isset($_GET['jobNo'])) {
        $jobNo = $_GET['jobNo'];

        // Fetch data from the card table
        $sql_card = "SELECT * FROM card WHERE jobNo = '$jobNo' LIMIT 1";
        $result_card = $conn->query($sql_card);
        $card = $result_card->fetch_assoc();

        // Fetch data from the card_item table
        $sql_items = "SELECT * FROM card_item WHERE jobNo = '$jobNo'LIMIT 1";
        $result_items = $conn->query($sql_items);

        // Fetch data from the card_spares table
        $sql_spares = "SELECT * FROM card_spare WHERE jobNo = '$jobNo'LIMIT 1";
        $result_spares = $conn->query($sql_spares);
    } else {
        echo "No job number provided.";
       // echo "<pre>"; print_r($_GET); echo "</pre>"; // <-- debug line
        exit();
    }

    // Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    echo "<pre>";
    print_r($_POST);
    echo "</pre>";
    exit();

    // Get the form data
    $jobNo = mysqli_real_escape_string($conn,$_POST['jobNumber']);
    $date = mysqli_real_escape_string($conn,$_POST['date']);
    $customerName = mysqli_real_escape_string($conn,$_POST['customerName']);
    $technicianName = mysqli_real_escape_string($conn,$_POST['technicianName']);
    $lpo = mysqli_real_escape_string($conn,$_POST['lpo']);
    $dateStarted = mysqli_real_escape_string($conn,$_POST['dateStarted']);
    $dateFinished = mysqli_real_escape_string($conn,$_POST['dateFinished']);

    // Update the card table
    $sql_card = "UPDATE card SET 
                    date = '$date', 
                    customer_name = '$customerName', 
                    technician_name = '$technicianName', 
                    lpo_no = '$lpo', 
                    date_started = '$dateStarted', 
                    date_finished = '$dateFinished' 
                 WHERE jobNo = '$jobNo'";
    if (!$conn->query($sql_card)) {
        echo "Error updating card: " . $conn->error;
        exit();
    }

    // Delete existing items and spares for the jobNo
    if (!$conn->query("DELETE FROM card_item WHERE jobNo = '$jobNo'")) {
        echo "Error deleting items: " . $conn->error;
        exit();
    }
    if (!$conn->query("DELETE FROM card_spare WHERE jobNo = '$jobNo'")) {
        echo "Error deleting spares: " . $conn->error;
        exit();
    }

    // Insert updated items
    if (!empty($_POST['machineSerialNumbers']) && !empty($_POST['jobDescriptions'])) {
        $machineSerialNumbers =mysqli_real_escape_string($conn, $_POST['machineSerialNumbers']);
        $jobDescriptions = mysqli_real_escape_string($conn,$_POST['jobDescriptions']);

        foreach ($machineSerialNumbers as $index => $serialNumber) {
            $jobDescription = $jobDescriptions[$index];
            $sql_item = "INSERT INTO card_item (jobNo, machine_serial_number, job_description) 
                         VALUES ('$jobNo', '$serialNumber', '$jobDescription')";
            if (!$conn->query($sql_item)) {
                echo "Error inserting item: " . $conn->error;
                exit();
            }
        }
    }

    // Insert updated spares
    if (!empty($_POST['spareParts']) && !empty($_POST['quantities']) && !empty($_POST['unitCosts'])) {
        $spareParts = $_POST['spareParts'];
        $quantities = $_POST['quantities'];
        $unitCosts = $_POST['unitCosts'];

        foreach ($spareParts as $index => $sparePart) {
            $quantity = $quantities[$index];
            $unitCost = $unitCosts[$index];
            $total = $quantity * $unitCost;
            $sql_spare = "INSERT INTO card_spare (jobNo, spare_part, quantity, unit_cost, total) 
                          VALUES ('$jobNo', '$sparePart', '$quantity', '$unitCost', '$total')";
           if (!$conn->query($sql_spare)) {
            echo "Error inserting spare: " . $conn->error;
            exit();
        }
        }
    }

    // Redirect to the viewcard page after updating
    if ($conn->query($sql_card) === TRUE) {
        header("Location: viewcard.php");
        exit();
    } else {
        echo "Error: " . $sql_card . "<br>" . $conn->error;
    }
    
}
    ?>

    <form id="editJobCardForm" method="POST" action="editcard.php?jobno=<?php echo $jobno; ?>" style="background-color: white; width: 60%; padding: 20px;">
        <h2 style="text-align: center;">Edit Job Card</h2>

        <label for="jobNumber">JOB NUMBER:</label>
        <input type="text" id="jobNumber" name="jobNumber" value="<?php echo $card['jobNo']; ?>" readonly><br><br>

        <label for="date">DATE:</label>
        <input type="date" id="date" name="date" value="<?php echo $card['date']; ?>" required><br><br>

        <label for="customerName">CUSTOMER:</label>
        <input type="text" id="customerName" name="customerName" value="<?php echo $card['customer_name']; ?>" required><br><br>

        <label for="technicianName">TECHNICIAN NAME:</label>
        <input type="text" id="technicianName" name="technicianName" value="<?php echo $card['technician_name']; ?>" required><br><br>

        <label for="lpo">LPO/REF:</label>
        <input type="text" id="lpo" name="lpo" value="<?php echo $card['lpo_no']; ?>" required><br><br>

        <label for="dateStarted">TIME JOB STARTED:</label>
        <input type="date" id="dateStarted" name="dateStarted" value="<?php echo $card['date_started']; ?>" required><br><br>

        <label for="dateFinished">TIME JOB FINISHED:</label>
        <input type="date" id="dateFinished" name="dateFinished" value="<?php echo $card['date_finished']; ?>" required><br><br>

        <h3>Item Description</h3>
        <div id="itemDescriptionContainer">
            <?php while ($item = $result_items->fetch_assoc()) { ?>
                <div class="item-description">
                    <label for="machineSerialNumber">ITEM/MACHINE SERIAL NUMBER:</label>
                    <input type="text" class="machine-serial-number" name="machineSerialNumbers[]" value="<?php echo $item['machine_serial_number']; ?>" required>
                    <label for="jobDescription">JOB DESCRIPTION/INSTRUCTION:</label>
                    <textarea class="job-description" name="jobDescriptions[]" required><?php echo $item['job_description']; ?></textarea>
                </div>
            <?php } ?>
        </div>
        <button type="button" onclick="addItem()">Add item/machine</button><br><br>

        <h3>Spare Parts Used</h3>
        <div id="sparePartsContainer">
            <?php while ($spare = $result_spares->fetch_assoc()) { ?>
                <div class="spare-part">
                    <label for="sparePart">SPARES USED:</label>
                    <input type="text" class="spare-part-name" name="spareParts[]" value="<?php echo $spare['spare_part']; ?>" required>
                    <label for="quantity">QUANTITY:</label>
                    <input type="text" class="spare-part-quantity" name="quantities[]" value="<?php echo $spare['quantity']; ?>" required>
                    <label for="unitCost">UNIT COST:</label>
                    <input type="text" class="spare-part-unit-cost" name="unitCosts[]" value="<?php echo $spare['unit_cost']; ?>" required>
                </div>
            <?php } ?>
        </div>
        <button type="button" onclick="addSparePart()">Add Spare Part</button><br><br>

        <button type="button" onclick="generatePDF()">Download as PDF</button>

        <input type="submit" value="Update Job Card">
    </form>

    <script>
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

        function addItem() {
            const container = document.getElementById('itemDescriptionContainer');
            const div = document.createElement('div');
            div.className = 'item-description';
            div.innerHTML = `
                <label for="machineSerialNumber">ITEM/MACHINE SERIAL NUMBER:</label>
                <input type="text" name="machineSerialNumbers[]" required>
                <label for="jobDescription">JOB DESCRIPTION/INSTRUCTION:</label>
                <textarea name="jobDescriptions[]" required></textarea>
            `;
            container.appendChild(div);
        }

        function addSparePart() {
            const container = document.getElementById('sparePartsContainer');
            const div = document.createElement('div');
            div.className = 'spare-part';
            div.innerHTML = `
                <label for="sparePart">SPARES USED:</label>
                <input type="text" name="spareParts[]" required>
                <label for="quantity">QUANTITY:</label>
                <input type="text" name="quantities[]" required>
                <label for="unitCost">UNIT COST:</label>
                <input type="text" name="unitCosts[]" required>
            `;
            container.appendChild(div);
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
</body>
</html>
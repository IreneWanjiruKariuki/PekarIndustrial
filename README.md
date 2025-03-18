# PekarIndustrial

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Invoice</title>
    <link rel="stylesheet" href="css.css/style.css">
    <style>
        .large-checkbox {
            transform: scale(2.0); /* Increase the size of the checkbox */
            margin-right: 10px; /* Optional: Add some space to the right of the checkbox */
            float: left;
        }
        .inline-label {
            display: inline-block;
            margin-right: 10px;
        }
        .ite {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            margin-bottom: 10px;
        }
        .ite label, .ite input, .ite textarea {
            margin-right: 10px;
        }
        .ite textarea {
            flex-grow: 1;
            resize: horizontal;
        }
    </style>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script>
        function generateInvoiceNo() {
            let lastInvoiceNo = localStorage.getItem('lastInvoiceNo');
            if (!lastInvoiceNo) {
                lastInvoiceNo = 254;
            }
            const newInvoiceNo = parseInt(lastInvoiceNo) + 1;
            localStorage.setItem('lastInvoiceNo', newInvoiceNo);
            return `${String(newInvoiceNo).padStart(3, '0')}`;
        }

        function resetInvoiceNo() {
            localStorage.removeItem('lastInvoiceNo');
            alert("Invoice number has been reset.");
            document.getElementById('invoiceNo').value = generateInvoiceNo(); // Update the invoice number after reset
        }

        window.onload = function() {
            document.getElementById('invoiceNo').value = generateInvoiceNo(); // Set the invoice number when the page loads
        }
    </script>
</head>
<body background="images/img5.jpg">
    <nav>
        <a href="home.html">HOME</a>
        <a href="viewcard.html">JOB CARDS</a>
        <a href="viewnote.html">DELIVERY NOTES</a>
        <a href="viewinvoice.html">INVOICES</a>
        <div class="search-container">
            <input type="text" placeholder="Search..." id="search">
            <button type="submit">🔍</button>
        </div>
    </nav>
    <div class="cont">
        <img src="images/image.png" width="1255" height="150" class="d-inline-block align-top" alt="Logo">
    </div>
    <form id="invoiceForm" style="background-color: white; width: 60%; padding: 20px;">
        <h2 style="text-align: center;">INVOICE</h2>
        <label for="invoiceNo">INVOICE NO:</label>
        <input type="text" id="invoiceNo" required readonly><br><br>

        <label for="name">NAME:</label>
        <input type="text" id="name" required><br><br>

        <label for="address">ADDRESS:</label>
        <input type="text" id="address" required><br><br>

        <label for="lpo">LPO NO:</label>
        <input type="text" id="lpo" required><br><br>

        <label for="contact">CONTACT:</label>
        <input type="text" id="contact" required><br><br>

        <label for="deliveryNo">DELIVERY NO/JOB CARD NO:</label>
        <input type="text" id="deliveryNo" required><br><br>

        <label for="tel">TEL:</label>
        <input type="text" id="tel" required><br><br>

        <label for="dated">DATE:</label>
        <input type="date" id="dated" required><br><br>

        <h3>ITEMS</h3>
        <div id="itemsContainer">
            <div class="ite">
                <label for="item1">ITEM CODE:</label>
                <input type="text" id="item1" class="item-code" required>
                <label for="description1">DESCRIPTION:</label>
                <textarea id="description1" class="item-description" required></textarea>
                <label for="quantity1">QTY:</label>
                <input type="text" id="quantity1" class="item-quantity" required>
                <label for="unit1">UNIT PRICE:</label>
                <input type="text" id="unit1" class="item-unit" required>
                <label for="vatable1" class="inline-label">VATABLE:</label>
                <input type="checkbox" id="vatable1" class="item-vatable large-checkbox">
            </div>
        </div>
        <button type="button" onclick="addItem()">Add Item</button><br><br>

        <button type="button" onclick="generatePDF()">Download as PDF</button>

        <button type="button" onclick="resetInvoiceNo()">Reset Invoice Number</button>
    </form>
    <script>
        let itemCount = 1;

        function addItem() {
            itemCount++;
            const container = document.getElementById('itemsContainer');
            const newItem = document.createElement('div');
            newItem.classList.add('ite');
            newItem.innerHTML = `
            <label for="item${itemCount}">ITEM CODE:</label>
            <input type="text" id="item${itemCount}" class="item-code" required>
            <label for="description${itemCount}">DESCRIPTION:</label>
            <textarea id="description${itemCount}" class="item-description" required></textarea>
            <label for="quantity${itemCount}">QTY:</label>
            <input type="text" id="quantity${itemCount}" class="item-quantity" required>
            <label for="unit${itemCount}">UNIT PRICE:</label>
            <input type="text" id="unit${itemCount}" class="item-unit" required>
            <label for="vatable${itemCount}" class="inline-label">VATABLE:</label>
            <input type="checkbox" id="vatable${itemCount}" class="item-vatable large-checkbox">
            `;
            container.appendChild(newItem);
        }

        function calculateTotals() {
            let total = 0;
            let totalVAT = 0;
            const items = document.querySelectorAll('.ite');
            items.forEach((item, index) => {
                const unitPrice = parseFloat(item.querySelector('.item-unit').value) || 0;
                const quantity = parseFloat(item.querySelector('.item-quantity').value) || 0;
                const totalCost = unitPrice * quantity;

                const isVatable = item.querySelector('.item-vatable').checked;
                let vat = 0;
                if (isVatable) {
                    vat = totalCost * 0.16;
                    totalVAT += vat;
                }

                total += totalCost - vat;
            });

            const grandTotal = total + totalVAT;

            return { total, totalVAT, grandTotal };
        }

        async function generatePDF() {
            const { total, totalVAT, grandTotal } = calculateTotals();

            const { jsPDF } = window.jspdf;
            const doc = new jsPDF();

            // Getting input values
            const name = document.getElementById('name').value;
            const invoiceNo = document.getElementById('invoiceNo').value;
            const address = document.getElementById('address').value;
            const lpo = document.getElementById('lpo').value;
            const contact = document.getElementById('contact').value;
            const deliveryNo = document.getElementById('deliveryNo').value;
            const tel = document.getElementById('tel').value;
            const dated = document.getElementById('dated').value;
            
            // Ensure jsPDF is properly loaded
            if (!jsPDF) {
                alert("jsPDF failed to load. Please check your internet connection.");
                return;
            }

            const img = new Image();
            img.src = 'images/image.png'; // Replace with your image path
            doc.addImage(img, 'PNG', 10, 10, 50, 35);
    
            // Add two small paragraphs on the top right corner
            doc.setFont("helvetica", "normal");
            doc.setFontSize(10);
            doc.setTextColor(128, 128, 128);
            doc.text("Plumbing works, Mechanical & Electrical plant installations HVAC, Infra-Red thermography and other maintenance solutions and General Contractors", 70, 15, { maxWidth: 140 });
            doc.text("Location: Kasarani Mwiki Road", 70, 25);
            doc.text("P.O BOX 4384-00200 City Square Nairobi", 70, 29.5);
            doc.text("Email: pekar.industrial@gmail.com", 70, 33.5);
            doc.text("Cell Phone: 0722301274/0721301274", 70, 38);
            doc.text("PIN Number: P051398673W", 70, 42);
            // Adding content to PDF
            doc.setFont("helvetica", "bold");
            doc.setFontSize(18);
            doc.setTextColor(0, 0, 0);
            doc.setLineWidth(0.7);
            doc.rect(15, 50, 180, 10);
            doc.text("REF: INVOICE", 81, 57);
            doc.setFont("helvetica", "normal");

            // Adding form details
            doc.setFontSize(11);
            doc.setLineWidth(0.2);
            doc.setTextColor(50, 50, 50);
            doc.text(`Name:`, 20, 66);
            doc.setTextColor(0, 0, 0);
            doc.text(`${name}`, 36, 66);
            doc.text(`_____________________`, 35, 66.5);
        
            doc.setTextColor(50, 50, 50);
            doc.text(`Invoice Number:`, 103, 66);
            doc.setTextColor(0, 0, 0);
            doc.text(`${invoiceNo}`, 137, 66);
            doc.text(`___________________`, 136, 66.5);

            doc.setTextColor(50, 50, 50);
            doc.text(`Address:`, 20, 73);
            doc.setTextColor(0, 0, 0);
            doc.text(`${address}`, 40, 73);
            doc.text(`____________________`, 39, 73.5);

            doc.setTextColor(50, 50, 50);
            doc.text(`LPO Number:`, 103, 73);
            doc.setTextColor(0, 0, 0);
            doc.text(`${lpo}`, 133, 73);
            doc.text(`_____________________`, 132, 73.5);

            doc.setTextColor(50, 50, 50);
            doc.text(`Contact:`, 20, 80);
            doc.setTextColor(0, 0, 0);
            doc.text(`${contact}`, 39, 80);
            doc.text(`____________________`, 38, 80.5);

            doc.setTextColor(50, 50, 50);
            doc.text(`Delivery no/Job Card no:`, 103, 80);
            doc.setTextColor(0, 0, 0);
            doc.text(`${deliveryNo}`, 150, 80);
            doc.text(`_____________`, 149, 80.5);

            doc.setTextColor(50, 50, 50);
            doc.text(`Tel:`, 20, 87);
            doc.setTextColor(0, 0, 0);
            doc.text(`${tel}`, 33, 87);
            doc.text(`____________________`, 32, 87.5);

            doc.setTextColor(50, 50, 50);
            doc.text(`Date:`, 103, 87);
            doc.setTextColor(0, 0, 0);
            doc.text(`${dated}`, 118, 87);
            doc.text(`____________________`, 117, 87.5);

            doc.setTextColor(128, 128, 128);
            doc.text(`ITEM CODE`, 12, 97, { maxWidth: 21 });
            doc.text(`DESCRIPTION`, 31, 97);
            doc.text(`QTY`, 117, 97);
            doc.text(`UNIT PRICE`, 131, 97, { maxWidth: 20 });
            doc.text(`VAT`, 155, 97, { maxWidth: 20 });
            doc.text(`TOTAL COST`, 179, 97, { maxWidth: 20 });

            // Adding items
            let yPos = 102;
            doc.setTextColor(0, 0, 0);
            const items = document.querySelectorAll('.ite');
            items.forEach((item, index) => {
                const itemCode = item.querySelector('.item-code').value;
                const description = item.querySelector('.item-description').value;
                const quantity = item.querySelector('.item-quantity').value;
                const unitPrice = parseFloat(item.querySelector('.item-unit').value).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                const totalCost = parseFloat(item.querySelector('.item-unit').value) * parseFloat(quantity);
                const isVatable = item.querySelector('.item-vatable').checked;
                const vat = isVatable ? totalCost * 0.16 : 0;
                const totalCostWithVAT = (totalCost - vat).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

                const descriptionLines = doc.splitTextToSize(description, 90);
                const lineHeight = 4.5;
                const itemHeight = descriptionLines.length * lineHeight;

                doc.text(`${itemCode}`, 13, yPos + lineHeight);
                doc.text(descriptionLines, 26, yPos + lineHeight, { maxWidth: 90 });
                doc.text(`${quantity}`, 119, yPos + lineHeight);
                doc.text(`${unitPrice}`, 128, yPos + lineHeight);
                doc.text(`${vat.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`, 151.5, yPos + lineHeight);
                doc.text(`${totalCostWithVAT}`, 177, yPos + lineHeight);

                yPos += itemHeight + 1; // Adding some space between items
            });

            // Adding totals
            yPos += 3;
            doc.setTextColor(128, 128, 128);
            doc.setFont("helvetica", "bold");
            doc.text(`TOTAL:`, 56, yPos + 6.5);
            doc.setTextColor(0, 0, 0);
            doc.text(`${total.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`, 87, yPos + 6.5);

            yPos += 8;
            doc.setTextColor(128, 128, 128);
            doc.text(`VAT:`, 56, yPos + 6.5);
            doc.setTextColor(0, 0, 0);
            doc.text(`${totalVAT.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`, 87, yPos + 6.5);

            yPos += 8;
            doc.setTextColor(128, 128, 128);
            doc.text(`GRAND TOTAL:`, 56, yPos + 6.5, { maxWidth: 30 });
            doc.setTextColor(0, 0, 0);
            doc.text(`${grandTotal.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`, 87, yPos + 6.5);

            yPos += 13;
            doc.setFont("helvetica", "normal");
            doc.text(`Signed:`, 20, yPos + 7); 
            img.src = 'images/final_signature.jpg'; // Replace with your image path
            doc.addImage(img, 'JPG', 40, yPos - 5, 25, 25);
            
            yPos += 25;
            doc.text(`______________________________________________`, 20, yPos);
            doc.text(`FOR: PEKAR INDUSTRIAL AND CONSTRUCTION LTD`, 20, yPos - 1);

            yPos += 3.5;
            doc.rect(20, yPos, 180, 15);
            doc.text(`NOTE: Cheque to be drawn to Pekar Industrial and Construction Limited`, 45, yPos + 5);
            doc.text(`BANK: Consolidated Bank of Kenya A/C No.10011301000125, Branch:Koinange Street`, 34, yPos + 11);

            // Saving the PDF
            doc.save("invoice.pdf");
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
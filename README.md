

<script>

        function resetInvoiceNo() {
            localStorage.removeItem('lastInvoiceNo');
            alert("Invoice number has been reset.");
            document.getElementById('invoiceNo').value = generateInvoiceNo(); // Update the invoice number after reset
        }

        window.onload = function() {
            document.getElementById('invoiceNo').value = generateInvoiceNo(); // Set the invoice number when the page loads
        }
    </script>

<?php
    if(isset($_POST['create_note'])){
        $deliveryNo = mysqli_real_escape_string($conn, $_POST['delivery_no']);
        $deliverTo = mysqli_real_escape_string($conn, $_POST['deliver_to']);
        $lpo = mysqli_real_escape_string($conn, $_POST['lpo_no']);
        $dated = mysqli_real_escape_string($conn, $_POST['dated']);
        $deliveryDate = mysqli_real_escape_string($conn, $_POST['delivery_date']);
        $deliveredBy = mysqli_real_escape_string($conn, $_POST['delivered_by']);
    }
    //$items = [];
        //foreach ($_POST['items'] as $item) {
            //$items[] = [
                //'item' => mysqli_real_escape_string($conn, $item['item']),
                //'description' => mysqli_real_escape_string($conn, $item['description']),
                //'unit' => mysqli_real_escape_string($conn, $item['unit']),
                //'quantity' => mysqli_real_escape_string($conn, $item['quantity']),
            //];
        //}
    $insert_note = "INSERT INTO note (delivery_no,deliver_to,lpo_no,dated,delivery_date,delivered_by) 
    VALUES ('$deliveryNo','$deliverTo','$lpo','$dated','$deliveryDate','$deliveredBy')";

    if($conn->query($insert_note) === True){
        header('Location: viewnote.php');
        exit();
    } else{
        echo "Error: " . $insert_note . "<br>" . $conn->error;
    }
    ?>

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $deliveryNo = $_POST['deliveryNo'];
    $deliverTo = $_POST['deliverTo'];
    $lpo = $_POST['lpo'];
    $dated = $_POST['dated'];
    $deliveryDate = $_POST['deliveryDate'];
    $deliveredBy = $_POST['deliveredBy'];
    $item = $_POST['item'];
    $description = $_POST['description'];
    $unit = $_POST['unit'];
    $quantity = $_POST['quantity'];

    // Insert data into the database
    $insertnote = $conn->prepare("INSERT INTO note (delivery_no, deliver_to, lpo_no, dated, delivery_date, delivered_by, item, description, unit, quantity) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    for ($i = 0; $i < count($item); $i++) {
        $insertnote->bind_param("ssssssssss", $deliveryNo, $deliverTo, $lpo, $dated, $deliveryDate, $deliveredBy, $item[$i], $description[$i], $unit[$i], $quantity[$i]);
        $insertnote->execute();
    }

    $insertnote->close();
    $conn->close();

    // Redirect to the viewnote.html page
    header("Location: viewnote.html");
    exit();
}
<?php
// Include your database connection file here
include('db.php');

// Fetch staff records from the database
$result = $conn->query("SELECT id, name, image FROM staff");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Complaint Submission</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

<h1>Submit a Complaint</h1>

<form action="submit_complaint.php" method="POST" enctype="multipart/form-data">
    <div class="form-row">
        <div class="form-column">
            <label for="client_name">Client Name:</label>
            <input type="text" id="client_name" name="client_name" required>
        </div>

        <div class="form-column">
            <label for="client_email">Client Email:</label>
            <input type="email" id="client_email" name="client_email" required>
        </div>

        <div class="form-column">
            <label for="client_phone">Client Phone (Optional):</label>
            <input type="text" id="client_phone" name="client_phone">
        </div>
    </div>

    <div class="form-row">
        <div class="form-column">
            <label for="complaint_title">Complaint Title:</label>
            <input type="text" id="complaint_title" name="complaint_title" required>
        </div>

        <div class="form-column">
            <label for="category">Category:</label>
            <select id="category" name="category">
                <option value="Packing">Packing</option>
                <option value="Billing">Billing</option>
                <option value="Technical">Technical</option>
            </select>
        </div>

        <div class="form-column">
            <label for="urgency">Urgency:</label>
            <select id="urgency" name="urgency">
                <option value="Low">Low</option>
                <option value="Medium">Medium</option>
                <option value="High">High</option>
            </select>
        </div>
    </div>

    <div class="form-row">
        <div class="form-column">
            <label for="preferred_date">Preferred Resolution Date:</label>
            <input type="date" id="preferred_date" name="preferred_date">
        </div>

  
        <div class="form-column">
            <label for="files">Attach Files:</label>
            

            <input type="file" id="files" name="files[]" multiple>
            <p style="font-size: 11px; font-weight:bold;"> If your packaging is damaged or if there are any products you purchased from us, you can upload them here.</p>
        </div>
    </div>

    <div class="form-column">
            <label for="staff">Assign Staff:</label>
            <select id="staff" name="staff[]" multiple>
                <?php
                // Loop through staff records and output them as options
                while ($staff = $result->fetch_assoc()) {
                    echo '<option value="' . $staff['id'] . '" data-image="' . $staff['image'] . '">' . $staff['name'] . '</option>';
                }
                ?>
            </select>
        </div>

        <div id="staffImageContainer" style="margin-top: 20px; display:none;" class="form-column">
            <label for="staffImage">Staff Image:</label>
            <img id="staffImage" src="" style="max-width: 150px;" />
        </div>

    <button type="submit">Submit Complaint</button>
</form>

<script>
    // JavaScript to handle staff image display when a staff is selected
    document.getElementById('staff').addEventListener('change', function () {
        var staffSelect = this;
        var selectedOption = staffSelect.options[staffSelect.selectedIndex];
        var staffImage = selectedOption.getAttribute('data-image');

        // Display the selected staff's image
        var imageElement = document.getElementById('staffImage');
        var imageContainer = document.getElementById('staffImageContainer');

        if (staffImage) {
            imageElement.src = staffImage; // Set the image source to the selected staff's image path
            imageContainer.style.display = 'block';  // Show the image container
        } else {
            imageContainer.style.display = 'none';  // Hide the image container if no image is selected
        }
    });
</script>

</body>
</html>

<?php
// Close the database connection
$conn->close();
?>

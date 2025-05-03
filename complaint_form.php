
<?php
// db connection
include('db.php');

// Insert complaint
function insertComplaint($conn, $staff_ids, $complaint_desc, $client_name, $client_email, $client_phone = null, $preferred_date = null) {
    if (empty($complaint_desc)) throw new Exception("Complaint description is required");
    if (empty($client_name)) throw new Exception("Client name is required");

    $sql = "INSERT INTO complaints (staff_ids, complaint_desc, client_name, client_email, client_phone, preferred_date) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    if (!$stmt) throw new Exception("Prepare failed: " . $conn->error);

    $stmt->bind_param("ssssss", $staff_ids, $complaint_desc, $client_name, $client_email, $client_phone, $preferred_date);
    if (!$stmt->execute()) throw new Exception("Execute failed: " . $stmt->error);

    return $stmt->insert_id;
}

$message = '';
$message_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $staff_ids = implode(',', $_POST['staff_ids'] ?? []);
        $complaint_desc = $_POST['complaint_desc'] ?? '';
        $client_name = $_POST['client_name'] ?? '';
        $client_email = $_POST['client_email'] ?? '';
        $client_phone = $_POST['client_phone'] ?? null;
        $preferred_date = $_POST['preferred_date'] ?? null;

        $complaint_id = insertComplaint($conn, $staff_ids, $complaint_desc, $client_name, $client_email, $client_phone, $preferred_date);

        $uploadDir = 'uploads/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

        // Prepare statement for file insertion
        $file_stmt = $conn->prepare("INSERT INTO complaint_files (complaint_id, file_path, file_type, uploaded_at) VALUES (?, ?, ?, NOW())");
        if (!$file_stmt) throw new Exception("File insert prepare failed: " . $conn->error);

        // Handle image/pdf files
        if (!empty($_FILES['files']['name'][0])) {
            foreach ($_FILES['files']['tmp_name'] as $key => $tmp_name) {
                if ($_FILES['files']['error'][$key] === UPLOAD_ERR_OK) {
                    $file_name = basename($_FILES['files']['name'][$key]);
                    $file_size = $_FILES['files']['size'][$key];
                    $file_type = $_FILES['files']['type'][$key];
                    $target_path = $uploadDir . uniqid() . '_' . $file_name;

                    $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'application/pdf'];
                    if (!in_array($file_type, $allowed_types)) throw new Exception("Invalid file type for $file_name");

                    if ($file_size > 5000000) throw new Exception("File $file_name is too large");

                    if (move_uploaded_file($tmp_name, $target_path)) {
                        $safe_path = $conn->real_escape_string($target_path);
                        $file_stmt->bind_param("iss", $complaint_id, $safe_path, $file_type);
                        $file_stmt->execute();
                    } else {
                        throw new Exception("Failed to upload $file_name");
                    }
                }
            }
        }

        // Handle voice file
        if (isset($_FILES['voice_file']) && $_FILES['voice_file']['error'] === UPLOAD_ERR_OK) {
            $file_name = basename($_FILES['voice_file']['name']);
            $file_type = $_FILES['voice_file']['type'];
            $file_size = $_FILES['voice_file']['size'];
            $target_path = $uploadDir . uniqid() . '_' . $file_name;

            $allowed_voice_types = ['audio/webm', 'audio/mpeg', 'audio/wav'];
            if (!in_array($file_type, $allowed_voice_types)) throw new Exception("Invalid voice file type.");

            if ($file_size > 10000000) throw new Exception("Voice file too large");

            if (move_uploaded_file($_FILES['voice_file']['tmp_name'], $target_path)) {
                $safe_path = $conn->real_escape_string($target_path);
                $file_stmt->bind_param("iss", $complaint_id, $safe_path, $file_type);
                $file_stmt->execute();
            } else {
                throw new Exception("Failed to upload voice file.");
            }
        }

        echo "<script>alert('Complaint submitted successfully!');</script>";
        $message_type = 'success';
        $_POST = [];
    } catch (Exception $e) {
        $message = "Error: " . $e->getMessage();
        $message_type = 'error';
    }
}

// Staff fetch
$staff_result = $conn->query("SELECT id, name, image FROM staff");
if (!$staff_result) die("Query failed: " . $conn->error);
?>

<style>
      body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    line-height: 1.6;
    max-width: 900px;
    margin: 0 auto;
    background-color: #f4f6f9;
    padding: 20px;
}

form {
    background-color: #ffffff;
    padding: 20px;
    border-radius: 10px;
    margin: auto;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

h2 {
    text-align: center;
    color: #333;
    margin: 0;
}

/* Form layout */
.form-row {
    display: flex;
    flex-wrap: wrap;
    gap: 20px;
}

.form-group {
    flex: 1 1 calc(33.33% - 20px); /* 3 columns with gap */
    min-width: 200px;
    margin-bottom: 20px;
}

@media (max-width: 768px) {
    .form-group {
        flex: 1 1 calc(50% - 20px); /* 2 columns on tablets */
    }
}

@media (max-width: 480px) {
    .form-group {
        flex: 1 1 100%; /* 1 column on phones */
    }
}

/* Inputs & labels */
label {
    font-weight: 600;
    
    display: block;
    color: #444;
}

.required {
    color: red;
}

input[type="text"],
input[type="email"],
input[type="date"],
input[type="datetime-local"],
textarea,
select {
    width: 100%;
    padding: 12px;
    border: 1px solid #ccc;
    border-radius: 8px;
    font-size: 14px;
    background-color: #fefefe;
    box-sizing: border-box;
    transition: border-color 0.3s;
}

input:focus,
textarea:focus {
    border-color: #2e86de;
    outline: none;
}

textarea {
    resize: vertical;
    min-height: 100px;
}

/* File upload */
.file-upload {
    padding: 8px;
    margin-top: 5px;
}

.file-hint {
    font-size: 12px;
    color: #666;
    margin-top: 5px;
}

/* Staff selection */
.staff-selection {
    display: flex;
    flex-wrap: wrap;
    gap: 15px;
    margin-top: 10px;
}

.staff-option {
    border: 2px solid transparent;
    padding: 2px;
    border-radius: 10px;
    background-color: #f9f9f9;
    text-align: center;
    cursor: pointer;
    transition: all 0.3s ease;
    width: 80px;
}

.staff-option:hover {
    border-color: #ccc;
}

.staff-option img {
    width: 70px;
    height: 70px;
    object-fit: cover;
    border-radius: 50%;
    margin-bottom: 10px;
}

@media (max-width: 600px) {
    .staff-selection {
        justify-content: center;
    }

    .staff-option {
        width: 100px;
    }
}

/* Buttons */
button[type="submit"] {
    width: 100%;
    background-color: #4f46e5;
    color: white;
    padding: 12px;
    font-size: 16px;
    font-weight: bold;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    transition: background-color 0.3s;
}

button[type="submit"]:hover {
    background-color: #1b66c9;
}

/* Messages */
.message {
    padding: 10px;
    margin-bottom: 20px;
    border-radius: 4px;
}

.success {
    background-color: #dff0d8;
    color: #3c763d;
    border: 1px solid #d6e9c6;
}

.error {
    background-color: #f2dede;
    color: #a94442;
    border: 1px solid #ebccd1;
}

    </style>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit Complaint</title>
    
</head>
<body>

    
    
    <?php if ($message): ?>
        <div class="message <?php echo $message_type; ?>">
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>
    <?php
// Ensure $conn is your mysqli connection
$query = "SELECT id, name, image FROM staff";
$result = $conn->query($query);

if (!$result) {
    die("Query failed: " . $conn->error);
}
?>

<form method="POST" enctype="multipart/form-data">

    <h2>Submit a Complaint</h2>
        <div class="form-group">
            <label for="staff_ids" style="text-align: center;">Complaint Against (select one or more):</label>
            <div class="staff-selection">
        <?php while ($staff = $result->fetch_assoc()): ?>
            <label class="staff-option" required>
                <input type="checkbox" name="staff_ids[]" value="<?= $staff['id'] ?>" style="display: none;">
                <img src="<?= htmlspecialchars($staff['image']) ?>" alt="Staff">
                <div><?= htmlspecialchars($staff['name']) ?></div>
            </label>
        <?php endwhile; ?>
    </div>

    <script>
        document.querySelectorAll('.staff-option').forEach(option => {
            option.addEventListener('click', () => {
                const checkbox = option.querySelector('input[type="checkbox"]');
                checkbox.checked = !checkbox.checked;
                option.style.borderColor = checkbox.checked ? '#2e86de' : 'transparent';
                option.style.backgroundColor = checkbox.checked ? '#e6f0ff' : '#f9f9f9';
            });
        });
    </script>
        </div>
        <h3 style="text-align: center;">Voice Recording</h3>
        <p style="text-align: center; margin: -14px;">آپ اپنی شکایت آواز کے ساتھ بھی ریکارڈ کر سکتے ہیں۔</p>
            <div id="recorder" style="text-align: center; margin-top: 20px;">
                <button type="button" id="startRecording" style="background-color: #4CAF50; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer;">Start Recording</button>
                <button type="button" id="stopRecording" disabled style="background-color: #f44336; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer;">Stop Recording</button>
                <button type="button" id="resetRecording" style="background-color: #ff9800; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; margin-left: 10px;">Reset</button>
                <div style="margin-top: 10px; font-size: 16px; font-weight: bold; color: #555;">
                <span id="recordTime" style="display: inline-block; padding: 5px 10px; background-color: #e0e0e0; border-radius: 20px; min-width: 60px; text-align: center;">0:00</span>
                </div>
                <audio id="audioPreview" controls style="display:none; margin-top: 10px; width: 100%;"></audio>
                <input type="file" name="voice_file" id="voiceFileInput" style="display: none;">
            </div>

        <script>
            const resetBtn = document.getElementById("resetRecording");

            resetBtn.onclick = () => {
            if (mediaRecorder && mediaRecorder.state === "recording") {
                mediaRecorder.stop();
            }
            recordedChunks = [];
            startTime = null;
            recordTimeEl.textContent = "0:00";
            audioPreview.style.display = "none";
            audioPreview.src = "";
            voiceInput.value = "";
            startBtn.disabled = false;
            stopBtn.disabled = true;
            };
        </script>
        </div>  <div class="form-row">
        <div class="form-group">
            <label for="client_name">Your Name <span class="required">*</span></label>
            <input type="text" id="client_name" name="client_name" required>
        </div>

        <div class="form-group">
            <label for="client_email">Your Email </label>
            <input type="email" id="client_email" name="client_email" >
        </div>

        <div class="form-group">
            <label for="client_phone">Phone Number <span class="required">*</span></label>
            <input type="text" id="client_phone" name="client_phone" required>
        </div>
    </div>
    <div class="form-group">
            <label for="complaint_desc">Complaint Description <span class="required">*</span></label>
            <textarea id="complaint_desc" name="complaint_desc" required></textarea>
        </div>

    <div class="form-row">
        <div class="form-group">
            <label for="preferred_date">Incident Date</label>
          
            <input type="datetime-local" id="preferred_date" name="preferred_date" onclick="this.showPicker()">

        </div>

        <div class="form-group">
            <label for="files">Upload Image</label>
            <input type="file" id="files" name="files[]" multiple class="file-upload">
            <p class="file-hint">Upload JPG, PNG, PDF. Max 5MB each.</p>
        </div>

     
    </div>


    <button type="submit">Submit Complaint</button>
    </form>
</body>
</html>
<script>
let mediaRecorder;
let recordedChunks = [];
let startTime;
const startBtn = document.getElementById("startRecording");
const stopBtn = document.getElementById("stopRecording");
const recordTimeEl = document.getElementById("recordTime");
const audioPreview = document.getElementById("audioPreview");
const voiceInput = document.getElementById("voiceFileInput");

startBtn.onclick = async () => {
    console.log("Start button clicked. Requesting microphone access...");

    try {
        // Request permission and start recording
        const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
        console.log("Permission granted. Microphone access acquired.");

        mediaRecorder = new MediaRecorder(stream);
        recordedChunks = [];

        mediaRecorder.ondataavailable = (e) => {
            if (e.data.size > 0) recordedChunks.push(e.data);
        };

        mediaRecorder.onstop = () => {
            const blob = new Blob(recordedChunks, { type: "audio/webm" });
            const audioURL = URL.createObjectURL(blob);
            audioPreview.src = audioURL;
            audioPreview.style.display = "block";

            // Convert blob to File and set to file input
            const file = new File([blob], "voice_recording.webm", { type: "audio/webm" });
            const dataTransfer = new DataTransfer();
            dataTransfer.items.add(file);
            voiceInput.files = dataTransfer.files;
        };

        mediaRecorder.start();
        startBtn.disabled = true;
        stopBtn.disabled = false;
        startTime = Date.now();
        updateTimer();
    } catch (err) {
        console.error("Permission denied or error occurred: ", err);
        alert("Microphone permission denied or an error occurred. Please allow microphone access.");
        startBtn.disabled = false;
    }
};

stopBtn.onclick = () => {
    mediaRecorder.stop();
    startBtn.disabled = false;
    stopBtn.disabled = true;
};

// Timer for display
function updateTimer() {
    if (!startTime) return;
    const now = Date.now();
    const diff = Math.floor((now - startTime) / 1000);
    const minutes = Math.floor(diff / 60);
    const seconds = diff % 60;
    recordTimeEl.textContent = `${minutes}:${seconds < 10 ? "0" : ""}${seconds}`;
    if (mediaRecorder && mediaRecorder.state === "recording") {
        setTimeout(updateTimer, 1000);
    }
}
</script>

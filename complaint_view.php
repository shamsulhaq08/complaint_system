<?php
session_start();
if (!isset($_SESSION['admin_user'])) {
    header("Location: login.php");
    exit;
}
?>
<?php
include 'db.php';

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT * FROM complaints ORDER BY id DESC";
$result = $conn->query($sql);
// Get the admin username for the logged-in user
$admin_user = $_SESSION['admin_user'];
$username = $admin_user['username'];
?>

<!DOCTYPE html>
<html>
<head>
    <title>View Complaints</title>
    <link rel="stylesheet" href="admin_styles.css">
    <link rel="stylesheet" href="styles.css">


    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>

        .complaint-box {
            background: #fff;
            padding: 15px;
            margin-bottom: 15px;
            border-left: 5px solid #4CAF50;
            cursor: pointer;
            box-shadow: 0 1px 6px rgba(0,0,0,0.1);
        }

        .complaint-box:hover {
            background: #f9f9f9;
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0; top: 0;
            width: 100%; height: 100%;
            overflow: auto;
            background-color: rgba(0,0,0,0.6);
        }

        .modal-content {
            background-color: #fff;
            margin: 10% auto;
            padding: 20px;
            border-radius: 6px;
            width: 80%;
            max-width: 700px;
            position: relative;
        }

        .close {
            color: #aaa;
            position: absolute;
            top: 15px;
            right: 20px;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }

        .close:hover {
            color: black;
        }

        .files a {
            display: block;
            margin-bottom: 5px;
            color: #007bff;
        }
        .pagination a {
    display: inline-block;
    padding: 8px 12px;
    margin: 0 4px;
    background-color: #2e86de;
    color: white;
    border-radius: 5px;
    text-decoration: none;
}
.pagination span {
    padding: 8px 12px;
    margin: 0 4px;
    font-weight: bold;
}

/* Modal Background */
.modal {
    display: none; /* Hidden by default */
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    overflow: auto;
    background-color: rgba(0,0,0,0.5); /* Black with opacity */
}

/* Modal Content Box */
.modal-content {
    background-color: #fff;
    margin: 10% auto;
    padding: 30px;
    border: 1px solid #888;
    width: 80%;
    max-width: 600px;
    border-radius: 8px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.3);
    font-family: Arial, sans-serif;
    color: #333;
}

/* Close Button */
.close {
    color: #aaa;
    float: right;
    font-size: 28px;
    font-weight: bold;
    cursor: pointer;
    transition: color 0.3s ease;
}

.close:hover,
.close:focus {
    color: #000;
}

/* Paragraphs inside modal */
.modal-content p {
    margin: 15px 0;
    line-height: 1.5;
}

/* FontAwesome Icons */
.modal-content i {
    color: #007BFF;
    margin-right: 8px;
}

.file-item {
    margin-bottom: 15px;
}

.file-item img {
    border: 2px solid #ddd;
    border-radius: 5px;
}

.file-item audio {
    width: 100%;
    max-width: 300px;
    margin-top: 10px;
}

.file-item a {
    color: #007bff;
    text-decoration: none;
}

.file-item a:hover {
    text-decoration: underline;
}

    </style>
</head>
<body>
    

<div class="admin-wrapper">
    <!-- Sidebar -->
    <aside class="admin-sidebar">
    <?php include 'sidebar.php'; ?>
    </aside>

    <!-- Main content -->
    <main class="admin-main">
        <div class="admin-header">
        <div class="container">
      
          
<h1>Complaint List</h1>

<?php
// Default to page 1 if no page is set
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$complaints_per_page = 8;
$offset = ($page - 1) * $complaints_per_page;

// Count total complaints
$total_sql = "SELECT COUNT(*) AS total FROM complaints";
$total_result = $conn->query($total_sql);
$total_row = $total_result->fetch_assoc();
$total_complaints = $total_row['total'];
$total_pages = ceil($total_complaints / $complaints_per_page);

// Fetch complaints with LIMIT and OFFSET
$sql = "SELECT * FROM complaints ORDER BY id DESC LIMIT $complaints_per_page OFFSET $offset";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $complaint_id = $row['id'];
        $modal_id = "modal_" . $complaint_id;

        echo "<div class='complaint-box' onclick=\"document.getElementById('$modal_id').style.display='block'\">";
        echo "<i class='fa fa-user'></i> Customer Name: <strong>" . htmlspecialchars($row['client_name']) . "</strong> &nbsp;&nbsp;&nbsp;";
        echo "<i class='fa fa-calendar'></i> Incident Date: <strong>" . htmlspecialchars($row['preferred_date']) . "</strong><br>";
        echo "</div>";

        echo "<div id='$modal_id' class='modal'>";
        echo "<div class='modal-content'>";
        echo "<span class='close' onclick=\"document.getElementById('$modal_id').style.display='none'\">&times;</span>";

        echo "<p><i class='fa fa-user'></i> <strong>Customer Name:</strong> " . htmlspecialchars($row['client_name']) . " (" . htmlspecialchars($row['client_email']) . ")</p>";
        echo "<p><i class='fa fa-phone'></i> <strong>Phone:</strong> " . htmlspecialchars($row['client_phone']) . "</p>";
        echo "<p><i class='fa fa-file-alt'></i> <strong>Description:</strong>" . nl2br(htmlspecialchars($row['complaint_desc'])) . "</p>";
        echo "<p><i class='fa fa-calendar'></i> <strong>Incident Date:</strong> " . htmlspecialchars($row['preferred_date']) . "</p>";
     // Assigned Staff Names (from staff_ids column)
$staff_ids_string = $row['staff_ids'];
if (!empty($staff_ids_string)) {
    // Clean and split IDs
    $staff_ids_array = array_filter(array_map('intval', explode(',', $staff_ids_string)));
    if (count($staff_ids_array) > 0) {
        $staff_ids_list = implode(',', $staff_ids_array);
        $staff_sql = "SELECT name FROM staff WHERE id IN ($staff_ids_list)";
        $staff_result = $conn->query($staff_sql);
        if ($staff_result && $staff_result->num_rows > 0) {
            echo "<p><strong>Complaint Against :</strong><br>";
            while ($staff_row = $staff_result->fetch_assoc()) {
                $staff_name = htmlspecialchars($staff_row['name']);
                echo "<div style='margin-bottom: 5px;'>👤 $staff_name</div>";
            }
            echo "</p>";
        } else {
            echo "<p><strong>Complaint Against:</strong> Not found</p>";
        }
    } else {
        echo "<p><strong>Complaint Against Staff:</strong> None</p>";
    }
} else {
    echo "<p><strong>Complaint Against Staff:</strong> None</p>";
}


// Attached Files
$file_sql = "SELECT file_path FROM complaint_files WHERE complaint_id = $complaint_id";
$file_result = $conn->query($file_sql);

if ($file_result->num_rows > 0) {
    echo "<p><strong>Attached Files:</strong><br>";
    while ($file_row = $file_result->fetch_assoc()) {
        $file = htmlspecialchars($file_row['file_path']);
        
        // Get the file extension to check if it's an image or audio
        $file_extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
        
        // Check if the file is an image
        if (in_array($file_extension, ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'svg'])) {
            echo "<div class='file-item'>
                    <img src='$file' alt='Image' style='max-width: 200px; max-height: 200px;'><br>
                    <a href='$file' target='_blank'>View Image</a>
                  </div><br>";
        }
        // Check if the file is an audio file
        elseif (in_array($file_extension, ['mp3', 'wav', 'ogg', 'aac'])) {
            echo "<div class='file-item'>
                    <audio id='audio-player' src='$file' type='audio/$file_extension'></audio>
                    <div class='audio-controls'>
                        <button class='play-btn' onclick='togglePlayPause()'>Play</button>
                        <button class='pause-btn' onclick='pauseAudio()' style='display: none;'>Pause</button>
                        <input type='range' class='seek-bar' value='0' max='100' onchange='setAudioTime()'>
                        <label class='volume-label'>Volume</label>
                        <input type='range' class='volume-bar' value='100' max='100' onchange='setVolume()'>
                    </div><br>
                    <a href='$file' target='_blank'>Listen to Audio</a>
                  </div><br>";
        }
        // If it's another file type (e.g., pdf, doc), just display a link
        else {
            echo "<div class='file-item'>
                    <a href='$file' target='_blank'>Listen to Audio</a>
                  </div><br>";
        }
    }
    echo "</p>";
}
echo "</div></div>";


    }

    // Pagination controls
    echo "<div class='pagination' style='margin-top: 20px; text-align:center;'>";
    if ($page > 1) {
        echo "<a href='?page=" . ($page - 1) . "'>&laquo; Prev</a> ";
    }
    echo "<span> Page $page of $total_pages </span>";
    if ($page < $total_pages) {
        echo " <a href='?page=" . ($page + 1) . "'>Next &raquo;</a>";
    }
    echo "</div>";

} else {
    echo "<p>No complaints found.</p>";
}
$conn->close();
?>

<script>
// Close modals when clicking outside of them
window.onclick = function(event) {
    const modals = document.querySelectorAll('.modal');
    modals.forEach(function(modal) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    });
};
</script>

<script>
    // JavaScript for custom audio controls
    const audioPlayer = document.getElementById('audio-player');
    const playButton = document.querySelector('.play-btn');
    const pauseButton = document.querySelector('.pause-btn');
    const seekBar = document.querySelector('.seek-bar');
    const volumeBar = document.querySelector('.volume-bar');

    // Toggle Play/Pause
    function togglePlayPause() {
        if (audioPlayer.paused) {
            audioPlayer.play();
            playButton.style.display = 'none';
            pauseButton.style.display = 'inline-block';
        } else {
            audioPlayer.pause();
            playButton.style.display = 'inline-block';
            pauseButton.style.display = 'none';
        }
    }

    // Pause audio
    function pauseAudio() {
        audioPlayer.pause();
        playButton.style.display = 'inline-block';
        pauseButton.style.display = 'none';
    }

    // Update seek bar
    audioPlayer.addEventListener('timeupdate', () => {
        const progress = (audioPlayer.currentTime / audioPlayer.duration) * 100;
        seekBar.value = progress;
    });

    // Set audio time from seek bar
    function setAudioTime() {
        const newTime = (seekBar.value / 100) * audioPlayer.duration;
        audioPlayer.currentTime = newTime;
    }

    // Set volume
    function setVolume() {
        audioPlayer.volume = volumeBar.value / 100;
    }
</script>

   
    </main>

  

</body>
</html>

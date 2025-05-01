<!DOCTYPE html>
<html>
<head>
    <title>Welcome to Complaint Portal</title>
    <style>
        body {
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #e9f5f5, #d2f3f3);
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .container {
            text-align: center;
            background: white;
            padding: 60px 40px;
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            max-width: 600px;
            width: 90%;
        }

        h1 {
            margin-bottom: 20px;
            color: #333;
        }

        p {
            margin-bottom: 40px;
            font-size: 16px;
            color: #666;
        }

        .btn-group {
            display: flex;
            justify-content: center;
            gap: 20px;
            flex-wrap: wrap;
        }

        .btn {
            padding: 14px 25px;
            font-size: 16px;
            border: none;
            border-radius: 8px;
            background-color: #4CAF50;
            color: white;
            text-decoration: none;
            transition: background-color 0.3s ease;
            min-width: 150px;
        }

        .btn:hover {
            background-color: #45a049;
        }

        @media (max-width: 480px) {
            .btn {
                width: 100%;
            }
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Welcome to the Complaint Portal</h1>
    <p>Please choose an option below to proceed:</p>

    <div class="btn-group">
        <a href="feedback_form.php" class="btn">Feedback</a>
        <a href="complaint_form.php" class="btn">Staff Complaint</a>
    </div>
</div>

</body>
</html>

<?php
// Define Google Apps Script URL
$scriptURL = 'https://script.google.com/macros/s/AKfycby7KVABjqo-I0Cgdg-dKoHTP1A_2XLRoMGTU73z5Qa1nKdMVi8XxfP9ZI1CA0m6yqyGng/exec';

// Default row to display
$rowToFetch = isset($_GET['row']) ? intval($_GET['row']) : 2;

// Handle POST request and redirect to next row
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $postData = http_build_query([
        'row'  => $_POST['row'],
        'col3' => $_POST['col3'],
        'col4' => $_POST['col4']
    ]);

    $opts = ['http' => [
        'method'  => 'POST',
        'header'  => 'Content-type: application/x-www-form-urlencoded',
        'content' => $postData
    ]];

    $context  = stream_context_create($opts);
    $result = file_get_contents($scriptURL, false, $context);
    $updateResponse = json_decode($result, true);
    
    if ($updateResponse['status'] === 'success') {
        // Redirect to the next row
        $nextRow = intval($_POST['row']) + 1;
        header("Location: ?row=$nextRow");
        exit();
    } else {
        echo "<p style='color: red;'>Update failed!</p>";
    }
}

// Fetch data from Apps Script
$response = file_get_contents($scriptURL . '?row=' . $rowToFetch);
$data = json_decode($response, true);

if (isset($data['data'])) {
    $col1 = htmlspecialchars($data['data'][0]);
    $col2 = htmlspecialchars($data['data'][1]);
    $col3 = htmlspecialchars($data['data'][2]);
    $col4 = htmlspecialchars($data['data'][3]);
} else {
    $col1 = $col2 = $col3 = $col4 = 'No data found.';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Row</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        

        /* Page Background */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #1c2541;
            color: #fff;
            margin: 0;
            padding: 15px 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        /* Container */
        .form-wrapper {
            background: #1c003e;
            border-radius: 16px;
            padding: 30px;
           height:100%;
            backdrop-filter: blur(10px);
            max-width: 500px;
            width: 100%;
            display: flex;
            flex-direction: column;
            justify-content:center;
            align-items:center;
        }

        h2 {
            color: #ff6b35;
            font-size: 38px;
            font-weight: 700;
            margin-bottom: 55px;
            text-align: center;
        }

        form {
            width: 100%;
        }

        label {
            display: block;
            margin-left:10px;
            margin-top: 12px;
            margin-bottom: 6px;
            font-weight: 600;
            color: #ff6b35;
        }

        input[type="text"]
         {
            width: 100%;
            padding: 12px;

            border: none;
            border-radius: 10px;
            background: rgba(255, 255, 255, 0.05);
            color: #fff;
            font-size: 16px;
            
            max-width: 475px;
             color: #cccccc;
        }
        .status{
           width: 100%;
            padding: 12px;

            border: none;
            border-radius: 10px;
            background: rgba(255, 255, 255, 0.05);
            color: #fff;
            font-size: 16px;
            
            max-width: 500px;
             color: #cccccc;
        }
        
        input[type="text"]:focus,
        select:focus {
            background: rgb(255, 255, 255);
            color:BLACK;
            outline: none;
            width:100%;
        }

        input[readonly] {
            background: rgba(255, 255, 255, 0.08);
            
            color: #cccccc;
        }

        button {
            width: 100%;
            padding: 12px;
            background-color: #ff6b35;
            border: none;
            border-radius: 10px;
            color: rgb(8, 8, 8);
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            margin-top: 20px;
        }

        /* @media (max-width: 480px) {
            .form-wrapper {
                padding: 25px 20px 25px 20px;
               
            }

            h2 {
                font-size: 22px;
        }
           input[type="text"]{
            margin-right:30px;
           }



            input[type="text"],
            .status,
            button {
                font-size: 15px;
                
            }
        } */
    </style>
</head>
<body>
<div class="form-wrapper">
    <h2>Driver detail</h2>

    <?php if (!empty($errorMessage)): ?>
        <p style="color: red; text-align:center;"><?= $errorMessage ?></p>
    <?php endif; ?>

    <form method="post">
        <input type="hidden" name="row" value="<?= $rowToFetch ?>">

        <label>Name</label>
        <input type="text" value="<?= $col1 ?>" readonly>

        <label>Number</label>
        <input type="text" value="<?= $col2 ?>" readonly>

        <label>Status</label>
        <select name="col3" class="status">
            <option value="line busy" <?= $col3 === 'line busy' ? 'selected' : '' ?>> Line Busy</option>
            <option value="ringing" <?= $col3 === 'ringing' ? 'selected' : '' ?>>Ringing</option>
            <option value="not interested" <?= $col3 === 'not interested' ? 'selected' : '' ?>> Not Interested</option>
            <option value="switched off" <?= $col3 === 'switched off' ? 'selected' : '' ?>>Switched Off</option>
            <option value="out of service" <?= $col3 === 'out of service' ? 'selected' : '' ?>> Out of Service</option>
            <option value="not answered" <?= $col3 === 'not answered' ? 'selected' : '' ?>> Not Answered</option>
            <option value="interested(call back)" <?= $col3 === 'interested(call back)' ? 'selected' : '' ?>>Interested (Call Back)</option>
            <option value="interested (coming to office)" <?= $col3 === 'interested (coming to office)' ? 'selected' : '' ?>> Interested (Coming to Office)</option>
        </select>

        <label>Comments</label>
        <input type="text" name="col4" value="<?= $col4 ?>">

        <button type="submit">Update</button>
    </form>

    <form method="get">
        <input type="hidden" name="row" value="<?= $rowToFetch - 1 ?>">
        <button type="submit">Previous</button>
    </form>
</div>
</body>
</html>

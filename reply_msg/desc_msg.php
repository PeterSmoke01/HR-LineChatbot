<?php
    $subtitle = $row['subtitle'];
    $description = $row['description'];
    
    $message = [
        "type" => "text",
        "text" => "$subtitle\n\n$description"
    ];

    // Encode the message to JSON
    $message = json_encode($message, JSON_UNESCAPED_UNICODE);
?>

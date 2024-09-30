<?php
// Assume $rows is an array containing rows from your database query

// Create an array to store unique titles
$uniqueTitles = [];
$footer_contents = [];

foreach ($query as $row) {
    // Check if the title is not already in the uniqueTitles array
    if (!in_array($row['title'], $uniqueTitles)) {
        // Add the title to the uniqueTitles array
        $uniqueTitles[] = $row['title'];
        
        // Add the footer content
        $footer_contents[] = [
            'type' => 'button',
            'style' => 'link',
            'height' => 'sm',
            'action' => [
                'type' => 'message',
                'label' => $row['title'],
                'text' => $row['title']
            ]
        ];
    }
}

$titleMessage = [
    'type' => 'flex',
    'altText' => 'โปรดเลือกหัวข้อ',
    'contents' => [
        'type' => 'bubble',
        'body' => [
            'type' => 'box',
            'layout' => 'vertical',
            'contents' => [
                [
                    'type' => 'text',
                    'text' => 'โปรดเลือกหัวข้อ',
                    'weight' => 'bold',
                    'size' => 'xl',
                    'align' => 'center'
                ],
            ]
        ],
        'footer' => [
            'type' => 'box',
            'layout' => 'vertical',
            'spacing' => 'sm',
            'contents' => $footer_contents,
        ]
    ]
];

$message = json_encode($titleMessage, JSON_UNESCAPED_UNICODE);
?>

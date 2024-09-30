<?php
$footer_contents = [];
foreach ($GLOBALS['related_subtitles'] as $subtitle_data) {
    $footer_contents[] = [
        'type' => 'button',
        'style' => 'link',
        'height' => 'sm',
        'action' => [
            'type' => 'message',
            'label' => $subtitle_data['subtitle'],
            'text' => $subtitle_data['subtitle']
        ]
    ];
}

$flexMessage = [
    'type' => 'flex',
    'altText' => $GLOBALS['related_title'],
    'contents' => [
        'type' => 'bubble',
        'body' => [
            'type' => 'box',
            'layout' => 'vertical',
            'contents' => [
                [
                    'type' => 'text',
                    'text' => $GLOBALS['related_title'], // แสดง subtitle ที่เลือก
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
            'flex' => 0
        ]
    ]
];

$message = json_encode($flexMessage, JSON_UNESCAPED_UNICODE);
?>

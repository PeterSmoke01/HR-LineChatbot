<?php
$message = '{
    "type": "flex",
    "altText": "Flex Message",
    "contents": {
        "type": "bubble",
        "direction": "ltr",
        "header": {
            "type": "box",
            "layout": "vertical",
            "contents": [
            {
                "type": "text",
                "text": "ระเบียบในการทำงาน",
                "weight": "bold",
                "size": "lg",
                "align": "center",
                "contents": []
            }
            ]
        },
        "footer": {
            "type": "box",
            "layout": "vertical",
            "spacing": "md",
            "position": "default",
            "contents": [
            {
                "type": "button",
                "action": {
                "type": "message",
                "label": "1. เวลาทำงาน",
                "text": "................................."
                },
                "color": "#06DA15FF",
                "style": "primary"
            },
            {
                "type": "button",
                "action": {
                "type": "message",
                "label": "2. เสาร์หยุด",
                "text": "................................."
                },
                "color": "#06DA15FF",
                "style": "primary"
            },
            {
                "type": "button",
                "action": {
                "type": "message",
                "label": "3. การลงเวลาทำงาน",
                "text": "................................."
                },
                "color": "#06DA15FF",
                "style": "primary"
            },
            {
                "type": "button",
                "action": {
                "type": "message",
                "label": "4. การจ่ายเงินเดือน",
                "text": "................................."
                },
                "color": "#06DA15FF",
                "style": "primary"
            }
            ]
        }
    }
}'

?>
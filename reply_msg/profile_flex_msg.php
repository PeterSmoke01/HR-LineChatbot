<?php
// ตรวจสอบค่าว่างของสถานะ
$statusMessage = !empty($row['status_message']) ? $row['status_message'] : " ";

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
                "text": "ข้อมูลผู้ใช้งาน",
                "weight": "bold",
                "size": "lg",
                "align": "center",
                "contents": []
            }
            ]
        },
        "hero": {
            "type": "image",
            "url": "'.$row['picture_url'].'",
            "size": "full",
            "aspectRatio": "1.51:1",
            "aspectMode": "fit"
        },
        "body": {
            "type": "box",
            "layout": "vertical",
            "contents": [
            {
                "type": "box",
                "layout": "horizontal",
                "contents": [
                {
                    "type": "text",
                    "text": "User ID :",
                    "contents": []
                },
                {
                    "type": "text",
                    "text": "'.$row['uid'].'",
                    "contents": []
                }
                ]
            },
            {
                "type": "box",
                "layout": "horizontal",
                "contents": [
                {
                    "type": "text",
                    "text": "ชื่อ :",
                    "contents": []
                },
                {
                    "type": "text",
                    "text": "'.$row['display_name'].'",
                    "contents": []
                }
                ]
            },
            {
                "type": "box",
                "layout": "horizontal",
                "contents": [
                {
                    "type": "text",
                    "text": "สถานะ :",
                    "contents": []
                },
                {
                    "type": "text",
                    "text": "'.$statusMessage.'",
                    "contents": []
                }
                ]
            }
            ]
        }
    }
}';
?>

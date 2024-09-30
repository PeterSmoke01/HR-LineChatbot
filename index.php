<?php
    include("conn.php");
    $LINEData = file_get_contents('php://input');
    $jsonData = json_decode($LINEData,true);
    $replyToken = $jsonData["events"][0]["replyToken"];
    $text = $jsonData["events"][0]["message"]["text"];
    $userId = $jsonData['events'][0]['source']['userId'];
    $utype = $jsonData['events'][0]['type'];

    function getLINEProfile($datas){
        $datasReturn = [];
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $datas['url'],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                "Authorization: Bearer ".$datas['token'],
                "cache-control: no-cache"
            ),
        ));
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
        if($err){
            $datasReturn['result'] = 'E';
            $datasReturn['message'] = $err;
        }else{
            if($response == "{}"){
                $datasReturn['result'] = 'S';
                $datasReturn['message'] = 'Success';
            }else{
                $datasReturn['result'] = 'E';   
                $datasReturn['message'] = $response;
            }
        }
        return $datasReturn;
    }

    if($utype == 'follow'){
        $LINEDatas['url'] = "https://api.line.me/v2/bot/profile/".$userId;
        $LINEDatas['token'] = "v4yC7dflftH1HbyfKXUNbtDDRA6KKb+h5j0fMDLrVjTvw2bsJlk7G3Pr3va42D6VpIJmJw/7zTVKfHQ5JUpxEwySEzRZfVRR/YsRJHxJEb0MRJZRl3b4PnuvvYnglyZb19p5MkQCQTj+Ain6PbjxwwdB04t89/1O/w1cDnyilFU=";
        $result = getLINEProfile($LINEDatas);
        $replyprofile = json_decode($result['message']);
    
        file_put_contents('log-profile.txt', $result['message'] . PHP_EOL, FILE_APPEND);
        
        $uid = $userId; //เก็บ UserId ของผู้ใช้งาน
        $displayName = $replyprofile->{'displayName'}; //เก็บ ชื่อ หรือ displayname ของผู้ใช้งาน
        $statusMessage = $replyprofile->{'statusMessage'}; //เก็บ สถานะ ของผู้ใช้งาน
        $pictureUrl = $replyprofile->{'pictureUrl'}; //เก็บ รูปภาพของผู้ใช้งาน ในที่นี้จะเป็น URL
    
        $sql = "SELECT * FROM user_profile WHERE uid = '$uid'";
        $query = $conn->query($sql);
    
        if($query->num_rows < 1){
            $sql = "INSERT INTO user_profile (uid, display_name, status_message, picture_url) VALUES ('$uid', '$displayName', '$statusMessage', '$pictureUrl')";
            $conn->query($sql);
        }else{
            $sql = "UPDATE user_profile SET display_name = '$displayName', status_message = '$statusMessage', picture_url = '$pictureUrl' WHERE uid = '$uid'";
            $conn->query($sql);
        }     
    }
    
    function sendMessage($replyJson,$token){
        $datasReturn = [];
        $curl = curl_init();
        curl_setopt_array($curl, array(
        CURLOPT_URL => $token['URL'],
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => $replyJson,
        CURLOPT_HTTPHEADER => array(
            "authorization: Bearer ".$token['AccessToken'],
            "cache-control: no-cache",
            "content-type: application/json; charset=UTF-8",
        ),
        ));

        $result = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            $datasReturn['result'] = 'E';
            $datasReturn['message'] = $err;
        } else {
            if($result == "{}"){
            $datasReturn['result'] = 'S';
            $datasReturn['message'] = 'Success';
            }else{
            $datasReturn['result'] = 'E';
            $datasReturn['message'] = $result;
            }
        }

        return $result;
    }

    // -----------------------------------------------------------------------------------------------------
    
    $user_id = $jsonData['events'][0]['source']['userId'];
    // บันทึกคำถามของผู้ใช้ลงในฐานข้อมูล
    $question_sql = "INSERT INTO user_questions (user_id, question, response) VALUES ('$user_id', '$text', '')";
    $conn->query($question_sql);

    // ดึงข้อมูลจากฐานข้อมูล
    $query = $conn->query("SELECT * FROM user_profile WHERE uid = '$user_id'");

    // ตรวจสอบจำนวนแถวที่พบ
    file_put_contents('log.txt', "Found " . $query->num_rows . " user(s):" . PHP_EOL, FILE_APPEND);

    if ($query->num_rows > 0) {
        file_put_contents('log.txt', 'พบข้อมูลผู้ใช้' . PHP_EOL, FILE_APPEND);
        $row = $query->fetch_assoc();
        $status = $row['status'];
        
        // ตรวจสอบว่า $text เป็นตัวเลข 10 ตัวหรือไม่
        if($status == 'not_started'){
            include("reply_msg/welcome_msg.php");
            // $response_text = "กรุณากรอกรหัสพนักงานก่อนใช้งาน";
            if (ctype_digit($text)) {
                
                $employee_id = intval($text);
                // ทำการอัปเดตฐานข้อมูล
                $sql = "UPDATE user_profile SET employee_id='$text', status='complete' WHERE uid='$user_id'";
                if ($conn->query($sql) === TRUE) {
                    // บันทึกลงใน log ถ้าอัปเดตฐานข้อมูลสำเร็จ
                    file_put_contents('log.txt', "Updated employee ID to: $text" . PHP_EOL, FILE_APPEND);
                }
                // ดึงข้อมูลทั้งหมดจากฐานข้อมูล
                $sql = "SELECT * FROM question_ans";
                $query = $conn->query($sql);
                include("reply_msg/register2_msg.php");
                include("reply_msg/title_msg.php");
                // $response_text = $titleMessage['messages'][0]['text'];
                file_put_contents('log.txt', 'title_msg.php called' . PHP_EOL, FILE_APPEND);
            }
        }
        elseif ($status == 'complete') {
            file_put_contents('log.txt', 'Status: ' . $status . PHP_EOL, FILE_APPEND);
            file_put_contents('log.txt', 'text: ' . $text . PHP_EOL, FILE_APPEND);
            // ดึงข้อมูลทั้งหมดจากฐานข้อมูล
            $sql = "SELECT * FROM question_ans";
            $query = $conn->query($sql);

            $related_title = '';
            $related_data = [];

            $found_title = false;
            $found_subtitle = false;

            while ($row = $query->fetch_assoc()) {
                if (strcasecmp($text, $row['title']) == 0) { // เปรียบเทียบแบบไม่สนใจตัวพิมพ์เล็ก/ใหญ่
                    $found_title = true;
                    $related_title = $row['title'];
                    break; // พบ title ที่ตรงกันแล้ว
                    file_put_contents('log.txt', 'found title' . PHP_EOL, FILE_APPEND);
                }
                if (strcasecmp($text, $row['subtitle']) == 0) { // เปรียบเทียบแบบไม่สนใจตัวพิมพ์เล็ก/ใหญ่
                    $found_subtitle = true;
                    $related_subtitle = $row['subtitle'];
                    $related_description = $row['description'];
                    break; // พบ subtitle ที่ตรงกันแล้ว
                    file_put_contents('log.txt', 'found subtitle' . PHP_EOL, FILE_APPEND);
                }
            }
            if ($found_title) {
                // ดึงข้อมูล subtitle ที่เกี่ยวข้องกับ title ที่ผู้ใช้เลือก
                $stmt = $conn->prepare("SELECT subtitle, description FROM question_ans WHERE title = ?");
                $stmt->bind_param("s", $related_title);
                $stmt->execute();
                $result = $stmt->get_result();
                file_put_contents('log.txt', 'show subtitle' . PHP_EOL, FILE_APPEND);
                        
                $related_subtitles = [];
                while ($row = $result->fetch_assoc()) {
                    $related_subtitles[] = [
                        'subtitle' => $row['subtitle'],
                    ];
                }
                // ตรวจสอบว่ามี subtitle ที่เกี่ยวข้องหรือไม่
                if (count($related_subtitles) > 0) {
                    // ส่งข้อมูลไปยัง subtitle_msg.php
                    $GLOBALS['related_subtitles'] = $related_subtitles;
                    $GLOBALS['related_title'] = $related_title; // ส่ง title ที่เกี่ยวข้องไปด้วย
                    include("reply_msg/subtitle_msg.php");
                    // $response_text = $flexMessage['messages'][0]['text'];
                    file_put_contents('log.txt', 'subtitle_msg.php called' . PHP_EOL, FILE_APPEND);
                } else {
                    // กรณีไม่พบ subtitle ที่เกี่ยวข้อง
                    include("reply_msg/unknown_msg.php");
                    file_put_contents('log.txt', 'unknown_msg.php called' . PHP_EOL, FILE_APPEND);
                }
            } elseif ($found_subtitle) {
                // ดึงข้อมูล description ที่เกี่ยวข้องกับ subtitle ที่ผู้ใช้เลือก
                $GLOBALS['related_subtitle'] = $related_subtitle;
                $GLOBALS['related_description'] = $related_description;
                include("reply_msg/desc_msg.php");
                $response_text = json_encode($message, JSON_UNESCAPED_UNICODE);
                file_put_contents('log.txt', 'desc_msg.php called' . PHP_EOL, FILE_APPEND);
            
            } elseif ($text == "ระเบียบบริษัท"){
                include("reply_msg/title_msg.php");
                // $response_text = json_encode($titleMessage, JSON_UNESCAPED_UNICODE);
                file_put_contents('log.txt', 'title_msg.php called' . PHP_EOL, FILE_APPEND);
            } else {
                // ค้นหา subtitle ที่คล้ายกัน
                $stmt = $conn->prepare("SELECT subtitle, description FROM question_ans");
                $stmt->execute();
                $result = $stmt->get_result();
                file_put_contents('log.txt', 'searching similar subtitles' . PHP_EOL, FILE_APPEND);
                
                $best_match = '';
                $highest_similarity = 0;
                
                while ($row = $result->fetch_assoc()) {
                    $similarity = 0;
                    similar_text($text, $row['subtitle'], $similarity);
                    if ($similarity > $highest_similarity) {
                        $highest_similarity = $similarity;
                        $best_match = $row['subtitle'];
                        $related_description = $row['description'];
                        file_put_contents('log.txt', 'Current best match: ' . $best_match . ' with similarity: ' . $similarity . PHP_EOL, FILE_APPEND);
                    }
                }
                
                if ($highest_similarity > 50) { // เปอร์เซ็นต์ความคล้ายที่คุณต้องการกำหนด
                    $GLOBALS['related_subtitle'] = $best_match;
                    $GLOBALS['related_description'] = $related_description;
                    file_put_contents('log.txt', 'Best match found: ' . $best_match . PHP_EOL, FILE_APPEND);
                    file_put_contents('log.txt', 'Related description: ' . $related_description . PHP_EOL, FILE_APPEND);
                    // ตรวจสอบว่ามีข้อมูล description ที่จะส่งไปหรือไม่
                    if (!empty($related_description)) {
                        // สร้างข้อความ
                        $message = json_encode([
                            "type" => "text",
                            "text" => "$best_match\n\n$related_description"
                        ], JSON_UNESCAPED_UNICODE);
                        // $response_text = "$best_match\n\n$related_description";
                        $response_text = json_encode($message, JSON_UNESCAPED_UNICODE);
                    }
                    file_put_contents('log.txt', 'desc_msg.php called' . PHP_EOL, FILE_APPEND);
                } else {
                    // กรณีไม่พบ subtitle ที่เกี่ยวข้อง
                    include("reply_msg/unknown_msg.php");
                    // $response_text = "ไม่เข้าใจสิ่งที่ท่านต้องการ โปรดดูคำสั่งจากเมนู";
                    file_put_contents('log.txt', 'unknown_msg.php called' . PHP_EOL, FILE_APPEND);
                }
            }
        } else {
            file_put_contents('log.txt', "Invalid input. Please enter a 10-digit integer." . PHP_EOL, FILE_APPEND);
            include("reply_msg/register1_msg.php");
            // $response_text = "Invalid input. Please enter a 10-digit integer.";
        }
    }
        
        if (similar_text($text, "profile") >= 5) {
            $sql = "SELECT * FROM user_profile WHERE uid = '$userId'";
            $query = $conn->query($sql);
            if ($query->num_rows > 0) {
                $row = $query->fetch_assoc();
                include("reply_msg/profile_flex_msg.php");
                // Log ข้อความที่สร้างขึ้น
                file_put_contents('log.txt', 'profile_flex_msg.php called' . PHP_EOL, FILE_APPEND);
            } else {
                $message = '{
                    "type" : "text",
                    "text" : "ไม่พบผู้ใช้งาน"
                }';
            }
        }

        // ตรวจสอบว่ามี user_id ใน session หรือไม่
        if (isset($_SESSION['user_id'])) {
            $user_id = $_SESSION['user_id'];

            // สอบถามข้อมูลจาก user_profile ด้วย user_id
            $query = $conn->query("SELECT * FROM user_profile WHERE user_id = '$user_id'");

            // ตรวจสอบว่าพบข้อมูลผู้ใช้หรือไม่
            if ($query->num_rows > 0) {
                $row = $query->fetch_assoc();
                $status = $row['status'];

                // เริ่มเมนู และตรวจสอบว่า $message ถูกตั้งค่าหรือไม่
                if (!isset($message) || empty($message)) {
                    file_put_contents('log.txt', 'พบข้อมูลผู้ใช้' . PHP_EOL, FILE_APPEND);
                    if ($status == 'not_started') {
                        // เลือกสังกัด
                        include("reply_msg/register1_msg.php");
                        $conn->query("UPDATE user_profile SET status='selecting_department' WHERE user_id='$user_id'");
                        file_put_contents('log.txt', 'register1_msg.php called' . PHP_EOL, FILE_APPEND);
                    }
                }
            } else {
                echo "User not found.";
            }
            // ปิดการเชื่อมต่อฐานข้อมูล
            $conn->close();
        } else {
            echo "User ID not found in session.";
        }

        // บันทึกคำตอบของบอทลงในฐานข้อมูล
        $response_array = json_decode($message, true);
        $response_text = $response_array['text'];

        $response_sql = "UPDATE user_questions SET response='$response_text' WHERE user_id='$user_id' AND question='$text'";
        $conn->query($response_sql);
        
    // -----------------------------------------------------------------------------------------------------

    $replymessage = json_decode($message);

    $lineData['URL'] = "https://api.line.me/v2/bot/message/reply";
    $lineData['AccessToken'] = "v4yC7dflftH1HbyfKXUNbtDDRA6KKb+h5j0fMDLrVjTvw2bsJlk7G3Pr3va42D6VpIJmJw/7zTVKfHQ5JUpxEwySEzRZfVRR/YsRJHxJEb0MRJZRl3b4PnuvvYnglyZb19p5MkQCQTj+Ain6PbjxwwdB04t89/1O/w1cDnyilFU=";
    $replyJson["replyToken"] = $replyToken;
    $replyJson["messages"][0] = $replymessage;
    $encodeJson = json_encode($replyJson);
    $results = sendMessage($encodeJson,$lineData);

    // Log การส่งข้อความ
    file_put_contents('log.txt', 'Sending message: ' . $encodeJson . PHP_EOL, FILE_APPEND);
    file_put_contents('log.txt', 'Response from LINE: ' . $results . PHP_EOL, FILE_APPEND);

    http_response_code(200);
?>
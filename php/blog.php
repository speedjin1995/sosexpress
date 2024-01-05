<?php
require_once "db_connect.php";

session_start();
$allowed = array("jpg" => "image/jpg", "jpeg" => "image/jpeg", "gif" => "image/gif", "png" => "image/png");

if(isset($_POST['engTitle'], $_POST['chTitle'])){
	$title_en = filter_input(INPUT_POST, 'engTitle', FILTER_SANITIZE_STRING);
	$title_ch = filter_input(INPUT_POST, 'chTitle', FILTER_SANITIZE_STRING);
	$engBlog = $_POST['engBlog'];
	$chineseBlog = $_POST['chineseBlog'];

    if($_POST['blogId'] != null && $_POST['blogId'] != ''){
        if ($update_stmt = $db->prepare("UPDATE blog SET title_en=?, title_ch=?, en=?, ch=? WHERE id=?")) {
            $update_stmt->bind_param('sssss', $title_en, $title_ch, $engBlog, $chineseBlog, $_POST['blogId']);
            
            // Execute the prepared query.
            if (! $update_stmt->execute()) {
                echo json_encode(
					array(
						"status"=> "failed", 
						"message"=> $update_stmt->error
					)
				); 
            }
            else{
                echo json_encode(
					array(
						"status"=> "success", 
						"message"=> "Updated Successfully!!" 
					)
				);
            }
        }
    }
    else{
        if ($insert_stmt = $db->prepare("INSERT INTO blog (title_en, title_ch, en, ch) VALUES (?, ?, ?, ?)")) {
            $insert_stmt->bind_param('ssss', $title_en, $title_ch, $engBlog, $chineseBlog);
            
            // Execute the prepared query.
            if (! $insert_stmt->execute()) {
                echo json_encode(
					array(
						"status"=> "failed", 
						"message"=> $insert_stmt->error
					)
				);
            }
            else{
                echo json_encode(
					array(
						"status"=> "success", 
						"message"=> "Added Successfully!!" 
					)
				);
            }
        }
    }
}
else{
    echo json_encode(
        array(
            "status"=> "failed", 
            "message"=> "Please fill in all the fields"
        )
    );   
}
?>
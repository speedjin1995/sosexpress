<?php
require_once "db_connect.php";

session_start();
$allowed = array("jpg" => "image/jpg", "jpeg" => "image/jpeg", "gif" => "image/gif", "png" => "image/png");

if(isset($_POST['engTitle'])){
	$title_en = filter_input(INPUT_POST, 'engTitle', FILTER_SANITIZE_STRING);
	$engBlog = $_POST['engBlog'];

    if($_POST['blogId'] != null && $_POST['blogId'] != ''){
        if ($update_stmt = $db->prepare("UPDATE blog SET title_en=?, content_en=? WHERE id=?")) {
            $update_stmt->bind_param('sss', $title_en, $engBlog, $_POST['blogId']);
            
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
        if ($insert_stmt = $db->prepare("INSERT INTO blog (title_en, content_en) VALUES (?, ?)")) {
            $insert_stmt->bind_param('ss', $title_en, $engBlog);
            
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
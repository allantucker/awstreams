<?php

require_once 'connection.php';

class products
{

    public static function save_product()
    {
        $connection = \connection::initilize_connection();

        $pro_name = trim($_POST['pro_name']);
        $pro_name = mysqli_real_escape_string($connection,$pro_name);

        $cat_id = intval($_POST['cat_id']);
        $cat_id = mysqli_real_escape_string($connection,$cat_id);

        $pro_id = intval($_POST['pro_id']);
        $pro_id = mysqli_real_escape_string($connection,$pro_id);

        $pro_img = "";

        if (!empty($_FILES["pro_img"]["tmp_name"]))
        {
            $return_arr = self::upload_image($_FILES);
            if (!empty($return_arr['img_path']))
            {
                $pro_img = $return_arr['img_path'];
            }
            else{
                $msg_error = $return_arr['error_msg'];
                $msg_error_type = "error";
                return [
                    "msg_error" => $msg_error,
                    "msg_error_type" => $msg_error_type,
                ];
            }
        }

        if ($pro_id == 0)
        {
            $sql = "INSERT INTO products (pro_name, cat_id, pro_img)
            VALUES ('$pro_name', $cat_id , '$pro_img')";
        }
        else{

            if (empty($pro_img))
            {
                $sql = "update products set 
                      pro_name = '$pro_name', 
                      cat_id = $cat_id
                       
                       where pro_id = $pro_id
                  ";
            }
            else{
                $sql = "update products set 
                      pro_name = '$pro_name', 
                      cat_id = $cat_id,
                      pro_img = '$pro_img'
                       
                       where pro_id = $pro_id
                  ";
            }


        }

        if ($connection->query($sql) === TRUE) {
            $msg_error = "Product is saved Successfully";
            $msg_error_type = "success";
        } else {

            $msg_error = "Error: " . $sql . "<br>" . $connection->error;
            $msg_error_type = "danger";
        }

        return [
            "msg_error" => $msg_error,
            "msg_error_type" => $msg_error_type,
        ];

    }

    private static function upload_image($files_arr)
    {

        $target_dir = "uploads/";
        $file_name = basename($files_arr["pro_img"]["name"]);
        $file_name_arr = explode('.',$file_name);
        $encrypted_name = md5($file_name_arr[0].time().rand(1,999999));
        $file_name = $encrypted_name.'.'.$file_name_arr[1];
        $target_file = $target_dir . $file_name;
        $image_type = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));

        $error_msg = "";
        $img_path = "";
        $upload_flag = 1;

        if (file_exists($target_file)) {
            $error_msg = "Sorry, file already exists.";
            $upload_flag = 0;
        }
        if ($files_arr["pro_img"]["size"] > 500000) {
            $error_msg =  "Sorry, your file is too large.";
            $upload_flag = 0;
        }
        if($image_type != "jpg" && $image_type != "png" && $image_type != "jpeg"
            && $image_type != "gif" ) {
            $error_msg =  "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
            $upload_flag = 0;
        }
        if ($upload_flag == 0) {
            $error_msg =  "Sorry, your file was not uploaded.";
        } else {
            if (move_uploaded_file($files_arr["pro_img"]["tmp_name"], $target_file)) {
                $img_path =  $target_file;
                $error_msg =  "";
            } else {
                $error_msg =  "Sorry, there was an error uploading your file.";
            }
        }

        return [
            "img_path" => $img_path,
            "error_msg" => $error_msg,
        ];

    }

    public static function get_all_products()
    {

        $connection = \connection::initilize_connection();

        $get_all_pros_sql="
                SELECT 
                pro.*,
                
                #cat
                cat.cat_id as 'cat_id',
                cat.cat_name as 'cat_name',
                cat.parent_id as 'parent_id'
                
                FROM products as pro
                INNER JOIN categories as cat on (pro.cat_id = cat.cat_id)
            ";
        $get_all_pros_results=mysqli_query($connection,$get_all_pros_sql);

        return $get_all_pros_results;
    }

    public static function remove_pro()
    {

        $connection = \connection::initilize_connection();
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['pro_id']) && !empty($_POST['pro_id']))
        {
            $output['status'] = "success";
            $output['msg'] = "";

            $pro_id = intval($_POST['pro_id']);
            $pro_id = mysqli_real_escape_string($connection,$pro_id);


            $sql = "DELETE FROM products WHERE pro_id=$pro_id";

            if ($connection->query($sql) === TRUE) {
                $output['status'] = "success";
                $output['msg'] = "";

            } else {
                $output['status'] = "error";
                $output['msg'] = " Error Occurred !! ";

            }

            // Close connection
            mysqli_close($connection);

            echo json_encode($output);
            return;

        }

    }


}
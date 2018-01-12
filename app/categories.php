<?php

require_once 'connection.php';

class categories
{

    public static function save_category()
    {
        $connection = \connection::initilize_connection();

        $cat_name = trim($_POST['cat_name']);
        $cat_name = mysqli_real_escape_string($connection,$cat_name);

        $parent_id = intval($_POST['parent_id']);
        $parent_id = mysqli_real_escape_string($connection,$parent_id);

        $cat_id = intval($_POST['cat_id']);
        $cat_id = mysqli_real_escape_string($connection,$cat_id);

        if ($cat_id == 0)
        {
            $sql = "INSERT INTO categories (cat_name, parent_id)
            VALUES ('$cat_name', '$parent_id')";
        }
        else{
            $sql = "update categories set 
                      cat_name = '$cat_name', 
                      parent_id = '$parent_id'
                       
                       where cat_id = $cat_id
                  ";
        }

        if ($connection->query($sql) === TRUE) {
            $msg_error = "Category is saved Successfully";
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

    public static function get_all_categories()
    {

        $connection = \connection::initilize_connection();

        $get_all_cats_sql="
                SELECT 
                #cat
                cat.cat_id as 'cat_id',
                cat.cat_name as 'cat_name',
                cat.parent_id as 'parent_id',
                
                #parent
                IFNULL(parent_cat.cat_id,'')as 'parent_cat_id',
                IFNULL(parent_cat.cat_name,'')as 'parent_cat_name',
                IFNULL(parent_cat.parent_id,'')as 'parent_parent_id'
                
                FROM categories as cat 
                LEFT OUTER JOIN categories as parent_cat on (cat.parent_id = parent_cat.cat_id)
            ";
        $get_all_cats_results=mysqli_query($connection,$get_all_cats_sql);

        return $get_all_cats_results;
    }

    public static function get_all_parent_categories()
    {

        $connection = \connection::initilize_connection();

        $get_all_cats_sql="
                SELECT 
                *
                FROM categories
                where parent_id = 0
            ";
        $get_all_cats_results=mysqli_query($connection,$get_all_cats_sql);

        return $get_all_cats_results;
    }

    public static function get_parent_cat_childs()
    {

        $connection = \connection::initilize_connection();

        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['parent_id']) && !empty($_POST['parent_id']))
        {
            $output['html_data'] = "";

            $parent_id = intval($_POST['parent_id']);
            $parent_id = mysqli_real_escape_string($connection,$parent_id);


            $get_parent_childs_sql="
                SELECT *
                FROM categories 
                where parent_id = $parent_id
            ";
            $get_parent_childs_results = mysqli_query($connection,$get_parent_childs_sql);

            if ($get_parent_childs_results->num_rows)
            {
                $output['html_data'] .= '<div class="col-md-6 childs_div from_parent_'.$parent_id.'">
                                <label for="">
                                    <b>Select Parent Category ?</b>
                                </label>
                                <select class="form-control select_parent_cat">
                                    <option value="'.$parent_id.'">-- Is Child --</option>';
                while($row = $get_parent_childs_results->fetch_assoc()){
                    $output['html_data'] .= '<option value="'.$row['cat_id'].'">'.$row['cat_name'].'</option>';
                }
                $output['html_data'] .= '</select>
                            </div>';
            }


            // Close connection
            mysqli_close($connection);

            echo json_encode($output);
            return;

        }

    }

    public static function remove_cat()
    {

        $connection = \connection::initilize_connection();
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['cat_id']) && !empty($_POST['cat_id']))
        {
            $output['status'] = "success";
            $output['msg'] = "";

            $cat_id = intval($_POST['cat_id']);
            $cat_id = mysqli_real_escape_string($connection,$cat_id);


            $sql = "DELETE FROM categories WHERE cat_id=$cat_id";

            if ($connection->query($sql) === TRUE) {

                // remove all products related to cat_id
                $pro_sql = "DELETE FROM products WHERE cat_id=$cat_id";
                $check = $connection->query($pro_sql);

                self::remove_all_childs($connection,$cat_id);

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

    private static function remove_all_childs($connection,$cat_id)
    {
        $get_parent_childs_sql="
                SELECT *
                FROM categories 
                where parent_id = $cat_id
            ";
        $get_parent_childs_results = mysqli_query($connection,$get_parent_childs_sql);

        if ($get_parent_childs_results->num_rows)
        {
            while($row = $get_parent_childs_results->fetch_assoc()){

                $new_cat_id = $row['cat_id'];
                $sql = "DELETE FROM categories WHERE cat_id=$new_cat_id";

                if ($connection->query($sql) === TRUE) {
                    self::remove_all_childs($connection,$new_cat_id);

                } else {
                    $output['status'] = "error";
                    $output['msg'] = " Error Occurred !! ";
                }

            }
        }
    }

}
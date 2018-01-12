<?php require_once 'config.php'?>
<?php include 'app/categories.php'?>
<?php include 'app/products.php'?>


<?php

    $meta_title = " Products Page ";

    session_start();

    // if he is logged in redirect to dashboard

    if (!isset($_SESSION) || !count($_SESSION))
    {
        header("location: index.php");
    }


    #region save category
    $msg_error = "";
    $msg_error_type = "danger";
    if ($_SERVER["REQUEST_METHOD"] == "POST")
    {

        if (empty(trim($_POST['pro_name'])))
        {
            $msg_error .= " Product Name is Required !!";
        }

        if ((!isset($_POST['cat_id']) || $_POST['cat_id'] == 0))
        {
            $msg_error .= " Category is Required !!";
        }

        if (empty($msg_error))
        {

            $return_arr = \products::save_product();

            $msg_error = $return_arr["msg_error"];
            $msg_error_type = $return_arr["msg_error_type"];

        }

    }

    #endregion


    #region Get All products with join

        $get_all_pros_results = products::get_all_products();

    #endregion

    #region Get All Categories with join

        $get_all_cats_results = categories::get_all_parent_categories();

    #endregion

?>

<?php require_once 'components/header.php'?>

<div class="container-fluid">
    <div class="row">

        <div class="col-md-3">
            <?php include 'components/menu.php'?>
        </div>

        <div class="col-md-9">

            <?php if(!empty($msg_error)): ?>
                <div class="alert alert-<?= $msg_error_type ?>">
                    <?php echo $msg_error; ?>
                </div>
            <?php endif; ?>

            <br><br>
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <h3 class="panel-title">Show All Products</h3>
                </div>
                <div class="panel-body categories_container">
                    <table class="table table-hover">
                        <thead>
                            <th>#</th>
                            <th>Product Image</th>
                            <th>Product Name</th>
                            <th>Category Name</th>
                            <th>Edit</th>
                            <th>Delete</th>
                        </thead>
                        <tbody>

                        <?php $counter = 0; ?>
                        <?php while($row = $get_all_pros_results->fetch_assoc()): ?>
                            <tr>
                                <td><?= $counter + 1; ?></td>
                                <td>
                                    <?php if(!empty($row['pro_img'])): ?>
                                        <img src="<?= SITE_URL.$row['pro_img'] ?>" width="100">
                                    <?php endif; ?>
                                </td>
                                <td><?= $row['pro_name']; ?></td>
                                <td><?= $row['cat_name']; ?></td>
                                <td>
                                    <a href="" class="btn btn-info edit_pro"
                                       data-pro_id="<?= $row['pro_id']; ?>"
                                       data-pro_name="<?= $row['pro_name']; ?>"
                                       data-cat_id="<?= $row['cat_id']; ?>"
                                       data-cat_name="<?= $row['cat_name']; ?>">
                                        Edit <i class="fa fa-edit"></i>
                                    </a>
                                </td>
                                <td>
                                    <a href="#" class="btn btn-danger remove_pro" data-pro_id="<?= $row['pro_id']; ?>">
                                        Remove <i class="fa fa-close"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php $counter++; ?>
                        <?php endwhile; ?>

                        </tbody>
                    </table>
                </div>
            </div>

            <br><br>
            <?php if($get_all_cats_results->num_rows): ?>

                <div class="panel panel-warning">
                    <div class="panel-heading">
                        <h3 class="panel-title">
                            Manage Product
                            <a href="" class="btn btn-success add_new_pro_btn"><i class="fa fa-plus"></i></a>
                        </h3>
                    </div>
                    <div class="panel-body">

                        <form method="post" enctype="multipart/form-data">

                            <div class="row">

                                <div class="col-md-6 col-md-offset-3 manage_edit_pro hide_div">
                                    Your Current Category is : <b></b> <br>
                                    <label for="change_parent">
                                        Check to Change Category
                                    </label>
                                    <input type="checkbox" id="change_parent" class="form-control change_parent">
                                </div>

                                <div class="col-md-6">
                                    <label for="">
                                        <b>Product Name</b>
                                    </label>
                                    <input type="text" required class="form-control pro_name" name="pro_name">
                                </div>

                                <div class="col-md-6">
                                    <label for="">
                                        <b>Select Category ?</b>
                                    </label>
                                    <select class="form-control select_parent_cat parent_select">
                                        <option value="0">-- Select Category --</option>
                                        <?php while($row = $get_all_cats_results->fetch_assoc()): ?>
                                            <option value="<?= $row['cat_id'] ?>"><?= $row['cat_name'] ?></option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>

                                <input type="hidden" name="pro_id" class="pro_id" value="0">
                                <input type="hidden" name="cat_id" class="pro_cat_id" value="0">

                                <div class="col-md-12 get_child_cats"></div>

                                <div class="col-md-6">
                                    <label for="">
                                        <b>Upload Product Image</b>
                                    </label>
                                    <input type="file" name="pro_img">
                                </div>

                                <div class="col-md-12 save_cat_btn">
                                    <button type="submit" class="btn btn-success">Submit</button>
                                </div>

                            </div>


                        </form>

                    </div>
                </div>

                <?php else: ?>
                <div class="alert alert-danger">
                    <b>
                        Please add Categories for First then add products !!
                    </b>
                </div>

            <?php endif; ?>

        </div>

    </div>
</div>


<?php require_once 'components/footer.php'?>

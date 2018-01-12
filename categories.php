<?php require_once 'config.php'?>
<?php include 'app/categories.php'?>

<?php

    $meta_title = " Categories Page ";

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

        if (empty(trim($_POST['cat_name'])))
        {
            $msg_error .= " Category Name is Required !!";
        }

        if (!isset($_POST['parent_id']))
        {
            $msg_error .= " Parent Category is Required !!";
        }

        if (empty($msg_error))
        {

            $return_arr = \categories::save_category();

            $msg_error = $return_arr["msg_error"];
            $msg_error_type = $return_arr["msg_error_type"];

        }

    }

    #endregion


    #region Get All Categories with join

        $get_all_cats_results = categories::get_all_categories();

    #endregion

    $get_all_parents = [];

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
                    <h3 class="panel-title">Show All Categories</h3>
                </div>
                <div class="panel-body categories_container">
                    <table class="table table-hover">
                        <thead>
                            <th>#</th>
                            <th>Category Name</th>
                            <th>Parent Category Name</th>
                            <th>Edit</th>
                            <th>Delete</th>
                        </thead>
                        <tbody>

                            <?php $counter = 0; ?>
                            <?php while($row = $get_all_cats_results->fetch_assoc()): ?>
                                <?php
                                    if ($row['parent_id'] == 0)
                                    {
                                        $get_all_parents[] = $row;
                                    }
                                ?>
                                <tr>
                                    <td><?= $counter + 1; ?></td>
                                    <td><?= $row['cat_name']; ?></td>
                                    <td><?= $row['parent_cat_name']; ?></td>
                                    <td>
                                        <a href="" class="btn btn-info edit_cat"
                                           data-cat_id="<?= $row['cat_id']; ?>"
                                           data-cat_name="<?= $row['cat_name']; ?>"
                                           data-parent_id="<?= $row['parent_id']; ?>"
                                           data-parent_cat_name="<?= $row['parent_cat_name']; ?>">
                                            Edit <i class="fa fa-edit"></i>
                                        </a>
                                    </td>
                                    <td>
                                        <a href="#" class="btn btn-danger remove_cat" data-cat_id="<?= $row['cat_id']; ?>">
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
            <div class="panel panel-warning">
                <div class="panel-heading">
                    <h3 class="panel-title">
                        Manage Category
                        <a href="" class="btn btn-success add_new_cat_btn"><i class="fa fa-plus"></i></a>
                    </h3>
                </div>
                <div class="panel-body">

                    <form method="post">

                        <div class="row">

                            <div class="col-md-6 col-md-offset-3 manage_edit_cat hide_div">
                                Your Current Parent is : <b></b> <br>
                                <label for="change_parent">
                                    Check to Change Parent
                                </label>
                                <input type="checkbox" id="change_parent" class="form-control change_parent">
                            </div>

                            <div class="col-md-6">
                                <label for="">
                                    <b>Category Name</b>
                                </label>
                                <input type="text" required class="form-control cat_name" name="cat_name">
                            </div>

                            <div class="col-md-6">
                                <label for="">
                                    <b>Select Parent Category ?</b>
                                </label>
                                <select class="form-control select_parent_cat parent_select">
                                    <option value="0">-- Is Parent --</option>
                                    <?php if(count($get_all_parents)): ?>
                                        <?php foreach($get_all_parents as $key => $parent_cat): ?>
                                            <option value="<?= $parent_cat['cat_id'] ?>"><?= $parent_cat['cat_name'] ?></option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>

                            <input type="hidden" name="cat_id" class="cat_id" value="0">
                            <input type="hidden" name="parent_id" class="parent_id" value="0">

                            <div class="col-md-12 get_child_cats"></div>


                            <div class="col-md-12 save_cat_btn">
                                <button type="submit" class="btn btn-success">Submit</button>
                            </div>

                        </div>


                    </form>

                </div>
            </div>

        </div>

    </div>
</div>


<?php require_once 'components/footer.php'?>

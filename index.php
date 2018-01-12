<?php require_once 'config.php'?>
<?php include 'app/login.php'?>
<?php

    $meta_title = " Login Page ";

    session_start();

    // if he is logged in redirect to categories

    if (isset($_SESSION) && count($_SESSION))
    {
        header("location: categories.php");
    }


    $username = $password = "";
    $username_err = $password_err = "";
    $password = "";
    $error_msg = "";

    if (isset($_COOKIE['remember_username']))
    {
        $username = $_COOKIE['remember_username'];
    }

    if (isset($_COOKIE['remember_password']))
    {
        $password = $_COOKIE['remember_password'];
    }

    if($_SERVER["REQUEST_METHOD"] == "POST"){

        $return_arr = \login::check_login();

        if (isset($return_arr['username_err']))
        {
            $username_err = $return_arr['username_err'];
        }

        if (isset($return_arr['password_err']))
        {
            $password_err = $return_arr['password_err'];
        }

        if (isset($return_arr['error_msg']))
        {
            $error_msg = $return_arr['error_msg'];
        }

    }

?>

<?php require_once 'components/header.php'?>

<div class="container">
    <div class="row">

        <?php if(!empty($error_msg)): ?>
            <div class="alert alert-danger"><?= $error_msg; ?></div>
        <?php endif; ?>

        <div class="col-sm-6 col-md-4 col-md-offset-4">
            <h1 class="text-center login-title">Sign in to continue</h1>
            <div class="account-wall">
                <img class="profile-img" src="<?= SITE_URL.'public/images/login_img.png' ?>"
                     alt="">
                <form class="form-signin" action="<?php echo SITE_URL; ?>" method="POST">
                    <div class="form-group <?php echo (!empty($username_err) || !empty($password_err)) ? 'has-error' : ''; ?>">
                        <label>Username:<sup>*</sup></label>
                        <input type="text" name="username" class="form-control" value="<?php echo $username; ?>">
                        <span class="help-block"><?php echo (!empty($username_err)?$username_err:$password_err); ?></span>
                    </div>
                    <div class="form-group <?php echo (!empty($password_err) || !empty($username_err)) ? 'has-error' : ''; ?>">
                        <label>Password:<sup>*</sup></label>
                        <input type="password" name="password" class="form-control" value="<?php echo $password; ?>">
                        <span class="help-block"><?php echo (!empty($username_err)?$username_err:$password_err); ?></span>
                    </div>
                    <label class="checkbox pull-left">
                        <input type="checkbox" name="remember_me">
                        Remember me
                    </label>
                    <button class="btn btn-lg btn-primary btn-block" type="submit">
                        Sign in</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once 'components/footer.php'?>

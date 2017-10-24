<?php
    include "reg_and_login.php";
    include 'register.php';
    require "dbconnect.php";
    if(isset($_POST['login_mail'],$_POST['login_pass'])){
        $mail = clean($_POST['login_mail']);
        $pass = clean($_POST['login_pass']);
        if(!empty_field($mail,$pass)){
            $surveyor = new first_staff;
            if($res = $surveyor->loggedin($mail, $pass)){
               $_SESSION['svy_ser'] = $res[1];
               $_SESSION['svy_id'] = $res[0];
               header("Location: profile.php");
            }else{
                $feedback = feedback('Invalid email and/or password','feedback_failure');
            }
        }else{
            $feedback = feedback("Both fields are required",'feedback_info');
        }
    }
    $title = "Login";
    $keywords = "";
    $description = "Login with your accout";
    $init = new initializer($title,$keywords,$description);
?>
<!DOCTYPE html>
<html>
    <?php $init->head("../"); ?>
    <body>
        <nav class="nav navbar-default" role=""navigation>
            <div class="navbar-header">
                <span class="navbar-brand">Are you a member?</span>
            </div>
        </nav>
        <div class="container">
            <?php if(isset($feedback)){ echo $feedback; } ?>
            <div class="row">
                <div id="login-div" class="col-xs-10 col-xs-offset-1 col-sm-10 col-sm-offset-1 col-md-6 col-md-offset-3 col-lg-6 col-lg-offset-3 mini_div">
                    <form class="form-horizonatal" action="" method="POST">
                        <div class="form-group">
                            <input type="email" name="login_mail" placeholder="Email" value="<?php if(isset($mail)){ echo $mail;} ?>" class="form-control"/>
                        </div>
                        <div class="form-group">
                            <input type="password" name="login_pass" placeholder="Password" class="form-control"/>
                        </div>
                        <button type="submit" class="btn btn-primary btn-block">Sign in</button>
                        <h4 class="text-center">Forgot password? <a href="code_sender.php">reset it</a></h4>
                    </form>
                    <h2 class="text-center line_on_sides"><span>or</span></h2>
                    <h4 class="text-center">Not a member! <a href="../index.php">Go home</a></h4>
                </div>
            </div>
        </div>
        <nav class="nav navbar-default navbar-fixed-bottom" role=""navigation>
            <div class="navbar-header">
                <span class="navbar-brand text-center">Surveyors <?php echo date("Y"); ?></span>
            </div>
        </nav>
    </body>
</html>

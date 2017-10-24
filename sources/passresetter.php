<?php
    include "../inc_in_all.php";
    include "register.php";
    include "dbconnect.php";
    include "reg_and_login.php";
    $title = "Password reset";
    $keywords = "code";
    $description = "This is a password reset page";
    $init = new initializer($title,$keywords,$description);
    
    if(isset($_POST['Email'],$_POST['reset_code'],$_POST['new_pass'],$_POST['new_pass_again'])){
        $Email = clean($_POST['Email']);
        $reset_code = $_POST['reset_code'];
        $new_pass = clean($_POST['new_pass']);
        $new_pass_again = clean($_POST['new_pass_again']);
        $code_in_db = clean(data_exists('reset_code','surveyors','Email',$Email));
        if($reset_code == $code_in_db){
            $pass_update = new new_surveyor();
            $pass_update->validate_data($Email, $new_pass, $new_pass_again);
            if($pass_update->good_to_go()){
                $hash = new Hash();
                $salt = $hash->makeSalt(33);
                if($pass_update->update_field('Password','surveyors', $hash->enc_pwd($new_pass,$salt), $Email) && $pass_update->update_field('salt','surveyors',$salt, $Email)){
                    $feedback = feedback('Password updated successfully, redirecting you to your account in 5 seconds ...','feedback_success');
                    session_unset($_SESSION['svy_id']);
                    $_SESSION['svy_id'] = data_exists('SID','surveyors','Email',$Email);
                    header("Location: profile.php");
                }else{
                    $feedback = feedback('Password update failure','feedback_failure');
                }
            }else{
                $feedback = feedback("Your passwords either don't match or they are too short",'feedback_success');
            }
        }else{
            $feedback = feedback('Wrong code','feedback_failure');
        }
    }
?>
<!DOCTYPE html>
<html>
    <?php $init->head('../'); ?>
    <body>
        <div class="container">
            <nav class="nav navbar-default navbar-fixed-top" role=""navigation>
                <div class="navbar-header">
                    <span class="navbar-brand">Reset your password</span>
                </div>
            </nav>
            <div class="col-xs-10 col-xs-offset-1 col-sm-10 col-sm-offset-1 col-md-6 col-md-offset-3 col-lg-6 col-lg-offset-3" style="margin-top: 5%">
                <?php if(isset($feedback)){ echo $feedback; } ?>
                <?php if(isset($_GET['info'])){ echo feedback($_GET['info'],'feedback_success').'<br /><br />'; } ?>
                <form action="<?php echo $current_file; ?>" method="POST" class="">
                    <div class="form-group">
                        <input type="email" name="Email" class="form-control" value="<?php if(isset($_GET['email'])){ echo $_GET['email'];} ?>" />
                    </div>
                    <p>Enter the code you received in the field below</p>
                    <div class="form-group">
                        <input type="number" name="reset_code" class="form-control" placeholder="Reset code"/>
                    </div>
                    <p>Provide new password</p>
                    <div class="form-group">
                        <input type="password" name="new_pass" placeholder="New Password" class="form-control"/>
                    </div>
                    <div class="form-group">
                        <input type="password" name="new_pass_again" placeholder="New Password again" class="form-control"/>
                    </div>
                    <button type="submit" class="btn btn-success">Submit</button>
                </form>
                <legend class="text-center">or</legend>
                <h2 class="text-center"><a href="../index.php">Go home</a></h2>

            </div>
            <nav class="nav navbar-default navbar-fixed-bottom">
                <div class="navbar-header">
                    <span class="navbar-brand text-center">Surveyors <?php echo date("Y"); ?></span>
                </div>
            </nav>
        </div>
    </body>
</html>

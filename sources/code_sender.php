<?php
    include "../inc_in_all.php";
    include "register.php";
    include "dbconnect.php";
  
    include "reg_and_login.php";
    
    $title = "Reset code receiver";
    $keywords = "";
    $description = "This page receives a password reset code";
    $init = new initializer($title,$keywords,$description);
    if(isset($_POST['rmail'],$_POST['meanz']) && !empty($_POST['rmail'])){
        $data = new new_surveyor();
        $email = clean($_POST['rmail']);
        $meanz = clean($_POST['meanz']);
        $code = rand(10000,99999);
        $msg = "Professional Surveyors Uganda: Your password reset code is $code";
        $notify = new notifications;
        if(data_exists('Email', 'surveyors','Email',$email)){
           if($data->update_field('reset_code','surveyors',$code, $email)){
               if($meanz == "email"){
                    include "mail_and_sms.php";
                    if($notify->send_mail($email,$msg,'Password reset code')){
                        $feedback = feedback("Your password reset code has been sent to your email","feedback_success");
                        header("Location: passresetter.php?info=$feedback&email=$email");
                    }else{
                        $feedback = feedback("Mail service down","feedback_failure");
                    } 
               }else{
                  $number = data_exists('Tel','surveyors','Email',$email);
                  if($number){
                    if($notify->send_sms($number, $msg)){
                        $feedback = feedback("Code sent by sms, it will arrive shortly",'feedback_info');
                        header("Location: passresetter.php?info=$feedback&email=$email");
                    }else{
                        $feedback = feedback("Something went wrong with the sms sender","feedback_failure");
                    }   
                  }else{
                      $feedback = feedback("Your number does not exist in our database. You have to use an email","feedback_info");
                  }
              }
            }                
       }else{
                $feedback = feedback("Email not in our database","feedback_info");
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
            <div style="margin-top: 100px;" class="col-xs-10 col-xs-offset-1 col-sm-10 col-sm-offset-1 col-md-6 col-md-offset-3 col-lg-6 col-lg-offset-3" style="margin-top: 5%">
                <?php if(isset($feedback)){ echo $feedback; } ?>
                <form id="rmail_form" action="<?php echo $current_file; ?>" method="POST" class="">
                    <p>Enter the email you used to register with us</p>
                    <div class="form-group">
                        <input type="email" name="rmail" class="form-control"/><br />
                    </div>
                    <p>Send code by:
                    <div class="form-group col-lg-6">
                        <select class="form-control" name="meanz">
                            <option>email</option>
                            <option>sms</option>
                        </select>
                    </div>
                    </p>
                    <button id="rmail_but" class="btn btn-success" type="submit">Send code</button>
                </form><br />
                <h2 class="text-center line_on_sides"><span style="background-color: rgb(230,230,230);">or</span></h2>
                <h2 class="text-center"><a href="../index.php">Go home</a></h2>
            </div>
            <nav class="nav navbar-default navbar-fixed-bottom" role=""navigation>
                <div class="navbar-header">
                    <span class="navbar-brand text-center">Surveyors <?php echo date("Y"); ?></span>
                </div>
            </nav>
        </div>
    </body>
</html>

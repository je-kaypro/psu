<?php
    include "../inc_in_all.php";
    include "phpqrcode/qrlib.php";
    if(isset($_SESSION['svy_id'])){
        if($_SESSION['svy_id'] == 1){
            include "admin.php";
        }else{
            include 'profile_inc.php';
            $data = new user_data();
            $u_type = $data->get('User type','surveyors');
            $work_place = $data->get('Work place','surveyors');
            if($u_type === 'Secondary Admin'){
                include "sec_admin.php";
            }else{
                include "register.php";
                include "reg_and_login.php";
                include 'file_fns.php';
                $file = new file_fns;
                if(isset($_FILES['new_pp']) && !empty($_FILES['new_pp'])){
                    $pp = $_FILES['new_pp'];
                    if($file->upload_profile_pic($pp)){
                        $feedback = feedback('Profile picture changed','feedback_success');
                    }
                }

                if(isset($_POST['Address'],$_POST['Tel'],$_POST['Work_place'])){
                    $Address = clean($_POST['Address']);
                    $Tel = clean($_POST['Tel']);
                    $Work_place = clean($_POST['Work_place']);
                    $Email = data_exists('Email','surveyors','SID',$_SESSION['svy_id']);
                    $surveyor_up = new new_surveyor();
                    if($surveyor_up->update_data($Address, $Tel, $Work_place)){
                        $feedback = feedback("Profile update successful",'feedback_success');
                    }else{
                        $feedback = feedback("Profile update failed",'feedback_failure');
                    }
                }

                if(isset($_FILES['surveyor_work'],$_POST['office'],$_POST['work_name']) && !empty($_FILES['surveyor_work']['name'])){
                   $file = $_FILES['surveyor_work'];
                   $office = clean($_POST['office']);
                   $work_name = clean($_POST['work_name']);
                   $file_name = explode('.',$_FILES['surveyor_work']['name']);
                   $ext = end($file_name);
                   if($ext == 'zip'){
                       $files = new file_fns;
                       if($files->zip_file_handled($file,$_SESSION['svy_id'],$office,$work_name)){
                           $feedback = feedback('Your work has been uploaded successfully','feedback_success');
                       }else{
                           $feedback = feedback('Failed to handle zip','feedback_failure');
                       }
                   }else{
                       $feedback = feedback('Unsupported file format, only zip files are allowed','feedback_failure');
                   }
                }
                
                if(isset($_POST['old_pass'],$_POST['new_pass'],$_POST['new_pass_again'])){
                    $old_pass = clean($_POST['old_pass']);
                    $new_pass = clean($_POST['new_pass']);
                    $new_pass_again = clean($_POST['new_pass_again']);
                    $pass_in_db = $data->get('password','surveyors');
                    $salt_in_db = $data->get('salt','surveyors');
                    $hash = new Hash();
                    
                    if(!empty_field($old_pass,$new_pass,$new_pass_again)){
                        if($pass_in_db === $hash->enc_pwd($old_pass, $salt_in_db)){
                            if($new_pass === $new_pass_again){
                                $new_salt = $hash->makeSalt(33);
                                $new_pass_hash = $hash->enc_pwd($new_pass, $new_salt);
                                if($data->update('password','surveyors',$new_pass_hash) && $data->update('salt','surveyors',$new_salt)){
                                    $feedback = feedback('Password successfully changed','feedback_success');
                                }else{
                                    $feedback = feedback('Password change failed','feedback_failure');
                                }
                            }else{
                                $feedback = feedback('The new passwords don\'t match','feedback_failure');
                            }
                           
                        }else{
                            $feedback = feedback('The old password you provided is wrong','feedback_failure');
                        }
                    }else{
                        $feedback = feedback('All fields are required','feedback_info');
                    }
                }

                $title = "Member profile";
                $keywords = "Member";
                $description = "This page displayes member profiles";
                $init = new initializer($title,$keywords,$description);
?>
<!DOCTYPE html>
<html>
    <?php $init->head("../"); ?>
    <body>
        <h1 class="text-center">PROFESSIONAL SURVEYORS UGANDA</h1>
        <div class="row top_nav">
            <nav class="nav navbar-default navbar-inverse">
                <div class="navbar-header col-xs-7">
                    <span class="navbar-brand"><a href="profile.php" class="ln_const_size">Member Profile</a></span>
                </div>
                <div class="ul_div col-xs-5">
                    <ul class="nav navbar-nav navbar-right" id="profile_nav">
                        <li><a href="../index.php">Go home</a></li>
                        <li class="white_lns"><a href="logout.php">Sign out</a></li>
                        <li class="dropdown">
                            <a class="dropdown-toggle pull-right" data-toggle="dropdown" href="#">
                                &#9776Menu</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>
            <ul class="menu_from_left text-center">
                    <li><a href="logout.php">Sign out</a></li>
                    <li><a href="../index.php">Go home</a></li>
            </ul>
        </div>
        <div class="container">
            <?php if(isset($feedback)){ echo $feedback; } ?>
            <div class="row middle">
                <div class="col-xs-12 col-sm-5 col-md-5 col-lg-5">
                    <img src="<?php echo no_xss_thru($data->get('Profile Pic','images')); ?>" alt="Profile pic">
                    <div id="pp_label"><button class="btn btn-info btn-sm" id="pp_update_but">Change Picture</button></div>
                    <div id="pp_update_div">
                        <form class="form-inline" enctype="multipart/form-data" action="profile.php" method="POST">
                            <div class="form-group">
                                <input type="file" name="new_pp"/>
                            </div>
                            <button type="submit" class="btn btn-success btn-sm">upload</button>
                        </form>
                    </div>
                    <div class="mini_div">
                        <?php
                            $files = new file_fns;
                            $files->display_surveyor_work($_SESSION['svy_id']);
                        ?>
                    </div>
                    <h4>QR Code</h4>
                    <img src="../imgz/<?php echo no_xss_thru($data->get('Serial Number','surveyors')).'.png'; ?>" alt="QR Code">
                </div>
                <div class="col-xs-12 col-sm-7 col-md-7 col-lg-7 mini_div" id="profile_display_div">
                    <?php
                        $fields = array('Serial Number','Surname','Other names','Sex','Address','Tel','Email','Work place','Nationality','Membership');
                        foreach ($fields as $field){
                            if($field != "Membership"){
                                echo '<div class="info_portion"><label>'.no_xss_thru($field).':</label>'.no_xss_thru($data->get($field,'surveyors')).'</div>';
                            }else{
                                echo '<div class="info_portion"><label>'.no_xss_thru($field).':</label>'. no_xss_thru(round(((365*24*60*60) - (time() - $data->get($field, 'surveyors')))/(24*60*60),0)).' days</div>';
                            }    
                        }
                        ?><br>
                        <div class="text-center">
                            <button id="info_updater" class="btn btn-success text-center">Update profile</button>
                            <button id="password_changer" class="btn btn-info text-center">Change Password</button>
                        </div>
                </div>
                <div class="col-xs-12 col-sm-7 col-md-7 col-lg-7 mini_div" id="update_form_div">
                    <?php
                        $controls = array('Serial Number'=>'label','Surname'=>'label','Other names'=>'label','Nationality'=>'label','Email'=>'label','Address'=>'text','Tel'=>'text','Work place'=>'text'); 
                    ?>
                    <form action="profile.php" method="POST" class="form-horizontal">
                        <h2 class="line_on_sides"><span>You can only update a few fields</span></h2>
                        <?php
                            foreach ($controls as $p_holder => $type) {
                               if($type == 'text' || $type=='email'){
                                   echo '<label class="col-sm-3">'.$p_holder.'</label><div class="form-group col-sm-9"><input class="form-control" type="'.$type.'" name="'.implode('_', explode(' ',$p_holder)).'" value="'.$data->get($p_holder, 'surveyors').'" required/></div>';
                               }else{
                                   echo '<div class="info_portion"><label>'.$p_holder.':</label>'.$data->get($p_holder, 'surveyors').'</div>';
                               }
                            }
                        ?>
                        <button type="submit" class="btn btn-success center-block">Submit new info</button><br />
                    </form>
                </div>
                <div id="password_change_form_div" class="hidden">
                    <h3 class="text-center text-primary">Change password</h3>
                    <form action="profile.php" method="POST">
                        <div class="form-group">
                            <input placeholder="Current password" type="password" name="old_pass" class="form-control" required/>
                        </div>
                        <div class="form-group">
                            <input placeholder="New password" type="password" name="new_pass" class="form-control" required/>
                        </div>
                        <div class="form-group">
                            <input placeholder="New password again" type="password" name="new_pass_again" class="form-control" required/>
                        </div>
                        <button class="btn btn-info center-block">Submit</button>
                    </form>
                </div>
            </div>
            <div class="row mini_div">
                <h1 class="prompter text-center">Institution ID</h1>
                <div id="inst_id" class="col-xs-12 col-sm-10 col-sm-offset-1 col-md-6 col-md-offset-3 col-lg-6 col-lg-offset-3">
                    <h3 class="text-center inst-header">PROFESSIONAL SURVEYORS UGANDA</h3>
                    <div class="col-xs-5 col-sm-5 col-md-5 col-lg-5 id-img">
                        <img class="img-responsive" src="<?php echo no_xss_thru($data->get('Profile Pic','images')); ?>" alt="Profile pic">
                    </div>
                    <div class="col-xs-7 col-sm-7 col-md-7 col-lg-7 id-info">
                        <?php
                            $fields = array('Serial Number','Surname','Other names','Sex','Work place','Nationality');
                            foreach ($fields as $field){
                                echo '<label>'.no_xss_thru($field).':</label>&nbsp;'.no_xss_thru($data->get($field,'surveyors')).'<br />';
                            }
                        ?>
                        <div class="red-line"></div>
                        <div class="red-line"></div>
                    </div>
                </div>
                <div id="id_front">
                    <h3 class="text-center inst-header">PROFESSIONAL SURVEYORS UGANDA</h3>
                    <div class="col-xs-5 col-sm-5 col-md-5 col-lg-5 id-img">
                        <img class="img-responsive" src="<?php echo no_xss_thru($data->get('Profile Pic','images')); ?>" alt="Profile pic">
                    </div>
                    <div class="col-xs-7 col-sm-7 col-md-7 col-lg-7 id-info">
                        <?php
                            $fields = array('Serial Number','Surname','Other names','Sex','Work place','Nationality');
                            foreach ($fields as $field){
                                echo '<label>'.no_xss_thru($field).':</label>&nbsp;'.no_xss_thru($data->get($field,'surveyors')).'<br />';
                            }
                        ?>
                        <div class="red-line"></div>
                        <div class="red-line"></div>
                    </div>
                </div>
                <div id="id_back">
                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 id-back">
                        <div class="row">
                            <div class="col-xs-7 col-sm-7 col-md-7 col-lg-7 id-info">
                                <?php
                                    $fields = array('Address','Tel','Email','User type');
                                    foreach ($fields as $field){
                                        echo '<label>'.no_xss_thru($field).':</label>&nbsp;'.no_xss_thru($data->get($field,'surveyors')).'<br />';
                                    }
                                    echo '<br />'.no_xss_thru($data->get('Serial Number','surveyors')).'>>>'.no_xss_thru($data->get('Surname','surveyors')).'>>>'.no_xss_thru($data->get('Other names','surveyors'))
                                ?>
                                <h5 class="text-center">
                                    If found, please call the above mentioned contact or deliver it to the office stated on the front side
                                </h5>
                            </div>
                            <div class="col-xs-5 col-sm-5 col-md-5 col-lg-5">
                                <img class="right" src="../imgz/<?php echo no_xss_thru($data->get('Serial Number','surveyors')).'.png'; ?>" alt="QR Code">
                            </div>
                        </div>
                        <div class="red-line"></div>
                        <div class="red-line"></div>
                    </div>
                </div>
                <div class="col-lg-12 text-center">
                    <button id="show_front" class="btn btn-primary">Front</button>
                    <button id="show_back" class="btn btn-info">Back</button>
                </div>
            </div>
            <div class="row" id="work_div">
                <h1 class="prompter text-center">Have some work? upload it now</h1>
                <form class="form-horizontal col-md-8 col-lg-8 col-md-offset-2" enctype="multipart/form-data" method="POST" action="profile.php">
                    <div class="form-group">
                        <label>Browse for zip file:</label>
                        <input type="file" name="surveyor_work"/>
                    </div>
                    <div class="form-group">
                        <label>Which office will work on it?</label>
                        <select name="office">
                          <?php $data->display_surveyor_offices(); ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <input type="text" class="form-control" name="work_name" placeholder="Give your work a name"/>
                    </div>
                    <button type="submit" class="btn btn-success btn-lg center-block">Upload work</button>
                </form>
            </div>
        </div>
        <nav class="nav navbar-default row">
            <div class="navbar-header">
                <span class="navbar-brand text-center">Surveyors <?php echo date("Y"); ?></span>
            </div>
        </nav>
    </body>
</html>
<?php
        }
      }
    }else{
        include "login.php";
    }
?>

<?php
    require "register.php";
    require "dbconnect.php";
    require "reg_and_login.php";
    require "file_fns.php";
    include "surveyor_searcher.php";
    include "profile_inc.php";
    if(isset($_POST['current_pass'],$_POST['new_pass'],$_POST['new_pass_retype'])){
        $c_p = clean($_POST['current_pass']);
        $n_p = clean($_POST['new_pass']);
        $n_pr = clean($_POST['new_pass_retype']);
        if(empty_field($c_p,$n_p,$n_pr)){
            $feedback = feedback("You missed one or more required field(s)",'feedback_failure');
        }else{
            $email = data_exists('Email','surveyors','SID',1);
            $current_pass = data_exists('Password','surveyors','SID',1);
            $current_salt = data_exists('salt','surveyors','SID',1);
            $surveyor_reg = new new_surveyor();
            $surveyor_reg->validate_data($email,$n_p,$n_pr);
            if($surveyor_reg->good_to_go()){
                $hash = new Hash();
                if($hash->enc_pwd($c_p,$current_salt) == $current_pass){
                    $new_salt = $hash->makeSalt(33);
                    if($surveyor_reg->update_field('Password','surveyors', $hash->enc_pwd($n_p,$new_salt), $email) && $surveyor_reg->update_field('salt','surveyors',$new_salt, $email)){
                        $feedback = feedback('Admin password changed successfully','feedback_success');
                    }
                }else{
                    $feedback = feedback('The current password you provided is invalid','feedback_failure');
                }
            }else{
                $feedback = feedback('Your passwords either don\'t match or are weak','feedback_info');
            }
        }
    }
    
    if(isset($_POST['email'],$_POST['pass'])){ 
       //Assigning variables
        $u_type = clean($_POST['user_type']);
        $s_name = clean($_POST['surname']);
        $o_name = clean($_POST['other_name']);
        $sex = clean($_POST['sex']);
        $addr = clean($_POST['address']);
        $tel = clean($_POST['tel_no']);
        $email = clean($_POST['email']);
        $nat = clean($_POST['nationality']);
        $wkplace = clean($_POST['c_wk_place']);
        $qual = clean($_POST['qualific']);
        $pass = clean($_POST['pass']);
        $pass_retype = clean($_POST['pass_retype']);
        if(empty_field($s_name,$o_name,$tel,$email,$nat,$wkplace,$qual,$pass,$pass_retype)){
            $feedback = feedback("You missed one or more required field(s)",'feedback_info');
        }else{
            $surveyor_reg = new new_surveyor();
            $surveyor_reg->validate_data($email,$pass,$pass_retype);
            if($surveyor_reg->good_to_go()){
                if(!$surveyor_reg->exists('surveyors', $email)){
                    $hash = new Hash();
                    $salt = $hash->makeSalt(33);         
                    if($surveyor_reg->registered(serial_no(),$u_type,$s_name,$o_name,$sex,$addr,$tel,$email,$nat,$wkplace,$qual, $hash->enc_pwd($pass,$salt), $salt)){
                        $files = new file_fns;
                        $query = "SELECT `SID`,`Serial Number` FROM `surveyors` WHERE `Email`='".$email."'";
                        $myquery = $db->query($query);
                        if($myquery){
                            $result = $myquery->fetch_array();
                            $sno = $result[1];
                            $SID = $result[0];
                            if($surveyor_reg->initialize_images($SID)){
                               $feedback = feedback('Registered successfully','feedback_success'); 
                               $notify = new notifications;
                               $message = "Professional Surveyors Uganda - registration details: Login mail:- $email, Password:- $pass. Please login and change your password for better security";
                               $subject = "Surveyor registration notification";
                               if($notify->send_sms($tel,$message) || $notify->send_mail($email, $message, $subject)){
                                   $not_feedback = feedback("Registration notification sent",'feedback_info');
                               }else{
                                   $not_feedback = feedback("Notification service down",'feedback_failure');
                               }
                            }
                        }
                    }
                }else{
                    $feedback = feedback('Email has already been used','feedback_info');
                }
            }else{
                $feedback = feedback("errors in the closet",'feedback_failure');
            }
        }
    }
    
    if(isset($_POST['D_ID'])){
        $data = new user_data;
        $user = new file_fns;
        $sid = $_POST['D_ID'];
        $s_no = $data->get('Serial Number', 'surveyors',Null, $sid);
        $pp = $data->get('Profile Pic', 'images', Null, $sid);
        $name = $data->get('Surname','surveyors',Null,$sid);
        if($user->deleted($sid, $s_no, $pp)){
            $feedback = feedback($name.' deleted successfully', 'feedback_success');
        }
    }
    if(isset($_POST['R_ID'])){
        $data = new user_data;
        $sid = $_POST['R_ID'];
        $name = $data->get('Surname','surveyors',Null,$sid);
        if($data->update('Membership','surveyors',time(),$sid)){
            $feedback = feedback($name."'s membership reset successfully",'feedback_success');
        }
    }
        
    $title = "Admin";
    $keywords = "Administrator,surveyor";
    $description = "I administer the site";
    $init = new initializer($title,$keywords,$description);
?>
<!DOCTYPE html>
<html>
    <?php $init->head("../"); ?>
    <body>
        <h1 class="text-center">PROFESSIONAL SURVEYORS UGANDA</h1>
        <div class="row top_nav">
            <nav class="nav navbar-default navbar-inverse"  id="reg_nav">
                <div class="navbar-header col-xs-7">
                    <span class="navbar-brand"><big><a href="profile.php" class="ln_const_size">Administrator</a></big></span>
                </div>
                <div class="ul_div col-xs-5">
                    <ul class="nav navbar-nav navbar-right">
                        <li><a href="../index.php">Go home</a></li>
                        <li class="active"><a href="profile.php">Add surveyor</a></li>
                        <li><a href="#" class="chng_pass">Change password</a></li>
                        <li><a href="logout.php">Logout</a></li>
                        <li class="dropdown">
                            <a class="dropdown-toggle text-center pull-right" data-toggle="dropdown" href="#">
                                &#9776Menu</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>
            <ul class="text-center menu_from_left pull-left hidden-md hidden-lg">
                    <li><a href="../index.php">Go home</a></li>
                    <li class="active"><a href="profile.php">Add surveyor</a></li>
                    <li><a href="#" class="chng_pass">Change password</a></li>
                    <li><a href="logout.php">Logout</a></li>
            </ul>
        </div>
        <div class="container">
            <div class="row" id="admin-div">
                <?php if(isset($feedback)){ echo $feedback.'<br />';} if(isset($not_feedback)){ echo $not_feedback; } ?>
                <div id="admin_pass_changer">
                    <form method="POST" action="profile.php">
                        <h3 class="text-center">All fields are mandatory!</h3>
                        <div class="form-group">
                            <input type="password" name="current_pass" class="form-control" placeholder="Current password" required/>
                        </div>
                        <div class="form-group">
                            <input type="password" name="new_pass" class="form-control" placeholder="New password" required/>
                        </div>
                        <div class="form-group">
                            <input type="password" name="new_pass_retype" class="form-control" placeholder="New password again" required/>
                        </div>
                        <button type="submit" class="btn btn-primary center-block" style="width: 200px;">Submit</button>
                        <h2 class="text-center line_on_sides"><span>or</span></h2>
                        <h2 class="text-center uniq_links"><a href="profile.php">Add surveyor</a></h2>
                    </form>
                </div>
                <div class="col-xs-12 col-sm-12 col-md-7 col-lg-7">
                    <div class="mini_div">
                        <h2 class="text-center"><span><span class="invisible_on_sm">Registered </span>Surveyors</span></h2>
                        <form class="form-inline" method="POST" action="<?php echo $current_file; ?>">
                            <div class="form-group">
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <label>Find surveyor</label>
                                    </span>
                                    <input type="text" class="form-control" name="svy_srch_term" id="svy_srch_term" />
                                    <span class="input-group-btn">    
                                        <button type="submit" class="btn btn-primary">Go</button>
                                    </span>
                                </div>
                            </div>
                        </form><br />
                        <div id="surveyors_container">
                         <?php
                            if(isset($_POST['svy_srch_term']) && !empty($_POST['svy_srch_term'])){
                                include 'surveyor_search_nonajax.php';
                            }else{
                                $surveyors = new user_data;
                                $results = $surveyors->registered_surveyors();
                                while($surveyor = $results->fetch_assoc()){
                                    if(!($surveyor['SID'] == 1 || $surveyor['SID'] == 2)){
                        ?>
                                    <form method="POST" action="<?php echo $current_file; ?>" class="form-inline surveyor_display">
                                        <label class="label_fixed_width"><?php echo no_xss_thru($surveyor['Surname']).' '.no_xss_thru($surveyor['Other names']); ?></label>
                                        <button class="comfirm_before_submission" type="submit" name="D_ID" value="<?php echo $surveyor['SID']; ?>">Delete</button>
                                        <button type="submit" name="R_ID" value="<?php echo $surveyor['SID']; ?>">Reset Membership</button>
                                    </form>
                        <?php
                                    }
                            }
                          }
                        ?>
                        </div>
                    </div>
                </div>
                <div id="reg-div" class="mini_div col-xs-12 col-sm-12 col-md-5 col-lg-5">
                    <h2 class="text-center line_on_sides"><span>Register as</span></h2>
                    <form action="<?php echo $current_file; ?>" method="POST" class="form-horizontal">
                        <div class="form-group">
                            <div class="input-group">
                                <span class="input-group-addon">User type</span>
                                <select name="user_type" class="form-control">
                                    <option>Normal Surveyor</option>
                                    <option>Secondary Admin</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <input type="text" name="surname" class="form-control" value="<?php if(isset($s_name)){echo $s_name;} ?>" placeholder="Surname (Prof/Dr/Mr/Mrs/Ms)" required/>
                        </div>
                        <div class="form-group">
                            <input type="text" name="other_name" class="form-control" value="<?php if(isset($o_name)){echo $o_name;} ?>" placeholder="Other Names (in full)" required/>
                        </div>
                        <div class="form-group">
                            <div class="input-group">
                                <span class="input-group-addon">Sex</span>
                                <select name="sex" class="form-control">
                                    <option>Male</option>
                                    <option>Female</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <input type="text" name="address" class="form-control" value="<?php if(isset($addr)){echo $addr;} ?>" placeholder="Address"/>
                        </div>
                        <div class="form-group">
                            <input type="tel" name="tel_no" class="form-control" value="<?php if(isset($tel)){echo $tel;} ?>" placeholder="Tel. no." required/>
                        </div>
                        <div class="form-group">
                            <input type="email" name="email" class="form-control" value="<?php if(isset($email)){echo $email;} ?>" placeholder="Email" required/>
                        </div>
                        <div class="form-group">
                            <input type="text" name="nationality" class="form-control" value="<?php if(isset($nat)){echo $nat;} ?>" placeholder="Nationality" required/>
                        </div>
                        <div class="form-group">
                            <input type="text" name="c_wk_place" class="form-control" value="<?php if(isset($wkplace)){echo $wkplace;} ?>" placeholder="Current Work Place" />
                        </div>
                        <div class="form-group">
                            <input type="text" name="qualific" class="form-control" value="<?php if(isset($qual)){echo $qual;} ?>" placeholder="Qualifications" required/>
                        </div>
                        <div class="form-group">
                            <input type="password" name="pass" class="form-control" placeholder="Password" required/>
                        </div>
                          <div class="form-group">
                            <input type="password" name="pass_retype" class="form-control" placeholder="Password again" required/>
                        </div>
                        <button type="submit" class="btn btn-primary center-block" style="width: 200px; margin-bottom: 20px">Submit info</button>
                    </form>
                </div>
            </div>
        </div>
        <?php $init->display_footer(); ?>
    </body>
</html>


<?php
    require "register.php";
    require "dbconnect.php";
    require "reg_and_login.php";
    include "surveyor_searcher.php";
    if(isset($_POST['current_pass'],$_POST['new_pass'],$_POST['new_pass_retype'])){
        $c_p = clean($_POST['current_pass']);
        $n_p = clean($_POST['new_pass']);
        $n_pr = clean($_POST['new_pass_retype']);
        if(empty_field($c_p,$n_p,$n_pr)){
            $feedback = feedback("You missed one or more required field(s)",'feedback_failure');
        }else{
            $email = $data->get('Email','surveyors');
            $current_pass = $data->get('Password','surveyors');
            $current_salt = $data->get('salt','surveyors');
            $surveyor_reg = new new_surveyor();
            $surveyor_reg->validate_data($email,$n_p,$n_pr);
            if($surveyor_reg->good_to_go()){
                $hash = new Hash();
                $new_salt = $hash->makeSalt(33);
                if($hash->enc_pwd($c_p,$current_salt) == $current_pass){
                    if($surveyor_reg->update_field('Password','surveyors',$hash->enc_pwd($n_p,$new_salt), $email) && $surveyor_reg->update_field('salt','surveyors',$new_salt, $email)){
                        $feedback = feedback('Admin password changed successfully','feedback_success');
                    }
                }
            }else{
                $feedback = feedback('Your passwords either don\'t match or are weak','feedback_info');
            }
        }
    }
    
    if(isset($_POST['R_ID'])){
        $sid = clean($_POST['R_ID']);
        $name = no_xss_thru($data->get('Surname','surveyors',Null,$sid));
        if($data->update('Membership','surveyors',time(),$sid)){
            $feedback = feedback($name."'s membership reset successfully",'feedback_success');
        }
    }
    
    if(isset($_POST['approved']) || isset($_POST['rejected'])){
        if(isset($_POST['approved'])){
            $to_workon = clean($_POST['approved']);
            $value = 1;
        }elseif(isset($_POST['rejected'])){
            $to_workon = clean($_POST['rejected']);
            $value = 2;
        }
        if($data->approve_or_reject($to_workon,$value)){
            $feedback = feedback('Approval or rejection successfull', 'feedback_success');
        }else{
             $feedback = feedback('Action failed', 'feedback_failure');
        }
    }
    
    if(isset($_SESSION['reply'])){
        $feedback = feedback($_SESSION['reply'],'feedback_info');
    }
     
    $title = "Sec admin";
    $keywords = "Administrator,surveyor";
    $description = "Handles secondary stuff";
    $init = new initializer($title,$keywords,$description);
?>
<!DOCTYPE html>
<html>
    <?php $init->head("../"); ?>
    <body>
        <h1 class="text-center">PROFESSIONAL SURVEYORS UGANDA</h1>
        <div class="row top_nav">
            <nav class="nav navbar-default navbar-inverse"  id="reg_nav" role=""navigation>
                <div class="navbar-header col-xs-7">
                    <span class="navbar-brand"><big><a class="ln_const_size" href="profile.php">Sec Admin</a></big></span>
                </div>
                <div class="ul_div col-xs-5">
                    <ul class="nav navbar-nav navbar-right">
                        <li><a href="../index.php">Go home</a></li>
                        <li class="active"><a href="profile.php">Execute duties</a></li>
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
                    <li class="active"><a href="profile.php">Execute duties</a></li>
                    <li><a href="#" class="chng_pass">Change password</a></li>
                    <li><a href="logout.php">Logout</a></li>
            </ul>
        </div>
        <div class="container">
         <div id="prompt_feedback"></div>
         <?php
            if(isset($_POST['V_P'])){
               $data = new user_data();
               $sid = clean($_POST['V_P']);
               $s_no = no_xss_thru($data->get('Serial Number','surveyors',Null, $sid));
               $fallback = TRUE;
               $prefix = '../';
               include '../show_profile.php';
            }else{
         ?>
            <div class="row" id="admin-div">
                <?php if(isset($feedback)){ echo $feedback.'<br />';} if(isset($not_feedback)){ echo $not_feedback; } ?>
                <div id="admin_pass_changer">
                    <div class="mini_div">
                        <form method="POST" action="profile.php">
                            <h3 class="text-center">All fields are mandatory</h3>
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
                            <h2 class="text-center uniq_links"><a href="profile.php">Execute duties</a></span></h2>
                        </form>
                    </div>
                </div>
                <div id="sec_admin_dut" class="col-xs-12 col-sm-12 col-md-8 col-lg-8 col-md-offset-2 col-lg-offset-2">
                    <div class="mini_div">
                        <h2 class="line_on_sides"><span><span class="invisible_on_sm">Registered </span>Surveyors</span></h2>
                        <form class="form-inline" method="POST" action="<?php echo $current_file; ?>">
                            <div class="form-group">
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <label>Find surveyor</label>
                                    </span>
                                    <input type="text" class="form-control" name="srched" id="srched" />
                                    <span class="input-group-btn">    
                                        <button type="submit" class="btn btn-primary">Go</button>
                                    </span>
                                </div>
                            </div>
                        </form><br />
                        <div id="surveyors_container">
                         <?php
                            if(isset($_POST['V_W'])){
                                include 'file_fns.php';
                                $data = new user_data;
                                $files = new file_fns;
                                $id_and_snum = explode(' ',$_POST['V_W']);
                                $svy_id = $id_and_snum[0];
                                $svy_serial = $id_and_snum[1];
                                $name = no_xss_thru($data->get('Surname','surveyors',$svy_serial,$svy_id)).' '.no_xss_thru($data->get('Other names','surveyors',$svy_serial,$svy_id));
                                $files->display_surveyor_work($svy_id,$name);
                            }elseif(isset($_POST['srched']) && !empty($_POST['srched'])){
                                $admin = 'sec_admin';
                                include 'surveyor_search_nonajax.php';
                            }else{
                                $surveyors = new user_data;
                                $results = $surveyors->registered_surveyors();
                                while($surveyor = $results->fetch_assoc()){
                                    if(!($surveyor['SID'] == 1 || $surveyor['User type'] == 'Secondary Admin')){
                        ?>
                                    <form method="POST" action="<?php echo $current_file; ?>" class="form-inline surveyor_display">
                                        <div class="form-control">
                                            <label class="label_fixed_width"><?php echo no_xss_thru($surveyor['Surname']).' '.no_xss_thru($surveyor['Other names']); ?></label>
                                        </div>
                                        <button type="submit" name="V_P" value="<?php echo $surveyor['SID']; ?>">View Profile</button>
                                        <button type="submit" name="V_W" value="<?php echo $surveyor['SID'].' '.$surveyor['Serial Number']; ?>">View Work</button>
                                    </form>
                        <?php
                            }
                           }
                          }
                        ?>
                        </div>
                    </div>
                </div>
            </div>
         <?php } ?>
        </div>
        <?php $init->display_footer(); ?>
    </body>
</html>
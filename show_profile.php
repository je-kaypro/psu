<?php 
    if(isset($fallback)){
        $add = '../';
    }else{
        $add = '';
    }
    if(isset($prefix)){
        $addon = $prefix;
    }else{
        $addon = '';
    }
    if(!isset($fallback)){
?>
<div class="row top_nav">
    <nav class="nav navbar-default navbar-inverse" role=""navigation id="ind_nav">
        <div class="navbar-header col-xs-7">
            <span class="navbar-brand">Member Profile</span>
        </div>
        <div class="ul_div col-xs-5">
            <ul class="nav navbar-nav navbar-right">
                  <li><a href="<?php echo $add; ?>sources/profile.php">Sign in</a></li>
                  <li><a href="<?php echo $add; ?>index.php">Go home</a></li>
                  <li class="dropdown menu_ln">
                    <a class="dropdown-toggle pull-right" data-toggle="dropdown" href="#">
                        &#9776Menu</span>
                    </a>
                </li>
            </ul>
        </div>
    </nav>
    <ul class="text-center menu_from_left menu_side">
        <li><a href="sources/profile.php">Sign in</a></li>
        <li><a href="index.php">Go home</a></li>
    </ul>
</div>
<?php
    }
?>
<div class="row middle">
    <div class="col-xs-12 col-sm-5 col-md-5 col-lg-5">
        <div id="pp_label"><h4>Profile Picture</h4></div>
        <img src="<?php echo $addon. no_xss_thru(substr($data->get('Profile Pic','images',$s_no,$sid),3)); ?>" alt="Profile pic">
        <h4>QR Code</h4>
        <img src="<?php echo $addon; ?>imgz/<?php echo no_xss_thru($data->get('Serial Number','surveyors',$s_no,$sid)).'.png'; ?>" alt="QR Code">
        <h4>Institution ID</h4>
    </div>
    <div class="col-xs-12 col-sm-7 col-md-7 col-lg-7 mini_div" id="profile_display_div">
        <?php
            $fields = array('Serial Number','Surname','Other names','Sex','Address','Tel','Email','Work place','Nationality','Membership');
            foreach ($fields as $field){
                   if($field != "Membership"){
                        echo '<div class="info_portion"><label class="">'. no_xss_thru($field).':</label>'.no_xss_thru($data->get($field, 'surveyors',$s_no)).'</div>';
                   }else{
                        echo '<div class="info_portion"><label class="">'. no_xss_thru($field).':</label>'. no_xss_thru(round(((365*24*60*60) - (time() - $data->get($field, 'surveyors',$s_no)))/(24*60*60),0)).' days</div>';
                   } 
            }
        ?>
    </div>
</div>
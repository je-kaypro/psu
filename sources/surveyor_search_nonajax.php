<?php
  @include_once 'dbconnect.php';
  include_once 'register.php';
  if (isset($_POST['search_term']) && !empty($_POST['search_term'])) {
    $search = clean($_POST['search_term']);
  }
    
  if (isset($_POST['svy_srch_term']) && !empty($_POST['svy_srch_term'])) {
    $search = clean($_POST['svy_srch_term']);
  }
  if (isset($_POST['srched']) && !empty($_POST['srched'])) {
    $search = clean($_POST['srched']);
  }
   if(isset($search)){
      $search = '%'.$search.'%';
      $query = "SELECT `SID`,`User type`,`Surname`,`Other names`,`Serial Number` FROM `surveyors` WHERE CONCAT(`Surname`,' ',`Other names`) LIKE ? OR CONCAT(`Other names`,' ',`Surname`) LIKE ?";
      if(isset($admin)){
          $query = "SELECT `SID`,`User type`,`Surname`,`Other names`,`Serial Number` FROM `surveyors` WHERE CONCAT(`Surname`,' ',`Other names`) LIKE ? OR CONCAT(`Other names`,' ',`Surname`) LIKE ?";
      }
      $myquery = $db->prepare($query);
      $myquery->bind_param('ss',$search,$search);
      if($myquery->execute()){
        if(isset($_POST['search_term']) || isset($_POST['svy_srch_term']) || isset($_POST['srched'])){
           $myquery->bind_result($id,$user_type,$sname,$oname,$snum);
        }
        if(isset($_POST['search_term'])) {
          while ($myquery->fetch()) {
            $svy_name = $sname.' '.$oname;
            $s_no = $snum;
            $sid = $id;
            if(!($sid == 1 || $user_type == 'Secondary Admin')){
?>
    <div class="search_results_portion">
        <?php echo '<a href="index.php?s_no='.$s_no.'">'.$svy_name.'</a>'; ?>
    </div>
<?php
            }
           }
         }
        if(isset($_POST['svy_srch_term'])) {
          while ($myquery->fetch()) {
            $svy_name = $sname.' '.$oname;
            $s_no = $snum;
            $sid = $id;
            if(!($sid == 1 || $user_type == 'Secondary Admin')){
?>
            <form method="POST" action="profile.php" class="form-inline surveyor_display">
                <div class="form-control">
                    <label class="label_fixed_width"><?php echo $svy_name; ?></label>
                </div>
                <button class="comfirm_before_submission" type="submit" name="D_ID" value="<?php echo $sid; ?>">Delete</button>
                <button type="submit" name="R_ID" value="<?php echo $sid; ?>">Reset Membership</button>
            </form>
<?php
            }
          }
        }
        if(isset($_POST['srched'])) {
           while ($myquery->fetch()) {
            $svy_name = $sname.' '.$oname;
            $s_no = $snum;
            $sid = $id;
            if(!($sid == 1 || $user_type == 'Secondary Admin')){
?>
            <form method="POST" action="profile.php" class="form-inline surveyor_display">
                <div class="form-control">
                    <label class="label_fixed_width"><?php echo $svy_name; ?></label>
                </div>
                <button type="submit" name="V_P" value="<?php echo $sid; ?>">View Profile</button>
                <button type="submit" name="V_W" value="<?php echo $sid.' '.$s_no; ?>">View Work</button>
            </form>
<?php
        }
       }
      }
    }
  }
?>

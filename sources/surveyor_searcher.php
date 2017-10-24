<?php
  @include_once '../inc_in_all.php';
  @include_once 'dbconnect.php';
  include_once 'register.php';
  if (isset($_POST['search']) && !empty($_POST['search'])) {
    $search = clean($_POST['search']);
  }

  if(isset($_POST['srch_term']) && !empty($_POST['srch_term'])){
     $search = clean($_POST['srch_term']);
  }

  if(isset($_POST['admin_srch']) && !empty($_POST['admin_srch'])){
     $search = clean($_POST['admin_srch']);
  }
  
  if(isset($search)){
      $search = '%'.$search.'%';
      $query = "SELECT `SID`,`User type`,`Surname`,`Other names`,`Serial Number` FROM `surveyors` WHERE CONCAT(`Surname`,' ',`Other names`) LIKE ? OR CONCAT(`Other names`,' ',`Surname`) LIKE ?";
      $myquery = $db->prepare($query);
      $myquery->bind_param('ss',$search,$search);
      if($myquery->execute()){
        $myquery->bind_result($id,$user_type,$sname,$oname,$snum);
        if(isset($_POST['search'])) {  
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
        if(isset($_POST['srch_term'])) {
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
        if(isset($_POST['admin_srch'])) {
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

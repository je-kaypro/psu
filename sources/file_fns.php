<?php
  class file_fns{
    public function generate_file_name($parts,$ext){
      return implode($parts).'.'.$ext;
    }
    
    private function data_exists($data,$table,$id,$folder_name = null){
        global $db;
        if($table === 'work'){
            $query = "SELECT `folder` FROM `$table` WHERE `SID`=".$_SESSION['svy_id']." AND `folder`='$folder_name'";
        }else{
            $query = "SELECT `$data` FROM `$table` WHERE `SID`=$id";
        }
        $query_run = $db->query($query);
        if($query_run->num_rows > 0){
            return true;
        }
    }
    
    private function upload_file_name($name,$table,$field,$sid=null,$office=null){
       global $db;
       $date = date('d-m-Y');
       if(is_null($sid)){
         $sid = $_SESSION['svy_id'];
         if($table == 'work' && !$this->data_exists('folder','work',$sid,$name)){
            $query = 'INSERT INTO `work` VALUES("","'.$sid.'","'.$name.'","'.$office.'",0,"","'.$date.'")';
         }else{
            $query = "UPDATE `$table` SET `$field`='".$name."' WHERE `SID`=".$sid." AND `folder`='$name'";  
         }
       }else{
         $query = "UPDATE `$table` SET `$field`='".$name."' WHERE `SID`=".$sid;  
       }
       
       if($db->query($query)){
               return true; 
       }else{
           echo $db->error;
       }
    }
    
    public function file_uploaded($filepath,$location,$parts_array,$ext,$table,$field,$sid=null,$office=null){
        global $db;
        if (is_uploaded_file($filepath['tmp_name'])){
          $filename = $this->generate_file_name($parts_array,$ext);
          $tmp_name = $filepath['tmp_name'];
          if (move_uploaded_file($tmp_name,$location.$filename)) {
            if(!($location === '../imgz/')){
                if($this->upload_file_name($location.$filename,$table,$field,$sid,$office)){
                    return true;
                }
            }else{
                return true;
            }
          }
        }
    }

    public function upload_profile_pic($profile_pic){
      $pic_name = $profile_pic['name'];
      $name_pts = explode('.',$pic_name);
      $ext = end($name_pts);
      $sid = $_SESSION['svy_id'];
      $name_parts = array('profile_pic',$sid);
      $uploaded_image = $this->file_uploaded($profile_pic,'../imgz/',$name_parts,$ext,'images','Profile Pic');
      if ($uploaded_image){
        $data = new user_data;
        if($data->update('Profile Pic','images','../imgz/profile_pic'.$sid.'.'.$ext)){
            return true;
        }
      }
    }
    public function get_file($file,$table,$pid=null){
            //Retrieves a file location from any table in the database jackdb
            if (is_null($pid)) {
                    $id = $_SESSION['sch_id'];
            }else{
                    $id=$pid;
            }
            global $db;
            $query = "SELECT `$file` FROM `$table`  WHERE `sch_id`=".$id;
            try {
                    if(!$myquery = $db->query($query)){
                            throw new Exception('File not available',1);
                    }else{
                            return $myquery;
                    }
            } catch (Exception $e) {
                    return $e->getMessage();
            }
    }

    public function deleted($sid,$s_no,$pp){
        global $db;
        $query1 = "DELETE FROM `surveyors` WHERE `SID`=$sid";
        $query2 = "DELETE FROM `images` WHERE `SID`=$sid";
          if($db->query($query1) && $db->query($query2)){
                if(@unlink($pp) && @unlink('../imgz/'.$s_no.'.png')){
                  return true;
                }else{
                  return false;
                }
          }
    }
    
    public function zip_file_handled($file,$ownerz_id,$office,$work_name){
      $parts_array = array($work_name);
      if($this->file_uploaded($file,'../work/', $parts_array,'zip','work','folder',null,$office)){
         $up_zip = '../work/'.$this->generate_file_name($parts_array,'zip');
         $zip = new ZipArchive();
         $result = $zip->open($up_zip);
         if($result){
             $zip->extractTo(substr($up_zip,0, strlen($up_zip)-3));
             $zip->close();
             unlink($up_zip);
             return true;
         }
      }
    }
    
    public function display_surveyor_work($svy_id,$name=null){
        global $db;
        $query = "SELECT `work_id`,`folder`,`office`,`approved`,`approved_by`,`date uploaded` FROM `work` WHERE `SID`=$svy_id";
        $query_run = $db->prepare($query);
        if($query_run->execute()){
            $query_run->bind_result($work_id,$folder,$office,$approved,$approved_by, $date);
            $query_run->store_result();
            if(!is_null($name)){
                echo '<h2 class="blue">Work submitted by '.no_xss_thru($name).'</h2>';
            }else{
                echo '<h2 class="blue">Your work</h2>';
            }
            if($query_run->num_rows == 0){
                if(!is_null($name)){
                    echo 'No work for this surveyor';
                }else{
                    echo 'If you submit any work, you will see it here';
                }
                return;
            }
          
            while($f = $query_run->fetch()){
                 $folder = substr($folder,0,-4);
                 if(is_dir($folder)){
                    if($d_handle = opendir($folder)){
 ?>
            <div class="info_portion">
<?php
                echo "<h3>".substr($folder,8).", Uploaded on $date</h3><hr>";
                while($file = readdir($d_handle)){
                    if(!($file == '.' || $file == '..')){
                        echo '<div class="uniq_links">><a href="'.$folder.'/'.$file.'" target="_blank">'.$file.'</a></div><br />';
                    }
                }
                if($approved == 0){
                    global $u_type;
                    if($u_type == 'Secondary Admin'){
?>
                    <form action="profile.php" method="POST">
                        <button type="submit" class="btn btn-primary wider_but" name="approved" value="<?php echo $work_id; ?>">Approve</button>
                        <button type="submit" class="btn btn-danger wider_but" name="rejected" value="<?php echo $work_id; ?>">Reject</button>
                    </form>
<?php
                    }else{
                        echo '<h2 class="text-warning">Approval pending</h2>';
                    }
                }elseif($approved == 1){
?>
                <div class="approved">
                    &check; Approved by <?php echo no_xss_thru($approved_by).', '.no_xss_thru($office); ?>
                </div>
<?php
                }elseif($approved == 2){
?>
                <div class="rejected">
                    &cross; Rejected by <?php echo no_xss_thru($approved_by).', '.no_xss_thru($office); ?>
                </div>
<?php
                }
?>
            </div>
<?php
                    }
                 }
            }
         }
    }
  }
?>

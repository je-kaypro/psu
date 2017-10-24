<?php
	include 'dbconnect.php';
	$current_file = $_SERVER['SCRIPT_NAME'];
	class user_data{
		//functions for retrieving, inserting and updating single values in jackdb tables
		public function get($info,$table,$sno=null,$sid=null){
			//Retrieves a piece of information from any table in the database jackdb
			if (is_null($sno)) {
                            $id = $_SESSION['svy_id'];
                            $query = "SELECT `$info` FROM `$table`  WHERE `SID`=".$id;
			}else{
                            $s_no=$sno;
                            $query = "SELECT `$info` FROM `$table`  WHERE `Serial Number`='".$s_no."'";
			}
                        if(!is_null($sid)){
                            $query = "SELECT `$info` FROM `$table`  WHERE `SID`='".$sid."'";
                        }
			global $db;
			try {
                            $myquery = $db->prepare($query);
                            if(!$myquery->execute()){
                                throw new Exception('Data not available',1);
                            }else{
                                $myquery->bind_result($info);
                                $myquery->fetch();
                                return $info;
                            }
			} catch (Exception $e) {
				return $e->getMessage();
			}
		}
                
                public function registered_surveyors(){
                    global $db;
                    $query = "SELECT `SID`,`User type`,`Serial Number`,`Surname`,`Other names` FROM `surveyors`";
                    if($myquery = $db->query($query)){
                        return $myquery = $db->query($query);
                    }
                }

		public function insert($info,$table,$uid=null){
			global $db;
			if ($uid == null) {
				$sch_id = $_SESSION['sch_id'];
			}else{
				$sch_id = $uid;
			}
			$query = "NSERT INTO `$table` VALUES(?, ?, ?)";
                        $myquery = $db->prepare($query);
                        $id = '';
                        $myquery->bind_param('iis',$id,$sch_id,$info);
			if ($myquery->execute()) {
                                $myquery->close();
				return true;
			}else{
				return $db->error;
			}
		}

		public function update($info,$table,$value,$id=Null){
			//Updates a piece of information in any given table in the database jackdb
			global $db;
                        if(is_null($id)){
                            $query = "UPDATE `$table` SET `$info`=? WHERE `SID`=".$_SESSION['svy_id'];
                        }else{
                           $query = "UPDATE `$table` SET `$info`=? WHERE `SID`=".$id; 
                        }
                        $myquery = $db->prepare($query);
			$myquery->bind_param('s',$value);
			if($myquery->execute()){
				$myquery->close();
				return true;
			}
		}
                
                public function delete_surveyor($sid){
                    global $db;
                    if($db->query($query1) && $db->query($query2)){
                        return true;
                    }
                }
                public function display_surveyor_offices(){
                    global $db;
                    $query = "SELECT `Work place` FROM `surveyors` WHERE `User type`='Secondary Admin'";
                    $myquery = $db->prepare($query);
                    if($myquery->execute()){
                        $listed = array();
                        $myquery->bind_result($work);
                        while($myquery->fetch()){
                            if(!array_search($work,$listed)){
                               array_push($listed,$work); 
                            }
                        }
                        foreach($listed as $office){
                            echo '<option>'.no_xss_thru($office).'</option>';
                        }
                        return;
                    }
               }
               
               public function approve_or_reject($to_workon,$val){
                   //This function approves or rejects work
                   global $db;
                   $admin = $this->get('Surname','surveyors').' '.$this->get('Other names', 'surveyors');
                   $query = "UPDATE `work` SET `approved`=?,`approved_by`=? WHERE `work_id`=?";
                   $myquery = $db->prepare($query);
                   $myquery->bind_param('isi',$val,$admin,$to_workon);
                   if($myquery->execute()){
                       $myquery->close();
                       return true;
                   }
               }
	}
?>

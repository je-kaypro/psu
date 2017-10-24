<?php
    function empty_field(){
        $fields = func_get_args();
        foreach($fields as $f){
            if(empty($f)){
                return true;
            }
        }
        return false;
    }
    
    function  data_exists($data,$table,$uniq_field,$uniq_data){
        global $db;
        $query = "SELECT `$data` FROM `$table` WHERE `$uniq_field`=?";
        $myquery = $db->prepare($query);
        $myquery->bind_param('s',$uniq_data);
        if($myquery->execute()){
          $myquery->bind_result($info);
          $myquery->fetch();
          return $info;  
        }
    }
    class reg_errors{
            var $mail_error = '';
            var $pwd_error = '';
            var $pwd_match_error = '';

            function __set($var, $val){
                    return $this->$var = $val;
            }

            function __get($var){
                    return $this->$var;
            }

    }

    class datachecks extends reg_errors{
            private function validlen($str,$minlen=0,$maxlen=500){
                    //returns true if a given string lies between a given minimum and a given maximum
                    $len = strlen($str);
                    if ($len>=$minlen && $len<=$maxlen  ) {
                            return true;
                    }else{
                            return false;
                    }
            }

            public function validpwd($pwd){
                    //Returns true if a password contains both numbers and alphabetical characters
                    if($this->validlen($pwd,8)){
                            if (preg_match('/[a-zA-Z0-9]+[@#-_]*/',$pwd)) {
                                    return true;
                            }
                    }
            }

            public function pwdmatch($pwd1,$pwd2){
                    //returns true if password 1 matches its retype (password 2)
                    if ($pwd1 === $pwd2) {
                            return True;
                    }
            }
            public function validmail($email){
                    //Checks for the validity of an email
                    if (filter_var($email,FILTER_VALIDATE_EMAIL)){
                            return true;
                    }
            }
    }

    class new_surveyor extends datachecks{
            public function validate_data($email,$pass,$pass_retype){
                    if (!$this->validmail($email)) {
                            $this->mail_error = "Invalid mail";
                    }
                    if (!$this->validpwd($pass)) {
                            $this->pwd_error = "Weak password";
                    }
                    if (!$this->pwdmatch($pass,$pass_retype)) {
                            $this->pwd_match_error = "Passwords don't match";
                    }
            }

            public $errors_array;
            public function good_to_go(){
                    //checks whether user data for registration has registered any errors.
                    //Returns true if no errors are registered, false otherwise.
                    global $errors_array;
                    $errors_array = array($this->mail_error,$this->pwd_error,$this->pwd_match_error);
                    foreach ($errors_array as $error) {
                            if ($error) {
                                    return false;
                            }
                    }
                    return true;
            }

            public function exists($table,$email){
                    global $db;
                    $query = "SELECT `Email` FROM `$table`  WHERE `Email`=?";
                    $myquery = $db->prepare($query);
                    $myquery->bind_param('s',$email);
                    try{
                            if(!$myquery->execute()) {
                                    throw new Exception('Failed to retrieve data');
                            }else{
                                $myquery->bind_result($Mail);
                                    $myquery->fetch();
                                    if($Mail == $email){
                                            return true;
                                    }
                                $myquery->close();
                            }
                    }catch(Exception $exc){
                            $msg = $exc->getMessage();
                            return false;
                    }
            }

            public function registered(){
                    //Using prepared statements to protect against SQL injection.
                    global $db;
                    $fields = func_get_args();
                    $query = "INSERT INTO `surveyors` VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                    $myquery = $db->prepare($query);
                    $sid = '';
                    $reset_c = 0;
                    $mem = time();
                    $myquery->bind_param('isssssssssssssii',$sid,$fields[0],$fields[1],$fields[2],$fields[3],$fields[4],$fields[5],$fields[6],$fields[7],$fields[8],$fields[9],$fields[10],$fields[11],$fields[12],$reset_c,$mem);
                    if ($myquery->execute()) {
                            $myquery->close();
                            return true;
                    }
            }
            public function initialize_images($sid){
                global $db;
                $query = "INSERT INTO `images` VALUES(?, ?)";
                $f = '';
                $myquery = $db->prepare($query);
                $myquery->bind_param('is',$sid,$f);
                if($myquery->execute()){
                    $myquery->close();
                    return true;
                }else{
                    echo $db->error;
                }
            }

            public function update_data($addr,$tel,$wp){
                    global $db;
                    $query = "UPDATE `surveyors` SET `Address`=?,`Tel`=?,`Work place`=? WHERE `SID`=".$_SESSION['svy_id'];
                    $myquery = $db->prepare($query);
                    $myquery->bind_param('sss',$addr,$tel,$wp);
                    if($myquery->execute()){
                            $myquery->close();
                            return true;
                    }
            }
            public function update_field($field,$table,$data,$email){
                    global $db;
                    $query = "UPDATE `$table` SET `$field`=? WHERE `Email`=?";
                    $myquery = $db->prepare($query);
                    echo $db->error;
                    $myquery->bind_param('ss',$data,$email);
                    if($myquery->execute()){
                            $myquery->close();
                            return true;
                    }
            }
    }
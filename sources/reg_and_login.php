<?php
class Hash{
    //salt improves security by generating a random number of characters we append on to our hashed password
    public static function enc_pwd($string, $salt = ''){
        return hash('md5', $string.$salt);
    }

    public static function makeSalt($length){
        return mcrypt_create_iv($length);
    }
}

function serial_no(){
    $reg_time = time();
    $rand_no = rand(1000,1000000);
    $full_s = $reg_time + $rand_no;
    $hash = strtoupper(sha1($full_s));
    $part = substr($hash,9,14);
    QRcode::png("https://jacksonkamya48.000webhostapp.com/index.php?s_no=$part","../imgz/$part".".png", "L", 4, 4);
    return $part;
}
  
class notifications{
    public function send_mail($email,$msg,$subject){
        $headers = "From: surveyors@gmail.com";
        if(mail($email, $subject, $msg, $headers)){
            return true;
        }
    }
    
    public function send_sms($tel,$msg){
        include 'mail_and_sms.php';
        if(SendSMS("127.0.0.1", 8800,'ISU','nicho2017', $tel, $msg)){
            return true;
        }
    }
}
class first_staff extends Hash{
    private function fetch_salt($email){
        global $db;
        $query = "SELECT `salt` FROM `surveyors` WHERE `Email`=?";
        $myquery = $db->prepare($query);
        $myquery->bind_param('s',$email);
        if($myquery->execute()){
            $myquery->bind_result($salt);
            $myquery->fetch();
            $myquery->close();
            return $salt;
        }
    }
    public function loggedin($email,$pass){
      global $db;
      $query = "SELECT `SID`,`Serial Number` FROM `surveyors` WHERE `Email`=? AND `Password`=?";
      $myquery = $db->prepare($query);
      $salt = $this->fetch_salt($email);
      $pass = $this->enc_pwd($pass,$salt);
      $myquery->bind_param('ss',$email,$pass);
      if($myquery->execute()){
          $myquery->bind_result($id,$sno);
          $myquery->store_result();
          if($myquery->num_rows == 1){
              $res_array = [];
              while($myquery->fetch()){
                  array_push($res_array,$id);
                  array_push($res_array, $sno);
              }
              $myquery->close();
              return $res_array;
          }
      }
    }
}
?>

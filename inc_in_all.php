<?php
    ob_start();
    session_start();
    error_reporting(0);
    $current_file = $_SERVER['SCRIPT_NAME'];
    function feedback($body,$class){
        return '<div class="'.$class.'">'.$body.'</div>';
    }
    
    function clean($str){
        return addslashes(trim($str));
    }
    
    function no_xss_thru($str){
        return htmlentities($str);
    }
    class initializer{
        private $title;
        private $keywords;
        private $description;
        public function __construct($t,$keyws,$descr){
            $this->title = $t;
            $this->keywords = $keyws;
            $this->description = $descr;
            
        }
        
        public function head($root=""){
?>      
            <head>
                <title><?php echo $this->title; ?></title>
                <script type="text/javascript" language="javascript" src="<?php echo $root; ?>jquery/jquery-3.1.1.js"></script>
                <script type="text/javascript" language="javascript" src="<?php echo $root; ?>jquery/jquery-ui.js"></script>
                <script type="text/javascript" language="javascript" src="<?php echo $root; ?>customjs/custom.js"></script>
                <script type="text/javascript" src="<?php echo $root; ?>bootstrap-3.3.7-dist/js/bootstrap.js"></script>
                <link rel="stylesheet" type="text/css" href="<?php echo $root; ?>bootstrap-3.3.7-dist/css/bootstrap.css">
                <link rel="stylesheet" href="<?php echo $root; ?>custom_css/custom.css">
                <meta name="viewport" content="width=device-width; initial-scale=1.0">
                <meta charset="utf-8">
                <meta name="keywords" content="<?php echo $this->keywords; ?>">
                <meta name="description" content="<?php echo $this->description; ?>">
                <meta name="author" content="je-kaypro">
                <meta name=“robots” content= “noindex,nofollow”>
                <link rel="icon" href="images/icon.jpg">
            </head>
<?php
            return;
        }
        
        public function display_footer(){
 ?>
            <div class="row" id="footer">
                <nav class="nav navbar-default" role=""navigation>
                    <div class="navbar-header">
                        <span class="navbar-brand text-center">Surveyors <?php echo date("Y"); ?></span>
                    </div>
                </nav>
            </div>
 <?php
            return;
        }
    }
?>
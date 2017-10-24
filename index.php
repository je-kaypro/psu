<?php
    include "inc_in_all.php";
    include "sources/surveyor_searcher.php";
    include 'sources/profile_inc.php';
    $data = new user_data();
    include 'sources/dbconnect.php';
    $title = "INSTITUTION OF SURVEYORS UGANDA";
    $keywords = "surveying,surveyors,land surveying,QR Code";
    $description = "We help to reduce non-qualified land surveyors in the field of land surveying by providng means of authenthication using QR Code";
    $init = new initializer($title,$keywords,$description);
?>
<!DOCTYPE html>
<html>
    <?php $init->head(); ?>
    <body id="index">
        <div class="container">
            <div class="row head-text"><h1 class="text-center">PROFESSIONAL SURVEYORS UGANDA</h1></div>
            <?php
                if(isset($_GET['s_no'])){
                    $s_no = clean($_GET['s_no']);
                    $sid = no_xss_thru($data->get('SID','surveyors',$s_no));
                    include 'show_profile.php';
                }else{
             ?>
            <div class="row" id="index_mid">
                <div class="col-xs-12 col-sm-12 col-md-8 col-md-offset-2 col-lg-8 col-lg-offset-2 no-margin-bot">
                    <h2 class="text-center">Find surveyor(s)</h2>
                    <form id="search_form" action="<?php echo $current_file; ?>" method="POST" class="form-horizontal">                       
                        <div class="input-group">
                            <input type="text" id="search_term" name="search_term" placeholder="Name or location" class="form-control" />
                            <span class="input-group-btn">
                                <button type="submit" class="btn btn-primary">Go</button>
                            </span>
                        </div>
                    </form>
                </div>
                <div id="search_results_container" class="col-xs-12 col-sm-12 col-md-8 col-md-offset-2 col-lg-8 col-lg-offset-2">
                    <?php include 'sources/surveyor_search_nonajax.php'; ?>
                </div>
                <div class="col-xs-12 col-sm-12 col-md-8 col-md-offset-2 col-lg-8 col-lg-offset-2">
                    <h2 class="line_on_sides"><span class="text-center">or</span></h2>
                    <h2 class="text-center uniq_links"><a href="sources/profile.php">Login</a> as a member</h2>
                </div>
            </div>
            <?php } ?>
        </div>
        <nav class="nav navbar-default navbar-fixed-bottom" role=""navigation>
            <div class="navbar-header">
                <span class="navbar-brand text-center">Surveyors <?php echo date("Y"); ?></span>
            </div>
        </nav>
    </body>
</html>

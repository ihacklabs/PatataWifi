<?
include_once dirname(__FILE__)."/../../../config/config.php";
include_once dirname(__FILE__)."/../_info_.php";

require_once WWWPATH."/includes/login_check.php";
require_once WWWPATH."/includes/filter_getpost.php";
include_once WWWPATH."/includes/functions.php";

include "options_config.php";

if(isset($_GET['service']) and $_GET['service'] == "responder" and isset($_GET['action'])) {

    if ($_GET['action'] == "start") {

        // CREATE LOG FILE
        if (!file_exists($mod_logs)) {
            touch($mod_logs);
            chmod($mod_logs, 0666);
        }

        // COPY LOG
        if (file_exists($mod_logs) and 0 < filesize($mod_logs)) {
            exec_fruitywifi(BIN_CP." $mod_logs $mod_logs_history/".gmdate("Ymd-H-i-s").".log");
            exec_fruitywifi(BIN_ECHO." -n > $mod_logs");
        }

        // ADD selected options
        $options = "";
        foreach ($opt_responder as $key=>$option) {
             if ($option[0] == "1") {
                $options .= " -" . $key . " " . $option[2];
            }
        }

        // CHECK ROUTE
        $ifRouteOn = exec(BIN_ROUTE."|grep default");
        if ($ifRouteOn == "") {
            exec_fruitywifi(BIN_ROUTE." add default gw $io_in_ip");
        }

        exec_fruitywifi("cd Responder-master; ./Responder.py -i $io_in_ip -b On -r On -I $io_action > /dev/null 2 &");

    } elseif($_GET['action'] == "stop") {
        // STOP MODULE
        exec_fruitywifi(BIN_KILLALL." Responder.py");

        // COPY LOG
        if (file_exists($mod_logs) and 0 < filesize($mod_logs)) {
            exec_fruitywifi(BIN_CP." $mod_logs $mod_logs_history/".gmdate("Ymd-H-i-s").".log");
            exec_fruitywifi(BIN_ECHO." -n > $mod_logs");
        }

    }

}

if (isset($_GET['install']) and $_GET['install'] == "install_$mod_name") {

    if(!is_dir($mod_logs_history)) {
        exec_fruitywifi(BIN_MKDIR." -p $mod_logs_history");
        exec_fruitywifi(BIN_CHOWN." fruitywifi:fruitywifi $mod_logs_history");
    }

    exec_fruitywifi(BIN_CHMOD." 755 install.sh");
    exec_fruitywifi("./install.sh > ".LOGPATH."/install.txt &");

    header('Location: '.WEBPATH.'/modules/install.php?module='.$mod_name);
    exit;
}

if (isset($_GET['page']) and $_GET['page'] == "status") {
    header('Location: '.WEBPATH.'/action.php');
} else {
    header('Location: '.WEBPATH.'/modules/action.php?page='.$mod_name);
}

?>
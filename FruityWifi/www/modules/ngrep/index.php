<?
include_once dirname(__FILE__)."/../../config/config.php";
include_once dirname(__FILE__)."/_info_.php";

require_once WWWPATH."/includes/login_check.php";
require_once WWWPATH."/includes/filter_getpost.php";
include_once WWWPATH."/includes/functions.php";

include "includes/options_config.php";

?>
<!DOCTYPE HTML>
<html lang="en">
<head>
<meta charset="utf-8" />
<title>FruityWifi</title>
<script src="<?=WEBPATH?>/js/jquery.js"></script>
<script src="<?=WEBPATH?>/js/jquery-ui.js"></script>
<link rel="stylesheet" href="<?=WEBPATH?>/css/jquery-ui.css" />
<link rel="stylesheet" href="<?=WEBPATH?>/css/style.css" />

<script>
$(function() {
    $( "#action" ).tabs();
    $( "#result" ).tabs();
});

</script>

</head>
<body>

<? include WWWPATH."/includes/menu.php"; ?>
<br/>
<?

$logfile = @$_GET['logfile'];
$action = @$_GET['action'];
$tempname = @$_GET['tempname'];
$service = @$_POST['service'];

// DELETE LOG
if ($logfile != "" and $action=="delete") {
    if(file_exists($mod_logs_history.$logfile.".log")) {
        if(unlink($mod_logs_history.$logfile.".log")) {
            echo "Eliminado ".$mod_logs_history.$logfile.".log<br/>";
        } else {
            echo "Error al eliminar ".$mod_logs_history.$logfile.".log<br/>";
        }
    }
}

// SET MODE
if (isset($_POST['change_mode']) and $_POST['change_mode'] == "1") {
    $ss_mode = $service;
    exec_fruitywifi(BIN_SED." -i 's/ss_mode.*/ss_mode = \\\"".$ss_mode."\\\";/g' includes/options_config.php");
}



?>

<div class="rounded-top" align="left"> &nbsp; <b>ngrep</b> </div>
<div class="rounded-bottom">
  <form name="ss_mode" style="margin=0px" action="index.php" method="POST">

    &nbsp;&nbsp;version <?=$mod_version?><br>
    <?
    if (file_exists("/usr/bin/ngrep")) {
        echo "&nbsp;&nbsp;&nbsp; ngrep <font style='color:lime'>installed</font><br>";
    } else {
        //echo "&nbsp;&nbsp;&nbsp; ngrep <font style='color:red'>install</font><br>";
        echo "&nbsp;&nbsp;&nbsp; ngrep <a href='includes/module_action.php?install=install_ngrep' style='color:red'>install</a><br>";
    }
    ?>

    <?
    $ismoduleup = exec("ps auxww | grep ngrep | grep -v -e 'grep ngrep'");
    if ($ismoduleup != "") {
        echo "&nbsp;&nbsp;&nbsp; ngrep  <font color=\"lime\"><b>enabled</b></font>.&nbsp; | <a href=\"includes/module_action.php?service=ngrep&action=stop&page=module\"><b>stop</b></a>";
        //echo "&nbsp;&nbsp;&nbsp;&nbsp; mdk3  <font color=\"lime\"><b>enabled</b></font>.&nbsp; | <a href='#' onclick='document.ss_mode.submit();'><b>stop</b></a>&nbsp;";
        //echo "<input type='hidden' name='service' value=''>";
        echo "<input type='hidden' name='action' value='stop'>";
        echo "<input type='hidden' name='page' value='module'>";
    } else {
        echo "&nbsp;&nbsp;&nbsp; ngrep  <font color=\"red\"><b>disabled</b></font>. | <a href=\"includes/module_action.php?service=ngrep&action=start&page=module\"><b>start</b></a>";
        //echo "&nbsp;&nbsp;&nbsp;&nbsp; mdk3  <font color=\"red\"><b>disabled</b></font>. | <a href='#' onclick='document.ss_mode.submit();'><b>start</b></a>";
        //echo "<input type='hidden' name='service' value=''>";
        echo "<input type='hidden' name='action' value='start'>";
        echo "<input type='hidden' name='page' value='module'>";
    }
    ?>

    <select name="service" class="module" onchange='this.form.submit()'>
        <?
        $template_path = "$mod_path/includes/templates/";
        $templates = glob($template_path.'*');
        //print_r($templates);

        for ($i = 0; $i < count($templates); $i++) {
            $filename = str_replace($template_path,"",$templates[$i]);
            if ($filename == $ss_mode) echo "<option selected>"; else echo "<option>";
            echo "$filename";
            echo "</option>";
        }
        ?>
    </select>

    <input type="hidden" name="change_mode" value="1">
  </form>
</div>

<br>


<div id="msg" style="font-size:largest;">
Loading, please wait...
</div>

<div id="body" style="display:none;">


    <div id="result" class="module">
        <ul>
            <li><a href="#result-1">Output</a></li>
            <li><a href="#result-2">Options</a></li>
            <li><a href="#result-3">Lists</a></li>
            <li><a href="#result-4">History</a></li>
			<li><a href="#result-5">About</a></li>
        </ul>
        <div id="result-1">
            <form id="formLogs-Refresh" name="formLogs-Refresh" method="POST" autocomplete="off" action="index.php">
            <input type="submit" value="refresh">
            <br><br>
            <?
                if ($logfile != "" and $action == "view") {
                    $filename = $mod_logs_history.$logfile.".log";
                } else {
                    $filename = $mod_logs;
                }

                $data = open_file($filename);

                // REVERSE
                //$data_array = explode("\n", $data);
                //$data = implode("\n",array_reverse($data_array));

            ?>
            <textarea id="output" class="module-content" style="font-family: courier;"><?=htmlspecialchars($data)?></textarea>
            <input type="hidden" name="type" value="logs">
            </form>

        </div>

        <!-- START Deauthentication / Disassociation Amok Mode -->

        <div id="result-2">
            <form id="formInject" name="formInject" method="POST" autocomplete="off" action="includes/save.php">
            <input type="submit" value="save">
            <br><br>

            <div class="module-content" style="b-ackground-color:#000; b-order:1px dashed;">
            <table>
                <!-- // OPTION i -->
                <tr>
                    <? $opt = "i"; ?>
                    <td><input type="checkbox" name="options[]" value="<?=$opt?>" <? if ($mode_ngrep[$opt][0] == "1") echo "checked" ?> ></td>
                    <td>-<?=$opt?></td>
                    <td> is ignore case</td>
                </tr>
                <!-- // OPTION w -->
                <tr>
                    <? $opt = "w"; ?>
                    <td><input type="checkbox" name="options[]" value="<?=$opt?>" <? if ($mode_ngrep[$opt][0] == "1") echo "checked" ?> ></td>
                    <td>-<?=$opt?></td>
                    <td nowrap> is word-regex (expression must match as a word)</td>
                </tr>
                <!-- // OPTION M -->
                <tr>
                    <? $opt = "M"; ?>
                    <td><input type="checkbox" name="options[]" value="<?=$opt?>" <? if ($mode_ngrep[$opt][0] == "1") echo "checked" ?> ></td>
                    <td style="padding-right:10px">-<?=$opt?></td>
                    <td nowrap> is don&#39;t do multi-line match (do single-line match instead)</td>
                </tr>
            </table>
            </div>

            <input type="hidden" name="type" value="mode_ngrep">
            </form>
            <br>
            <?
                $filename = "$mod_path/includes/mode_d.txt";

                $data = open_file($filename);

            ?>

        </div>

        <!-- START LISTS -->

        <div id="result-3" >
            <form id="formTemplates" name="formTemplates" method="POST" autocomplete="off" action="includes/save.php">
            <input type="submit" value="save">

            <br><br>
            <?
                if ($tempname != "") {
                    $filename = "$mod_path/includes/templates/".$tempname;

                    $data = open_file($filename);

                } else {
                    $data = "";
                }

            ?>

            <input id="inject" name="newdata" type="text" class="module-content" style="font-family: courier; height:20px" value="<?=htmlspecialchars($data)?>">
            <input type="hidden" name="type" value="templates">
            <input type="hidden" name="action" value="save">
            <input type="hidden" name="tempname" value="<?=$tempname?>">
            </form>

        <br>

        <table border=0 cellspacing=0 cellpadding=0>
            <tr>
            <td class="general">
                Template
            </td>
            <td>
            <form id="formTempname" name="formTempname" method="POST" autocomplete="off" action="includes/save.php">
                <select name="tempname" onchange='this.form.submit()'>
                <option value="0">-</option>
                <?
                $template_path = "$mod_path/includes/templates/";
                $templates = glob($template_path.'*');
                //print_r($templates);

                for ($i = 0; $i < count($templates); $i++) {
                    $filename = str_replace($template_path,"",$templates[$i]);
                    if ($filename == $tempname) echo "<option selected>"; else echo "<option>";
                    echo "$filename";
                    echo "</option>";
                }
                ?>
                </select>
                <input type="hidden" name="type" value="templates">
                <input type="hidden" name="action" value="select">
            </form>
            </td>
            <tr>
            <td class="general" style="padding-right:10px">
                Add/Rename
            </td>
            <td>
            <form id="formTempname" name="formTempname" method="POST" autocomplete="off" action="includes/save.php">
                <select name="new_rename">
                <option value="0">- add template -</option>
                <?
                $template_path = "$mod_path/includes/templates/";
                $templates = glob($template_path.'*');
                //print_r($templates);

                for ($i = 0; $i < count($templates); $i++) {
                    $filename = str_replace($template_path,"",$templates[$i]);
                    echo "<option>";
                    //if ($filename == $tempname) echo "<option selected>"; else echo "<option>";
                    echo "$filename";
                    echo "</option>";
                }
                ?>

                </select>
                <input class="ui-widget" type="text" name="new_rename_file" value="" style="width:150px">
                <input type="submit" value="add/rename">

                <input type="hidden" name="type" value="templates">
                <input type="hidden" name="action" value="add_rename">

            </form>
            </td>
            </tr>

            <tr><td><br></td></tr>

            <tr>
            <td>

            </td>
            <td>
            <form id="formTempDelete" name="formTempDelete" method="POST" autocomplete="off" action="includes/save.php">
                <select name="new_rename">
                <option value="0">-</option>
                <?
                $template_path = "$mod_path/includes/templates/";
                $templates = glob($template_path.'*');
                //print_r($templates);

                for ($i = 0; $i < count($templates); $i++) {
                    //$filename = $templates[$i];
                    $filename = str_replace($template_path,"",$templates[$i]);
                    echo "<option>";
                    echo "$filename";
                    echo "</option>";
                }
                ?>

                </select>

                <input type="submit" value="delete">

                <input type="hidden" name="type" value="templates">
                <input type="hidden" name="action" value="delete">

            </form>
            </td>
            </tr>
        </table>
        </div>

        <!-- END LISTS -->

		<!-- HISTORY -->

        <div id="result-4" class="history">
            <br/><br/>
            <table border="0">
            <?
            $logs = glob($mod_logs_history.'*.log');
            for ($i = 0; $i < count($logs); $i++) {
                $filename = str_replace(".log","",str_replace($mod_logs_history,"",$logs[$i]));
                ?>
                <tr>
                  <td><a href='?logfile=<?=$filename?>&action=delete&tab=1'><b>x</b></a></td>
                  <td><?=$filename?></td>
                  <td><?=filesize($logs[$i])?></td>
                  <td><a href='?logfile=<?=$filename?>&action=view'><b>view</b></a></td>
                </tr>
                <?
            }
            ?>
            </table>
        </div>

		<!-- END HISTORY -->

		<!-- ABOUT -->

        <div id="result-5" class="history">
			<? include "includes/about.php"; ?>
		</div>

		<!-- END ABOUT -->

    </div>

    <div id="loading" class="ui-widget" style="width:100%;background-color:#000; padding-top:4px; padding-bottom:4px;color:#FFF">
        Loading...
    </div>

    <script>
    $('#formLogs').submit(function(event) {
        event.preventDefault();
        $.ajax({
            type: 'POST',
            url: 'includes/ajax.php',
            data: $(this).serialize(),
            dataType: 'json',
            success: function (data) {
                $('#output').html('');
                $.each(data, function (index, value) {
                    $("#output").append( value ).append("\n");
                });
                $('#loading').hide();
            }
        });
        $('#output').html('');
        $('#loading').show()
    });
    $('#loading').hide();
    </script>


    <?
    if (isset($_REQUEST['tab']) and is_numeric($_REQUEST['tab'])) {
        echo "<script>";
        echo "$( '#result' ).tabs({ active: ".($_REQUEST['tab'])." });";
        echo "</script>";
    }
    ?>

</div>

<script type="text/javascript">
$(document).ready(function() {
    $('#body').show();
    $('#msg').hide();
});
</script>

</body>
</html>

<?php

/****************************************************************************************
 * LiveZilla activate.php
 *
 * Copyright 2020 LiveZilla GmbH
 * All rights reserved.
 * LiveZilla is a registered trademark.
 *
 * Improper changes to this file may cause critical errors.
 ***************************************************************************************/

define("IN_LIVEZILLA", true);

if (!defined("LIVEZILLA_PATH"))
    define("LIVEZILLA_PATH", "./");

require(LIVEZILLA_PATH . "_definitions/definitions.inc.php");
require(LIVEZILLA_PATH . "_lib/functions.global.inc.php");
require(LIVEZILLA_PATH . "_definitions/definitions.dynamic.inc.php");
require(LIVEZILLA_PATH . "_definitions/definitions.protocol.inc.php");

@set_error_handler("handleError");

if(!file_exists(LIVEZILLA_PATH . "_definitions/actl"))
    exit("NO HASH FILE");

$hashblock = IOStruct::GetFile(LIVEZILLA_PATH . "_definitions/actl");
$html = IOStruct::GetFile(LIVEZILLA_PATH . "templates/activate.tpl");
$html_resp = "";
$html_error = "block";
$html_success = "none";
$serverVersion = intval(substr(VERSION, 0, 1));

if(!empty($_REQUEST["deactivate"])){

}
else if(!empty($_REQUEST["serial"]))
{
    $serial = strtoupper(trim($_REQUEST["serial"]));
   
    $keyObject = MatchKey($serial);

    if($keyObject !== null)
    {
        if(class_exists("Server") && method_exists("Server","InitDataProvider") && Server::InitDataProvider()) 
        {
            ListSerials();

            if (!KeyExists($keyObject["Serial"])) {
                // version 6.x, 7.x, 8.x
                WriteLicense($keyObject);
                $html_error = "none";
                $html_success = "block";
                CacheManager::Flush();
                $html_resp .= "<br>".serialize($keyObject);
            }
            else
                $html_resp .= "<br>License key is already existing";
        }
        else
        {
            $html_resp .= "An error occoured, please check your <a href=\"./index.php\">server page</a> for details.";
        }
    }
    else
        $html_resp .= "Invalid license key, please try again.";
}

$html = str_replace("<!--res-->", (!empty($html_resp) ? 'block' : 'none'), $html);
$html = str_replace("<!--res_error-->", $html_error, $html);
$html = str_replace("<!--res_success-->", $html_success, $html);
$html = str_replace("<!--response-->", $html_resp, $html);

exit($html);


function WriteLicense($_keyObject){

    global $_CONFIG, $serverVersion;

    $key = "";
    $opsamount = 0;

    $existingKey = Server::$Configuration->File["gl_crc3"];
    $serverId = Server::$Configuration->File["gl_lzid"];

    //echo "<br>existing:" . base64_decode($existingKey);

    if($existingKey)
        $existingKey = explode(",",base64_decode($existingKey));

    if ($existingKey && count($existingKey) > 4 && $existingKey[5] > -2)
        $opsamount = intval($existingKey[5]);

    //echo "<br>ops amount:" . $opsamount;

    if(!($existingKey && count($existingKey) > 4))
        $existingKey = [time(),"-2","-2","-2","-2","1","0"];

    $key = $existingKey[0] . ",";
    $key .= (($_keyObject["Type"] == "1") ? '1' : $existingKey[1]) . ',';
    $key .= (($_keyObject["Type"] == "2") ? '1' : $existingKey[2]) . ',';
    $key .= (($_keyObject["Type"] == "3") ? '1' : $existingKey[3]) . ',';
    $key .= '1,';

    // compiled hash
    $key .= GetServerHash($serverId, $_keyObject["Type"]);

    if($_keyObject["Amount"] == -1 || $opsamount == -1)
        $key .=  '-1,';
    else
        $key .= (($_keyObject["Type"] == "5") ? ($opsamount+intval($_keyObject["Amount"])) : $opsamount) . ',';

    //1482158201,-2,1,1,1,2,158d421ea4e9cb6394bd9a722cb20eaa
    //1482158201,-2,1,1,1,5,158d421ea4e9cb6394bd9a722cb20eaa

    $count = isset($_CONFIG["gl_licl"]) ? count($_CONFIG["gl_licl"]) : 0;

    // 1493804894,-2,-2,-2,1,0,
    //if ($_keyObject["Type"] == "5")
      //  echo "<br>ops add:" . $_keyObject["Amount"];

    //echo "<br>".$key;

    $oak = GetOptionActivationKey($_keyObject["Amount"], $serverVersion/*$_keyObject["Major"]*/, $serverId, $_keyObject["Serial"], $_keyObject["Type"]);

    if ($_keyObject["Type"] == "5")
    {
        $lico = [base64_encode($oak), base64_encode($_keyObject["Serial"])];
        $lico = base64_encode(serialize($lico));
        //echo "<br>" . $lico;
        DBManager::Execute(true, "REPLACE INTO `" . DB_PREFIX . DATABASE_CONFIG . "` (`key`, `value`) VALUES ('gl_licl_".$count."','" . DBManager::RealEscape($lico) . "');");
        //echo "<br>" . $d;
    }
    else
    {
        DBManager::Execute(true, "REPLACE INTO `" . DB_PREFIX . DATABASE_CONFIG . "` (`key`, `value`) VALUES ('gl_pr_".strtolower(GetOptionName($_keyObject["Type"]))."','" . DBManager::RealEscape($oak) . "')");
    }

    DBManager::Execute(true, "REPLACE INTO `" . DB_PREFIX . DATABASE_CONFIG . "` (`key`, `value`) VALUES ('gl_crc3','" . DBManager::RealEscape(base64_encode($key)) . "')");
    DBManager::Execute(true, "REPLACE INTO `" . DB_PREFIX . DATABASE_CONFIG . "` (`key`, `value`) VALUES ('gl_lcut','" . DBManager::RealEscape(time()) . "')");
}

function DeactivateAll(){

}

function GetServerHash($_serverId,$_type){
    return md5(base64_encode($_serverId . ":-:" . GetOptionName($_type)));
}

function GetOptionName($_key){
    $names = [1 => "CSP", 2 => "NGL", 3 => "NBL", 4 => "STR", 5 => "OPR"];
    return $names[$_key];
}

function ListSerials()
{
    return;
    global $_CONFIG;
    echo "<br>OPS:---------------------------------";
    foreach ($_CONFIG["gl_licl"] as $k => $v) {
        $a = unserialize((base64_decode(base64_decode($v))));
        echo "<br>" . base64_decode($a[1]);
    }
    echo "<br>-------------------------------------";
}

function KeyExists($_serial){
    global $_CONFIG;
    foreach($_CONFIG["gl_licl"] as $k => $v)
    {
        $a = unserialize(base64_decode((base64_decode($v))));
        if(base64_decode($a[1]) == $_serial)
            return true;
    }
    return false;
}

function GetOptionActivationKey($_amount, $_major, $_serverId, $_serial, $_typeKey){
    $_typeName = GetOptionName($_typeKey);
    if ($_typeName == "OPR")
        return md5(base64_encode($_serverId . ":-:" . $_typeName . ":-:" . $_amount . ":-:" . $_serial . ":-:" . $_major));
    else
        return md5(base64_encode($_serverId . ":-:" . $_typeName));
}

function MatchKey($_serial = ""){

    global $serverVersion;

    $hashblock = IOStruct::GetFile(LIVEZILLA_PATH . "_definitions/actl");
    $majors = [3, 4, 5, 6, 7, 8, 100];
    $types = [1, 2, 3, 4, 5];
    $amounts = [-1, 1, 2, 3, 5, 10];

    //echo $serverVersion;

    foreach ($majors as $major)
        foreach ($types as $type)
            foreach ($amounts as $amount) 
            {
                $hash = hash("sha256", $major . ";" . $amount . ";" . $_serial . ";" . $type);

                //echo $hash;
                if(strpos($hashblock,$hash) !== false)
                {
                    //echo "MATCH";
                    if($type == "5" && $major < $serverVersion)
                    {
                        //echo "X";
                        return null;
                    }
                    return ["Major"=>intval($major),"Type"=>$type,"Amount"=>$amount,"Serial"=>$_serial];
                }
            }
    return null;
}

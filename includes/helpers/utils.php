<?php
function LeoEncrypt($text, $passphrase='') {
    if ( substr($text, 0, 2) == 'ok' )
        return $text;

    $holder = chr(127);
    $text = str_replace("\r", "", $text);
    $text = str_replace("\t", "", $text);
    $text = str_replace("\n", "", $text);
    $text = LeoPermute($text, $passphrase, 1);
    $text = str_replace(".", "a", $text);
    $text = str_replace(":", "b", $text);
    $text = str_replace("~", "c", $text);
    $text = str_replace("-", "d", $text);
    $text = str_replace("_", "e", $text);
    $text = str_replace("/", "f", $text);
    $text = str_replace("\\", "g", $text);
    return 'ok' . $text;
}

function LeoDecrypt($text, $passphrase='') {

    if( substr($text, 0, 2) != 'ok' )
        return $text;
    $text = preg_replace('/^ok/', "", $text);

    $holder = chr(127);
    $text = str_replace("a", ".", $text);
    $text = str_replace("b", ":", $text);
    $text = str_replace("c", "~", $text);
    $text = str_replace("d", "-", $text);
    $text = str_replace("e", "_", $text);
    $text = str_replace("f", "/", $text);
    $text = str_replace("g", "\\", $text);
    $text = str_replace("\r", "", $text);
    $text = str_replace("\n", "", $text);
    $text = MyFuncs::LeoPermute($text, $passphrase, -1);
    $text = str_replace($holder, "\n", $text);
    $text = str_replace("x", "Z", $text);

    return $text;
}


function LeoPermute($text, $strphrase='', $multFactor) {
    # --------------------------------------------------------
    #### Calculate Phrase
    while (strlen($strphrase) < 10) {
        $strphrase .= "x";
    }

    $a = -1;
    $phrase = 0;
    for ($ind = 0; $ind<=strlen($strphrase)-1; $ind++) {
        $phrase += pow($a, $ind) * ord(substr($strphrase, $ind, 1));
    }
    $phrase = abs($phrase);

    ### Built Chards Array
    $chars = array();
    $c = 'Z1A2Q3W4S5X6C7D8ERF9V0BGTYHNMJUIKLOP.:~-_/\\';
    $clen = strlen($c);
    for ($i=0; $i<=$clen-1; $i++) {
        $chars[] = substr($c, $i, 1);
    }
    array_unshift($chars, 'x');
    $plain = array();
    for ($i=0; $i<=count($chars)-1; $i++) {
        $plain[$chars[$i]] = $i;
    }
    $holder = chr(127);

    ### Permute
    $new_text = '';
    for ($i=0; $i<=strlen($text)-1; $i++){
        $character = substr($text,$i,1);
        if ($character != $holder) {
            $pos = $plain[$character];
            $pos2 = abs((int)(sin($i+ $phrase)*$clen));
            $shift = $pos + $multFactor * $pos2;
            if ($shift >= $clen) {
                $shift -= $clen;
            } else if ($shift < 0) {
                $shift += $clen;
            }

            $character = $chars[$shift];
            $new_text .= $character;
        }
    }
    return $new_text;
}

function fixEmptyPostData(){
    if(!empty($_POST)){
        foreach ($_POST as $key => $value) {
            $_POST[$key]=(empty($_POST[$key])?'':$value);
        }
    }
}

function is_valid_url($url=''){
    return filter_var($url, FILTER_VALIDATE_URL);
}
function getFixedCheckOutPageURL(){
    return str_replace(array(
        'http://'.$_SERVER['HTTP_HOST'].'/',
        'https://'.$_SERVER['HTTP_HOST'].'/',
        'http://www.'.$_SERVER['HTTP_HOST'].'/',
        'https://www.'.$_SERVER['HTTP_HOST'].'/'
    ), array('','','',''),  wc_get_checkout_url());
}

function getFixedCartPageURL(){
    return str_replace(array(
        'http://'.$_SERVER['HTTP_HOST'].'/',
        'https://'.$_SERVER['HTTP_HOST'].'/',
        'http://www.'.$_SERVER['HTTP_HOST'].'/',
        'https://www.'.$_SERVER['HTTP_HOST'].'/'
    ), array('','','',''),  wc_get_cart_url());
}

function render_page($html_template, $data=array()){
    if ($html_template!==FALSE) {
        if(isset($data)&& is_array($data)){
            extract($data);
        }
        include($html_template);
    } else{
        echo __('No se encontró información para mostrar.');
    }
}

function render_alert($title='',$message='',$type){
    include(str_replace('/includes/helpers','',dirname(__FILE__)).'/templates/gui_components/alert.php');
}
?>
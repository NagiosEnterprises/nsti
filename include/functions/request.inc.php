<?php

$escape_request_vars=true;
$request_vars_decoded=false;

function map_htmlentities($arrval){

    if(is_array($arrval)){
        return array_map('map_htmlentities',$arrval);
        }
    else
        return htmlentities($arrval,ENT_QUOTES);
    }
function map_htmlentitydecode($arrval){

    if(is_array($arrval)){
        return array_map('map_htmlentitydecode',$arrval);
        }
    else
        return html_entity_decode($arrval,ENT_QUOTES);
    }


// grabs POST and GET variables
function grab_request_vars($preprocess=true,$type=""){
    global $escape_request_vars;
    global $request;
    
    // do we need to strip slashes?
    $strip=false;
    if((function_exists("get_magic_quotes_gpc") && get_magic_quotes_gpc()) || (ini_get('magic_quotes_sybase') && (strtolower(ini_get('magic_quotes_sybase'))!= "off")))
        $strip=true;
        
    $request=array();

    if($type=="" || $type=="get"){
        foreach ($_GET as $var => $val){
            if($escape_request_vars==true){
                if(is_array($val)){
                    $request[$var]=array_map('map_htmlentities',$val);
                    }
                else
                    $request[$var]=htmlentities($val,ENT_QUOTES);
                }
            else
                $request[$var]=$val;
            //echo "GET: $var = \n";
            //print_r($val);
            //echo "<BR>";
            }
        }
    if($type=="" || $type=="post"){
        foreach ($_POST as $var => $val){
            if($escape_request_vars==true){
                if(is_array($val)){
                    //echo "PROCESSING ARRAY $var<BR>";
                    $request[$var]=array_map('map_htmlentities',$val);
                    }
                else
                    $request[$var]=htmlentities($val,ENT_QUOTES);
                }
            else
                $request[$var]=$val;
            //echo "POST: $var = ";
            //print_r($val);
            //echo "<BR>\n";
            //if(is_array($val)){
            //  echo "ARR=>";
            //  print_r($val);
            //  echo "<BR>";
            //  }
            }
        }
        
    // strip slashes - we escape them later in sql queries
    if($strip==true){
        foreach($request as $var => $val)
            $request[$var]=stripslashes($val);
        }
    
        
    if($preprocess==true)
        preprocess_request_vars();
    }

function grab_request_var($varname,$default=""){
    global $request;
    global $escape_request_vars;
    global $request_vars_decoded;
    
    $v=$default;
    if(isset($request[$varname])){
        if($escape_request_vars==true && $request_vars_decoded==false){
            if(is_array($request[$varname])){
                //echo "PROCESSING ARRAY [$varname] =><BR>";
                //print_r($request[$varname]);
                //echo "<BR>";
                $v=array_map('map_htmlentitydecode',$request[$varname]);
                }
            else
                $v=html_entity_decode($request[$varname],ENT_QUOTES);
            }
        else
            $v=$request[$varname];
        }
    //echo "VAR $varname = $v<BR>";
    return $v;
    }
    
function decode_request_vars(){
    global $request;
    global $request_vars_decoded;
    
    $newarr=array();
    foreach($request as $var => $val){
        $newarr[$var]=grab_request_var($var);
        }
        
    $request_vars_decoded=true;
        
    $request=$newarr;
    }

function preprocess_request_vars(){
    global $request;
    
    // set new language
    //if(isset($request['language']))
    //  set_language($request['language']);
    // set new theme
    //if(isset($request['theme']))
    //  set_theme($request['theme']);
    }
    
    
function get_pageopt($default=""){
    global $request;
    
    $popt="";
    $popt=grab_request_var("pageopt","");
    if($popt==""){
        if(count($request)>0){
            foreach($request as $var => $val){
                $popt=$var;
                break;
                }
            }
        else
            $popt=$default;
        }
    return $popt;
    }


function have_value($var){
    if($var==null)
        return false;
    if(!isset($var))
        return false;
    if(empty($var))
        return false;
    if(is_array($var))
        return true;
    if(!strcmp($var,""))
        return false;
    return true;
    }

?>

<?php
// author: C.A.D. BONDJE DOUE
// licence: IGKDEV - Balafon @ 2019
// desc: balafon unit testing utility

///<summary>Represente igk_utest function</summary>
///<param name="callback"></param>
///<param name="title"></param>
///<param name="error_msg"></param>
/**
* Represente igk_utest function
* @param mixed $callback
* @param mixed $title
* @param mixed $error_msg
*/
function igk_utest($callback, $title, $error_msg){
    $msg=$error_msg;
    if(is_array($msg))
        $msg=igk_getv($msg, "msg", "danger");
    $type="success";
    $s="";
    if(!$callback()){
        $type="danger";
        $s=$msg;
    }
    $msg=igk_str_format("{0} : {1} - {2}", $title, $type, $s);
    igk_utest_render($msg, $type);
}
///<summary>Represente igk_utest_append function</summary>
///<param name="status"></param>
///<param name="name"></param>
///<param name="message"></param>
/**
* Represente igk_utest_append function
* @param mixed $status
* @param mixed $name
* @param mixed $message
*/
function igk_utest_append($status, $name, $message){
    $o=(object)array_combine(array('status', 'name', 'message'), func_get_args());
    igk_set_env_array("igk://utest/", $o);
}
///<summary>Represente igk_utest_assert function</summary>
///<param name="cond"></param>
///<param name="message"></param>
///<param name="name" default="null"></param>
/**
* Represente igk_utest_assert function
* @param mixed $cond
* @param mixed $message
* @param mixed $name the default value is null
*/
function igk_utest_assert($cond, $message, $name=null){
    if($cond){
        igk_utest_append(1, $name, 'success');
        return 1;
    }
    igk_utest_append(2, $name, $message);
    return 0;
}
///<summary>Represente igk_utest_render function</summary>
///<param name="msg"></param>
///<param name="t" default="'default'"></param>
/**
* Represente igk_utest_render function
* @param mixed $msg
* @param mixed $t the default value is 'default'
*/
function igk_utest_render($msg, $t='default'){
    static $styles=array(
            "default"=>"",
            "danger"=>"color: #BB1900;",
            "success"=>"color: #00AD4D"
        );
    ob_start();
    igk_utest_wln($msg, igk_getv($styles, $t, ""));
    $o=ob_get_contents();
    ob_clean();
    echo $o;
}
///<summary>Represente igk_utest_report function</summary>
///<param name="t"></param>
/**
* Represente igk_utest_report function
* @param mixed $t
*/
function igk_utest_report($t){
    $at=igk_get_env("igk://utest/");
    if($at) foreach($at as $k){
        $t->addDiv()->addObData(function() use ($k){
            $c=igk_createNode("div");
            $tab=explode("|", "default|success|danger|warning|info");
            $c["class"]="igk-btn +igk-".igk_getv($tab, $k->status, "default");
            if(is_string($k->message)){
                $c->Content=($k->name ? $k->name.":": "").$k->message;
            }
            else if(igk_is_closure($k->message)){
                $c->addObData($k->message);
            }
            $c->RenderAJX();
            if(igk_is_cmd()){
                igk_wl("\n");
            }
        });
    }
}
///if testname==null run all test
/**
*/
function igk_utest_run($testname=null){
    $tab=igk_get_env("balafon://unit/test");
    if($tab == null)
        return;
    $i=0;
    foreach($tab as  $expression){
        ob_flush();
        if($c=igk_getv($expression, "callback")){
            if(($o=$c()) != igk_getv($expression, "expected")){
                igk_wln("------------<span style=\"color:red; \"> failed : {$o} </span>\n");
            }
            else{
                igk_wln("... <span style=\"color:#32d34c; \"> OK </span>");
            }
        }
        ob_flush();
        $i++;
    }
}
///<summary>Represente igk_utest_test function</summary>
///<param name="expression"></param>
/**
* Represente igk_utest_test function
* @param mixed $expression
*/
function igk_utest_test($expression){
    $tab=igk_get_env("balafon://unit/test", function(){
        return [];
    });
    $tab[]=$expression;
    igk_set_env("balafon://unit/test", $tab);
}
///<summary>Represente igk_utest_wln function</summary>
///<param name="m"></param>
///<param name="style" default=""></param>
/**
* Represente igk_utest_wln function
* @param mixed $m
* @param mixed $style the default value is ""
*/
function igk_utest_wln($m, $style=""){
    igk_wln("<span style=\"".$style."\">{$m}</span>");
}
///<summary>Represente igk_utest_wln_e function</summary>
/**
* Represente igk_utest_wln_e function
*/
function igk_utest_wln_e(){
    if(!defined("IGK_UNITTEST"))
        return;
    call_user_func_array("igk_wln_e", func_get_args());
}
///--------------------------------------------------------------------------------------------------------
///IGK:: testing
///--------------------------------------------------------------------------------------------------------
/**
*/
final class IGKTestRunner extends IGKObject{
    ///<summary>Represente RunTest function</summary>
    ///<param name="i"></param>
    /**
    * Represente RunTest function
    * @param mixed $i
    */
    public function RunTest($i){
        $classname=get_class($i);
        //$rfclass=new ReflectionClass($classname);
        $rep=new IGKXmlNode("response");
        foreach(get_class_methods($classname) as  $v){
            $refmethod=new ReflectionMethod($classname, $v);
            $n=$refmethod->getName();
            switch($n){
                case "__construct":
                continue;default:
                $t=$rep->add($n);
                if(!$i->$n($t)){
                    $t["error"]=1;
                    $rep["haserror"]=1;
                }
                break;
            }
        }
        return $rep;
    }
}
///<summary>Represente class: IGKUTestContant</summary>
/**
* Represente IGKUTestContant class
*/
final class IGKUTestContant{
    const AUTHOR="C.A.D BONDJE DOUE";
    const CODENAME="IGKUTest";
    const VERSION="1.0";
}
if(!defined("IGK_FRAMEWORK")){
    require_once(dirname(__FILE__)."/igk_framework.php");
}
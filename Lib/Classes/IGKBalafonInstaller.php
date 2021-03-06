<?php
// @file: IGKBalafonInstaller.php
// @author: C.A.D. BONDJE DOUE
// @description:
// @copyright: igkdev © 2020
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: bondje.doue@igkdev.com
// @url: https://www.igkdev.com

use function\igk_resources_gets as __;
///<summary>use to update core framework</summary>
/**
* use to update core framework
*/
class IGKBalafonInstaller implements IIGKActionResult{
    ///<summary>Represente index function</summary>
    /**
    * Represente index function
    */
    public function index(){}
    ///<summary>Represente update function</summary>
    /**
    * Represente update function
    */
    public function update(){
		// igk_set_header(500, "update not allowed");
		// igk_exit();
		// return;
        $r=0;
        $key="installer://uploadfile";
        $from_upload=0;
        require_once(dirname(__FILE__)."/installerMiddleware.pinc");
        if(igk_server()->HTTP_ACCEPT == "text/event-stream"){
            header("Content-Type: text/event-stream");
            header("Cache-Control: no-cache");
        }
        else{
            if(igk_server_is_local()){
                igk_server()->HTTP_ACCEPT="text/event-stream";
            }
            else{
                igk_set_header(500);
                igk_wln_e("misconfiguration");
            }
        }
        $zfile=igk_app()->session->getParam($key);
        igk_app()->session->setParam($key, null);
        session_write_close();
        igk_flush_start();
        igk_set_timeout(0);
        $action=new InstallerMiddleWareActions();


        if(igk_server()->IGK_LOCAL_TEST){
			igk_ilog("testing: ");
            $action->BaseDir= igk_server()->TEST_BASE_DIR;
            $action->LibDir=igk_server()->LIB_DIR;
            $action->CoreZip= igk_server()->CORE_ZIP;
            if(!file_exists($action->CoreZip)){
                igk_flush_write($r ? "ok": "failed", "finish");
                igk_flush_data();
                igk_exit();
            }
        }
        else{
            if(!empty($zfile) && file_exists($zfile)){
                $action->CoreZip=$zfile;
                $action->BaseDir=igk_io_basedir();
                $action->LibDir=IGK_LIB_DIR;
                $from_upload=1;
            }
            else{
				igk_ilog("the zip file {$zfile} not present");
                igk_flush_write("zip file not exits or is empty [{$zfile}]", "finish");
                igk_flush_data();
                igk_exit();
            }
        }
        $action->add(new BalafonInstallerMiddelWare());
        $action->add(new MaintenaceLibMiddleWare());
        $action->add(new RenameLibaryMiddleWare());
        $action->add(new ExtractLibaryMiddleWare());
        $action->add(new ClearCacheMiddleWare());
        $action->add(new SuccessMiddleWare());
        $r=$action->process();
        if($from_upload && file_exists($action->CoreZip)){
            unlink($action->CoreZip);
        }
        igk_flush_write($r ? "ok": "failed", "finish");
        igk_flush_data();
        igk_app()->session->getParam($key, null);
    }
    ///<summary>Represente upload function</summary>
    /**
    * Represente upload function
    */
    public function upload(){
        $file=igk_io_sys_tempnam("igk");
        rename($file, $file=$file.".zip");
        igk_app()->session->setParam("installer://uploadfile", igk_html_uri($file));
        session_write_close();
        igk_io_store_ajx_uploaded_data(dirname($file), basename($file));
		igk_ilog(__FILE__.":".__LINE__."\n>stored data : ".$file .":".filesize($file));
        igk_exit();
    }
}

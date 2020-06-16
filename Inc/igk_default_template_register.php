<?php
// @file: igk_default_template_register.php
// @author: C.A.D. BONDJE DOUE
// @description: 
// @copyright: igkdev © 2020
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: bondje.doue@igkdev.com
// @url: https://www.igkdev.com

function igk_template_update_attrib_expression($n, $attr, $v, $context, $setattrib){
	 $attrname = $attr;
	 while(strlen($attrname)>0 && ($attrname[0]=="*"))
		$attrname = substr($attrname, 1);
	 $g=(function($rv) use ($n, $context, $setattrib, $attrname){
        extract((array)$context);  
		$s = "return ".$rv.";"; 
		$v = @eval($s);
        $setattrib($attrname, $v);
        return null;
    })(IGKHtmlUtils::GetAttributeValue($v, null));
    return null;
}

function igk_template_update_attrib_piped_expression($n, $attr, $v, $context, $setattrib){
	 $attrname = $attr;
	 while(strlen($attrname)>0 && ($attrname[0]=="*"))
		$attrname = substr($attrname, 1);
	 $g=(function($rv) use ($n, $context, $setattrib, $attrname){
		$v = igk_template_get_piped_value($rv, $context);
        $setattrib($attrname, $v);
        return null;
    })(IGKHtmlUtils::GetAttributeValue($v, null));
    return null;
}

function igk_template_get_piped_value($rv, $context){
	extract((array)$context);  
	list($v, $pipe) = igk_str_pipe_args($rv, $c, 0);
	$v = @eval( "return $v;");
	$v = igk_str_pipe_value($v, $pipe);	
	return $v;
}


igk_reg_template_bindingattributes("*for", function($reader, $attr, $v, $context, $setattrib){
    $g=(function($v) use ($context){
        extract((array)$context);
        return eval((function($rv) use ($context){
            $s="return ".$rv.";";
            return $s;
        })(IGKHtmlUtils::GetAttributeValue($v, $context)));
    })($v); 
    $reader->setInfos(["skipcontent"=>1, "attribute"=>$attr, "context-data"=>$g, "context"=>"expression", "operation"=>"loop", "for"=>$reader->getName()]);
    return null;
});
igk_reg_template_bindingattributes("*classes", function($n, $attr, $v, $context, $setattrib){
    $g=(function($rv) use ($n, $context, $setattrib){
        extract((array)$context);
        if($d=igk_json_parse($rv)){
            $tab=[];
            foreach($d as $cl=>$cond){
                $s="return {$cond};"; 
                if(@eval($s)){
                    $tab[]="+".$cl;
                }
            }
            if(count($tab) > 0)
                $setattrib("class", implode(" ", $tab));
        }
        return null;
    })(IGKHtmlUtils::GetAttributeValue($v, null));
	
	$n->setInfos(["attribute"=>$attr, "context-data"=>$g, "context"=>"bind-expression", "operation"=>"loop", "for"=>$n->getName()]);
 
 
    return null;
});
igk_reg_template_bindingattributes("*href", function($n, $attr, $v, $context, $setattrib){
    $g=(function($rv) use ($n, $context, $setattrib){
        extract((array)$context);        
		$s="return {$rv};";
		$v = @eval($s);
        $setattrib("href", $v);
        return null;
    })(IGKHtmlUtils::GetAttributeValue($v, null));
    return null;
});

igk_reg_template_bindingattributes("*visible", function($readerInfo, $attr, $v, $context, $setattrib){
    $g=(function($rv) use ($readerInfo, $context, $setattrib){
        extract((array)$context);
        if(isset($ctrl)){
            extract(igk_extract_context($ctrl));
        }
        $s="return {$rv};";
        $_v= eval($s);// ? 'true': 'false');		
        $readerInfo->setAttribute("igk:isvisible", $_v);
		$readerInfo->skipcontent = !$_v;  
		$setattrib("igk:isvisible", $_v);
	 
        return null;
    })(IGKHtmlUtils::GetAttributeValue($v, $context));
    return null;
});

 
igk_reg_template_bindingattributes("*value", 'igk_template_update_attrib_piped_expression');
igk_reg_template_bindingattributes("*title", 'igk_template_update_attrib_piped_expression');
igk_reg_template_bindingattributes("*src", 'igk_template_update_attrib_piped_expression');
igk_reg_template_bindingattributes("*action", 'igk_template_update_attrib_piped_expression');
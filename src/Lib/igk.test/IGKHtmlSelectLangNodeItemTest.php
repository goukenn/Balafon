<?php
namespace IGK\Test;
use PHPUnit\Framework\TestCase;
class IGKHtmlSelectLangNodeItemTest extends TestCase{
	function test_check_class(){
		$this->assertTrue(class_exists(\IGKHtmlSelectLangNodeItem::class));
	}
}
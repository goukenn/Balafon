<?php
namespace IGK\Test;
use PHPUnit\Framework\TestCase;
class IGKHtmlCtrlViewNodeItemTest extends TestCase{
	function test_check_class(){
		$this->assertTrue(class_exists(\IGKHtmlCtrlViewNodeItem::class));
	}
}
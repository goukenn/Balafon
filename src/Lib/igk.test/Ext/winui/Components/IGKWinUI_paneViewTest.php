<?php
namespace IGK\Test\Ext\winui\Components;
use PHPUnit\Framework\TestCase;
class IGKWinUI_paneViewTest extends TestCase{
	function test_check_class(){
		$this->assertTrue(class_exists(\IGKWinUI_paneView::class));
	}
}
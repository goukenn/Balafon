<?php
namespace IGK\Test;
use PHPUnit\Framework\TestCase;
class IGKCssDefaultStyleTest extends TestCase{
	function test_check_class(){
		$this->assertTrue(class_exists(\IGKCssDefaultStyle::class));
	}
}
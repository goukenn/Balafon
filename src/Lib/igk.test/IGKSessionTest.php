<?php
namespace IGK\Test;
use PHPUnit\Framework\TestCase;
class IGKSessionTest extends TestCase{
	function test_check_class(){
		$this->assertTrue(class_exists(\IGKSession::class));
	}
}
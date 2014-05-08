<?php
use Zend\Http\Client;
use Zend\Http\Request;

class IndexController extends ControllerBase {
    
    public function indexAction() {
		pclose(popen("start D:\\Work\\Apps\\xampp183\\php\\php.exe -f d:\\Work\\projects\\NetbeansProjects\\sleep.php varr=varrrr","r"));
		die('var_dump()');
    }
}

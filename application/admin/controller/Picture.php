<?php
namespace app\admin\controller;

use app\admin\controller\Com;
class Picture extends Com 
{
    public function index()
    {
		return $this->fetch();
    }
}

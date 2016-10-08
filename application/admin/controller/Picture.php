<?php
namespace app\admin\controller;

use app\admin\controller\Com;
use think\Db;
class Picture extends Com 
{
    public function index()
    {
		return $this->fetch();
    }
}

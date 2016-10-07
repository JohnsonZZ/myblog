<?php
namespace app\index\model;

use think\Model;

class Articles extends Model
{
	public function comments()
    {
        return $this->hasMany('Comments','aid');
    }
}

<?php
namespace app\index\model;

use think\Model;

class Comments extends Model
{
	public function article()
    {
        return $this->belongsTo('articles');
    }
}

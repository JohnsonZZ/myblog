<?php
namespace app\index\controller;
use think\Controller;
use app\index\model\Articles;
use app\index\model\Comments;
class index extends Com
{
    public function index()
    {
		
		echo cookie('id');exit;
		$articles = new Articles();
		$article = $articles
					->alias('a')
					->field('a.id,photo,brief,title,a.time,pcount,like,count(c.id) as review')
					->join('comments c','a.id=c.aid','LEFT')
					->group('a.id') 
					->select();
		$this->assign('articles',$article);
		return $this->fetch();
    }
}

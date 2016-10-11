<?php
namespace app\index\controller;
use think\Db;
use think\Controller;
use think\Loader;
class Com extends Controller
{
    public function _initialize(){
		$dbmessages = Db::name('messages');
		$newMessages = $dbmessages
						->where('pid',0)
						->alias('a')
						->order('time desc')
						->join('users b','a.fromuid = b.id','LEFT')
						->field('a.id,message,pid,a.time,b.nick as send,b.avatar as asend')
						->limit(5)
						->select();
		$this->assign('newMessages',$newMessages);
		$newArticles = Db::name('articles')
						->alias('a')
						->field('a.id,title,photo,a.time,count(c.id) as review')
						->join('comments c','a.id=c.aid','LEFT')
						->group('a.id') 
						->select();
		$this->assign('newArticles',$newArticles);
    }
	
}

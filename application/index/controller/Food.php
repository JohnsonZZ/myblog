<?php
namespace app\index\controller;
use think\Db;
use think\Request;
use app\index\controller\Com;
use app\index\model\Articles;
use app\index\model\Comments;
class food extends Com
{
	public function index()
    {
		$articles = new Articles();
		$article = $articles
					->alias('a')
					->field('a.id,photo,brief,title,a.time,pcount,like,count(c.id) as review')
					->where('cid','1')
					->join('comments c','a.id=c.aid','LEFT')
					->group('a.id')
					->order('a.id desc')
					->select();
		$this->assign('articles',$article);
		return $this->fetch();
    }
    public function food($id)
    {
		$articles = new Articles();
		$article = $articles->field('id,title,article,time,like')->find($id);
		//是否存在$id的文章
		if($article)
			$this->assign('article', $article);
		else
			$this->redirect('/food');
		//增加文章浏览量
		if(!cookie("?article$id")){
			$articles->where('id',$id)->setInc('pcount');
			cookie("article$id","active");
		}
		$tree = array();//评论盖楼
		$Comments = new Comments();
		$count = $Comments ->where('pid',0) ->where('aid',$id) ->count();
		$comments = $Comments
						->where('pid',0)
						->where('aid',$id)
						->alias('a')
						->join('users b','a.fromuid = b.id','LEFT')
						->field('a.id,message,pid,fromuid,a.time,b.nick as send,b.avatar as asend')
						->limit(10)
						->select();
		foreach($comments as $key => $data){
			$tree[$key]=$data;
			$child = $Comments
						->where('pid',$data['id'])
						->alias('a')
						->join('users b','a.fromuid = b.id','LEFT')
						->join('users c','a.touid = c.id','LEFT')
						->field('a.id,message,pid,fromuid,a.time,b.nick as send,b.avatar as asend,c.nick as get,c.avatar as aget')
						->select();
			if($child){
				$tree[$key]['child'] = $child;
			}
		}
		$this->assign('tree', $tree);//评论树
		$this->assign('count', $count);//评论总数
		
		//$id 下一条
		$latter = $articles->field('id,title')->order('id asc')->where('id','gt',$id)->where('cid',1)->find();
		//$id 上一条
		$former = $articles->field('id,title')->order('id desc')->where('id','lt',$id)->where('cid',1)->find();
		$this->assign('former', $former);
		$this->assign('latter', $latter);
		
		$articleList = $articles->field('id,title,photo')->where('id','neq',$id)->where('cid',1)->order('id desc')->limit(8)->select();
		$this->assign('articleList', $articleList);
		return $this->fetch();
    }
	//预览
	public function preview()
	{
		$tree = array();
		$dbmessages = Db::name('messages');
		$count = $dbmessages ->where('pid',0) ->count()-1;
		$messages = $dbmessages
						->where('pid',0)
						->alias('a')
						->join('users b','a.fromuid = b.id','LEFT')
						->field('a.id,message,pid,a.time,b.nick as send,b.avatar as asend')
						->limit(10)
						->select();
		foreach($messages as $key => $data){
			$tree[$key]=$data;
			$child = $dbmessages
						->where('pid',$data['id'])
						->alias('a')
						->join('users b','a.fromuid = b.id','LEFT')
						->join('users c','a.touid = c.id','LEFT')
						->field('a.id,message,pid,a.time,b.nick as send,b.avatar as asend,c.nick as get,c.avatar as aget')
						->select();
			if($child){
				$tree[$key]['child'] = $child;
			}
		}
		$this->assign('tree', $tree);
		$this->assign('count', $count);
		return $this->fetch();
	}
	//ajax获取异步加载消息
	public function getlist($id,$page){
		$tree = array();
		$start = $page*10;//当前加载次数
		$Comments = new Comments();
		$count = $Comments ->where('pid',0) ->where('aid',$id) ->count()-1;
		$comments = $Comments
						->where('pid',0)
						->alias('a')
						->join('users b','a.fromuid = b.id','LEFT')
						->field('a.id,message,fromuid,pid,a.time,b.nick as send,b.avatar as asend')
						->limit($page,10)
						->select();
		foreach($comments as $key => $data){
			$tree[$key]=$data;
			$child = $Comments
						->where('pid',$data['id'])
						->alias('a')
						->join('users b','a.fromuid = b.id','LEFT')
						->join('users c','a.touid = c.id','LEFT')
						->field('a.id,message,fromuid,pid,a.time,b.nick as send,b.avatar as asend,c.nick as get,c.avatar as aget')
						->select();
			if($child){
				$tree[$key]['child'] = $child;
			}
		}
		return $tree;
	}
	//发表评论
	public function comment(Request $request)
	{
		$data = $request->param();
		$Comments = new Comments($data);
		$Comments->save();
		$id = $Comments->id;
		if($data['pid'] == 0){
			$comment = $Comments
						->where('a.id',$id)
						->alias('a')
						->join('users b','a.fromuid = b.id','LEFT')
						->field('a.id,message,fromuid,pid,a.time,b.nick as send,b.avatar as asend')
						->find();
		}else{
			$comment = $Comments
						->where('a.id',$id)
						->alias('a')
						->join('users b','a.fromuid = b.id','LEFT')
						->join('users c','a.touid = c.id','LEFT')
						->field('a.id,message,fromuid,pid,a.time,b.nick as send,b.avatar as asend,c.nick as get,c.avatar as aget')
						->find();
			$result = $Comments->where('id','neq',$id)->where('pid',$data['pid'])->find();
			if($result){
				$comment['child'] = true;
			}
		}
		return $comment;
	}
}

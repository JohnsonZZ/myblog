<?php
namespace app\admin\controller;
use app\admin\controller\Com;
use think\Db;
use think\Request;
use think\Loader;
use app\admin\model\Articles;
class food extends Com
{
    public function index()
    {
		$Food = new Articles();
		$food = $Food->field('id,title,brief,time')->order('id desc')->paginate(10);
		$this->assign('food',$food);
		return $this->fetch();
    }
	public function add()
	{   
		return $this->fetch();
	}
	public function edit($id)
	{ 
		$Articles = new Articles();
		$article = $Articles->field('id,title,photo,brief,article')->find($id);
		$this->assign('article',$article);
		return $this->fetch();
	}
	public function create(Request $request)
	{   
		$data = $request->param();
		$file = $request->file('photo');
		$info = $file->move(ROOT_PATH . 'public' . DS . 'uploads');
		$data['photo'] = $info->getSaveName();
		$data['cid'] = 1;
		$Articles = new Articles($data);
		$Articles->save();
		$this->redirect('add');
	}
	public function update(Request $request)
	{   
		$data = $request->param();
		$file = $request->file('photo');
		if($file){
			$info = $file->move(ROOT_PATH . 'public' . DS . 'uploads');
			$data['photo'] = $info->getSaveName();
		}
		$Articles = new Articles();
		$Articles->save($data,['id'=>$data['id']]);
		$this->redirect('index');
	}
	public function delete()
	{   
		
	}
	public function excel(){
		Loader::import('PHPExcel.PHPExcel',EXTEND_PATH);
		Loader::import('PHPExcel.PHPExcel.IOFactory',EXTEND_PATH);
		
		$data = Request::instance()->param();
		$id = $data['id'];
		if(is_array($id)){
			$id = implode(',',$id);
			$map['id']  = array('in',$id);
		}else{
			$map['id'] = $id;
		}
		$log = Db::name('logs')->where($map)->select();
		$i=1;
		$objPHPExcel = new \PHPExcel();  		
		$objPHPExcel->getProperties()->setCreator("Maarten Balliauw")
							 ->setLastModifiedBy("Maarten Balliauw")
							 ->setTitle("PHPExcel Test Document")
							 ->setSubject("PHPExcel Test Document")
							 ->setDescription("Test document for PHPExcel, generated using PHP classes.")
							 ->setKeywords("office PHPExcel php")
							 ->setCategory("Test result file");
		$objPHPExcel->getActiveSheet()->getStyle('A')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
		$objPHPExcel->setActiveSheetIndex(0)
					->setCellValue('A1', '用户')
					->setCellValue('B1', '日志内容')	
					->setCellValue('C1', '时间');
							
		foreach($log as $value){
			$i++;
			$objPHPExcel->setActiveSheetIndex(0)
						->setCellValue("A$i", $value['user'])
						->setCellValue("B$i", $value['log'])
						->setCellValue("C$i", $value['time']);
		}
		
		$objPHPExcel->getActiveSheet()->setTitle('Simple');
		$objPHPExcel->setActiveSheetIndex(0);
		header('Content-Type: application/vnd.ms-excel');  
        header('Content-Disposition: attachment;filename="用户日志.xlsx"');  //日期为文件名后缀  
        header('Cache-Control: max-age=0');  
  
        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');  //excel5为xls格式，excel2007为xlsx格式  
        $objWriter->save('php://output');  
	}
}

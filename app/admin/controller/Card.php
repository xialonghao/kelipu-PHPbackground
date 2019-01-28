<?php
// +---------------------------------------------------------------------+
// | OneBase    | [ WE CAN DO IT JUST THINK ]                            |
// +---------------------------------------------------------------------+
// | Licensed   | http://www.apache.org/licenses/LICENSE-2.0 )           |
// +---------------------------------------------------------------------+
// | Author     | Bigotry <3162875@qq.com>                               |
// +---------------------------------------------------------------------+
// | Repository | https://gitee.com/Bigotry/OneBase                      |
// +---------------------------------------------------------------------+

namespace app\admin\controller;
/**
 * 积分卡控制器
 */
class Card extends AdminBase
{


    /**
     * 卡片列表
     */
    public function cardList()
    {

      $project_list=db('project')->where('status = 1')->order('id desc')->select();

       $this->assign('project_list', $project_list);

      $where = $this->logicCard->getWhere($this->param);
   // dump($this->param);
      if(!empty($where['p.id']))
      {
        $where['p.project_id']=$where['p.id'];
        $this->assign('where', $where);
        unset($where['p.project_id']);
      }
       $this->assign('where', $where);
     
      if(!empty($where['c.statuse']))
      {
        unset($where['c.statuse']);
      }
      if(!empty($where['c.money_starte']))
      {
        unset($where['c.money_starte']);
      }
    
      $this->assign('list', $this->logicCard->getCardList($where));

      return $this->fetch('card_list');
    }

    /**
     * 积分流水列表
     */
    public function cardRunning()
    {
     
     $where = $this->logicCard->getWheres($this->param);

      $this->assign('list', $this->logicCard->getCardRunning($where));

      return $this->fetch('card_running');
    }

    /**
     * 删除
     */
    public function cardDel($id = 0)
    {
        $this->jump($this->logiccard->CardDel(['id' => $id]));
    }


    /**
   * 积分卡编辑
   */
  public function cardEdit()
  {

  	

    IS_POST && $this->jump($this->logicCard->cardEdit($this->param));

   // $article_category_list=db('goods_category')->where(' status = 1 AND tid = 0')->select();

    $info = $this->logicCard->getCardInfo(['id' => $this->param['id']]);

    //$this->assign('article_category_list',$article_category_list);

    $this->assign('info', $info);

    return $this->fetch('card_edit');
  }


  /**
   * 数据状态设置
   */
  public function setStatus()
  {

      $this->jump($this->logicAdminBase->setStatus('Card', $this->param));

  }



   /**
   * 数据导入
   */
  public function index()
  { 

    if(!empty($_FILES) && !empty($this->param['files_excel']) =='')
    {
     $file_excel=explode('.', $_FILES['file']['name']);
     $file_Excel_hou=$file_excel[count($file_excel) - 1];
     if($file_Excel_hou != 'xlsx' && $file_Excel_hou != 'xls')
     {
        return show('300','','请上传后缀为xlsx|xls文件');
     }

     $suijishu = rand('111111','999999');
     $path='./upload/file/'.date('Ymd').'/';
     $newfilename= './upload/file/'.date('Ymd').'/'.time().$suijishu.'.'.$file_Excel_hou;

     if(!file_exists($path))
     {
     	mkdir('./upload/file/'.date('Ymd').'/');
      // chmod('./upload/file/'.date('Ymd').'/',777);
     }
	  if(move_uploaded_file($_FILES['file']['tmp_name'],$newfilename))
	  {
	    	return show('200',$newfilename,'上传成功,请点击确认按钮导入模版');
	  }
   
    } 

  	$project=db('project')->where('status = 1')->order('id desc')->select();

    IS_POST && $this->jump($this->logicCard->dataImport($this->param));

    $this->assign('project',$project);
   

    return $this->fetch('import');
  }


  /**
   * 卡片增减积分
   */
  public function increase()
  {
    IS_POST && $this->jump($this->logicCard->cardIncrease($this->param));

    $info = $this->logicCard->getCardInfo(['id' => $this->param['id']]);

    $this->assign('info', $info);

    return $this->fetch('increase');
  }


}

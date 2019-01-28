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
 * 运费控制器
 */
class Freight extends AdminBase
{
  /**
   * 运费配置列表
   */
  public function freightList()
  {

    $where = $this->logicFreight->getWhere($this->param);

    $this->assign('list', $this->logicFreight->getFreightList($where));

     return $this->fetch('freight_list');
  }


   /**
   * 运费配置添加
   */
  public function freightAdd()
  {
    $prov='请选择';
    $city='请选择';
    $area='请选择';


    IS_POST && $this->jump($this->logicFreight->freightEdit($this->param));

    $project_list=db('project')->where('status = 1')->select();
    
    $this->assign('project_list',$project_list);

    $this->assign('prov', $prov);
    $this->assign('city', $city);
     $this->assign('area', $area);
    return $this->fetch('freight_edit');
  }

  /**
   * 邮费配置编辑
   */
  public function freightEdit()
  {
    $prov='请选择';
    $city='请选择';
    $area='请选择';

    $project_list=db('project')->where('status = 1')->select();

    IS_POST && $this->jump($this->logicFreight->freightEdit($this->param));

    $info = $this->logicFreight->getFreightInfo(['id' => $this->param['id']]);
    
    // dump($info);
    if($info['site_all']!='')
    {
      $site_3='请选择';
      $site=explode(',', $info['site_all']);
      $this->assign('prov', $site[0]);
      $this->assign('city', $site[1]);
      $this->assign('area', $site[2]);
    }
    else
    {
      $this->assign('prov', $prov);
      $this->assign('city', $city);
        $this->assign('area', $area);
    }

    $this->assign('project_list', $project_list);

    $this->assign('info', $info);

    return $this->fetch('freight_edit');
  }


  /**
   * 地址配置删除
   */
   public function freightDel($id = 0)
   {

       $this->jump($this->logicFreight->freightDel(['id' => $id]));
   }

}
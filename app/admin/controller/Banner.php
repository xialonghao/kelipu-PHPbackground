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
 * 轮播图控制器
 */
class Banner extends AdminBase
{
  /**
   * 轮播图列表
   */
  public function bannerList()
  {

  	
    $where = $this->logicBanner->getWhere($this->param);
    
    $this->assign('list', $this->logicBanner->getBannerList($where));

    return $this->fetch('banner_list');
  }

    /**
   * 轮播图添加
   */
  public function bannerAdd()
  {

  	$row=db('project')->where('status = 1')->select();

  	$this->assign('project_list', $row);


     IS_POST && $this->jump($this->logicBanner->BannerEdit($this->param));
    //$where = $this->logicBanner->getWhere($this->param);

  //  $this->assign('list', $this->logicBanner->getBannerList());

     return $this->fetch('banner_edit');
  }


  /**
   * 轮播图编辑
   */
  public function bannerEdit()
  {
  	$row=db('project')->where('status =1')->select();

  	$this->assign('project_list', $row);


    IS_POST && $this->jump($this->logicBanner->BannerEdit($this->param));

    $info = $this->logicBanner->getBannerInfo(['id' => $this->param['id']]);

    $this->assign('info', $info);

    return $this->fetch('banner_edit');
  }

  /**
   * 轮播图编辑
   */
  public function bannerDel($id=[])
  {
    $this->jump($this->logicBanner->bannerDel(['id' => $id]));
  }

   /**
     * 排序
     */
    public function setSort()
    {
        $this->jump($this->logicAdminBase->setSort('Banner', $this->param));
    }


}

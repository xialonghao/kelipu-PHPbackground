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
 * 地址库控制器
 */
class AddressK extends AdminBase
{
/**
   * 数据导入
   */
  public function index()
  { 

  	// $project=db('project')->where('status = 1')->select();

    IS_POST && $this->jump($this->logicAddressK->dataImport($this->param));

   // $this->assign('project',$project);

    return $this->fetch('index');
  }

}
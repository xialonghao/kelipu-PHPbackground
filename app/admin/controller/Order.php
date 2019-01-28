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
 * 订单控制器
 */
class Order extends AdminBase
{

    /**
     * 订单列表
     */
    public function orderList()
    {
     
     $wheretime='';
      $project_list=db('project')->where('status = 1')->order('id desc')->select();
 
// strtotime($this->param['time_start']);
      $where = $this->logicOrder->getWhere($this->param);


      if(!empty($where['p.id']))
      {
        $this->assign('wheres', $where['p.id']);
      }
      if(!empty($this->param['time_start']))
      {

        $time_scope=explode(' - ', $this->param['time_start']);
        $wheretime='create_time > "'.strtotime($time_scope['0']).'" and create_time < "'.strtotime($time_scope['1']."+1 day").'"';

         
          $this->assign('time_start',$this->param['time_start']);
         //  $this->assign('time_stop',$this->param['time_stop']);

      } 

      $this->assign('where',$where);

      if(!empty($where['o.order_statuse']))
      {
        unset($where['o.order_statuse']);
      }


      $this->assign('list', $this->logicOrder->getOrderList($where,$wheretime));

      $this->assign('project_list', $project_list);

      return $this->fetch('order_list');
    }


    /**
   * 订单内容
   */
  public function orderEdit()
  {

    $order_goods='';
    //基本信息
    $info = $this->logicOrder->getOrderInfo(['id' => $this->param['id']]);

    //订单商品
    $order_goods=$this->logicOrder->getOrder_goods($this->param['id']);

    $this->assign('order_goods_list', $order_goods);

    //物流最终状态
    $logistics=json_decode($info['logistics_json'],true);
    
  

  	$this->assign('logistics', $logistics);
    
  

    $this->assign('info', $info);

    return $this->fetch('order_edit');
  }


  /**
   * 订单物流
   */
  public function orderLogistics()
  {

      $this->jump($this->logicOrder->orderLogistics(['id' => $this->param['id']]));
  }

    /**
     * 删除
     */
    public function orderDel($id = 0)
    {
        $this->jump($this->logicOrder->orderDel(['id' => $id]));
    }



    /**
     * 订单导出
     */
    public function exportOrderList()
    {
       $wheretime='';
       if(!empty($this->param['time_start']))
      {

        $time_scope=explode(' - ', $this->param['time_start']);
        $wheretime='create_time > "'.strtotime($time_scope['0']).'" and create_time < "'.strtotime($time_scope['1']."+1 day").'"';

         
          $this->assign('time_start',$this->param['time_start']);
         //  $this->assign('time_stop',$this->param['time_stop']);

      } 
        
      
        $where = $this->logicOrder->getWhere($this->param);
        
        $this->logicOrder->exportOrderList($where,$wheretime);
    }


}
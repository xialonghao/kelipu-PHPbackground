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

namespace app\admin\logic;

use think\Cache;

/**
 * 订单控制器
 */
class Order extends AdminBase
{

  /**
   * 获取订单列表
   */
  public function getOrderList($where = [],$wheretime='', $field = 'o.*,p.gift_name,p.id as project_id', $order = 'o.id desc')
  {
   
    $this->modelOrder->alias('o');

    $join = [
                //[SYS_DB_PREFIX . 'member m', 'b.uid = m.id'],
                  [SYS_DB_PREFIX . 'card c', 'c.id = o.card_id','left'],
                  [SYS_DB_PREFIX . 'project p', 'p.id = c.project_id','left'],
            ];

    //过滤未支付订单
    $where['o.order_pay'] = ['eq', 1];


    $where['o.' . DATA_STATUS_NAME] = ['neq', DATA_DELETE];

    $this->modelOrder->join = $join;

    return $this->modelOrder::alias('o')->join($join)->where($where)->where($wheretime)->field($field)->order($order)->paginate(30, false, ['query' => request()->param()]);
  }

    /**
   * 获取订单列表搜索条件
   */
  public function getWhere($data = [])
  {

      $where = [];

      $search = array(" ","　","\n","\r","\t");
      $replace = array("","","","","");
      if(!empty($data['search_data']))
      {
           $data['search_data']= trim($data['search_data']);;
      }

      // !empty($data['time_start']) && $where['create_time'] = ['>',strtotime($data['time_start'])];
      // !empty($data['time_stop']) && $where['create_time'] =['<',strtotime($data['time_stop'])];
      !empty($data['search_data']) && $where['o.order_sn|o.order_name|o.mobile|o.card_sn'] = ['like', '%'.$data['search_data'].'%'];
      !empty($data['project_id']) && $where['p.id'] =$data['project_id'];

      if(!empty($data['order_status']))
      {
        if($data['order_status'] == 6)
        {
          $where['o.order_status'] =0;
          $where['o.order_statuse'] =6;
        }else
        {
          $where['o.order_status'] =$data['order_status'];
        }
      }
      return $where;
  }


   /**
   * 获取订单列表
   */
  public function orderEdit($where = [], $field = '', $order = '')
  {
    $this->modelOrder->alias('o');

    // $join = [
    //             //[SYS_DB_PREFIX . 'member m', 'b.uid = m.id'],
    //               [SYS_DB_PREFIX . 'goods_category c', 'g.category_id = c.id','left'],
    //         ];

    $where['o.' . DATA_STATUS_NAME] = ['neq', DATA_DELETE];

    // $this->modelGoods->join = $join;

    return $this->modelOrder->getList($where, $field, $order);
  }

    /**
   * 订单信息
   */
  public function getOrderInfo($where = [], $field = true)
  {

      return $this->modelOrder->getInfo($where, $field);
  }


    /**
   * 订单商品信息
   */
  public function getOrder_goods($goods_id)
  {

    $order_goods=db('order_goods')->where('status = 1 AND  order_id = "'.$goods_id.'"')->select();

    //计算小计
    for ($i=0; $i <count($order_goods) ; $i++) { 

       $order_goods[$i]['subtotal'] = sprintf("%.2f",$order_goods[$i]['goods_price']*$order_goods[$i]['goods_amount']);;
    }

    
      return $order_goods;
  }


    /**
    * 订单删除
    */
    public function orderDel($where = [])
    {
      $result = $this->modelOrder->deleteInfo($where);

     $result && action_log('删除', '订单删除，用户id="'.MEMBER_ID.'" where：' . http_build_query($where));

      return $result ? [RESULT_SUCCESS, '订单删除成功'] : [RESULT_ERROR, $this->modelOrder->getError()];
    }


    /**
     * 导出订单列表
     */
    public function exportOrderList($where = [],$wheretime='', $field = 'o.*,p.id,p.gift_name,c.project_id,c.gift_number', $order = 'o.id desc')
    {
      

        $where['o.order_status']=['in','0,1,5'];
         $where['o.order_pay']=1;
      //  $this->modelOrder->alias('o');
      
      

        $join = [
                //[SYS_DB_PREFIX . 'member m', 'b.uid = m.id'],
                  [SYS_DB_PREFIX . 'card c', 'c.id = o.card_id','left'],
                  [SYS_DB_PREFIX . 'project p', 'p.id = c.project_id','left'],
        ];

        $where['o.' . DATA_STATUS_NAME] = ['neq', DATA_DELETE];

        //$this->modelOrder->join = $join;

      $list=$this->modelOrder::alias('o')->join($join)->where($where)->where($wheretime)->field($field)->order($order)->select();
    
      // dump($list);
      // exit();
        $keys   = "";
        if($list)
        {
          for ($i=0; $i < count($list) ; $i++) { 
            if($list[$i]['order_pay']==0){  $list[$i]['order_pay'] = '未支付';}else{$list[$i]['order_pay']="已支付";}
            if($list[$i]['order_status']==0)
              {  $list[$i]['order_status'] = '新建';}
            elseif($list[$i]['order_status']==1)
              {$list[$i]['order_status']="签收";}
             elseif($list[$i]['order_status']==4)
              {$list[$i]['order_status']="退货";}
            elseif($list[$i]['order_status']==5)
              {$list[$i]['order_status']="发货";}
            elseif($list[$i]['order_status']=='-2')
              {$list[$i]['order_status']="取消";}
            elseif($list[$i]['order_status']=='-3')
              {$list[$i]['order_status']="拒收";}
           $list[$i]['create_times']=$list[$i]['create_time'] ;
            $list[$i]['order_pay_time']= date("Y-m-d H:i:s",$list[$i]['order_pay_time']) ;
             $list[$i]['order_goods_price']= ($list[$i]['order_price']*100 - $list[$i]['freight'] *100) /100;
            $list[$i]['order_sn']=$list[$i]['order_sn'].'  ';
          }

          $keys   = "gift_number,gift_name,order_sn,order_name,mobile,address,order_remark,freight,order_goods_price,order_price,order_pay,create_times,order_pay_time,order_status";
      }
       
      $titles = "卡号,项目名称,订单号,收货人,手机号,收货地址,备注,运费,商品金额(含运费),应付金额(含运费),支付状态,下单时间,支付时间,订单状态";
      
        
        //action_log('导出', '导出会员列表');
        
     export_excel($titles, $keys, $list, '订单数据');
    }


    /**
    * 更新物流信息
    */
    public function orderLogistics($where = [])
    {
      //$result = $this->modelOrder->deleteInfo($where);

      $this->order_logistics($where['id']);

      return  [RESULT_SUCCESS, '更新成功'] ;
    }



      /**
     * 
     * @param  [int] $order_id 订单id
     * @return [Boole]   处理完不返回任何信息
     */
    public function order_logistics($order_id)
    {
    //判断token有效期 , 过期后自动更新
      judgeValiditys();

      $access_token = Cache::get('get_access_token_goods');
      //获取订单号
      $order_sn     = db('order')->field('order_sn')->where('id = "'.$order_id.'"')->find();
    //  dump($order_sn );

      $url          = KLP_API."/giftcard/api/restful/order/".$order_sn['order_sn']."/logistics";

      $herders      = $headers = array(

      "Content-Type : application/json",
      "Colipu-Token :".$access_token['access_token'], 

      ); 

      $result       = juhecurl_gets($url,$herders);
      
   
 
      if( $result['success'] == true )
      {
        
        // echo $order_id;
         // dump($result['result']['orderTrack'][0] );
        // $rows=db('order')->where('id = "'.$order_id.'"')->select();
        if($result['result']['orderTrack'][0]['content']!='暂无该物流信息,请稍后查询。')
        {
          

          $data=[
          'logistics_json' => json_encode($result['result']['orderTrack'])
          ];
          
          $row=db('order')->where('id = "'.$order_id.'"')->update($data);
        }
        
        

      }
      if($result['errorcode'] == '-1')
      {
         get_access_tokens(API_USERNAME,API_PASSWORD);
         $this->order_logistics();
       }

      // return true;
      
    }
}
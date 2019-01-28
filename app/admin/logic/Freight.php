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

/**
 * 运费配置控制器
 */
class Freight extends AdminBase
{


  /**
   * 获取运费配置列表
   */
  public function getFreightList($where = [], $field = 'f.*,p.gift_name', $order = 'id desc')
  {
    $this->modelFreight->alias('f');

    $join = [
                //[SYS_DB_PREFIX . 'member m', 'b.uid = m.id'],
                  [SYS_DB_PREFIX . 'project p', 'f.project_id = p.id'],
            ];

    $where['f.' . DATA_STATUS_NAME] = ['neq', DATA_DELETE];

     $this->modelFreight->join = $join;

    return $this->modelFreight->getList($where, $field, $order);
  }


  /**
   * 获取运费配置搜索条件
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

      !empty($data['search_data']) && $where['p.gift_name'] = ['like', '%'.$data['search_data'].'%'];

      return $where;
  }


  /**
   * 运费配置编辑
   */
  public function freightEdit($data = [])
  {

   
      
      if($data['user_province']!='')
      {
        $data['site_all']=$data['user_province'].','.$data['user_city'].','.$data['user_area'];
        if($data['all'] != 0)
        {
           $data['site']=$data['user_province'].',所有';
           
        }else if($data['user_city'] == '请选择')
        {
           return [RESULT_ERROR, '请选择二级地址或选择所有选项'];
        }
        else if($data['user_city'] == '市辖区' || $data['user_city'] == '市辖县')
        {
          $data['site']=$data['user_province'].','.$data['user_area'];
        }
        else
        {
          $data['site']=$data['user_province'].','.$data['user_city'];
        }
      }
      else
      {
        $data['site']='';
        unset($data['user_province']);
        unset($data['user_city']);
        unset($data['user_area']);
      }
      if( $data['site']=='')
      {
         return [RESULT_ERROR, '请选择地址！'];
      }

      if($data['price'] < 0)
      {
        return [RESULT_ERROR, '运费金额必须大于0或等于0'];
      }
      $validate_result = $this->validateFreight->scene('edit')->check($data);

      if (!$validate_result) {

          return [RESULT_ERROR, $this->validateFreight->getError()];
      }
      //验证是否有重复的信息
      $ynzheng=db('freight')->where('project_id = "'.$data['project_id'].'" and site ="'.$data['site'].'" and status = 1')->find();

      if($ynzheng&&empty($data['id']))
      {
         return [RESULT_ERROR, '不能添加重复的邮费配置'];
      }
      $url = url('freightlist');

      $result = $this->modelFreight->setInfo($data);

      $handle_text = empty($data['id']) ? '新增' : '编辑';

     //$result && action_log($handle_text, '友情链接' . $handle_text . '，name：' . $data['name']);

      return $result ? [RESULT_SUCCESS, '操作成功', $url] : [RESULT_ERROR, '无操作/误操作'];
  }


  /**
   * 运费信息
   */
  public function getFreightInfo($where = [], $field = true)
  {
        return $this->modelFreight->getInfo($where, $field);
  }


    /**
   * 运费配置删除
   */
   public function freightDel($where = [])
   {
     $result = $this->modelFreight->deleteInfo($where);

    // $result && action_log('删除', '文章分类删除，where：' . http_build_query($where));

     return $result ? [RESULT_SUCCESS, '商品分类删除成功'] : [RESULT_ERROR, $this->modelFreight->getError()];
   }

}
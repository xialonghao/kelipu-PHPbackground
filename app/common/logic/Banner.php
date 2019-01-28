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

namespace app\common\logic;

/**
 * 轮播图逻辑
 */
class Banner extends LogicBase
{
    
   /**
   * 获取轮播图列表
   */
  public function getBannerList($where = [], $field = 'm.nickname,p.id as ids,b.img_name,b.status,b.id', $order = 'b.id desc')
  {

      $this->modelBanner->alias('b');

      $join = [
                  [SYS_DB_PREFIX . 'member m', 'b.uid = m.id'],
                    [SYS_DB_PREFIX . 'picture p', 'b.thumb = p.id','left'],
              ];

      $where['b.' . DATA_STATUS_NAME] = ['neq', DATA_DELETE];

      $this->modelBanner->join = $join;

      return $this->modelBanner->getList($where, $field, $order);
  }
}

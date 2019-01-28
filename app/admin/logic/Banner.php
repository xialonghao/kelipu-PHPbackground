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
 * 轮播图控制器
 */
class Banner extends AdminBase
{

  /**
   * 获取轮播图列表
   */
  public function getBannerList($where = [], $field = 'b.sort,m.nickname,p.id as ids,b.img_name,b.status,b.id,b.cate_affiliation', $order = 'b.sort asc , b.id desc')
  {

      $this->modelBanner->alias('b');

      $join = [
                  [SYS_DB_PREFIX . 'member m', 'b.uid = m.id'],
                    [SYS_DB_PREFIX . 'picture p', 'b.thumb = p.id','left'],
                    [SYS_DB_PREFIX . 'project pr', 'b.cate_affiliation = pr.id','left'],
              ];

      $where['b.' . DATA_STATUS_NAME] = ['neq', DATA_DELETE];

      $this->modelBanner->join = $join;

      return $this->modelBanner->getList($where, $field, $order);
  }

  /**
   * lunboto信息编辑
   */
  public function bannerEdit($data = [])
  {

      if(trim($data['img_name']) =='')
      {
         return [RESULT_ERROR, '请输入正确的图片名称'];
      }
      $validate_result = $this->validateBanner->scene('edit')->check($data);

      if (!$validate_result) {

          return [RESULT_ERROR, $this->validateBanner->getError()];
      }
      // if($data['cate_affiliation'] == 0)
      // {
      //    return [RESULT_ERROR, '请选择一个项目'];
      // }

      $url = url('bannerList');

      $result = $this->modelBanner->setInfo($data);

      $handle_text = empty($data['id']) ? '新增' : '编辑';

    //  $result && action_log($handle_text, '友情链接' . $handle_text . '，name：' . $data['name']);

      return $result ? [RESULT_SUCCESS, '操作成功', $url] : [RESULT_ERROR, '无操作/误操作'];
  }


  /**
   * 轮播图信息
   */
  public function getBannerInfo($where = [], $field = true)
  {

      return $this->modelBanner->getInfo($where, $field);
  }

  /**
   * 获取轮播图列表搜索条件
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
       

      !empty($data['search_data']) && $where['b.img_name|pr.gift_name'] = ['like', '%'.$data['search_data'].'%'];

      return $where;
  }


    /**
   * 轮播图删除
   */
   public function bannerDel($where = [])
   {
     $result = $this->modelBanner->deleteInfo($where);

    // $result && action_log('删除', '文章分类删除，where：' . http_build_query($where));

     return $result ? [RESULT_SUCCESS, '轮播图删除成功'] : [RESULT_ERROR, $this->modelBanner->getError()];
   }

   
}

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
 * 商品控制器
 */
class Goods extends AdminBase
{

  /**
   * 获取商品分类列表
   */
  public function getCategoryList($where = [], $field = 'g.*,p.gift_name', $order = 'g.sort asc , g.id asc')
  {

      $this->modelGoodsCategory->alias('g');

     $join = [
                  [SYS_DB_PREFIX . 'project p', 'p.id = g.cate_affiliation','left'],
            ];
      $where['g.' . DATA_STATUS_NAME] = ['neq', DATA_DELETE];

       $this->modelGoodsCategory->join = $join;

      return $this->modelGoodsCategory->getList($where, $field, $order,30);
  }

  /**
   * 获取商品列表
   */
  public function getGoodsList($where = [], $field = 'g.goods_erp,g.goods_clp,g.sort,g.is_boutique,g.goods_repertory,g.id,g.goods_name,g.goods_price,g.goods_market_price,g.status,c.cate_name,c.tid', $order = 'g.sort asc ,g.id desc,p.gift_name,p.id')
  {
    $this->modelGoods->alias('g');

    $join = [
                //[SYS_DB_PREFIX . 'member m', 'b.uid = m.id'],
                  [SYS_DB_PREFIX . 'goods_category c', 'g.category_id = c.id','left'],

                   [SYS_DB_PREFIX . 'project p', 'p.id = c.cate_affiliation','left'],
            ];

    $where['g.' . DATA_STATUS_NAME] = ['neq', DATA_DELETE];

    $this->modelGoods->join = $join;

    return $this->modelGoods->getList($where, $field, $order,30);
  }

  /**
   * 获取商品列表搜索条件
   */
  public function getWhere($data = [])
  {

      $where = [];

      $search = array(" ","　","\n","\r","\t");
      $replace = array("","","","","");
      if(!empty($data['search_data']))
      {
           $data['search_data']= trim($data['search_data']);
      }

      
       !empty($data['project_id']) && $where['p.id'] = $data['project_id'];
      !empty($data['search_data']) && $where['c.cate_name|g.goods_name|g.goods_erp|g.goods_clp'] = ['like', '%'.$data['search_data'].'%'];
       if(!empty($data['category_id']))
      {
        $row=db('goods_category')->where('id = "'.$data['category_id'].'"')->find();
           if($row['tid']==0)
          {
            $erji_id=db('goods_category')->where('tid = "'.$data['category_id'].'"')->select();
            $arr=[];
            for ($i=0; $i <count($erji_id); $i++) { 
              $arr[]= $erji_id[$i]['id'];
            }
            $str_id=implode(',', $arr);
          
            $where['g.category_ids'] = $data['category_id'];
            !empty($data['category_id']) && $where['g.category_id'] =   ['in', $str_id];
          }else
          {
            $where['g.category_ids'] = $data['category_id'];
            !empty($data['category_id']) && $where['g.category_id'] =  $data['category_id'];
          }
      }
      return $where;
  }

   /**
   * 获取商品分类列表搜索条件
   */
  public function getWheres($data = [])
  {

      $where = [];

      // $search = array(" ","　","\n","\r","\t");
      // $replace = array("","","","","");
      if(!empty($data['search_data']))
      {
           $data['search_data']= trim($data['search_data']);
      }
      !empty($data['search_data']) && $where['p.gift_name|g.cate_name'] = ['like', '%'.$data['search_data'].'%'];

      if(!empty($data['goods_category_default']))
      {
        if($data['goods_category_default'] == 2)
        {
           $where['g.goods_category_default'] = 0;
           $where['g.goods_category_defaults'] = 2;
         }else
         {
          $where['g.goods_category_default'] =$data['goods_category_default'];
         }
      }
      return $where;
  }


  /**
   * 商品分类编辑
   */
  public function goodsCategoryEdit($data = [])
  {

    if(trim($data['cate_name'])  == '')
      {
        return [RESULT_ERROR,'请输入正确的分类名称'];
      }
      //添加二级分类时走的
      if($data['tid'] != 0)
      {
        $tid=db('goods_category')->where('id = "'.$data['tid'].'" and status = 1 ')->find();
        $data['goods_category_default']= $tid['goods_category_default'];
        $data['goods_er']= 1;
         $data['cate_affiliation']= $tid['cate_affiliation'];
          
      }
    //修改一级分类时走的
     if($data['tid'] == 0 && !empty($data['id']))
     {
 

        db('goods_category')->where(['tid' =>$data['id']])->update(['goods_category_default' => $data['goods_category_default'] ]);
     }

    
     if(!empty($data['tids']))
     {
       $url ='/klp.php/goods/goodscategorysonlist.html?tid='.$data['tids'];
       unset($data['tids']);
     }else
     {
       $url ='/klp.php/goods/categorylist.html?'.$data['url'];
       unset($data['url']);
     }
      
      $validate_result = $this->validategoodsCategory->scene('editS')->check($data);

      if (!$validate_result) {

          return [RESULT_ERROR, $this->validategoodsCategory->getError()];
      }

     // $url = url('categorylist');

      $result = $this->modelGoodsCategory->setInfo($data);

      $handle_text = empty($data['id']) ? '新增' : '编辑';

     //$result && action_log($handle_text, '友情链接' . $handle_text . '，name：' . $data['name']);

      return $result ? [RESULT_SUCCESS, '操作成功', $url] : [RESULT_ERROR, '无操作/误操作'];
  }


  /**
   * 商品编辑
   */
  public function goodsEdit($data = [])
  {
      if(trim($data['goods_name'])  == '')
      {
        return [RESULT_ERROR,'请输入正确的商品名称'];
      }

      $validate_result = $this->validateGoods->scene('edit')->check($data);

      if($data['category_id'] == '')
      {
        return [RESULT_ERROR, '请选择分类'];
      }
      if (!$validate_result) {

           return [RESULT_ERROR, $this->validateGoods->getError()];

      }

      $row=db('goods_category')->where(' id = "'.$data['category_id'].'"')->find();

      if($row['tid']==0)
      {
        return [RESULT_ERROR, '请选择二级分类'];
      }

    if(trim($data['goods_repertory'])  < 0)
    {
      return [RESULT_ERROR, '库存不能小于0'];
    }
      $url = url('goodslist');

      $result = $this->modelGoods->setInfo($data);

      $handle_text = empty($data['id']) ? '新增' : '编辑';

    //  $result && action_log($handle_text, '友情链接' . $handle_text . '，name：' . $data['name']);

      return $result ? [RESULT_SUCCESS, '操作成功', $url] : [RESULT_ERROR, '无操作/误操作'];
  }


  /**
   * 商品分类信息
   */
  public function getGoodsCategoryInfo($where = [], $field = true)
  {

      return $this->modelGoodsCategory->getInfo($where, $field);
  }

  /**
   * 商品信息
   */
  public function getGoodsInfo($where = [], $field = true)
  {
        return $this->modelGoods->getInfo($where, $field);
  }

  /**
   * 商品分类删除
   */
   public function goodsCategoryDel($where = [])
   {

      //删除分类时删除商品
     $result=$this->delCategoryGoods($where['id']);

    //删除分类时  项目包含的分类id 也被清除
     $row=db('goods_category')->where('id = "'.$where['id'].'"')->find();
     if($row['cate_affiliation'] != 0)
     {
        //查出项目
        $map['category_id'] = ['like',"%".$where['id']."%"];
        $orw=db('project')->where($map)->find();
        $arr=explode(',', $orw['category_id']);
        for ($i=0; $i <  count($arr); $i++) { 
            if($arr[$i]  == $where['id'])
            {
              unset($arr[$i]);
            }
        }
        if(count($arr)>0)
        {
          $str=implode(',',$arr);
        }
       else
       {
          $str='';
       }
      db('project')->where('id = "'.$orw['id'].'"')->update(['category_id'=>$str]);
     }
      $result = $this->modelGoodsCategory->deleteInfo($where);

    // // $result && action_log('删除', '文章分类删除，where：' . http_build_query($where));

     return $result ? [RESULT_SUCCESS, '商品分类删除成功'] : [RESULT_ERROR, $this->modelGoodsCategory->getError()];
   }


   /**
    * 联动删除分类下面的商品  
    * 方法1：删除一级分类及二级分类及所有商品
    * 方法2：删除二级分类  及 二级分类下的所有商品
    */
    public function delCategoryGoods($id)
   {
    
        $rows=db('goods_category')->where('id = "'.$id.'"')->find();
        //判断是否是1及分类 
        if($rows['tid'] !=0)
        {
          //删除 方法2 的逻辑
          $where=[
            'category_id' => $id,
           // 'status'=>1
          ];
         // $this->modelGoods->deleteInfo($where);
          db('goods')->where($where)->update(['status' => '-1']);
          return true;
        }else
        {
          //删除 方法1 的逻辑
          //第一步 统计二级分类的id  在删除一级分类下的二级分类
          $ids=[];
          $rows=db('goods_category')->field('id')->where('tid = "'.$id.'"')->select();
          for($i=0;$i<count($rows);$i++)
          {
            $ids[]=$rows[$i]['id'];
          }
          $ids= implode(',',$ids);

          $where=[
            'tid' => $id,
          //  'status'=>1
          ];
          $wheres=[
            'category_id' => ['in',$ids],
          //   'status'=>1
          ];

          db('goods_category')->where($where)->update(['status' => '-1']);
          db('goods')->where($wheres)->update(['status' => '-1']);
          // $this->modelGoodsCategory->deleteInfo($where);

          // $this->modelGoods->deleteInfo($wheres);
          return true;
        }
   } 


   /**
    * 商品删除
    */
    public function goodsDel($where = [])
    {
      $result = $this->modelGoods->deleteInfo($where);

     // $result && action_log('删除', '文章分类删除，where：' . http_build_query($where));

      return $result ? [RESULT_SUCCESS, '商品删除成功'] : [RESULT_ERROR, $this->modelGoods->getError()];
    }



 /**
   * 数据导入
   */
  public function dataImport($datas=[])
  {

    if(empty($datas['category_id']))
    {
       return  [RESULT_ERROR, '请选择正确分类']; 
    }
    $row=db('goods_category')->where('id = "'.$datas['category_id'].'"')->find();

    
    if($row['tid']==0)
    {
       return  [RESULT_ERROR, '请选择二级分类导入']; 
    }
    if($datas['files_excel'] == '')
    {
      return  [RESULT_ERROR, '请先上传文件'];
    }
   
     
    //$file=db('file')->where('id = "'.$datas['file_id'].'"')->find();
  
    $test_url= $datas['files_excel'];
 
    $file_name=explode('/', $test_url);
    $file_excel=explode('.', $file_name[count($file_name) - 1]);

    if( $file_excel[1] !='xlsx' && $file_excel[1] != 'xls')
    {
      return  [RESULT_ERROR, '请上传后缀为xlsx|xls文件2'];
    }
        $excel_data = get_excel_data($test_url);
   
  
    if (empty($excel_data[1][0])||empty($excel_data[1][1])||empty($excel_data[1][2])||empty($excel_data[1][3])||empty($excel_data[1][4])||empty($excel_data[1][5])||empty($excel_data[1][6])||empty($excel_data[1][7])||empty($excel_data[1][8]))
    {
      return  [RESULT_ERROR, '请核对模版文件,确认无误后再导入']; 

    }else
    {
      if($excel_data[1][1]!="ERP商品号"||$excel_data[1][2]!="外部商品号"||$excel_data[1][3]!="外部商品名称"||$excel_data[1][4]!="单位")
      {
        return  [RESULT_ERROR, '请核对模版文件,确认无误后再导入']; 
      }
      if($excel_data[1][0]!="礼品册批次ID"||$excel_data[1][5]!="价格")
      {
        return  [RESULT_ERROR, '请核对模版文件,确认无误后再导入']; 
      }

       if($excel_data[1][6]!="图片地址")
      {
        return  [RESULT_ERROR, '请核对模版文件,确认无误后再导入']; 
      }

       if($excel_data[1][7]!="商品详情")
      {
        return  [RESULT_ERROR, '请核对模版文件,确认无误后再导入']; 
      }
      if($excel_data[1][8]!="库存") 
      {
        return  [RESULT_ERROR, '请核对模版文件,确认无误后再导入']; 
      }
    }  
     // dump($excel_data);
     // exit();
    for ($i=2; $i <= count($excel_data) ; $i++) {
    // dump($excel_data);
    
      if($excel_data[$i][0]==''&&$excel_data[$i][1]==''&&$excel_data[$i][2]==''&&$excel_data[$i][3]==''&&$excel_data[$i][4]==''&&$excel_data[$i][5]==''&&$excel_data[$i][6]==''&&$excel_data[$i][7]==''&&$excel_data[$i][8]=='')
      {
         continue;
         //return  [RESULT_ERROR, '上传文件内含有数据不全的商品,请核对上传文件']; 
      }

      if($excel_data[$i][0]==''||$excel_data[$i][1]==''||$excel_data[$i][2]==''||$excel_data[$i][3]==''||$excel_data[$i][4]==''||$excel_data[$i][5]==''||$excel_data[$i][6]==''||$excel_data[$i][7]==''||$excel_data[$i][8]=='')
      {
        
         return  [RESULT_ERROR, '上传文件内含有数据不全的商品,请核对上传文件']; 
      }

      if($datas['category1']=='D1')
      {
         $category_arr=db('goods_category')->where('cate_affiliation=0 and status != -1  and tid !=0 ')->select();
      }
      else
      {
         $category_arr=db('goods_category')->where('cate_affiliation="'.$datas['category1'].'" and status != -1   and tid !=0 ')->select();
      }
     $strs='';
      for ($t=0; $t < count($category_arr) ; $t++) { 
        $strs.=$category_arr[$t]['id'].',';
      }
     // dump($strs);
      //$category_str=implode(',', $category_arr['id']);
       $newstr =substr($strs,0,strlen($strs)-1); 
      $row = db('goods')->where('goods_erp = "'.$excel_data[$i][1].'" and category_id in ('.$newstr.') and status != -1   ')->select();
      // dump($row);
      // exit();
      if($row)
      {
        return  [RESULT_ERROR, '上传文件内含有重复的商品,请核对上传文件']; 
      }
      $data[]=[
          'goods_batch'=>$excel_data[$i][0],
          'goods_erp'=>$excel_data[$i][1],
          'goods_clp'=>$excel_data[$i][2],
          'goods_name'=>$excel_data[$i][3],
          'goods_unit'=>$excel_data[$i][4],
          'goods_price'=>$excel_data[$i][5],
          'category_id'=>$datas['category_id'],
          'status'=>1,//是否上下架
          'goods_repertory'=>$excel_data[$i][8],//库存
          'goods_img'=>$excel_data[$i][6],
          'goods_content'=>$excel_data[$i][7],
         
      ];

    }
    // dump($data);
   
    $goods=new Goods();

    $goods::startTrans();
    try{

      $row=$goods->insertAll($data);

      if(!$row)
      {
         $goods::rollback();
         return  [RESULT_ERROR, '上传文件数据存在问题,请重新调整后上传,请认真遵守模板规则'];
      }

      // 提交事务
      $goods::commit();
//      $url = url('goodslist');
      return [RESULT_SUCCESS, '上传成功' ];

    } catch (\Exception $e) {

      // 回滚事务
     $goods::rollback();
      return  [RESULT_ERROR, '上传文件数据存在问题,请重新调整后上传,请认真遵守模板规则'];
    }
    
  }

  //二级分类返回提示
 public function status_vi()
 {
 	return  [RESULT_ERROR, '上级分类为下架,本分类不可上架'];
 }


}

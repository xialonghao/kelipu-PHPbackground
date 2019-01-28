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
//1
namespace app\admin\controller;

use think\Request;
/**
 * 商品控制器
 */
class Goods extends AdminBase
{
  /**
   * 商品分类列表
   */
  public function categoryList()
  {
      $this->assign('url_arr','');
  
  if(!empty($_SERVER['REQUEST_URI']))
    {
      if(strpos($_SERVER['REQUEST_URI'],'?') !== false)
      {

        $url = $_SERVER['REQUEST_URI'];
        $url_arr=explode('?',$url);


      $this->assign('url_arr',$url_arr[1]);
      }
  
    }

    $where = $this->logicGoods->getWheres($this->param);

    $where['tid']=0;
     $this->assign('where',$where);

    if(!empty($where['g.goods_category_defaults'] ))
    {
       unset($where['g.goods_category_defaults']);
    }

    $this->assign('list', $this->logicGoods->getCategoryList($where));
   
     // dump($where); 
     return $this->fetch('category_list');
  }

  /**
   * 商品子分类列表
   */
  public function goodscategorySonList($tid = 0)
  {

    $where['tid'] = $tid;

 $this->assign('tid', $where['tid']);
  //  $where = $this->logicGoods->getWhere($this->param);

    $this->assign('list', $this->logicGoods->getCategoryList($where));
  

     return $this->fetch('category_son_list');
  }

    /**
   * 商品分类添加
   */
  public function goodsCategoryAdd()
  {

 $this->assign('url_arr','');
 $this->assign('tids','');
     $article_category_list=db('goods_category')->where('status = 1 AND tid = 0')->order('id desc')->select();

      IS_POST && $this->jump($this->logicGoods->goodsCategoryEdit($this->param));
    //$where = $this->logicBanner->getWhere($this->param);

    //$this->assign('list', $this->logicBanner->getBannerList());
    $this->assign('article_category_list',$article_category_list);
     return $this->fetch('category_edit');
  }

  /**
   * 商品分类编辑
   */
  public function goodsCategoryEdit()
  {
  	 $this->assign('url_arr','');

     $this->assign('tids','');

    IS_POST && $this->jump($this->logicGoods->GoodsCategoryEdit($this->param));

    if(!empty($this->param['urlarr']))
    {
      $this->assign('url_arr',urldecode($this->param['urlarr']));
    }
    
    if(!empty($this->param['tid']))
    {
      $this->assign('tids',$this->param['tid']);
    }
    

    $info = $this->logicGoods->getGoodsCategoryInfo(['id' => $this->param['id']]);

    if($info['tid'] == 0)
    {
       $article_category_list=[];
    }
    else
    {

      $article_category_list=db('goods_category')->where(' status = 1 AND tid = 0 And id!= "'.$info['id'].'"')->order('id desc')->select();
     // $this->assign('tid',$info['tid']);
    }
    

    // dump($info['id']);
    // dump($article_category_list);
    $this->assign('article_category_list',$article_category_list);

    $this->assign('info', $info);

    return $this->fetch('category_edit');
  }

  /**
   * 商品分类删除
   */
   public function goodsCategoryDel($id = 0)
   {

   	   $info=$this->logicGoods->delCategoryGoods($id);

       $this->jump($this->logicGoods->goodsCategoryDel(['id' => $id]));

   }

   /**
   * 测试商品分类删除
   */
   public function goodsCategoryDelss($id = 0)
   {

       $this->jump($this->logicGoods->delCategoryGoods($id));
   }

  /**
   * 数据状态设置
   */
  public function setStatus()
  {
      //查询点击修改状态的分类
      $orw=db('goods_category')->where('id = "'.$this->param['ids'].'"')->find();

      //一级分类走
      if($orw['tid'] == 0 && $orw['status'] == 1)
      {
        db('goods_category')->where('tid = "'.$orw['id'].'"')->update(['status'=>0]);
      }
      //二级分类走
      if($orw['tid'] != 0)
      {
        $tid_data=db('goods_category')->where('id = "'.$orw['tid'].'"')->find();
        if($tid_data['status']==0)
        {
            $this->jump($this->logicGoods->status_vi());
        }
      }
      
      $this->jump($this->logicAdminBase->setStatus('GoodsCategory', $this->param));

  }

  /**
   * 数据状态设置
   */
  public function setStatustt()
  {

      $this->jump($this->logicAdminBase->setStatus('Goods', $this->param));

  }

  /**
   * 数据状态设置
   */
  public function setStatustts()
  {

      $this->jump($this->logicAdminBase->setStatusBoutique('Goods', $this->param));

  }




  /**
   * 商品列表
   */
  public function goodsList()
  {
    $project_list=db('project')->where('status = 1')->order('id desc')->select();

    $goods_category_list=db('goods_category')->where('status = 1')->order('id desc')->select();

    $data=$this->classify($goods_category_list,0,1);

    $where = $this->logicGoods->getWhere($this->param);
  
    if(!empty($where['g.category_id']))
    {
      //$where['g.category_id']=false;
      //交换身份
    $where['g.category_idss']=  $where['g.category_id'];
    $where['g.category_id']=$where['g.category_ids'];
    $this->assign('where',$where);
    unset($where['g.category_ids']);
    $where['g.category_id']= $where['g.category_idss'];
    unset($where['g.category_idss']);
    }
    else
    {
       $this->assign('where',$where);
    }
   
    $this->assign('project_list',$project_list);
    $this->assign('goods_category_list',$data);
    $this->assign('list', $this->logicGoods->getGoodsList($where));
    // dump($this->logicGoods->getGoodsList($where));
 // dump($data);
     return $this->fetch('goods_list');
  }


  /**
   * 商品添加
   */
  public function goodsAdd()
  {

    $goods_category_list=db('goods_category')->where('status = 1')->order('id desc')->select();

    $data=$this->classify($goods_category_list,0,1);

    IS_POST && $this->jump($this->logicGoods->goodsEdit($this->param));

    $this->assign('goods_category_list',$data);

    return $this->fetch('goods_edit');
  }


  /**
   * 商品编辑
   */
  public function goodsEdit()
  {
    $goods_category_list=db('goods_category')->where('status = 1')->order('id desc')->select();

    $data=$this->classify($goods_category_list,0,1);

    IS_POST && $this->jump($this->logicGoods->goodsEdit($this->param));

    $info = $this->logicGoods->getGoodsInfo(['id' => $this->param['id']]);
    !empty($info) && $info['img_ids_array'] = str2arr($info['img_ids']);
    // dump($info);
    $this->assign('goods_category_list',$data);
    $this->assign('info', $info);
    return $this->fetch('goods_edit');
  }


  /**
   * 商品删除
   */
   public function goodsDel($id = 0)
   {

       $this->jump($this->logicGoods->goodsDel(['id' => $id]));
   }
   /*
    *无限极分类
   */
   public function classify($row=array(),$pid=0,$count=0)
    {

         static $arrr=array();//静态初始化
           foreach ($row as  $v){
               if($v['tid']==$pid){
                   $v['count']=$count;
      $v['cate_name'] = '|'.str_repeat('——',$count).'-|'.$v['cate_name'];
                   $arrr[]=$v;
                   //unset($row [$v]);//去掉不再使用的
                   $this->classify($row ,$v['id'],$count+1);
               }
           }
           // print_r($arrr);
           return $arrr;
    }



 /**
   * 数据导入
   */
  public function import() 
  { 

    $data=db('project')->where('status = 1')->order('id desc')->select();

    // $data=$this->classify($goods_category_list,0,1);
    // dump($this->param);
      if(!empty($_FILES) )
    {
       $file_excel=explode('.', $_FILES['file']['name']);
       $file_Excel_hou=$file_excel[count($file_excel) - 1];
        if($file_Excel_hou != 'xlsx' && $file_Excel_hou != 'xls')
       {
          return show('300','','请上传后缀为xlsx|xls文件4');
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

    IS_POST && $this->jump($this->logicGoods->dataImport($this->param));
    
    $this->assign('project_list',$data);


    return $this->fetch('import');
  }


  public function category_select() 
  {
    if (Request::instance()->isAjax())
    {
      $str=' <option value="0">请选择</option>';
      $name=input('param.name');
      $value=input('param.value');
   //   $msg=['name'=>input('param.name'),'value'=>input('param.value')];
      if($value==false&&$name=="category1")
      {
        $msg=['msg'=>'请选择正确的参数','code'=>201,'name'=>$name];
        return $msg;
      }
       if($value==false&&$name=="category2")
      {
        $msg=['msg'=>'请选择正确的参数','code'=>202,'name'=>$name];
        return $msg;
      }
      switch ($name)
      {
      

        case 'category1':
          if($value=='D1')
          {
            $row=db('goods_category')->where('status = 1 and tid = 0 and cate_affiliation = 0 and goods_category_default = 0')->order('sort asc ,id asc')->select();
          }else
          {
            $row=db('goods_category')->where('status = 1 and tid = 0 and cate_affiliation ='.$value.' and goods_category_default = 1')->order('sort asc ,id asc')->select();
          }
          if($row)
          { 
              for ($i=0; $i <count($row) ; $i++) { 
                $str.='<option value="'.$row[$i]['id'].'">'.$row[$i]['cate_name'].'</option>';
              }
          }
          $msg=['msg'=>'1','data'=>$str,'name'=>'category2'];
          break;
        case 'category2':
          $row=db('goods_category')->where('status = 1 and tid ='.$value.'')->select();
          if($row)
          { 
              for ($i=0; $i <count($row) ; $i++) { 
                $str.='<option value="'.$row[$i]['id'].'">'.$row[$i]['cate_name'].'</option>';
              }
          }
          $msg=['msg'=>'1','data'=>$str,'name'=>'category_id'];
          break;
        case 'category3':
          $msg=['msg'=>'无操作','code'=>200];
          break;
        default:
          $msg=['msg'=>'请重新选择','code'=>500];
          break;
      }
    }else
    {
      $msg=['msg'=>'请重新选择','code'=>500];
    }
   
    return $msg;
  }
    
  	
   /**
     * 排序
     */
    public function setSort()
    {
        $this->jump($this->logicAdminBase->setSort('GoodsCategory', $this->param));
    }

    /**
     * 排序
     */
    public function setSorts()
    {
        $this->jump($this->logicAdminBase->setSort('Goods', $this->param));
    }








}

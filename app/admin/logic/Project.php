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
 * 项目控制器
 */
class Project extends AdminBase
{
 
    /**
     * 项目信息编辑
     */
    public function projectEdit($accectall = [])
    {
        if(!empty($accectall['id']))
        {
           $rows=db('project')->where('gift_name = "'.trim($accectall['gift_name']).'" and status != -1 and id != "'.$accectall['id'].'"')->find();
         }else
         {
           $rows=db('project')->where('gift_name = "'.trim($accectall['gift_name']).'" and status != -1 ')->find();
         }
       
        if($rows)
        {
          return [RESULT_ERROR,'项目名称重复,请重新确认项目名称'];
        }

         if(trim($accectall['gift_name'])  == '')
        {
         return [RESULT_ERROR,'请输入正确的项目名称'];
        }
        if(empty($accectall['category_id']))
        {
            return [RESULT_ERROR, '分类必选'];
        }

         if(trim($accectall['show_price'])!='是' && trim($accectall['show_price'])!='否' && trim($accectall['show_price'])!='中')
        {
            return [RESULT_ERROR, '请正确选择的兑换类型'];
        }
        if(trim($accectall['show_price'])=='是' )
        {
          $accectall['show_price']=0;
        }
        else if(trim($accectall['show_price'])=='否')
        {
           $accectall['show_price']=1;
        }else
        {
          $accectall['show_price']=2;
        }
        if($accectall['invoice'] !=1 && $accectall['invoice'] !=2 )
        {
            return [RESULT_ERROR, '发票类型请填写1或2'];
        }
//开始校验字段

        if($accectall['invoice']==2)
        {
          if($accectall['invoice_gift']==''||$accectall['add_auty']=='')
          { 
             return [RESULT_ERROR, '普票:请填写‘抬头’跟‘税号’'];
          }
        }
        else
        {                 //抬头                          税号                         开户行                        银行账号                   发票电话
          if($accectall['invoice_gift']==''||$accectall['add_auty']==''||$accectall['start_bank']==''||$accectall['bank_id']==''||$accectall['telephone']==''||$accectall['site']=='')
          { 
             return [RESULT_ERROR, '增票:请填写全部发票信息'];
          }
        }


//结束校验字段
        $gory = '';
        foreach($accectall['category_id'] as $k=>$v){
            $gory.=$v.',';
        }
        $arrid = substr($gory,0,-1);
        $accectall['category_id']=$arrid;

        if($accectall['open_time']=='0000-00-00'||$accectall['tie_time']=='0000-00-00'||$accectall['tie_time']==''||$accectall['tie_time']=='')
        {
           return [RESULT_ERROR, '请选择正确的时间'];
        }


         if(strtotime($accectall['open_time']) > strtotime($accectall['tie_time']))
        {
          return   [RESULT_ERROR, '请选择正确的时间范围'];
        }

        $validate_result = $this->validateProject->scene('edit')->check($accectall);
        if (!$validate_result) {

         return [RESULT_ERROR, $this->validateProject->getError()];
         }
        $url = url('index');
       //    dump($accectall);
       // exit();
        $result = $this->modelProject->setInfo($accectall);
     
        if(empty($accectall['id'])){
            $wheres['id']=['in',$accectall['category_id']];
             $wheresst['tid']=['in',$accectall['category_id']];
            $datas=[
                'cate_affiliation'=>$result
            ];
            db('goods_category')->where($wheresst)->update($datas);
            $info=db('goods_category')->where($wheres)->update($datas);

        }else{
           // echo $accectall['category_id'];exit();
         //   $str=implode(',',$accectall['category_id']);
            db('goods_category')->where('cate_affiliation = "'.$accectall['id'].'" ')->update(['cate_affiliation' => 0]);
            $wheres['id']=['in',$accectall['category_id']];
            $wheress['tid']=['in',$accectall['category_id']];
            $datas=[
                'cate_affiliation'=>$accectall['id']
            ];
            db('goods_category')->where($wheress)->update($datas);
            $info=db('goods_category')->where($wheres)->update($datas);
        }

       // $validate_result = $this->validateProject->scene('edit')->check($accectall);

       // if (!$validate_result) {

       //     return [RESULT_ERROR, $this->validateBanner->getError()];
       // }
       //  $handle_text = empty($data['id']) ? '新增' : '编辑';

        //  $result && action_log($handle_text, '友情链接' . $handle_text . '，name：' . $data['name']);

        return $result ? [RESULT_SUCCESS, '操作成功', $url] : [RESULT_ERROR, '无操作/误操作'];
    }


    /**
     * 项目删除
     */
    public function ProjectcategoryDel($where = [])
    {

      //删除项目之前  把分类都剔除
      db('goods_category')->where('cate_affiliation = "'.$where['id'].'"')->update(['cate_affiliation' => 0]);
      
      $result = $this->modelProject->deleteInfo($where);

       return $result ? [RESULT_SUCCESS,'商品分类删除成功'] : [RESULT_ERROR, '删除失败'];
    }

  /**
   * 项目信息
   */
  public function getProjectInfo($where = [], $field = true)
  {

      return $this->modelProject->getInfo($where, $field);
  }





}

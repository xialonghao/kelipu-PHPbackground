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
 * 积分卡逻辑层
 */
class Card extends AdminBase
{
 

    /**
   * 积分卡列表
   */
  public function getCardList($where = [], $field = 'c.*,p.gift_name', $order = 'c.id desc')
  {

      $this->modelCard->alias('c');

      $join = [
                  [SYS_DB_PREFIX . 'project p', 'c.project_id = p.id','left'],
              ];

      if(!empty($where['c.status']))
      {
        if($where['c.status'] != 1)
        {
          $where['c.' . DATA_STATUS_NAME] = ['neq', DATA_DELETE];
        }

      }else
      {
          if(!isset($where['c.status']))
          {
             $where['c.' . DATA_STATUS_NAME] = ['neq', DATA_DELETE];
          }
        
      }

      $this->modelCard->join = $join;

      return $this->modelCard->getList($where, $field, $order,30);
  }

   /**
   * 流水列表
   */
  public function getCardRunning($where = [], $field = '', $order = 'id desc')
  {

      // $this->modelCard->alias('c');

      // $join = [
      //             [SYS_DB_PREFIX . 'project p', 'c.project_id = p.id','left'],
      //         ];

      // $where['c.' . DATA_STATUS_NAME] = ['neq', DATA_DELETE];

      // $this->modelCard->join = $join;

      return $this->modelCardRunning->getList($where, $field, $order,30);
  }

  /**
   * 获取积分卡流水列表搜索条件
   */
  public function getWheres($data = [])
  {

      $where = [];

      $search = array(" ","　","\n","\r","\t");
      $replace = array("","","","","");
      if(!empty($data['search_data']))
      {
           $data['search_data']= trim($data['search_data']);;
      }

      !empty($data['search_data']) && $where['order_sn'] = ['like', '%'.$data['search_data'].'%'];

      !empty($data['card_id']) && $where['card_id'] =$data['card_id'];

      return $where;
  }
  /**
   * 获取积分卡列表搜索条件
   */
  public function getWhere($data = [])
  {
 $where = [];

      $search = array(" ","　","\n","\r","\t");
      $replace = array("","","","","");
      if(!empty($data['search_data']))
      {
           $data['search_data']= str_replace($search,$replace,$data['search_data']);
      }

      !empty($data['search_data']) && $where['p.gift_name|c.gift_number|c.mobile'] = ['like', '%'.$data['search_data'].'%'];

      !empty($data['project_id']) && $where['p.id'] =$data['project_id'];
      if(!empty($data['card_status']))
      {
        if($data['card_status']==1||$data['card_status']==2)
        {
          if($data['card_status']==2)
          {
             $where['c.status'] = 0;
             $where['c.statuse'] = 2;
          }else
          {
             $where['c.status'] = $data['card_status'];
          }
         
        }else
        {
          if($data['card_status']==3)
          {
            $where['c.money_start'] =0;
             $where['c.money_starte'] = 3;
          }else
          {
            $where['c.money_start'] =1;
          }
          
        }
         
        
      }
      
      return $where;
  }



  /**
   * 积分卡编辑
   */
  public function cardEdit($data = [])
  {

      if(!empty($data['id']))
      {
         $row=db('card')->where('gift_number = "'.$data['gift_number'].'" and status != -1 and id != "'.$data['id'].'"')->find();
      }
     else
     {
       $row=db('card')->where('gift_number = "'.$data['gift_number'].'" and status != -1 ')->find();
     }
      if($row)
      {
        return [RESULT_ERROR,'不得修改为重复积分卡号'];
      }
      if(trim($data['gift_number'])  == '')
      {
        return [RESULT_ERROR,'请输入正确的积分卡编号'];
      }

      if($data['mobile']  != '')
      {
        if(!preg_match("/^1[3456789]\d{9}$/", $data['mobile'])){
            return [RESULT_ERROR,'请输入正确的手机号'];
        }else
        {
          $row=db('card')->where('mobile = "'.$data['mobile'].'" and id != "'.$data['id'].'" and status != -1 ')->find();
          if(time()> strtotime($row['tie_time']."+1 day"))
          {
              db('card')->where('id ="'.$row['id'].'"')->update(['mobile'=>'']);
          }else if($row)
          {
            return   [RESULT_ERROR, '手机号已被绑定,请重新填写一个'];
          }
        }
          
      }
     

      $validate_result = $this->validateCard->scene('edit')->check($data);

      if (!$validate_result) {

           return [RESULT_ERROR, $this->validateCard->getError()];

      }

  	  if(strtotime($data['open_time']) > strtotime($data['tie_time']))
  	  {
  	  		return   [RESULT_ERROR, '请选择正确的时间范围'];
  	  }

      $url = url('cardlist');

      $result = $this->modelCard->setInfo($data);

      $handle_text = empty($data['id']) ? '新增' : '编辑';

    //  $result && action_log($handle_text, '友情链接' . $handle_text . '，name：' . $data['name']);

      return $result ? [RESULT_SUCCESS, '操作成功', $url] : [RESULT_ERROR, '无操作/误操作'];
  }

  /**
   * 积分卡信息
   */
  public function getCardInfo($where = [], $field = true)
  {
      return $this->modelCard->getInfo($where, $field);
  }


   /**
    * 积分卡删除
    */
    public function cardDel($where = [])
    {
      $result = $this->modelCard->deleteInfo($where);

     // $result && action_log('删除', '文章分类删除，where：' . http_build_query($where));

      return $result ? [RESULT_SUCCESS, '积分卡删除成功'] : [RESULT_ERROR, $this->modelGoods->getError()];
    }


    /**
    * 积分卡增减积分
    */
    public function cardIncrease($where = [])
    {
      if(empty($where['integral'])||empty($where['id']))
      {
        return  [RESULT_ERROR, '系统繁忙，请重新进入此页面'];
      }
      //查出此卡的 余额 字段integral
      $row=db('card')->where('id = "'.$where['id'].'" ')->find();

      $integral= round($where['integral'],2); 
      

      if( $integral==0)
      {
         return  [RESULT_ERROR, '请输入正确的积分,大于0或小于0'];
      }
      //计算余额
      $money= round($row['money'],2) + $integral;

      //判断积分是否够减去的积分  不能为负数
      if($money<1)
      {
        return  [RESULT_ERROR, '积分不足，无法消减'];
      }
      if($integral<0)
      {
         $integral= explode('-',$integral);
         $integral = $integral[1];
      }

      $data=[
        'card_id' => $where['id'],
        'integral_before'=>round($row['money'],2),
        'integral'=>$integral,
        'integral_stop'=> $money,
        'create_time'=>time(),
        'operator' =>session('member_info')['id'],
      ];


      if($money<round($row['money'],2))
      {
        $data['marked'] = '扣除:积分("'.$integral.'")';
         action_log('积分操作', '扣除:积分("'.$integral.'")');
         
      }
      else
      {
        $data['marked'] = '馈赠:积分("'.$integral.'")';
        action_log('积分操作', '馈赠:积分("'.$integral.'")');
        $data['is_add'] = 0;
      }

      db('card')->where('id = "'.$where['id'].'" ')->update(['money'=>$money]);
      db('card_running')->insert($data);
      
      $url=url('cardlist');
    
      return [RESULT_SUCCESS, '操作成功', $url];
    }


    /**
     * 数据导入
     */
    public function dataImport($datas=[])
    {
    	
        $data=[];
         $url = url('cardlist');
        if($datas['project_id'] == '')
        {
          return  [RESULT_ERROR, '请选择项目'];
        }
      //  $file=db('file')->where('id = "'.$datas['file_id'].'"')->find();
        if($datas['files_excel'] == '')
        {
          return  [RESULT_ERROR, '请先上传文件'];
        }
        $test_url= $datas['files_excel'];
        $file_name=explode('/', $test_url);
        $file_excel=explode('.', $file_name[count($file_name) - 1]);

        if( !empty($file_excel[1] )!= 'xlsx' && $file_excel[1] != 'xls')
        {
          return  [RESULT_ERROR, '请上传后缀为xlsx|xls文件'];
        }
    

        $excel_data = get_excel_data($test_url);
     
       // DUMP($excel_data);
       // exit();

       if(empty($excel_data[1][0])||empty($excel_data[1][2])||empty($excel_data[1][3])||empty($excel_data[1][4])||empty($excel_data[1][5])||empty($excel_data[1][6])||empty($excel_data[1][7]))
        {
           return  [RESULT_ERROR, '请核对模版文件,确认无误后再导入'];
        }
        else
        {
            if($excel_data[1][1]!='礼品册编号')
            {
               return  [RESULT_ERROR, '请核对模版文件,确认无误后再导入'];
            }

            if($excel_data[1][0]==''||$excel_data[1][2]!='兑换密码'||$excel_data[1][3]!='金额'||$excel_data[1][4]!='开始时间'||$excel_data[1][5]!='结束时间')
            {
               return  [RESULT_ERROR, '请核对模版文件,确认无误后再导入'];
            }

            if($excel_data[1][1]!=='礼品册编号')
            {
               return  [RESULT_ERROR, '请核对模版文件,确认无误后再导入'];
            }

            if($excel_data[1][7]!=='手机号')
            {
            	return  [RESULT_ERROR, '请核对模版文件,确认无误后再导入'];
            }

        }
          
        for ($i=2;$i <=count($excel_data);$i++) {
  
          if($excel_data[$i][0]==''&&$excel_data[$i][1]==''&&$excel_data[$i][2]==''&&$excel_data[$i][3]==''&&$excel_data[$i][4]==''&&$excel_data[$i][5]==''&&$excel_data[$i][6]=='')
          {
             continue;
             //return  [RESULT_ERROR, '上传文件内含有数据不全的商品,请核对上传文件']; 
          }

          if($excel_data[$i][0]&&$excel_data[$i][1]&&$excel_data[$i][2]&&$excel_data[$i][3]&&$excel_data[$i][4]&&$excel_data[$i][5]&&$excel_data[$i][6])
            {
              $row=db('card')->where('gift_number = "'.$excel_data[$i][1].'" and status != -1 ')->find();
              if($row)
              {
                return  [RESULT_ERROR, '上传文件中含有已存在的卡号,请重新核对上传文件'];
              }
              if(!empty($excel_data[$i][7]))
              {
                if(!preg_match("/^1[3456789]\d{9}$/", $excel_data[$i][7])){
                    return [RESULT_ERROR,'上传文件中手机号格式有误,请重新核对上传文件'];
                }
              	 $rowss=db('card')->where('mobile = "'.$excel_data[$i][7].'" and status != -1 ')->find();
             
              	 if($rowss)
	              {
                  if(time()> strtotime($rowss['tie_time']."+1 day"))
                  {
                      db('card')->where('id ="'.$rowss['id'].'"')->update(['mobile'=>'']);
                  }
                  else
                  {
                    return  [RESULT_ERROR, '上传文件中含有已绑定的手机号,请重新核对上传文件'];
                  }
	                
	              }
	              $i7=$excel_data[$i][7];
              }
              else
              {
              	$i7='';
              }
                $excel_datas[$i][4]=explode('/', $excel_data[$i][4]);
                $excel_data[$i][4]=implode('-', $excel_datas[$i][4]);

                $excel_datas[$i][5]=explode('/', $excel_data[$i][5]);
                $excel_data[$i][5]=implode('-', $excel_datas[$i][5]);

                $data[]=[
                    'project_id'=>$datas['project_id'],
                    'gift_id'=>$excel_data[$i][0],
                    'gift_number'=>$excel_data[$i][1],
                    'password'=>$excel_data[$i][2],
                    'money'=>$excel_data[$i][3],
                    'bei_money'=>$excel_data[$i][3],
                    'open_time'=>$excel_data[$i][4],
                    'tie_time'=>$excel_data[$i][5],
                    'exchange'=>$excel_data[$i][6],
                    'mobile'=>$i7
                ];
            }
            else
            {
                return  [RESULT_ERROR, '上传文件内含有数据不全的积分卡,请核对上传文件']; 
            }
        }
      

        if(!$data)
        {
             return  [RESULT_ERROR, '上传数据有误，请核对后重新上传'];
        }
       
        $card=new Card();

        $card::startTrans();
        try{
            $row=$card->insertAll($data);

            if(!$row)
            {
                $card::rollback();
                return  [RESULT_ERROR, '上传数据有误，请核对后重新上传'];
            }

            // 提交事务
            $card::commit();
             
            return [RESULT_SUCCESS, '上传成功', $url];

        } catch (\Exception $e) {
            // 回滚事务
            $card::rollback();
            return  [RESULT_ERROR, '上传数据有误，请核对后重新上传'];
        }

    }


}

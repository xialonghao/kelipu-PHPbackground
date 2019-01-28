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
 * 订单控制器
 */
class AddressK extends AdminBase
{


	 /**
     * 数据导入
     */
    public function dataImport($datas=[])
    {
        $data=[];

        $file=db('file')->where('id = "'.$datas['file_id'].'"')->find();
        $test_url= '../public/upload/file/'.$file['path'];
        $excel_data = get_excel_data($test_url);
      // echo  134217728/1024/1024;
    // dump($excel_data);
    //     exit();
        // if($excel_data[1][0]==''||$excel_data[1][2]!=='兑换密码')
        // {
        //    return  [RESULT_ERROR, '请上传正确文件'];
        // }
        // for ($i=2;$i < count($excel_data);$i++) {
        //     if($excel_data[$i][0]&&$excel_data[$i][1])
        //     {
        //         $excel_datas[$i][4]=explode('/', $excel_data[$i][4]);
        //         $excel_data[$i][4]=implode('-', $excel_datas[$i][4]);

        //         $excel_datas[$i][5]=explode('/', $excel_data[$i][5]);
        //         $excel_data[$i][5]=implode('-', $excel_datas[$i][5]);

        //         $data[]=[
        //             'project_id'=>$datas['project_id'],
        //             'gift_id'=>$excel_data[$i][0],
        //             'gift_number'=>$excel_data[$i][1],
        //             'password'=>$excel_data[$i][2],
        //             'money'=>$excel_data[$i][3],
        //             'open_time'=>$excel_data[$i][4],
        //             'tie_time'=>$excel_data[$i][5],
        //             'exchange'=>$excel_data[$i][6],
        //         ];
        //     }
        // }
        try{
          

            for ($i=2;$i < count($excel_data);$i++) {
              if($excel_data[$i][0]&&$excel_data[$i][1])
              {
                  $data[]=[
                      'tuid'=>$excel_data[$i][0],
                      'number'=>$excel_data[$i][1],
                      'name'=>$excel_data[$i][2],
                  ];
              }
          }

         } catch (\Exception $e) {
            // 回滚事务
            echo $i;
           dump( $e->getMessage());   
           exit();
        }
       

        if(!$data)
        {
             return  [RESULT_ERROR, '请上传文件'];
        }
       
        $AddressK=new AddressK();
    //   $arr= $card->where('id != 1 ')->delete();
       // dump($data);
    // exit();
        $AddressK::startTrans();
        $da=$AddressK->select();
      
        try{
            $datas=array_chunk($data,40);
          
            for ($s=0; $s < count($datas) ; $s++) { 
           //   dump($datas[$s]);
              
              $row=$AddressK->insertAll($datas[$s]);
            }
          
            
           

            if(!$row)
            {
                $card::rollback();
                return  [RESULT_ERROR, '上传失败,请重新调整后上传'];
            }

            // 提交事务
            $AddressK::commit();
             $url = url('cardlist');
           return [RESULT_SUCCESS, '上传成功', $url];

        } catch (\Exception $e) {
            // 回滚事务
             dump( $e->getMessage());   
           exit();
            $AddressK::rollback();
            return  [RESULT_ERROR, '上传失败,请重新调整后上传'];
        }

    }
}

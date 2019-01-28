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

namespace app\admin\validate;

/**
 * 文章分类验证器
 */
class Project extends AdminBase
{

    // 验证规则
    protected $rule =   [
     
        'gift_name'            =>'require|max:200',
        'start_bank'           =>'max:200',
        'bank_id'              =>'max:200',
        'invoice_gift'         =>'max:200',
        'telephone'            =>'max:200',
        'quantity'             =>'max:200',
        // 'add_auty'             =>'require',
        'outerfirm_number'     =>'max:200',
        'outerfirm_middle'     =>'max:200',
        'invoice'              =>'require|max:1',
        'site'                 =>'max:200',

    ];
    //  // 验证规则
    // protected $rule =   [
     
    //     'gift_name'            =>'require|max:50',
    //     'start_bank'           =>'require|max:50',
    //     'bank_id'              =>'require|max:100',
    //     'invoice_gift'         =>'require|max:50',
    //     'telephone'            =>'max:80',
    //     'quantity'             =>'require|max:10',
    //     // 'add_auty'             =>'require',
    //     'outerfirm_number'     =>'require|max:100',
    //     'outerfirm_middle'     =>'require|max:100',
    //     'invoice'              =>'require|max:1',
    //     'site'                 =>'max:100',

    // ];

    // 验证提示
    protected $message = [
        // 'gift_name.require'        => '项目名称必填',
        'gift_name.max'        => '项目名称请填写少于200位',
        // 'start_bank.require'       => '开户银行必填',
        'start_bank.max'       => '开户银行请填写少于200位',
        // 'bank_id.require'          => '银行账号必填',
        'bank_id.max'          => '银行账号请填写少于200位',
        // 'invoice_gift.require'     => '发票抬头必填',
         'invoice_gift.max'     => '发票抬头请填写少于200位',
      //  'telephone.require'        => '发票电话必填',
        'telephone.max'        => '发票电话请填写少于200位',
        // 'quantity.require'         => '发行数量必填',
        'quantity.max'         => '发行数量最大值是200位',
      //  'add_auty.require'         => '增税必填',
        // 'add_auty.max'         => '增税请填写少于20位',
        // 'outerfirm_number.require' => '外部公司编号必填',
       'outerfirm_number.max' => '外部公司编号请填写少于200位',
        // 'outerfirm_middle.require' => '外部成本中心编号必填',
        'outerfirm_middle.max' => '外部成本中心编号请填写少于200位',
        'invoice.require'          => '发票类型必填',
        'invoice.max'          => '发票类型请填写1或2',
       // 'site.require'             => '发票地址必填',
        'site.max'             => '发票地址请填写少于200位',
    ];

    
    // 应用场景
    protected $scene = [
        'edit'  =>  ['category_id','gift_name','start_bank','bank_id','invoice_gift','telephone','quantity','outerfirm_number','outerfirm_middle','invoice','site'],
    ];
}

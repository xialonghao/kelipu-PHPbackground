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

namespace app\admin\controller;

/**
 * 项目控制器
 */
class Project extends AdminBase
{

    /**
     * 项目列表
     */
    public function index()
    {
        $info = db('project')->where('status = 1')->order('id desc')->paginate(10, false, ['var_page'=>'p']);
        $this->assign('list',$info);
        return $this->fetch('project_list');
    }


 
    // /**
    //  * 项目编辑
    //  */
    // public function articleEdit()
    // {

    //     $this->articleCommon();

    //     $info = $this->logicArticle->getArticleInfo(['a.id' => $this->param['id']], 'a.*,m.nickname,c.name as category_name');

    //     !empty($info) && $info['img_ids_array'] = str2arr($info['img_ids']);

    //     $this->assign('info', $info);

    //     return $this->fetch('article_edit');
    // }

  
    /**
     * 项目分类添加
     */
    public function articleCategoryAdd()
    {
        IS_POST && $this->jump($this->logicProject->projectEdit($this->param));
        $db=db('goods_category');
        $region=$db->where('status = 1 AND tid =0 AND cate_affiliation = 0 AND goods_category_default= 1')->select();

        $this->assign('region',$region);
        return $this->fetch('project_edit');
    }


    /**
     * 项目编辑
     */
    public function projectCategoryEdit()
    {

         IS_POST && $this->jump($this->logicProject->projectEdit($this->param));

        $info = $this->logicProject->getProjectInfo(['id' => $this->param['id']]);
        $db=db('goods_category');


        $region=$db->where('status = 1 AND tid =0 AND cate_affiliation = 0 AND goods_category_default= 1 or status = 1 AND tid =0 AND cate_affiliation = "'.$this->param['id'].'" AND goods_category_default= 1 ')->select();

        $this->assign('region',$region);
        
       // dump($region);

        $this->assign('info', $info);

        return $this->fetch('category_edit');
    }

    /**
     * 项目分类列表
     */
    public function articleCategoryList()
    {

        $this->assign('list', $this->logicArticle->getArticleCategoryList());
        return $this->fetch('article_category_list');
    }

    /**
     * 项目分类删除
     */
    public function ProjectCategoryDel($id = 0)
    {


        $this->jump($this->logicProject->ProjectcategoryDel(['id' => $id]));
    }

    /**
     * 数据状态设置
     */
    public function setStatus()
    {

        $this->jump($this->logicAdminBase->setStatus('Article', $this->param));
    }
}

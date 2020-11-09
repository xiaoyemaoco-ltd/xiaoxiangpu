<?php
namespace app\admin\controller\product;
use app\common\controller\Backend;
use app\admin\model\AdminLog;
class Spec extends Backend{
    protected $categoryModel = null;
    protected $SpecInfo = null;
    public function initialize(){
        parent::initialize(); // TODO: Change the autogenerated stub
        $this -> model = new \app\common\model\Spec();
        $this -> SpecInfo = new \app\common\model\SpecInfo();
//        $this->assign('category', $cate);
    }
    public function index(){
        if ($this->request->isAjax()) {
            [$where, $sort, $order, $offset, $limit] = $this->buildparams();
            $total = $this -> model
                ->where($where)
                ->order($sort, $order)
                ->count();
            $list = $this -> model
                ->where($where)
                ->order($sort, $order)
                ->limit($offset, $limit)
                ->select() -> toArray();
            $result = ['total' => $total, 'rows' => $list];
            return json($result);
        }
        return $this -> fetch();
    }

    public function add(){
        if($this->request->isPost()){
            $params = $this->request->post('row/a');
            $params['createtime'] = time();
            $result = $this->model->save($params);
            if ($result === false) {
                $this->error($this->model->getError());
            }
            $this->success();
        }
        return $this-> fetch();
    }

    /**
     * 分类编辑
     * @param null $ids
     * @return string
     * @throws \Exception
     */
    public function edit ($ids = null){
        $row = $this->model->find($ids);
        if (! $row) {
            $this->error(__('No Results were found'));
        }
        if ($this->request->isPost()) {
            $params = $this->request->post('row/a');
            $params['updatetime'] = time();
            $result = $row->save($params);
            if ($result === false) {
                $this->error($row->getError());
            }
            $this->success();
        }
        $this->assign('row', $row);
        return $this->fetch();
    }

    /**
     * 删除类目
     * @param string $ids
     * @throws \Exception
     */
    public function del ($ids = ''){
        if ($ids) {
            $this->model -> destroy($ids);
//            $this -> SpecInfo -> where('spec_id',$ids) -> delete();
            $this->success();
        }
        $this->error();
    }


}
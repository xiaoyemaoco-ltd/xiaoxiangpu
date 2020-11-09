<?php
/**
 * Created by PhpStorm.
 * User: LENOVO
 * Date: 2020/10/5
 * Time: 22:06
 */
namespace app\admin\controller\product;

use app\common\controller\Backend;
use app\Request;
use think\facade\Db;

class Category extends Backend
{
    protected $model = null;

    public function initialize()
    {
        parent::initialize(); // TODO: Change the autogenerated stub
        $this->model = new \app\admin\model\Category();
    }

    public function index (Request $request)
    {
        if ($request->isAjax()) {
            if ($request->request('keyField')) {
                return $this->selectpage();
            }
            [$where, $sort, $order, $offset, $limit] = $this->buildparams();
            $total = $this->model
                ->where($where)
                ->where('status', 1)
                ->count();
            $list = $this->model
                ->where($where)
                ->where('status', 1)
                ->order('createtime', 'desc')
                ->select();
            $result = ['total' => $total, 'rows' => $list];
            return json($result);
        }
        return $this-> fetch();
    }

    /**
     * 添加分类
     * @return string
     * @throws \Exception
     */
    public function add(){
        if ($this->request->isPost()) {
            $params = $this->request->post('row/a');
            if($params){
                $this->model->cate_name = $params['cate_name'];
                if ($params['status'] == 'normal') {
                    $this->model->status = 1;
                } else {
                    $this->model->status  = 9;
                }
                $this->model->createtime = time();
                $result = $this->model->save();
                if ($result) {
                    $this->success();
                } else {
                    $this->error();
                }
            }
            $this->error();
        }
        return $this-> fetch();
    }

    /**
     * 分类编辑
     * @param null $ids
     * @return string
     * @throws \Exception
     */
    public function edit ($ids = null)
    {
        $row = $this->model->find($ids);
        if (! $row) {
            $this->error(__('No Results were found'));
        }

        if ($this->request->isPost()) {
            $params = $this->request->post('row/a');
            if ($params['status'] == 'normal') {
                $params['status'] = 1;
            } else {
                $params['status'] = 9;
            }
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
    public function del ($ids = '')
    {
        if ($ids) {
            $this->model->destroy($ids);
            $this->success();
        }
        $this->error();
    }
}
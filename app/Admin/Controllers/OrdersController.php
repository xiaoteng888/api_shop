<?php

namespace App\Admin\Controllers;

use App\Models\Order as modelOrder;
use App\Admin\Repositories\Order;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;
use Dcat\Admin\Layout\Content;
use Illuminate\Http\Request;
use App\Exceptions\InvalidRequestException;
use Illuminate\Foundation\Validation\ValidatesRequests;
use App\Http\Requests\Admin\HandleRefundRequest;
use App\Exceptions\InternalException;

class OrdersController extends AdminController
{
    use ValidatesRequests;
    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(new Order(['user']), function (Grid $grid) {
            $grid->model()->whereNotNull('paid_at')->orderBy('paid_at', 'desc');
            $grid->column('no');
            $grid->column('user.name','姓名');            
            $grid->column('total_amount');            
            $grid->column('paid_at')->display(function($v){
                return $v;
            });            
            $grid->column('refund_status')->display(function($v){
                return modelOrder::$refundStatusMap[$v];
            });            
            $grid->column('ship_status')->display(function($v){
                return modelOrder::$shipStatusMap[$v];
            });
            $grid->disableCreateButton();
            $grid->actions(function ($actions) {
                // 禁用删除和编辑按钮
                $actions->disableDelete();
                $actions->disableEdit();
            });    
            // 禁用批量删除按钮
            $grid->disableBatchDelete();

            $grid->filter(function (Grid\Filter $filter) {
                $filter->equal('id');
        
            });
        });
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     *
     * @return Show
     */
    public function show($id,Content $content)
    {  
        return $content->header('订单')
            ->description('查看订单')
            ->body(view('admin.orders.show',['order' => modelOrder::find($id)]));
    }

    /*protected function detail($id)
    {
        return Show::make($id, new Order(), function (Show $show) {
            $show->no('订单流水号');
            $show->paid_at('支付时间');
            $show->payment_method('支付方式');
            $show->payment_no('支付渠道单号');
            $show->address('地址');
            $show->total_amount('订单金额');
        });
    }*/

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return Form::make(new Order(), function (Form $form) {
            $form->display('id');
            $form->text('no');
            $form->text('user_id');
            $form->text('address');
            $form->text('total_amount');
            $form->text('remark');
            $form->text('paid_at');
            $form->text('payment_method');
            $form->text('payment_no');
            $form->text('refund_status');
            $form->text('refund_no');
            $form->text('closed');
            $form->text('reviewed');
            $form->text('ship_status');
            $form->text('ship_data');
            $form->text('extra');
        
            $form->display('created_at');
            $form->display('updated_at');
        });
    }

    public function ship(modelOrder $order,Request $request)
    {
        // 判断当前订单是否已支付
        if(!$order->paid_at){
            throw new InvalidRequestException('订单未支付');
        }
        // 判断当前订单发货状态是否为未发货
        if($order->ship_status !== modelOrder::SHIP_STATUS_PENDING){
            throw new InvalidRequestException('该订单已发货');
        }
        // Laravel 5.5 之后 validate 方法可以返回校验过的值
        $data = $this->validate($request,[
            'express_company' => ['required'],
            'express_no' => ['required'],
        ],[],[
            'express_company' => '物流公司',
            'express_no' => '物流单号',
        ]);
        // 将订单发货状态改为已发货，并存入物流信息
        $order->update([
            'ship_status' => modelOrder::SHIP_STATUS_DELIVERED,
            'ship_data' => $data,
        ]);
        return redirect()->back();
    }

    public function handleRefund(modelOrder $order,HandleRefundRequest $request)
    {
        // 判断订单状态是否正确
        if($order->refund_status !== modelOrder::REFUND_STATUS_APPLIED){
            throw new InvalidRequestException('该订单状态不正确');
        }
        // 是否同意退款
        if($request->input('agree')){
            // 清空拒绝退款理由
            $extra = $order->extra ? : [];
            unset($extra['refund_disagree_reason']);
            $order->update([
                'extra' => $extra,
            ]);
            //调用退款逻辑
            $this->_refundOrer($order);
        }else{
            // 将拒绝退款理由放到订单的 extra 字段中
            $extra = $order->extra ? : [];
            $extra['refund_disagree_reason'] = $request->input('reason');
            // 将订单的退款状态改为未退款
            $order->update([
                'refund_status' => modelOrder::REFUND_STATUS_PENDING,
                'extra' => $extra,
            ]);
        }
        return $order;
    }

    public function _refundOrer(modelOrder $order)
    {
        // 判断该订单的支付方式
        switch($order->payment_method){
            case 'wechat':
            // 微信的先留空
                // todo
                 break;
            case 'alipay':
            // 用我们刚刚写的方法来生成一个退款订单号
            $refund_no = modelOrder::getAvailableRefundNo();
            // 调用支付宝支付实例的 refund 方法
            $res = app('alipay')->refund([
                'out_trade_no' => $order->no,
                'refund_amount' => $order->total_amount,
                'out_request_no' => $refund_no
            ]);
            // 根据支付宝的文档，如果返回值里有 sub_code 字段说明退款失败
            if($res->sub_code){
                // 将退款失败的保存存入 extra 字段
                $extra = $order->extra;
                $extra['refund_faild_code'] = $res->sub_code;
                // 将订单的退款状态标记为退款失败
                $order->update([
                    'refund_status' => modelOrder::REFUND_STATUS_FAILED,
                    'extra' => $extra,
                    'refund_no' => $refund_no,
                ]);
            }else{
                // 将订单的退款状态标记为退款成功并保存退款订单号
                $order->update([
                    'refund_status' => modelOrder::REFUND_STATUS_SUCCESS,
                    'refund_no' => $refund_no,
                ]);
            }
            break;
            default:
            // 原则上不可能出现，这个只是为了代码健壮性
            throw new InternalException('未知订单支付方式:'.$order->payment_method);
            break;
        }
    }
}

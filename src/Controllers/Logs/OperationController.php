<?php

namespace Wiledia\Backport\Controllers\Logs;

use Wiledia\Backport\Auth\Database\OperationLog;
use Wiledia\Backport\Grid;
use Wiledia\Backport\Layout\Content;
use Illuminate\Routing\Controller;

class OperationController extends Controller
{
    /**
     * Index interface.
     *
     * @param Content $content
     *
     * @return Content
     */
    public function index(Content $content)
    {
        return $content
            ->header(trans('admin.operation_log'))
            ->description(trans('admin.list'))
            ->body($this->grid());
    }

    /**
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new OperationLog());

        $grid->model()->orderBy('id', 'DESC');

        $grid->id('ID')->sortable()->sortable();
        $grid->user()->name('User');
        $grid->method()->display(function ($method) {
            $color = \Illuminate\Support\Arr::get(OperationLog::$methodColors, $method, 'grey');

            return "<span class=\"badge badge-$color\">$method</span>";
        });
        $grid->path()->badge('secondary');
        $grid->ip()->badge('dark');
        $grid->input()->display(function ($input) {
            $input = json_decode($input, true);
            $input = \Illuminate\Support\Arr::except($input, ['_pjax', '_token', '_method', '_previous_']);
            if (empty($input)) {
                return '<code>{}</code>';
            }

            return '<pre>'.htmlentities(json_encode($input, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)).'</pre>';
        });

        $grid->created_at(trans('admin.created_at'));

        $grid->actions(function (Grid\Displayers\Actions $actions) {
            $actions->disableEdit();
            $actions->disableView();
        });

        $grid->disableCreation();

        $grid->filter(function ($filter) {
            $userModel = config('backport.database.users_model');

            $filter->equal('user_id', 'User')->select($userModel::all()->pluck('name', 'id'));
            $filter->equal('method')->select(array_combine(OperationLog::$methods, OperationLog::$methods));
            $filter->like('path');
            $filter->equal('ip');
        });

        return $grid;
    }

    /**
     * @param mixed $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $ids = explode(',', $id);

        if (OperationLog::destroy(array_filter($ids))) {
            $data = [
                'status'  => true,
                'message' => trans('admin.delete_succeeded'),
            ];
        } else {
            $data = [
                'status'  => false,
                'message' => trans('admin.delete_failed'),
            ];
        }

        return response()->json($data);
    }
}

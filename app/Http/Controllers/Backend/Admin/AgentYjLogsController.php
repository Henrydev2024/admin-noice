<?php

namespace App\Http\Controllers\Backend\Admin;

use App\Models\Agent;
use App\Models\AgentYjLog;
use App\Services\AgentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class AgentYjLogsController extends AdminBaseController
{
    protected $create_field = ['agent_id', 'yl_money', 'money', 'last_month', 'remark'];
    protected $update_field = ['agent_id', 'yl_money', 'money', 'last_month', 'remark'];

    public function __construct(AgentYjLog $model)
    {
        if (App::runningInConsole()) {
            return;
        }

        $this->model = $model;
        parent::__construct();

        // 判断是否是传统代理模式
        app(AgentService::class)->checkTraditional();
    }

    public function index(Request $request)
    {
        $params = $request->all();
        $data = $this->model->where($this->convertWhere($params))->latest()->paginate(request('per_page', apiPaginate()));
        return view('admin.agentyjlog.index', compact('data', 'params'));
    }

    public function history(Agent $agent, Request $request)
    {
        $data = $this->model->where('agent_id', $agent->id)->latest()->paginate(request('per_page', apiPaginate()));
        return view('admin.agentyjlog.history', compact('data', 'agent'));
    }
}

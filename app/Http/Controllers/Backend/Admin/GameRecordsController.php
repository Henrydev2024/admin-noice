<?php

namespace App\Http\Controllers\Backend\Admin;

use App\Exports\ReportDetailExport;
use App\Exports\ReportExport;
use App\Models\BetHistories;
use App\Models\GameRecord;
use App\Models\Member;
use App\Models\TransactionHistory;
use App\Services\MemberService;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class GameRecordsController extends AdminBaseController
{
    private $memberService;

    protected $create_field = ['billno', 'api_name', 'name', 'betAmount', 'validBetAmount', 'netAmount', 'gameType', 'flag', 'betTime'];
    protected $update_field = ['billno', 'api_name', 'name', 'betAmount', 'validBetAmount', 'netAmount', 'gameType', 'flag', 'betTime'];

    public function __construct(GameRecord $model)
    {
        $this->model = $model;
        $this->memberService = app(MemberService::class);
        parent::__construct();
    }

    public function index(Request $request)
    {
        $params = $request->all();
        $query = BetHistories::with('member', 'apiGame');

        if (request('member_name')) {
            $query->where('member_name', 'like', '%' . request('member_name') . '%');
        }

        if (request('member_id')) {
            $query->where('member_id', request('member_id'));
        }

        if (request('result_bet_status')) {
            $query->where('result_bet_status', request('result_bet_status'));
        }

        if (request('api_name')) {
            $query->where('api_name', request('api_name'));
        }

        if (request('bet_product')) {
            $query->where('bet_product', request('bet_product'));
        }

        $data = $query->orderBy(BetHistories::getTableName() . '.id', 'desc')->paginate(request('per_page', apiPaginate()));

        return view('admin.gamerecord.index', compact('data', 'params'));
    }

    public function destroy(Request $request, $id)
    {
        $id = $request->get("ids") ?? $id;

        if (BetHistories::whereIn('id', $id)->delete()) {
            return $this->success(["reload" => true], trans('res.base.delete_success'));
        }

        return $this->failed(trans('res.base.delete_fail'));
    }

    public function report(Request $request)
    {
        // get member list with histories
        $members = app(Member::class)->getSboHistories($request->all());

        foreach ($members->items() as $member) {
            $histories = $member->sboRecords;
            $totalRecords = 0;
            $totalWin = 0;
            $totalLoss = 0;
            $totalAmount = 0;
            $totalAmountWin = 0;
            $totalAmountLoss = 0;
            $totalFsSbo = 0;

            // fs refund
            $fsInfo = $this->memberService->getFsSbo($member, ['is_fs_all' => true]);
            $fsSbo = data_get($fsInfo, 'data');

            $fsInfo = $this->memberService->getFsSboSaba($member, ['is_fs_all' => true]);
            $fsSboSaba = data_get($fsInfo, 'data');

            $fsInfo = $this->memberService->getFsSboAfb($member, ['is_fs_all' => true]);
            $fsSboAfb = data_get($fsInfo, 'data');

            $fsInfo = $this->memberService->getFsSboBti($member, ['is_fs_all' => true]);
            $fsSboBti = data_get($fsInfo, 'data');

            $fsSbo->map(function ($item) use (&$totalFsSbo) {
                $totalFsSbo += $item->fs_money;
            });

            $fsSboSaba->map(function ($item) use (&$totalFsSbo) {
                $totalFsSbo += $item->fs_money;
            });

            $fsSboAfb->map(function ($item) use (&$totalFsSbo) {
                $totalFsSbo += $item->fs_money;
            });

            $fsSboBti->map(function ($item) use (&$totalFsSbo) {
                $totalFsSbo += $item->fs_money;
            });

            foreach ($histories as $history) {
                if (!in_array($history->status, [TransactionHistory::STATUS_WIN, TransactionHistory::STATUS_LOST])) {
                    continue;
                }

                if ($history->status == TransactionHistory::STATUS_WIN) {
                    $totalWin += 1;
                    $totalAmountWin += $history->win_loss - $history->amount;
                }

                if ($history->status == TransactionHistory::STATUS_LOST) {
                    $totalLoss += 1;
                    $totalAmountLoss += $history->amount;
                }

                $totalRecords += 1;
                $totalAmount += $history->amount;
            }

            $member->total_records = $totalRecords;
            $member->total_win = $totalWin;
            $member->total_loss = $totalLoss;

            $member->total_amount = $totalAmount;
            $member->total_amount_win = $totalAmountWin;
            $member->total_amount_loss = $totalAmountLoss;

//            $member->total_fs_money = $totalFsSbo;

            $member->total_fs_money = abs($totalAmountWin - $totalAmountLoss)*0.0175;
        }
        if($request->excel == 1){
            return Excel::download(new ReportExport($members), 'report.xlsx');
        }

        $viewData = [
            'data' => $members,
            'params' => $request->all(),
        ];

        return view('admin.gamerecord.report', $viewData);
    }

    public function reportDetail(Request $request, $memberId)
    {
        $params = $request->all();
        $params['member_id'] = $memberId;
        $from = data_get($params, 'created_at_from');
        $to = data_get($params, 'created_at_to');
        $productType = data_get($params, 'product_type');
        $perPage = data_get($params, 'per_page', apiPaginate());

        $transactionHistoryModel = TransactionHistory::with('member', 'apiGame')
            ->where('member_id', $memberId)
            ->whereIn('status', [TransactionHistory::STATUS_WIN, TransactionHistory::STATUS_LOST])
            // filter by transaction_time
            ->when($from, function ($query) use ($from) {
                $query->where('transaction_time', '>=', $from);
            })
            ->when($to, function ($query) use ($to) {
                $query->where('transaction_time', '<=', $to);
            })

            // filter by product_type
            ->when($productType, function ($query) use ($productType) {
                $query->where('product_type', $productType);
            });
        $list_data = $transactionHistoryModel->orderBy(TransactionHistory::getTableName() . '.id', 'desc')->paginate($perPage);
        foreach ($list_data as $val){
            $val->commission_refund = $val->amount * 0.0175;
            $val->game_provider_text = $val->getGameProviderText();
            $val->product_type_text = $val->getProductTypeText();
        }

        if ($request->excel == 1){
            return Excel::download(new ReportDetailExport($list_data), 'report-detail.xlsx');
        }
        $viewData = [
            'data' => $list_data,
            'params' => $request->all(),
            'memberId' => $memberId,
        ];

        return view('admin.gamerecord.report_detail', $viewData);
    }

    public function betting(Request $request)
    {
        // get member list with histories
        $members = app(Member::class)->getBettingSboHistories($request->all());

        foreach ($members->items() as $member) {
            $histories = $member->sboRecords;
            $totalRecords = 0;
            $totalAmount = 0;

            foreach ($histories as $history) {
                if (!in_array($history->status, [TransactionHistory::STATUS_WAITING])) {
                    continue;
                }

                $totalRecords += 1;
                $totalAmount += $history->amount;
            }

            $member->total_records = $totalRecords;
            $member->total_amount = $totalAmount;
        }

        $viewData = [
            'data' => $members,
            'params' => $request->all(),
        ];

        return view('admin.gamerecord.betting', $viewData);
    }

    public function bettingDetail(Request $request, $memberId)
    {
        $params = $request->all();
        $params['member_id'] = $memberId;
        $from = data_get($params, 'created_at_from');
        $to = data_get($params, 'created_at_to');
        $productType = data_get($params, 'product_type');
        $perPage = data_get($params, 'per_page', apiPaginate());

        $transactionHistoryModel = TransactionHistory::with('member', 'apiGame')
            ->where('member_id', $memberId)
            ->whereIn('status', [TransactionHistory::STATUS_WAITING])
            // filter by transaction_time
            ->when($from, function ($query) use ($from) {
                $query->where('transaction_time', '>=', $from);
            })
            ->when($to, function ($query) use ($to) {
                $query->where('transaction_time', '<=', $to);
            })

            // filter by product_type
            ->when($productType, function ($query) use ($productType) {
                $query->where('product_type', $productType);
            });

        $viewData = [
            'data' => $transactionHistoryModel->orderBy(TransactionHistory::getTableName() . '.id', 'desc')->paginate($perPage),
            'params' => $request->all(),
        ];

        return view('admin.gamerecord.betting_detail', $viewData);
    }

    public function getBetDetail(Request $request, $id)
    {
        $response = app(Client::class)->request('POST', route('api.sbo.bet.detail'), [
            'json' => ['id' => $id]
        ]);
        $response = $response->getStatusCode() == 200 ? json_decode($response->getBody()->getContents(), true) : null;

        $viewData = [
            'url' => data_get($response, 'url'),
        ];

        return view('admin.gamerecord.get_bet_detail', $viewData);
    }
}

@extends('layouts.baseframe')

@section('title', $_title)

@section('css')
    <style>
        .table-hover > tbody >tr:hover {
            background-color: #f8eb95;
        }
    </style>
@endsection

@php
    $queryParams = request()->all();
    $reportPage = data_get($queryParams, 'page');
    $queryParams['report_page'] = $reportPage;
    unset($queryParams['page']);
@endphp

@section('content')
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header">
                <h4>{{ $_title }}</h4>
                <ul class="card-actions">
                    <li>
                        <button type="button" data-toggle="collapse" href="#searchContent" aria-expanded="false" aria-controls="searchContent"><i class="mdi mdi-chevron-double-down"></i> @lang('res.btn.collapse')</button>
                    </li>
                    <li>
                        <button type="button" onclick="window.location.reload()"><i class="mdi mdi-refresh"></i> @lang('res.btn.refresh')</button>
                    </li>
                </ul>
            </div>
            <div class="card-body collapse in" id="searchContent" aria-expanded="true">
                <form action="" method="get" id="searchForm" name="searchForm">
                    <div class="row">
                        @include('layouts._search_field',
                        [
                        'list' => [
                            'name' => ['name' => trans('res.member.field.name'), 'type' => 'text', 'col' => 'col-sm-3'],
                            'created_at' => ['name' => trans('res.transaction_history.field.created_at'), 'type' => 'datetime-from-to', 'col' => 'col-sm-3'],
                            'product_type' => ['name' => trans('res.transaction_history.field.product_type'), 'type' => 'select', 'data' => \App\Models\TransactionHistory::getProductType(), 'col' => 'col-sm-3'],
                            ]
                        ])

                        <div class="col-sm-6">
                            <div class="input-group">
                                <input type="text" value="" name="page" id="page_size" hidden>
                                <button type="submit" class="btn btn-primary" name="excel" value="0">@lang('res.btn.search')</button>&nbsp;
                                <button type="reset" class="btn btn-warning" onclick="window.location.href='{{ route('admin.gamerecords.report') }}'">@lang('res.btn.reset')</button>
                                <button type="submit" class="btn btn-success" style="margin-left: 10px" name="excel" value="1">Xuất excel</button>&nbsp;
                            </div>
                        </div>
                    </div>
                </form>

            </div>
        </div>

        <div class="card">
            <div class="card-body">
                @include('layouts._per_page')

                @include('layouts._paging')

                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover">
                        <thead>
                        <tr>
                            <th rowspan="2" class="align-middle bg-primary">{{ trans('res.transaction_history.field.member_id') }}</th>
                            <th colspan="3" class="text-center bg-primary">Thống kê lệnh cược</th>
                            <th colspan="5" class="text-center bg-primary">Thống kê tiền cược</th>
                        </tr>
                        <tr>
                            <th class="text-center bg-primary">Số lượng cược</th>
                            <th class="text-center bg-primary">Thắng</th>
                            <th class="text-center bg-primary">Thua</th>
                            <th class="text-center bg-primary">Tổng tiền cược</th>
                            <th class="text-center bg-primary">Thắng</th>
                            <th class="text-center bg-primary">Thua</th>
                            <th class="text-center bg-primary">Thắng/Thua</th>
                            <th class="text-center bg-primary">Hoàn trả</th>
                        </tr>
                        </thead>

                        <tbody>
                        @foreach ($data as $key => $item)
                            <tr>
                                <td><a href="{{ route('admin.gamerecords.report_detail', array_merge($queryParams, ['member_id' => $item->id])) }}">{{ $item->name }}</a></td>
                                <td class="text-right">{{ number_format($item->total_records) }}</td>
                                <td class="text-right">{{ number_format($item->total_win) }}</td>
                                <td class="text-right">{{ number_format($item->total_loss) }}</td>
                                <td class="text-right">{{ moneyFormat($item->total_amount) }}</td>
                                <td class="text-right">{{ moneyFormat($item->total_amount_win) }}</td>
                                <td class="text-right">{{ moneyFormat($item->total_amount_loss) }}</td>
                                <td class="text-right">{!! $item->getWinLossDiff($item->total_amount_win, $item->total_amount_loss) !!}</td>
                                <td class="text-right">{{ moneyFormat($item->total_fs_money) }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>

                @include('layouts._paging')
            </div>
        </div>
    </div>
@endsection
<script>
    document.addEventListener("DOMContentLoaded", function() {
        var currentPage = document.querySelector('.pagination .page-item.active');
        var currentPageNumber = currentPage.textContent.trim();
        document.getElementById('page_size').value = currentPageNumber;
    });
</script>

@extends('layouts.baseframe')

@section('title', $_title)

@section('css')
    <style>
        .table-hover > tbody > tr:hover {
            background-color: #f8eb95;
        }
    </style>
@endsection

@php
    $backParams = request()->all();
    $reportPage = data_get($backParams, 'report_page');
    $backParams['page'] = $reportPage;
    unset($backParams['report_page']);
@endphp

@section('content')
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header">
                <h4>{{ $_title }}</h4>
                <ul class="card-actions">
                    <li>
                        <button type="button" onclick="window.location.href='{{ route('admin.gamerecords.report', $backParams) }}'">
                            <i class="mdi mdi-skip-backward"></i> @lang('res.btn.back')
                        </button>
                    </li>
                </ul>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                @include('layouts._per_page')
                <form action="{{url('admin/gamerecords/report/detail/'.$memberId)}}" method="get" >
                    <input type="text" value="" name="page" id="page_size" hidden>
                    <button type="submit" class="btn btn-success" style="margin-left: 10px" name="excel" value="1">Xuất excel</button>&nbsp;
                </form>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover">
                        <thead>
                            <tr>
                                <th class="text-center align-middle bg-primary">{{ trans('res.transaction_history.field.time') }}</th>
                                <th class="text-center align-middle bg-primary">{{ trans('res.transaction_history.field.game') }}</th>
                                <th class="text-center align-middle bg-primary">{{ trans('res.transaction_history.field.amount') }}</th>
                                <th class="text-center align-middle bg-primary">{{ trans('res.transaction_history.field.win_loss') }}</th>
                                <th class="text-center align-middle bg-primary">{{ trans('res.transaction_history.field.status') }}</th>
                                <th class="text-center align-middle bg-primary">{{ trans('res.transaction_history.field.fs_detail') }}</th>
                                <th class="text-center align-middle bg-primary">{{ trans('res.transaction_history.field.balance_before') }}</th>
                                <th class="text-center align-middle bg-primary">{{ trans('res.transaction_history.field.balance_after') }}</th>
                            </tr>
                        </thead>

                        <tbody>
                        @foreach ($data as $key => $item)
                            <tr>
                                <td>
                                    <p>Ref Code: {{ $item->transfer_code }}</p>
                                    <p>{{ $item->transaction_time }}</p>
                                </td>
                                <td>
{{--                                    <p style="font-weight: bold;color: #b50000;">{!! $item->getGameTypeText() !!}</p>--}}
                                    <p style="font-weight: bold;color: #33cabb;">{!! $item->getGameProviderText() !!}</p>
                                    <p style="font-weight: bold;color: #4d5259;">{!! $item->getProductTypeText() !!}</p>
                                    <a href="javascript:void(0);" data-operate="iframe-page"
                                       data-url="{{ route('admin.gamerecords.getBetDetail', ['id' => $item->id]) }}"
                                       class="btn btn-warning btn-xs">
                                        Xem chi tiết
                                    </a>
                                </td>
                                <td class="text-right">{{ moneyFormat($item->amount) }}</td>
                                <td class="text-right">{!! $item->getWinLossText() !!}</td>
                                <td class="text-center">{!! $item->getStatusText() !!}</td>
                                <td class="text-left">{{ moneyFormat($item->commission_refund) }}</td>
                                <td class="text-right">{{ moneyFormat($item->balance_before) }}</td>
                                <td class="text-right">{{ moneyFormat($item->balance_after) }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>

                @if($data)
                    <div class="clearfix">
                        <div class="pull-left">
                            <p>@lang('res.common.total') <strong style="color: red">{{ $data->total() }}</strong> @lang('res.common.count')</p>
                        </div>
                        <div class="pull-right">
                            {!! $data->appends($params)->render() !!}
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h4>{{ $_title }}</h4>
                <ul class="card-actions">
                    <li>
                        <button type="button" onclick="window.location.href='{{ route('admin.gamerecords.report') }}'">
                            <i class="mdi mdi-skip-backward"></i> @lang('res.btn.back')
                        </button>
                    </li>
                </ul>
            </div>
        </div>
    </div>
@endsection
<script>
    document.addEventListener("DOMContentLoaded", function() {
        var currentPage = document.querySelector('.pagination .page-item.active');
        var currentPageNumber = currentPage.textContent.trim();
        document.getElementById('page_size').value = currentPageNumber;

        // var select = document.querySelector('.per_page');
        // var inputPage = document.getElementById('page_size');
        // select.addEventListener('change', function() {
        //     // Gán giá trị của select vào input
        //     inputPage.value = this.value;
        // });
    });
</script>

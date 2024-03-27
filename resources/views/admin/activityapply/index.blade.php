@extends('layouts.baseframe')

@section('title', $_title)

@section('content')
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header">
                <h4>{{ $_title }}</h4>
                <ul class="card-actions">
                    <li>
                        <button type="button" data-toggle="collapse" href="#searchContent" aria-expanded="false"
                                aria-controls="searchContent">
                            <i class="mdi mdi-chevron-double-down"></i> @lang('res.btn.collapse')
                        </button>
                    </li>
                    <li>
                        <button type="button" onclick="javascript:window.location.reload()">
                            <i class="mdi mdi-refresh"></i> @lang('res.btn.refresh')
                        </button>
                    </li>
                </ul>
            </div>
            <div class="card-body collapse in" id="searchContent" aria-expanded="true">
                <form action="" method="get" id="searchForm" name="searchForm">
                    <div class="row">
                        @include('layouts._search_field',
                        [
                        'list' => [
                            'member_lang' => ['name' => trans('res.member.field.lang'),'type' => 'select','data' => config('platform.lang_select')],
                            'member_name' => ['name' => trans('res.common.member_name'),'type' => 'text'],
                            'created_at' => ['name' => trans('res.common.created_at'),'type' => 'datetime']
                            ]
                        ])

                        <div class="col-lg-2 col-sm-2">
                            <div class="input-group">
                                <button type="submit" class="btn btn-primary">@lang('res.btn.search')</button>&nbsp;
                                <button type="reset" class="btn btn-warning"
                                        onclick="document.searchForm.reset()">@lang('res.btn.reset')</button>&nbsp;
                            </div>
                        </div>
                    </div>
                </form>

            </div>
        </div>

        <div class="card">
            <div class="card-toolbar clearfix">
                <div class="toolbar-btn-action">
                    {{-- <a class="btn btn-success m-r-5" href="#!"><i class="mdi mdi-check"></i> 启用</a>
                    <a class="btn btn-warning m-r-5" href="#!"><i class="mdi mdi-block-helper"></i> 禁用</a> --}}
                    <a class="btn btn-danger" id="batchDelete" data-operate="delete" data-url="/admin/activityapplies/ids">
                        <i class="mdi mdi-window-close"></i> @lang('res.btn.delete')
                    </a>
                </div>
            </div>

            <div class="card-body">
                @include('layouts._per_page')

                @include('layouts._paging')

                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                        <tr>
                            <th>
                                <label class="lyear-checkbox checkbox-primary">
                                    <input type="checkbox" id="check-all"><span></span>
                                </label>
                            </th>
                            <th width=120>@lang('res.activity.field.title')</th>
                            @include('layouts._table_header',['data' => \App\Models\ActivityApply::$list_field,'model' => 'activity_apply'])
                            <th width=100>@lang('res.common.updated_at')</th>
                            <th width=100>@lang('res.common.created_at')</th>
                            <th>@lang('res.common.operate')</th>
                        </tr>
                        </thead>

                        <tbody>
                        @foreach ($data as $item)
                            <tr>
                                <td>
                                    <label class="lyear-checkbox checkbox-primary">
                                        <input type="checkbox" name="ids[]" value="{{ $item->id }}"><span></span>
                                    </label>
                                </td>
                                <td title="{{ $item->activity->title ?? '' }}">{{ string_limit($item->activity->title ?? trans('res.common.deleted'),20) }}</td>
                                @include('layouts._table_body',['data' => \App\Models\ActivityApply::$list_field,'item' => $item])
                                <td>{{ $item->updated_at }}</td>
                                <td>{{ $item->created_at }}</td>
                                <td>

                                    @if($item->status == App\Models\ActivityApply::STATUS_NOT_DEAL)
                                        <a class="btn btn-xs btn-round btn-success" href="javascript:;"
                                           data-operate="iframe-page"
                                           data-message="@lang('res.activity_apply.index.notice_confirm')"
                                           data-url="{{ route('admin.activityapplies.confirm',['activityapply' => $item->id,'status' => App\Models\ActivityApply::STATUS_ENSURE]) }}"
                                           data-toggle="tooltip" data-original-title="@lang('res.activity_apply.index.btn_confirm')" data-title="@lang('res.activity_apply.index.btn_confirm')">
                                            <i class="mdi mdi-check"></i>
                                        </a>

                                        <a class="btn btn-xs btn-round btn-danger" href="javascript:;"
                                           data-operate="iframe-page"
                                           data-message="@lang('res.activity_apply.index.btn_reject')"
                                           data-url="{{ route('admin.activityapplies.confirm',['activityapply' => $item->id,'status' => App\Models\ActivityApply::STATUS_REJECT]) }}"
                                           data-toggle="tooltip" data-original-title="@lang('res.activity_apply.index.btn_reject')" data-title="@lang('res.activity_apply.index.btn_reject')">
                                            <i class="mdi mdi-close"></i>
                                        </a>
                                    @elseif($item->status == App\Models\ActivityApply::STATUS_ENSURE)

                                        <a class="btn btn-xs btn-round btn-info" href="javascript:;"
                                           data-toggle="tooltip" data-original-title="@lang('res.activity_apply.index.btn_bonus')"
                                           data-title="@lang('res.activity_apply.index.btn_bonus')" data-operate="iframe-page"
                                           data-url="{{ route('admin.activityapplies.bonus',['activityapply' => $item->id]) }}"
                                        >
                                            <i class="mdi mdi-coin"></i>
                                        </a>
                                    @endif

                                    @if($item->status != App\Models\ActivityApply::STATUS_NOT_DEAL)
                                        <a class="btn btn-xs btn-round btn-warning" href="javascript:;" data-operate="show-page"
                                           data-toggle="tooltip" data-original-title="@lang('res.btn.detail')"
                                           data-url="{{ route('admin.activityapplies.show', ['activityapply' => $item->id]) }}">
                                            <i class="mdi mdi-file-document-box"></i>
                                        </a>
                                    @endif

                                    <div class="btn-group">
                                        {{-- <a class="btn btn-xs btn-default"
                                            href="{{ route('admin.activityapplies.confirm',['activityapply' => $item->id]) }}" title=""
                                            data-toggle="tooltip" data-original-title="编辑"><i
                                                class="mdi mdi-pencil"></i></a> --}}

                                        {{-- <a class="btn btn-xs btn-default" href="javascript:;" data-operate="show-page"
                                            data-toggle="tooltip" data-original-title="详情"
                                            data-url="{{ route('admin.activityapplies.show', ['activityapply' => $item->id]) }}">
                                        <i class="mdi mdi-file-document-box"></i>
                                        </a> --}}

                                        {{-- <a class="btn btn-xs btn-default" href="javascript:;" data-operate="delete"
                                            data-toggle="tooltip" data-original-title="删除"
                                            data-url="{{ route('admin.activityapplies.destroy', ['activityapply' => $item->id]) }}">
                                            <i class="mdi mdi-window-close"></i>
                                        </a> --}}
                                    </div>
                                </td>
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

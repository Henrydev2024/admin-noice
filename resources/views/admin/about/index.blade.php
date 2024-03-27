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
                                'title' => ['name' => trans('res.about.field.title'),'type' => 'text'],
                                'type' => ['name' => trans('res.about.field.type'),'type' => 'select','data' => trans('res.option.about_type')],
                                'lang' => ['name' => trans('res.about.field.lang'),'type' => 'select','data' => config('platform.lang_fields')],
                                //'status' => ['name' => '用户状态','type' => 'select','data' => \App\Models\User::$statusMap],
                                'created_at' => ['name' => trans('res.common.created_at'),'type' => 'datetime']
                            ]
                        ])

                        <div class="col-lg-3 col-sm-3">
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
                    <a class="btn btn-primary m-r-5" href="{{ route("admin.abouts.create") }}"><i class="mdi mdi-plus"></i>
                        @lang('res.btn.add')</a>
                    {{-- <a class="btn btn-success m-r-5" href="#!"><i class="mdi mdi-check"></i> 启用</a>
                    <a class="btn btn-warning m-r-5" href="#!"><i class="mdi mdi-block-helper"></i> 禁用</a> --}}
                    <a class="btn btn-danger" id="batchDelete" data-operate="delete" data-url="/admin/abouts/ids">
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
                            @include('layouts._table_header',['data' => \App\Models\About::$list_field,'model' => 'about'])
                            <th>@lang('res.common.created_at')</th>
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
                                @include('layouts._table_body',['data' => \App\Models\About::$list_field,'item' => $item])

                                <td>{{ $item->created_at }}</td>

                                <td>
                                    <div class="btn-group">
                                        <a class="btn btn-xs btn-default"
                                           href="{{ route('admin.abouts.edit',['about' => $item->id]) }}" title=""
                                           data-toggle="tooltip" data-original-title="@lang('res.btn.edit')"><i
                                                    class="mdi mdi-pencil"></i></a>

                                        {{-- <a class="btn btn-xs btn-default" href="javascript:;" data-operate="show-page"
                                            data-toggle="tooltip" data-original-title="详情"
                                            data-url="{{ route('admin.abouts.show', ['about' => $item->id]) }}">
                                        <i class="mdi mdi-file-document-box"></i>
                                        </a> --}}

                                        <a class="btn btn-xs btn-default" href="javascript:;" data-operate="delete"
                                           data-toggle="tooltip" data-original-title="@lang('res.btn.delete')"
                                           data-url="{{ route('admin.abouts.destroy', ['about' => $item->id]) }}">
                                            <i class="mdi mdi-window-close"></i>
                                        </a>
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

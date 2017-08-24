@inject('module', 'scaffold.module')
@inject('columns', 'scaffold.columns')
@inject('actions', 'scaffold.actions')
@inject('filter', 'scaffold.filter')
@inject('template', 'scaffold.template')
@inject('sortable', 'scaffold.sortable')

@extends($template->layout())

@section('total')
    <sup class="small">({{ $items->total() }})</sup>
@endsection

@include($template->index('create'))
@include($template->index('filters'))

@section('scaffold.batch')
    <div class="row">
        <div class="col-sm-12 people-list">
            <div class="batch-options nomargin">
                @include($template->index('batch'))
                @include($template->index('scopes'))
            </div>
        </div>
    </div>
@endsection

@section('scaffold.content')
    <div class="panel">
        <div class="panel-body">
            <form method="post" id="collection" action="{{ route('scaffold.batch', ['page' => $module]) }}">
                <?=Form::hidden('batch_action', null, ['id' => 'batch_action'])?>
                <?=Form::token()?>
                <table class="table table-bordered">
                    <thead>
                    <tr>
                        <th width="10">
                            <label for="toggle_collection_{{ $key = mb_strtolower(str_random(5)) }}">
                                <input type="checkbox" class="simple toggle-collection" id="toggle_collection_{{ $key }}"/>
                            </label>
                        </th>
                        @each($template->index('header'), $columns, 'column')
                        <th class="actions" style="width: 10%; vertical-align: baseline">{{ trans('administrator::module.actions') }}</th>
                    </tr>
                    </thead>

                    <tbody>
                    @each($template->index('row'), $items, 'item')
                    </tbody>

                    @if ($items && count($items) > 10)
                        <tfoot>
                        <tr>
                            <th width="10">
                                <label for="toggle_collection_{{ $key = mb_strtolower(str_random(5)) }}">
                                    <input type="checkbox" class="simple toggle-collection" id="toggle_collection_{{ $key }}"/>
                                </label>
                            </th>
                            @each($template->index('header'), $columns, 'column')
                            <th class="actions" style="width: 10%; vertical-align: baseline">{{ trans('administrator::module.actions') }}</th>
                        </tr>
                        </tfoot>
                    @endif
                </table>
            </form>
            
            <?php
                $exportable = method_exists($module, 'formats') ? $module->formats() : [];
                $paginable  = method_exists($items, 'hasPages') ? $items->hasPages() : false;
            ?>
            @if ($exportable || $paginable)
            <div class="row">
                <div class="col-md-6 mt20">
                    @include($template->index('export'))
                </div>
                <div class="col-md-6 text-right">
                    @include($template->index('paginator'))
                </div>
            </div>
            @endif
        </div>
    </div>
@endsection
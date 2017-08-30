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
    @component('administrator::components.index.index', ['module' => $module, 'items' => $items])
        @slot('checkboxes')
            @if($actions->batch()->count())
                <th width="10">
                    <label for="toggle_collection_{{ $key = mb_strtolower(str_random(5)) }}">
                        <input type="checkbox"
                               class="simple toggle-collection"
                               id="toggle_collection_{{ $key }}"
                        />
                    </label>
                </th>
            @endif
        @endslot

        @slot('headers')
            @each($template->index('header'), $columns, 'column')
        @endslot

        @slot('actions')
            @unless($actions->readonly())
                <th class="actions" style="width: 10%; vertical-align: baseline">
                    {{ trans('administrator::module.actions') }}
                </th>
            @endunless
        @endslot

        @slot('rows')
            @foreach($items as $item)
                @include($template->index('row'))
            @endforeach
        @endslot

        @slot('exportable')
            @if ($exportable = method_exists($module, 'formats') && $module->formats())
                @include($template->index('export'))
            @endif
        @endslot

        @slot('paginator')
            @if (method_exists($items, 'hasPages') && $items->hasPages())
                @include($template->index('paginator'))
            @endif
        @endslot
    @endcomponent
@endsection
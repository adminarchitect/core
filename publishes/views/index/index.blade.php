@extends($template->layout())

@inject('template', 'scaffold.template')
@inject('sortable', 'scaffold.sortable')

@php($actions = $resource->actionsManager())
@php($columns = $resource->columns())

@section('total')
    <sup class="small">({{ $items->total() }})</sup>
@endsection

@include($template->index('create'))
@include($template->index('filters'))

@section('scaffold.batch')
    @if ($columns->count())
        <div class="row">
            <div class="col-sm-12 people-list">
                <div class="batch-options nomargin">
                    @include($template->index('batch'))
                    @include($template->index('scopes'))
                </div>
            </div>
        </div>
    @endif
@endsection

@section('scaffold.cards')
    @include('administrator::dashboard.widgets', ['widgets' => app('scaffold.cards')])
@endsection

@section('scaffold.content')
    @if ($columns->count())
        @component('administrator::components.index.index', ['module' => $resource, 'items' => $items])
            @slot('checkboxes')
                @if($actions->batch()->count() && !$actions->readonly())
                    <th width="10">
                        <label for="toggle_collection_{{ $key = mb_strtolower(\Illuminate\Support\Str::random(5)) }}">
                            <input type="checkbox"
                                   class="simple toggle-collection"
                                   id="toggle_collection_{{ $key }}"
                            />
                        </label>
                    </th>
                @endif
            @endslot

            @slot('headers')
                @each($template->index('header'), $columns->visibleOnPage('index'), 'column')
            @endslot

            @slot('actions')
                @unless($actions->readonly())
                    <th class="actions" style="width: 100px; min-width: 100px;"></th>
                @endunless
            @endslot

            @slot('rows')
                @foreach($items as $item)
                    @include($template->index('row'))
                @endforeach
            @endslot

            @slot('exportable')
                @if ($exportable = method_exists($resource, 'formats') && $resource->formats())
                    @include($template->index('export'))
                @endif
            @endslot

            @slot('paginator')
                @if (method_exists($items, 'hasPages') && $items->hasPages())
                    @include($template->index('paginator'))
                @endif
            @endslot

            @slot('gridBefore')
                {!! $resource->gridBefore() !!}
            @endslot

            @slot('gridAfter')
                {!! $resource->gridAfter() !!}
            @endslot
        @endcomponent
    @else
        @component('administrator::components.index.index', ['module' => $resource, 'items' => []])
            @slot('rows')
                Can not generate table. Not enough data.
            @endslot
        @endcomponent
    @endif
@endsection

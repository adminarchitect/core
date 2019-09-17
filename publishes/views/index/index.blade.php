@extends($resource->template()->layout())

@php($actions = $resource->actions())
@php($columns = $resource->columns())

@section('total')
    <sup class="small">({{ $items->total() }})</sup>
@endsection

@include($resource->template()->index('create'))
@include($resource->template()->index('filters'))

@section('scaffold.batch')
    @if ($columns->count())
        <div class="row">
            <div class="col-sm-12 people-list">
                <div class="batch-options nomargin">
                    @include($resource->template()->index('batch'))
                    @include($resource->template()->index('scopes'))
                </div>
            </div>
        </div>
    @endif
@endsection

@section('scaffold.cards')
    @include('administrator::dashboard.widgets', ['widgets' => $resource->cards()])
@endsection

@section('scaffold.content')
    @if ($columns->count())
        @component('administrator::components.index.index', ['resource' => $resource, 'items' => $items])
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
                @foreach($columns->visibleOnPage('index') as $column)
                    @include($resource->template()->index('header'), [
                        'column' => $column,
                        'resource' => $resource,
                    ])
                @endforeach
            @endslot

            @slot('actions')
                @unless($actions->readonly())
                    <th class="actions" style="width: 100px; min-width: 100px;"></th>
                @endunless
            @endslot

            @slot('rows')
                @foreach($items as $item)
                    @include($resource->template()->index('row'))
                @endforeach
            @endslot

            @slot('exportable')
                @if ($exportable = method_exists($resource, 'formats') && $resource->formats())
                    @include($resource->template()->index('export'))
                @endif
            @endslot

            @slot('paginator')
                @if (method_exists($items, 'hasPages') && $items->hasPages())
                    @include($resource->template()->index('paginator'))
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
        @component('administrator::components.index.index', ['resource' => $resource, 'items' => []])
            @slot('rows')
                Can not generate table. Not enough data.
            @endslot
        @endcomponent
    @endif
@endsection

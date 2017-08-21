@inject('module', 'scaffold.module')
<?php
$elements = $filter->filters();
$resetUrl = method_exists($module, 'defaultRoute') ? $module->defaultRoute() : route('scaffold.index', ['module' => $module]);

$filled = $elements ? $elements->reduce(function ($filled, $element) {
    if ($element->getInput()->getValue()) {
        $filled++;
    };

    return $filled;
}, 0) : 0;

$hasFilters = ($resetUrl != request()->fullUrl()) && $filled;
?>

@if ($filter && $elements && $elements->count())
@section('scaffold.filter')
    <div class="panel">
        <ul class="panel-options">
            <li><a class="panel-minimize"><i class="fa fa-chevron-down"></i></a></li>
        </ul>
        <div class="panel-heading">
            <h4 class="panel-title panel-minimize">{{ trans('administrator::module.filters') }}</h4>
        </div>
        <div class="panel-body" style="display: {{ ($hasFilters ? 'block' : 'none') }};">
            <form action="" data-id="filter-form" class="form">
                <input type="hidden" name="sort_by" value="{{ $sortable->element() }}"/>
                <input type="hidden" name="sort_dir" value="{{ $sortable->direction() }}"/>
                @if ($scope = $filter->scope())
                    <input type="hidden" name="scoped_to" value="{{ $scope }}"/>
                @endif

                @if ($magnet = app('scaffold.magnet'))
                    @foreach ($magnet->toArray() as $key => $value)
                        @if ($filter->has($key))
                            @continue;
                        @endif
                        <input type="hidden" name="{{ $key }}" value="{{ $value }}"/>
                    @endforeach
                @endif

                <div class="scaffold-filters">
                    <div class="inputs">
                        @foreach($elements as $element)
                            <div class="form-group">
                                <label for="{{ $element->id() }}">
                                    {{ $element->title() }}
                                    {!! $element->getInput()->html() !!}
                                </label>
                            </div>
                        @endforeach
                    </div>
                    <div class="buttons">
                        <button type="submit" class="btn btn-primary btn-block btn-quirk">
                            <i class="fa fa-search"></i>
                            {{ trans('administrator::buttons.search') }}
                        </button>
                        @if ($hasFilters)
                            <a class="btn-clear" href="{{ $resetUrl }}">{{ trans('administrator::buttons.clear_filters') }}</a>
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
@endif


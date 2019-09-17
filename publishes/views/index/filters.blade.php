<?php
$elements = $filter->filters();
$resetUrl = method_exists($resource, 'defaultRoute') ? $resource->defaultRoute() : route('scaffold.index', ['module' => $resource]);

$filled = $elements ? $elements->reduce(function ($filled, $element) {
    if ($element->value()) {
        ++$filled;
    }

    return $filled;
}, 0) : 0;

$hasFilters = ($resetUrl !== request()->fullUrl()) && $filled;
?>

@if ($filter && $elements && $elements->count())
@section('scaffold.filter')
    <div class="panel">
        <div class="panel-heading">
            <h4 class="panel-title">{{ trans('administrator::module.filters') }}</h4>
        </div>
        <div class="panel-body">
            <form action="" data-id="filter-form" class="form">
                <input type="hidden" name="sort_by" value="{{ $resource->sortableManager()->element() }}"/>
                <input type="hidden" name="sort_dir" value="{{ $resource->sortableManager()->direction() }}"/>
                @if ($scope = $filter->scope())
                    <input type="hidden" name="scoped_to" value="{{ $scope }}"/>
                @endif

                <div class="scaffold-filters">
                    <div class="inputs">
                        @foreach($elements as $element)
                            <div class="form-group">
                                <label for="{{ $element->id() }}">
                                    {{ $element->title() }}
                                </label>
                                {!! $element->render('index') !!}
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


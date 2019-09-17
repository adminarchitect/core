@extends($resource->template()->layout())

@inject('config', 'scaffold.config')

@section('scaffold.content')
    <h4>
        {{ trans('administrator::module.resources.translations') }}
        <sup class="small">({{ $pagination->total() }})</sup>
    </h4>

    <div class="panel">
        <ul class="panel-options">
            <li><a class="panel-minimize"><i class="fa fa-chevron-down"></i></a></li>
        </ul>
        <div class="panel-heading">
            <h4 class="panel-title panel-minimize">{{ trans('administrator::module.filters') }}</h4>
        </div>
        <div class="panel-body" style="display: {{ request('term') ? 'block' : 'none' }}">
            <form action="{{ route('scaffold.translations.store') }}" data-id="filter-form" class="form">
                <div class="scaffold-filters">
                    <div class="inputs">
                        <div class="form-group">
                            <label for="company_id">
                                {{ trans('administrator::buttons.search') }}
                                <input name="term" type="search" class="form-control"
                                       value="{{ app('request')->input('term') }}">
                            </label>
                        </div>
                    </div>
                    <div class="buttons">
                        <button type="submit" class="btn btn-primary btn-block btn-quirk">
                            <i class="fa fa-search"></i>
                            {{ trans('administrator::buttons.search') }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12 people-list">
            <div class="batch-options nomargin">
                @foreach($scopes->prepend('all') as $filter)
                    <a class="btn btn-link {{ app('request')->input('only') === $filter ? 'active' : '' }}"
                       href="{{ route('scaffold.translations.index', ['only' => $filter, 'term' => request('term')], false) }}">
                        {{ \Illuminate\Support\Str::title($filter) }}
                    </a>
                @endforeach
            </div>
        </div>
    </div>

    <div class="panel">
        <div class="panel-body">
            <form action="" method="post">
                {!! csrf_field() !!}
                <table class="table table-bordered">
                    <thead>
                    <tr>
                        <th width="20%" style="vertical-align: middle">Key</th>
                        <th style="vertical-align: middle">Translation</th>
                        <th width="20%">
                            <div class="btn-group global" style="padding-left: 0;">
                                @foreach($locales = \localizer\locales() as $loc)
                                    <button type="button"
                                            class="btn btn-default btn-sm {{ ($loc->isDefault() ? 'active' : '') }}"
                                            data-locale="{{$loc->iso6391()}}"
                                    >
                                        {{$loc->iso6391()}}
                                    </button>
                                @endforeach
                            </div>
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($pagination->items() as $key => $value)
                        <tr>
                            <td>
                                <?php
                                $parts = array_map(function ($part) {
                                    return \Illuminate\Support\Str::title(str_replace(['_', '-'], ' ', $part));
                                }, explode('.', $key));
                                ?>
                                <span class="bold">{!! ucwords(implode(' &raquo; ', $parts)) !!}</span>
                            </td>
                            <td>
                                <div class="translatable-item pull-left" style="width:100%">
                                    @foreach($locales as $locale)
                                        <div class="translatable {{ $locale->isDefault() ? '' : 'hidden'}}"
                                             style="width:100%"
                                             data-locale="{{$locale->iso6391()}}">
                                            <textarea
                                                    {{ app('scaffold.translations')->readonly($locale) ? 'disabled="disabled"' : '' }}
                                                    class="form-control" style="width:100%"
                                                    name="translation[{{ $key }}][{{ $locale->iso6391() }}]" cols="50"
                                                    rows="2">{{ $value[$locale->iso6391()] ?? '' }}
                                            </textarea>
                                        </div>
                                    @endforeach()
                                </div>
                            </td>
                            <td>
                                <div class="btn-group">
                                    @foreach($locales as $locale)
                                        <button
                                                type="button"
                                                class="btn btn-default btn-sm {{ ($locale->isDefault() ? 'active' : '') }}"
                                                data-locale="{{ $locale->iso6391() }}"
                                        >
                                            {{ $locale->iso6391() }}
                                        </button>
                                    @endforeach
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>

                    @if($pagination->total())
                        <tfoot>
                        <tr>
                            <th></th>
                            <th>
                                <button type="reset" class="btn btn-default pull-right">
                                    {{ trans('administrator::buttons.reset') }}
                                </button>
                            </th>
                            <th>
                                <button style="margin-right: 15px;" class="btn btn-block btn-primary">
                                    {{ trans('administrator::buttons.save') }}
                                </button>
                            </th>
                        </tr>
                        </tfoot>
                    @endif
                </table>
            </form>

            <div class="pull-right">
                {!! $pagination->links() !!}
            </div>
        </div>
    </div>
@endsection

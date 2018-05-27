@inject('template', 'scaffold.template')
@inject('module', 'scaffold.module')
@inject('actions', 'scaffold.actions')
@inject('widgets', 'scaffold.widget')

@extends($template->layout())

@section('scaffold.create')
    @include($template->view('create'))
@endsection

@section('scaffold.content')
    <div class="panel">
        <div class="panel-body">
            <ul class="nav nav-tabs nav-line">
                @foreach($tabs = $widgets->tabs() as $slug => $tabTitle)
                    <li role="presentation">
                        <a href="#tab_{{ $slug }}" aria-controls="tab_{{ $slug }}" role="tab"
                           data-toggle="tab">{{ $tabTitle }}</a>
                    </li>
                @endforeach
            </ul>

            <div class="tab-content">
                @foreach($tabs as $slug => $tabTitle)
                    <div class="tab-pane" id="tab_{{ $slug }}">
                        <?php $widgets->setTab($tabTitle); ?>

                        @foreach ($widgets->setPlacement('main-top')->filter() as $widget)
                            <div class="row mb20">
                                <div class="col-md-12">
                                    {!! $widget->render() !!}
                                </div>
                            </div>
                        @endforeach

                        <div class="row mb20">
                            <?php $sideWidgets = $widgets->setPlacement('sidebar')->filter(); ?>

                            <div class="col-md-{{ $sideWidgets->count() ? 8 : 12 }}">
                                @foreach ($widgets->setPlacement('model')->filter() as $widget)
                                    {!! $widget->render() !!}
                                @endforeach
                            </div>

                            @if ($sideWidgets->count())
                                <div class="col-md-4">
                                    @foreach($sideWidgets as $widget)
                                        <div class="widget mb20">
                                            {!! $widget->render() !!}
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        @foreach ($widgets->setPlacement('main-bottom')->filter() as $widget)
                            <div class="row mb20">
                                <div class="col-md-12">
                                    {!! $widget->render() !!}
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@append

@push('scaffold.js')
    <script>
        $(function() {
            var hash, tabs = ['{!! join("', '", array_keys($tabs)) !!}'].map(function(tab) {
                return '#tab_' + tab;
            });

            if ((hash = location.hash) && tabs.indexOf(hash) !== -1) {
                $('.nav-tabs a[href="' + hash + '"]').tab('show');
            } else {
                $('.nav-tabs a:first').tab('show');
            }
        });
    </script>
@endpush
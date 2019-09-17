@extends($resource->template()->layout())

@section('scaffold.create')
    @include($resource->template()->edit('create'))
@endsection

@section('scaffold.content')
    @php($form = $resource->form())
    <div class="panel">
        <div class="panel-body">
            @php($form->each->setModel($item))

            {!! Form::model($item, ['method' => 'post', 'files' => true]) !!}
            @foreach($form->visibleOnPage(\Terranet\Administrator\Scaffolding::PAGE_EDIT)->filter(function($item) {
                return $item instanceof \Terranet\Administrator\Field\Hidden;
            }) as $hidden)
                {!! $hidden->render(\Terranet\Administrator\Scaffolding::PAGE_EDIT) !!}
            @endforeach
            <table class="table table-striped-col">
                @each($resource->template()->edit('row'), $form->visibleOnPage(\Terranet\Administrator\Scaffolding::PAGE_EDIT), 'field')

                @unless($resource->actions()->readonly())
                    @include($resource->template()->edit('actions'))
                @endunless
            </table>
            {!! Form::close() !!}
        </div>
    </div>
@stop

@include($resource->template()->edit('scripts'))

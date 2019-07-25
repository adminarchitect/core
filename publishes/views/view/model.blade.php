@inject('module', 'scaffold.module')

<?php
$elements = $module->viewColumns()->each->setModel($item);
?>
<table class="table table-striped-col">
    <tr>
        <th colspan="2" class="btn-quirk">
            {{ (isset($title) ? $title: $module->singular()) }}
        </th>
    </tr>
    @foreach($elements->filter(function($e) {return !($e instanceof \Terranet\Administrator\Field\HasMany || $e instanceof \Terranet\Administrator\Field\Media);}) as $element)
        @if ($element instanceof \Terranet\Administrator\Collection\Group)
            @component('administrator::components.table.group')
                @slot('title', $element->title())
            @endcomponent
            @foreach($element->elements() as $element)
                @component('administrator::components.table.row')
                    @slot('label', Form::label($element->id(), $element->title()))
                    @slot('description', $element->getDescription())
                    @slot('input', $element->render(\Terranet\Administrator\Scaffolding::PAGE_VIEW))
                @endcomponent
            @endforeach
            @component('administrator::components.table.spacer')
            @endcomponent
        @elseif ($element instanceof \Terranet\Administrator\Field\HasMany)
            @continue
        @elseif ($element instanceof \Terranet\Administrator\Field\HasOne)
            @component('administrator::components.table.group')
                @slot('title', $element->title())
            @endcomponent
            {!! $element->render(\Terranet\Administrator\Scaffolding::PAGE_VIEW) !!}
        @else
            @component('administrator::components.table.row')
                @slot('label', Form::label($element->id(), $element->title()))
                @slot('description', $element->getDescription())
                @slot('input', $element->render(\Terranet\Administrator\Scaffolding::PAGE_VIEW))
            @endcomponent
        @endif
    @endforeach
</table>

@foreach($elements->whereInstanceOf(\Terranet\Administrator\Field\Media::class) as $element)
    <table class="table" style="margin-top: 16px;">
        @component('administrator::components.table.header')
            @slot('title')
                Media
            @endslot
        @endcomponent
        <tr>
            <td colspan="2">
                {!! $element->render(\Terranet\Administrator\Scaffolding::PAGE_VIEW) !!}
            </td>
        </tr>
    </table>
@endforeach

@foreach($elements->whereInstanceOf(\Terranet\Administrator\Field\HasMany::class) as $element)
    @if ($output = $element->render(\Terranet\Administrator\Scaffolding::PAGE_VIEW))
        <table class="table">
            @component('administrator::components.table.spacer')
            @endcomponent
            @component('administrator::components.table.header')
                @slot('title')
                    {{ $element->title() }}
                    @if ($relationModule = $element->relationModule())
                        @php($relation = $element->relation())
                        @php($key = method_exists($relation, 'getForeignPivotKeyName') ? $relation->getForeignPivotKeyName() : $relation->getForeignKeyName())
                        <div class="pull-right">
                            <a class="btn btn-quirk btn-default"
                               style="padding: 4px 12px 4px;"
                               href="{{ route('scaffold.create', [
                                    'module' => $relationModule->url(),
                                    $key => $relation->getParent()->getKey()
                                   ]) }}">
                                {{ trans('administrator::buttons.attach') }}
                            </a>
                        </div>
                    @endif
                @endslot
            @endcomponent
        </table>
        {!! $output !!}
    @endif
@endforeach

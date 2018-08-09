@inject('module', 'scaffold.module')

<?php
$elements = $module->viewColumns()->each->setModel($item);
?>
<table class="table table-striped-col">
    <tr>
        <th colspan="{{ $elements->count() }}" class="btn-quirk">
            {{ (isset($title) ? $title: $module->singular()) }}
        </th>
    </tr>
    @foreach($elements as $element)
        @if ($element instanceof \Terranet\Administrator\Collection\Group)
            @component('administrator::components.table.group')
                @slot('title', $element->title())
            @endcomponent
            @foreach($element->elements() as $element)
                {!! $element->render(\Terranet\Administrator\Scaffolding::PAGE_VIEW) !!}
            @endforeach
            @component('administrator::components.table.spacer')
            @endcomponent
        @elseif ($element instanceof \Terranet\Administrator\Field\HasMany)
            @continue
        @else
            {!! $element->render(\Terranet\Administrator\Scaffolding::PAGE_VIEW) !!}
        @endif
    @endforeach
</table>

@foreach($elements as $element)
    @if ($element instanceof \Terranet\Administrator\Field\HasMany)
        @if ($output = $element->render(\Terranet\Administrator\Scaffolding::PAGE_VIEW))
            <table class="table">
                @component('administrator::components.table.spacer')
                @endcomponent
                @component('administrator::components.table.header')
                    @slot('title', $element->title())
                @endcomponent
            </table>
            {!! $output !!}
        @endif
    @endif
@endforeach

<?php
$input = $field->getInput();
?>
@if ($field instanceof \Terranet\Administrator\Form\FormSection)
<tr>
    <td colspan="2" class="bg-primary">
        {{ $field->title() }}
    </td>
</tr>
@else
<tr class="{{ $input->hasErrors() ? 'has-error' : '' }} {{ (\admin\helpers\hidden_element($input) ? 'hidden' : '') }}">
    <td style="width: 20%; min-width: 200px;">
        {!! Form::label($input->getName(), $field->title()) !!}:
        @if ($description = $field->getDescription())
            <p class="small">{!! $description !!}</p>
        @endif
    </td>
    <td>
        {!! $input->html() !!}
    </td>
</tr>
@endif
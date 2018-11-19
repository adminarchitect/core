@if ($section)
    @component('administrator::components.table.spacer')
    @endcomponent
    <tr>
        <th colspan="2" class="btn-quirk">{{ strip_tags($label) }}</th>
    </tr>
    <tr>
        {!! $input or '' !!}
    </tr>
@else
    <tr>
        <td style="width: 20%; min-width: 200px;">
            {!! $label or '' !!}
            @if (isset($description) && !empty($description))
                <p class="small">{!! $description !!}</p>
            @endif
        </td>
        <td>{!! $input or '' !!}</td>
    </tr>
@endif

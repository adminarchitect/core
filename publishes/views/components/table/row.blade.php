@if (isset($section) && $section)
    @component('administrator::components.table.spacer')
    @endcomponent
    <tr>
        <th colspan="2" class="btn-quirk">{{ strip_tags($label) }}</th>
    </tr>
    <tr>
        {!! $input ?? '' !!}
    </tr>
    @component('administrator::components.table.spacer')
    @endcomponent
@else
    <tr>
        @if(!empty($label))
            <td style="width: 20%; min-width: 200px;">
                {!! $label ?? '' !!}
                @if (isset($description) && !empty($description))
                    <p class="small">{!! $description !!}</p>
                @endif
            </td>
        @endif
        <td {!! (!empty($label) ? '' : 'colspan="2"') !!}>{!! $input ?? '' !!}</td>
    </tr>
@endif

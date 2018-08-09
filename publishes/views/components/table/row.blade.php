<tr>
    <td style="width: 20%; min-width: 200px;">
        {!! $label or '' !!}
        @if (isset($description) && !empty($description))
            <p class="small">{!! $description !!}</p>
        @endif
    </td>
    <td>{!! $input or '' !!}</td>
</tr>
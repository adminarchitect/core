<html>
<head>
    <style>
        table.exportable th,
        table.exportable td {
            padding: 10px;
            margin: 0;
        }

        table.exportable th {
            background: #ccc;
        }

        table.exportable tbody tr:nth-child(even) {
            background: #eee;
        }
    </style>
</head>
<body>
<?php
$style = [
    'table' => 'width: 100%; border-collapse: collapse; border: 1px solid #ccc;',
    'caption' => 'font-size: 16px; text-align: right; padding: 10px 0;',
];
?>

<table class="exportable" style="{{ $style['table'] }}">
    <caption style="{{ $style['caption'] }}">{{ config('app.name') }} - {{ ucwords($module) }} / {{ $time->toFormattedDateString() }}</caption>

    @foreach($items as $item)
        @if ($loop->first)
        <thead>
            <tr>
                @foreach($attributes = array_keys($item->getAttributes()) as $header)
                    <th>{{ \Coduo\PHPHumanizer\StringHumanizer::humanize($header) }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
        @endif
            <tr>
                @foreach($attributes as $attribute)
                    <td>{{ $item->{$attribute} }}</td>
                @endforeach
            </tr>
    @endforeach
        </tbody>
</table>
</body>
</html>
@if ($collection->count())
    <?php
    $titles = array_keys(\admin\helpers\eloquent_attributes($collection->first()))
    ?>
    <table class="table table-striped-col">
        <tr>
            <th colspan="{{ count($titles) }}" class="btn-quirk">{{ $title }}</th>
        </tr>
        <tr>
            @foreach($titles as $key)
                <th>{{ \admin\helpers\str_humanize($key) }}</th>
            @endforeach
        </tr>
        @foreach($collection as $item)
            <tr>
                @foreach($titles as $key)
                    <td>{!! \admin\helpers\eloquent_attribute($item, $key) !!}</td>
                @endforeach
            </tr>
        @endforeach
    </table>
@endif
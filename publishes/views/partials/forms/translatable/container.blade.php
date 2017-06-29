<div class="translatable-block">
    <div class="tab-content">
        @foreach($inputs as $i => $input)
            <div class="tab-pane {{ ($input->getData()['locale']->id() === $current->id() ? 'active' : '') }}"
                 id="{{ $element->getName() }}_{{ $input->getData()['locale']->id() }}">
                {!! $input !!}
            </div>
        @endforeach
    </div>
    <ul class="nav nav-tabs nav-translatable">
        @foreach(\localizer\locales() as $i => $locale)
            <li class="{{ ($locale->id() == $current->id() ? 'active' : '') }}">
                <a href="#{{ $element->getName() }}_{{ $locale->id() }}" data-toggle="tab" aria-expanded="false">
                    <strong>{{ $locale->iso6391() }}</strong>
                </a>
            </li>
        @endforeach
    </ul>
</div>
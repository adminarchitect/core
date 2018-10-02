<div class="translatable-block">
    <div class="tab-content">
        @foreach($languages as $language)
            @php($field->setName($container->name($language)))
            <div class="tab-pane {{ ($language->id() === $locale->id() ? 'active' : '') }}"
                 id="translatable_{{ $field->id() }}_{{ $language->id() }}"
                 role="tabpanel"
            >
                <div class="translatable" data-locale="{{ $locale->iso6391() }}">
                    <?php
                    if (app('scaffold.translations')->readonly($language)) {
                        $field->setAttribute('readonly', true);
                    }
                    $field->setValue(
                        $container->value($language)
                    )
                    ?>
                    {!! $field->render(\Terranet\Administrator\Scaffolding::PAGE_EDIT) !!}
                </div>
            </div>
        @endforeach
    </div>
    <ul class="nav nav-tabs nav-translatable">
        @foreach($languages as $language)
            <li class="{{ ($language->id() == $locale->id() ? 'active' : '') }}">
                <a href="#translatable_{{ $field->id() }}_{{ $language->id() }}"
                   data-locale="{{ $language->iso6391() }}"
                   data-toggle="tab" aria-expanded="false"
                >
                    <strong>{{ $language->iso6391() }}</strong>
                </a>
            </li>
        @endforeach
    </ul>
</div>
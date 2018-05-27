<?php

namespace Terranet\Administrator\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Terranet\Administrator\Events\TranslationsChanged;
use Terranet\Administrator\Services\Translator;

ini_set('opcache.enable', 0);

class TranslationsController extends AdminController
{
    protected $translator;

    public function __construct(Translator $translator)
    {
        $this->translator = $translator->setLocales(
            $this->locales()
        );

        parent::__construct();
    }

    public function index(Request $request)
    {
        $page = $request->get('page', 1);
        $term = $request->get('term');
        $only = $request->get('only');

        $pagination = $this->translator->load(
            $term,
            'all' === $only ? null : $only,
            $page,
            20
        );

        $pagination->appends(compact('term', 'only'));

        return view(app('scaffold.template')->translations('index'), [
            'pagination' => $pagination,
            'scopes' => $this->translator->filters(),
        ]);
    }

    public function store(Request $request)
    {
        $redirectTo = redirect()->back()->with('messages', [trans('administrator::messages.update_success')]);

        if (empty($translation = $request->input('translation'))) {
            return $redirectTo;
        }

        $changed = [];
        foreach ($this->locales() as $locale) {
            // protect against saving foreign languages
            if (app('scaffold.translations')->readonly($locale)) {
                continue;
            }

            $changed[] = $locale->iso6391();
            $this->translator->save($translation, $locale->iso6391());
        }

        event(new TranslationsChanged($changed));

        return $redirectTo;
    }

    /**
     * @return Collection
     */
    protected function locales(): Collection
    {
        return \localizer\locales();
    }
}

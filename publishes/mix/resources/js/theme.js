class AdminArchitect {
    constructor() {
        [
            'Collections', 'BatchActions',
            'Fancybox', 'Translatable', 'Translations',
        ].map((method) => {
            AdminArchitect['handle' + method].call();
        });
    }

    static handleTranslatable() {
        // When Mui tab is switched it will switch all sibling Mui tabs.
        $('a[data-toggle="tab"]', '.nav-translatable').on('shown.bs.tab', function (e) {
            let fn = $(e.target),
                lc = fn.data('locale');

            fn.closest('form').find('a[data-locale="' + lc + '"]').tab('show');
        });
    }

    static handleTranslations() {
        const activate = function (fn) {
            fn.addClass('active').siblings('button').removeClass('active');
        };

        $('.global button[data-locale]').click(({target}) => {
            const fn = $(target), locale = fn.data('locale');
            $(fn).closest('table').find('tbody button[data-locale="' + locale + '"]').each(function (i, button) {
                $(button).trigger('click');
            });
            activate(fn);
        });

        $('tbody button[data-locale]').click(({target}) => {
            const fn = $(target), locale = fn.data('locale');
            fn.closest('tr').find('.translatable').each((i, e) => {
                const item = $(e);
                item[item.data('locale') === locale ? 'removeClass' : 'addClass']('hidden');
            });
            activate(fn);
        });
    }

    static handleCollections() {
        $(document).on('click', '.toggle-collection', ({target}) => {
            const fn = $(target);

            $('input[type=checkbox].collection-item').each((i, e) => {
                $(e).prop('checked', fn.prop('checked'));
            });
        });
    }

    static handleBatchActions() {
        const selected = () => {
            return $('input[type=checkbox]:checked.collection-item');
        };

        $(document).on('click', '.batch-actions a[data-action]', ({target}) => {
            if (!selected().length) {
                return false;
            }

            const $target = $(target);

            const msg = $target.data('confirmation') || '';
            if (msg.length && !window.confirm(msg)) {
                return false;
            }

            $('#batch_action').val($target.data('action'));
            $('#collection').submit();

            return false;
        });
    }

    static handleFancybox() {
        $('.fancybox').fancybox({
            afterLoad: function () {
                let width, height;
                if (width = $(this.element).data('width')) {
                    this.width = width;
                }

                if (height = $(this.element).data('height')) {
                    this.height = height;
                }
            },
        });
    }
}

$(() => new AdminArchitect);

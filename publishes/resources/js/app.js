class AdminArchitect {
    constructor() {
        [
            'SidebarNavigation', 'Panels', 'Collections', 'BatchActions',
            'DateControls', 'LiveSearch', 'Fancybox'
        ].map((method) => {
            AdminArchitect['handle' + method].call();
        });
    }

    static handleSidebarNavigation() {
        const toggleMenu = (marginLeft, marginMain) => {
            let emailList = ($(window).width() <= 768 && $(window).width() > 640) ? 320 : 360;

            if ($('.mainpanel').css('position') === 'relative') {
                $('.logopanel, .leftpanel').animate({left: marginLeft}, 'fast');
                $('.headerbar, .mainpanel').animate({left: marginMain}, 'fast');

                $('.emailcontent, .email-options').animate({left: marginMain}, 'fast');
                $('.emailpanel').animate({left: marginMain + emailList}, 'fast');

                let $body = $('body');
                if ('hidden' === $body.css('overflow')) {
                    $body.css({overflow: ''});
                } else {
                    $body.css({overflow: 'hidden'});
                }
            } else {
                $('.logopanel, .leftpanel').animate({marginLeft: marginLeft}, 'fast');
                $('.headerbar, .mainpanel').animate({marginLeft: marginMain}, 'fast');

                $('.emailcontent, .email-options').animate({left: marginMain}, 'fast');
                $('.emailpanel').animate({left: marginMain + emailList}, 'fast');
            }
        };

        $('#menuToggle').click(() => {
            let $panel = $('.mainpanel');
            let collapsedMargin = $panel.css('margin-left');
            let collapsedLeft = $panel.css('left');

            if (collapsedMargin === '220px' || collapsedLeft === '220px') {
                toggleMenu(-220, 0);
            } else {
                toggleMenu(0, 220);
            }
        });

        $('.nav-parent > a').on('click', ({target}) => {
            const $target = $(target);

            let gran = $target.closest('.nav');
            let parent = $target.parent();
            let sub = parent.find('> ul');

            if (sub.is(':visible')) {
                sub.slideUp(200);
                if (parent.hasClass('nav-active')) { parent.removeClass('nav-active'); }
            } else {
                $(gran).find('.children').each((i, e) => {
                    $(e).slideUp();
                });

                sub.slideDown(200);

                if (!parent.hasClass('active')) {
                    parent.addClass('nav-active');
                }
            }
            return false;
        });
    }

    static handlePanels() {
        // Close panel
        $('.panel-remove').click(({target}) => {
            $(target).closest('.panel').fadeOut(({target}) => {
                $(target).remove();
            });
        });

        // Minimize panel
        $('.panel-minimize').click(({target}) => {
            const parent = $(target).closest('.panel');

            parent.find('.panel-body').slideToggle(() => {
                let panelHeading = parent.find('.panel-heading');

                if (panelHeading.hasClass('min')) {
                    panelHeading.removeClass('min');
                } else {
                    panelHeading.addClass('min');
                }
            });
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

            if ((msg = $target.data('confirmation')) && !window.confirm(msg)) {
                return false;
            }

            $('#batch_action').val($target.data('action'));
            $('#collection').submit();

            return false;
        });
    }

    static handleDateControls() {
        $('[data-filter-type="date"]').datepicker({
            format: 'yyyy-mm-dd',
            clearBtn: false,
            multidate: false,
        });

        $('[data-filter-type="daterange"]').daterangepicker({
            format: 'YYYY-MM-DD',
            ranges: {
                'Today': [moment(), moment()],
                'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                'This Month': [moment().startOf('month'), moment().endOf('month')],
                'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
            },
            startDate: moment().subtract(29, 'days'),
            endDate: moment(),
        });
    }

    static handleLiveSearch() {
        $('[data-type="livesearch"]').selectize({
            valueField: 'id',
            labelField: 'name',
            searchField: ['name'],
            create: false,
            loadThrottle: 500,
            maxOptions: 100,
            load: function(query, callback) {
                if (!query.length >= 3) return callback();

                let selectize = $($(this)[0].$input);

                let baseUrl = selectize.data('url');
                let url = baseUrl + (-1 === baseUrl.indexOf('?') ? '?' : '&');
                url += 'query=' + query;

                $.ajax({
                    url: url,
                    type: 'GET',
                    error: callback,
                    success: function(res) {
                        if (!res.hasOwnProperty('items')) {
                            console.error(
                                'Livesearch response should have "items" collection. ' +
                                'Each element in collection must have at least 2 keys: "id" and "name"',
                            );

                            return false;
                        }

                        return callback(res.items);
                    },
                });
            },
        });
    }

    static handleFancybox() {
        $('.fancybox').fancybox({
            afterLoad: function() {
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

@inject('template', 'scaffold.template')
@inject('config', 'scaffold.config')

@extends($template->layout())

@section('scaffold.content')
    <script>
        window.mediaFiles = {!! json_encode($files) !!};
        window.XSRF_TOKEN = '{{ csrf_token() }}';
        window.UPLOADER_URL = '{{ route('scaffold.media.upload') }}';
        window.REQUEST_PATH = '{{ request('path', '') }}';
    </script>
    <h4>{{ trans('administrator::media.title') }}</h4>

    {!! $breadcrumbs !!}

    <div ng-controller="MediaController" ng-cloak>
        <div class="well well-asset-options">
            <div class="btn-toolbar btn-toolbar-media-manager">
                @include('administrator::media.partials.actions')
            </div><!-- btn-toolbar -->
        </div>

        <div class="row">
            <div class="col-xs-9">
                @include('administrator::media.partials.files')
            </div>

            <div class="col-xs-3">
                @include('administrator::media.partials.folders')
                @include('administrator::media.partials.dropzone')
                @include('administrator::media.partials.file_info')
            </div>
        </div>

        @include('administrator::media.partials.modals.mkdir')
        @include('administrator::media.partials.modals.rename')
        @include('administrator::media.partials.modals.move')
    </div>
@append

@section('scaffold.js')
    <script>
        jQuery(document).ready(function() {

            'use strict';

            jQuery('.thmb').hover(function() {
                var t = jQuery(this);
                t.find('.ckbox').show();
                t.find('.fm-group').show();
            }, function() {
                var t = jQuery(this);
                if (!t.closest('.thmb').hasClass('checked')) {
                    t.find('.ckbox').hide();
                    t.find('.fm-group').hide();
                }
            });

            jQuery('.ckbox').each(function() {
                var t = jQuery(this);
                var parent = t.parent();
                if (t.find('input').is(':checked')) {
                    t.show();
                    parent.find('.fm-group').show();
                    parent.addClass('checked');
                }
            });

            jQuery('.ckbox').click(function() {
                var t = jQuery(this);
                if (!t.find('input').is(':checked')) {
                    t.closest('.thmb').removeClass('checked');
                    enable_itemopt(false);
                } else {
                    t.closest('.thmb').addClass('checked');
                    enable_itemopt(true);
                }
            });

            jQuery('#selectall').click(function() {
                if (jQuery(this).is(':checked')) {
                    jQuery('.thmb').each(function() {
                        jQuery(this).find('input').attr('checked', true);
                        jQuery(this).addClass('checked');
                        jQuery(this).find('.ckbox, .fm-group').show();
                    });
                    enable_itemopt(true);
                } else {
                    jQuery('.thmb').each(function() {
                        jQuery(this).find('input').attr('checked', false);
                        jQuery(this).removeClass('checked');
                        jQuery(this).find('.ckbox, .fm-group').hide();
                    });
                    enable_itemopt(false);
                }
            });

            function enable_itemopt(enable) {
                if (enable) {
                    jQuery('.itemopt').removeClass('disabled');
                } else {

                    // check all thumbs if no remaining checks
                    // before we can disabled the options
                    var ch = false;
                    jQuery('.thmb').each(function() {
                        if (jQuery(this).hasClass('checked'))
                            ch = true;
                    });

                    if (!ch)
                        jQuery('.itemopt').addClass('disabled');
                }
            }
        });
    </script>
@append

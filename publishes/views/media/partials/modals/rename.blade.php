<div class="modal fade" id="rename" ng-if="selection.count() == 1">
    <form ng-submit="rename(name)">
        <div class="modal-dialog">
            <div class="modal-content">
                @include('administrator::media.partials.modals._header', ['title' => trans('administrator::media.rename')])
                <div class="modal-body">
                    <input type="text" class="form-control" ng-model="name" ng-value="selection.first().basename"/>
                </div>
                @include('administrator::media.partials.modals._footer')
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </form>
</div>
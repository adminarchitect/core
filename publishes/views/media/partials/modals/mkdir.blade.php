<div class="modal fade" id="mkdir">
    <form ng-submit="makeDirectory(name)">
        <div class="modal-dialog">
            <div class="modal-content">
                @include('administrator::media.partials.modals._header', ['title' => trans('administrator::media.mkdir')])
                <div class="modal-body">
                    <input type="text" class="form-control" ng-model="name" placeholder="Images"/>
                </div>
                @include('administrator::media.partials.modals._footer')
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </form>
</div>
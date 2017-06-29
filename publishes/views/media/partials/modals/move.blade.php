<div class="modal fade" id="move" ng-if="selection.exists()">
    <form ng-submit="move(target)">
        <div class="modal-dialog">
            <div class="modal-content">
                @include('administrator::media.partials.modals._header', ['title' => trans('administrator::media.move')])
                <div class="modal-body">
                    <select ng-model="target" class="form-control" ng-init="target=directories.first()">
                        <option ng-repeat="dir in directories" value="@{{ ('../' == dir ? dir : dir.filename) }}">@{{ ('../' == dir ? dir : dir.filename) }}</option>
                    </select>
                </div>
                @include('administrator::media.partials.modals._footer')
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </form>
</div>
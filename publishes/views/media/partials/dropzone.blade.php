<div class="panel panel-default" ng-show="!selection.exists() || selection.multiple()">
    <div class="panel-heading">
        <h3 class="panel-title">{{ trans('administrator::media.dropzone') }}</h3>
    </div>
    <div class="panel-body">
        <div class="text-center" ng-show="!selection.exists()">
            <div nv-file-drop uploader="uploader">
                <input type="file" nv-file-select multiple uploader="uploader" class="hidden"/>
                <div nv-file-over uploader="uploader"
                     class="media-drop-zone progress"
                     over-class="file-over">
                    {!! trans('administrator::media.drag_drop_to_upload')  !!}
                </div>
            </div>

            <div ng-if="uploader.queue.length" class="queue-container">
                <ul class="list-unstyled">
                    <li ng-repeat="item in uploader.queue">
                        <div class="text-left queue-item">
                            <a ng-disabled="!item.isError" class="pull-right" ng-click="item.remove()"><i class="fa fa-trash"></i></a>
                            <span class="queue-item-name" ng-bind="item.file.name" ng-class="{'text-red': item.isError}"></span>
                        </div>
                    </li>
                </ul>

                <div class="progress mt20">
                    <div class="progress-bar progress-bar-green" role="progressbar" aria-valuemin="0" aria-valuemax="100" ng-style="{'width': uploader.progress + '%'}"></div>
                </div>
                <button class="btn btn-warning btn-block" ng-click="uploader.uploadAll()">{{ trans('administrator::buttons.upload') }}</button>
            </div>
        </div>

        <div ng-show="selection.multiple()">
            <button class="btn btn-warning btn-block" data-toggle="modal" data-target="#move">
                <i class="fa fa-share"></i>
                {{ trans('administrator::media.buttons.move') }}
            </button>
            <button class="btn btn-danger btn-block" ng-click="remove()">
                <i class="fa fa-trash"></i>
                {{ trans('administrator::media.buttons.delete') }}
            </button>
        </div>
    </div>
</div>
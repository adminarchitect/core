<div ng-class="{'panel-warning': !files.length, 'panel-default': files.length}">
    <div class="panel-heading" ng-if="!files.length">
        <h3 class="panel-title">{{ trans('administrator::media.no_files_found') }}</h3>
    </div>

    <div ng-if="files.length" class="media-list" id="media-library">
        <div class="row filemanager">
            <div class="col-xs-6 col-sm-4 col-md-3 col-lg-2" ng-repeat="file in files">
                <div class="thmb" ng-class="{'checked': selection.has(file)}">
                    <label class="ckbox">
                        <input type="checkbox" ng-click="selection.toggle(file)">
                        <span></span>
                    </label>
                    <div class="thmb-prev text-center" style="padding: 40px; background: #d4d9e3">
                        <i class="@{{ file.icon }}" style="font-size: 96px;"></i>
                    </div>
                    <h5 class="fm-title">@{{ file.basename }}</h5>
                    <small class="text-muted">Created: @{{ file.createdAt }}</small>
                    <small class="text-muted text-primary">@{{ file.size }} Bytes</small>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="panel panel-default" ng-show="!selection.exists() && directories.length">
    <div class="panel-heading">
        <h3 class="panel-title">{{ trans('administrator::media.folders') }}</h3>
    </div>
    <div class="panel-body">
        <ul class="folder-list">
            <li ng-repeat="dir in directories" ng-if="dir">
                <div class="pull-right" ng-if="dir !== '../'" >
                    <a ng-click="remove(dir)"><i class="fa fa-trash"></i></a>
                </div>
                <div class="pull-left">
                    <a style="margin-left: 10px; cursor: pointer;" ng-if="dir" ng-click="open(dir)">
                        <i class="fa fa fa-folder-open"></i>
                        @{{ dir.filename || dir }}
                    </a>
                </div>
                <div class="clearfix"></div>
            </li>
        </ul>
    </div>
</div>
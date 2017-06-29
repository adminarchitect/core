<div class="panel panel-default" ng-show="selection.exists() && !selection.multiple()">
    <div class="panel-heading">
        <h3 class="panel-title">{{ trans('administrator::media.file_info') }}</h3>
    </div>

    <div class="panel-body">
        <div style="display:flex; flex-direction: row; justify-content: center; align-items: flex-start;">
            <img class="img-responsive" ng-src="@{{ selection.info('url') }}" ng-if="selection.info('isImage')"/>
        </div>

        <h5 class="profile-username text-center">
            <div ng-if="!selection.info('isImage')">
                <i class="@{{ selection.info('icon') }}" style="font-size: 96px;"></i>
                <br/><br/>
            </div>
            @{{ selection.info('basename') }}
        </h5>

        <ul class="list-group list-group-unbordered media-file-info">
            <li class="list-group-item">
                <b>{{ trans('administrator::media.info.size') }}</b>
                <span class="pull-right">
                    @{{ selection.info('size') }} Bytes
                </span>
            </li>
            <li class="list-group-item">
                <b>{{ trans('administrator::media.info.created_at') }}</b><br />
                @{{ selection.info('createdAt') }}
            </li>
            <li class="list-group-item">
                <b>{{ trans('administrator::media.info.updated_at') }}</b><br />
                @{{ selection.info('updatedAt') }}
            </li>
        </ul>
        <a target="_blank" class="btn btn-primary btn-block" ng-href="@{{ selection.info('url') }}">
            {{ trans('administrator::media.buttons.download') }}
        </a>
    </div>
</div>
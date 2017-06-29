<div class="box-tools">
    <div class="btn-group">
        <button type="button" class="btn btn-default" ng-click="upload()">
            <i class="fa fa-upload"></i>&nbsp;{{ trans('administrator::media.buttons.upload') }}
        </button>
        <button type="button" class="btn btn-default" data-toggle="modal" data-target="#mkdir">
            <i class="fa fa-folder"></i>&nbsp;{{ trans('administrator::media.buttons.mkdir') }}
        </button>
    </div>
    <div class="btn-group ml10" style="padding-left: 10px; border-left: 1px solid #818181;" ng-if="selection.exists()">
        <button type="button" class="btn btn-warning" data-toggle="modal" data-target="#move">
            <i class="fa fa-share"></i>&nbsp;{{ trans('administrator::media.buttons.move') }}
        </button>

        <button type="button" class="btn btn-warning" data-toggle="modal" data-target="#rename" ng-if="1 === selection.count()">
            <i class="fa fa-i-cursor"></i>&nbsp;{{ trans('administrator::media.buttons.rename') }}
        </button>

        <button type="button" class="btn btn-danger" ng-click="remove()">
            <i class="fa fa-trash"></i>&nbsp;{{ trans('administrator::media.buttons.delete') }}
        </button>
    </div>
</div>
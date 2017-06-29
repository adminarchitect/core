<div class="modal-footer">
    <button type="button" class="btn btn-default pull-left" data-dismiss="modal">{{ trans('administrator::media.buttons.close') }}</button>
    <span ng-if="responseMessage()"
          ng-class="{'text-green': responseOk(), 'text-red': !responseOk()}"
          ng-bind="responseMessage()" class="media-list__inline-response"></span>
    <button type="submit" class="btn btn-primary">
        <i class="fa fa-save"></i>&nbsp;{{ trans('administrator::media.buttons.save') }}
    </button>
</div>
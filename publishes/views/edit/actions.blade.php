<tr>
    <td colspan="2" class="text-center">
        <button name="save" class="btn btn-primary btn-quirk">
            <i class="fa fa-save"></i>
            {{ trans('administrator::buttons.save') }}
        </button>
        <button name="save_return" class="btn btn-primary btn-quirk">
            <i class="fa fa-rotate-left"></i>
            {{ trans('administrator::buttons.save_return') }}
        </button>
        @if ($actions->authorize('create'))
            <button name="save_create" class="btn btn-primary btn-quirk">
                <i class="fa fa-rotate-right"></i>
                {{ trans('administrator::buttons.save_create') }}
            </button>
        @endif
    </td>
</tr>

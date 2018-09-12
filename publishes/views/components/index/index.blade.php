<div class="panel">
    <div class="panel-body">
        <form method="post" id="collection" action="{{ route('scaffold.batch', ['page' => $module]) }}">
            <?php echo Form::hidden('batch_action', null, ['id' => 'batch_action']); ?>
            <?php echo Form::token(); ?>

            {{ $gridBefore ?? '' }}

            <table class="table table-bordered">
                <thead>
                <tr>
                    {{ $checkboxes ?? '' }} {{ $headers ?? '' }} {{ $actions ?? '' }}
                </tr>
                </thead>

                <tbody>
                {{ $rows ?? '' }}
                </tbody>

                @if ($items && count($items) > 10)
                    <tfoot>
                    <tr>
                        {{ $checkboxes ?? '' }} {{ $headers ?? '' }} {{ $actions ?? '' }}
                    </tr>
                    </tfoot>
                @endif
            </table>

            {{ $gridAfter ?? '' }}
        </form>

        @if (trim($exportable ?? null) || trim($paginator ?? null))
            <div class="row">
                <div class="col-md-6 mt20">{{ $exportable }}</div>
                <div class="col-md-6 text-right">{{ $paginator }}</div>
            </div>
        @endif
    </div>
</div>

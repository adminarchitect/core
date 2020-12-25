<div class="panel">
    <div class="panel-body">
        <form method="post" id="collection" target="_self" action="{{ route('scaffold.batch', ['module' => $resource->url()]) }}">
            <?php echo Form::hidden('batch_action', null, ['id' => 'batch_action']); ?>
            <?php echo Form::token(); ?>

            {{ $gridBefore ?? '' }}

            <div style="overflow-x: scroll">
                <table class="table">
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
            </div>
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

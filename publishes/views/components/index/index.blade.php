<div class="panel">
    <div class="panel-body">
        <form method="post" id="collection" action="{{ route('scaffold.batch', ['page' => $module]) }}">
            <?=Form::hidden('batch_action', null, ['id' => 'batch_action'])?>
            <?=Form::token()?>
            <table class="table table-bordered">
                <thead>
                <tr>
                    {{ $checkboxes }} {{ $headers }} {{ $actions }}
                </tr>
                </thead>

                <tbody>
                    {{ $rows }}
                </tbody>

                @if ($items && count($items) > 10)
                    <tfoot>
                    <tr>
                        {{ $checkboxes }} {{ $headers }} {{ $actions }}
                    </tr>
                    </tfoot>
                @endif
            </table>
        </form>

        @if (trim($exportable) || trim($paginator))
            <div class="row">
                <div class="col-md-6 mt20">{{ $exportable }}</div>
                <div class="col-md-6 text-right">{{ $paginator }}</div>
            </div>
        @endif
    </div>
</div>
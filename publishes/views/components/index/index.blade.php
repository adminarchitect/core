<div class="panel">
    <div class="panel-body">
        <form method="post" id="collection" action="{{ route('scaffold.batch', ['page' => $module]) }}">
            <?=Form::hidden('batch_action', null, ['id' => 'batch_action'])?>
            <?=Form::token()?>
            <table class="table table-bordered">
                <thead>
                <tr>
                    {{ $checkboxes or '' }} {{ $headers or '' }} {{ $actions or '' }}
                </tr>
                </thead>

                <tbody>
                {{ $rows or '' }}
                </tbody>

                @if ($items && count($items) > 10)
                    <tfoot>
                    <tr>
                        {{ $checkboxes or '' }} {{ $headers or '' }} {{ $actions or '' }}
                    </tr>
                    </tfoot>
                @endif
            </table>
        </form>

        @if (trim($exportable ?? null) || trim($paginator ?? null))
            <div class="row">
                <div class="col-md-6 mt20">{{ $exportable }}</div>
                <div class="col-md-6 text-right">{{ $paginator }}</div>
            </div>
        @endif
    </div>
</div>
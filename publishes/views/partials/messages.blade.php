<!-- Validation errors -->
@if (isset($errors) && $errors->count())
    <div class="alert alert-danger alert-dismissable">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
        {{ $errors->first() }}
    </div>
@endif

<!-- Success messages -->
@if (Session::has('messages'))
    <div class="alert alert-success alert-dismissable">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
        {{ collect(Session::get('messages'))->first() }}
    </div>
@endif

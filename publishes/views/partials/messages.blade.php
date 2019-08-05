<!-- Validation errors -->
@if (isset($errors) && $errors->count())
    <div class="alert alert-danger alert-dismissible" role="alert">
        {{ $errors->first() }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif

<!-- Success messages -->
@if (Session::has('messages'))
    <div class="alert alert-success alert-dismissible" role="alert">
        {{ collect(Session::get('messages'))->first() }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif

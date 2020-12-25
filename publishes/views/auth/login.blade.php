@inject('config', 'scaffold.config')

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">

    <title>{{ strip_tags($config->get('title')) }} &raquo; {{ trans('administrator::module.login') }}</title>

    @include('administrator::partials.styles')
</head>
<body class="signwrapper">

    <div class="sign-overlay"></div>
    <div class="signpanel"></div>

    <div class="panel signin">
        <div class="panel-heading">
            <h1>{!! $config->get('title') !!}</h1>
            <h4 class="panel-title">{{ $config->get('welcome') }}</h4>
        </div>
        <div class="panel-body">
            @include(\Terranet\Administrator\Architect::template()->partials('messages'))

            {!! Form::open() !!}
            <div class="form-group mb10">
                <div class="input-group">
                    <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
                    {!! Form::text($identity = $config->get('auth.identity', 'username'), null, ['class' => 'form-control', 'placeholder' => trans('administrator::module.credentials.' . $identity)]) !!}
                </div>
            </div>
            <div class="form-group">
                <div class="input-group">
                    <span class="input-group-addon"><i class="glyphicon glyphicon-lock"></i></span>
                    {!! Form::password($credential = $config->get('auth.credential', 'password'), ['class' => 'form-control', 'placeholder' => trans('administrator::module.credentials.' . $credential)]) !!}
                </div>
            </div>
            <div class="form-group">
                <div class="pull-left">
                    <input name="remember_me" type="hidden" value="0"/>
                    <input type="checkbox" name="remember_me" value="1"/> {{ trans('administrator::buttons.remember_me') }}
                </div>
                <div class="clearfix"></div>
            </div>
            <div class="form-group">
                <button class="btn btn-success btn-quirk btn-block">{{ trans('administrator::buttons.sign_in') }}</button>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
    <script src="{{ mix('manifest.js', 'admin') }}"></script>
    <script src="{{ mix('vendor.js', 'admin') }}"></script>
</body>
</html>

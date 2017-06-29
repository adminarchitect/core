<?php
$user = auth('admin')->user();
$pict = asset('/admin/images/admin.png');
?>
<li>
    <div class="btn-group">
        <button type="button" class="btn btn-logged" data-toggle="dropdown">
            @if (app('scaffold.config')->get('gravatar', true) && Gravatar::exists($user->email))
                <img src="{{ Gravatar::get($user->email, ['size' => 160, 'fallback' => $pict]) }}" alt="{{ $user->name }}" />
            @else
                <img src="{{ $pict }}" alt="{{ $user->name }}">
            @endif
            {{ auth('admin')->user()->name }}
            <span class="caret"></span>
        </button>
        {!! $navigation->render('tools', '\Terranet\Administrator\Navigation\Presenters\Bootstrap\NavbarPresenter') !!}
    </div>
</li>
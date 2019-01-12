<?php
$user = auth('admin')->user();
$pict = asset('/admin/images/admin.png');
?>
<li>
    <div class="btn-group">
        <button type="button" class="btn btn-logged" data-toggle="dropdown">
            <img src="{{ $pict }}" alt="{{ $user->name }}">

            {{ $user->name }}
            <span class="caret"></span>
        </button>

        {!! app('scaffold.navigation')->render('tools', '\Terranet\Administrator\Navigation\Presenters\Bootstrap\NavbarPresenter') !!}
    </div>
</li>
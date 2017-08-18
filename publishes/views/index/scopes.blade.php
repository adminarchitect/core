@if ($scopes = $filter->scopes())
    @foreach($scopes as $scope)
        <a href="{{ $filter->makeScopedUrl($slug = $scope->id()) }}"
           class="btn btn-link{{ ($filter->scope() == $slug ? ' active' : '') }}">
            {!! (($icon = $scope->icon()) ? '<i class="'.$icon.'"></i>' : '') !!} {{ $scope->title() }}
        </a>
    @endforeach
@endif

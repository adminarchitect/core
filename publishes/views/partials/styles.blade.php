<link rel="stylesheet" href="{{ mix('admin/app.css') }}">
<link rel="stylesheet" href="{{ mix('admin/vendor.css') }}">

@if(file_exists(public_path($icons = 'admin/glyphicons.css')))
    <link rel="stylesheet" href="{{ mix($icons) }}">
@endif
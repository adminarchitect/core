<link rel="stylesheet" href="{{ mix('app.css', 'admin') }}">
<link rel="stylesheet" href="{{ mix('vendor.css', 'admin') }}">

@if(file_exists(public_path($icons = 'glyphicons.css')))
    <link rel="stylesheet" href="{{ mix($icons) }}">
@endif
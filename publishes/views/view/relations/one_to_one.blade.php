@inject('template', 'scaffold.template')

@include($template->view('model'), [
    'title' => $title,
    'item'  => $related
])
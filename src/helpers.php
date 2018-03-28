<?php

namespace {

    if (!function_exists('array_build')) {

        /**
         * Build a new array using a callback (Original method was deprecetad since version 5.2).
         *
         * @param array $array
         * @param callable $callback
         * @return array
         */
        function array_build($array, callable $callback)
        {
            $results = [];

            foreach ($array as $key => $value) {
                list($innerKey, $innerValue) = call_user_func($callback, $key, $value);

                $results[$innerKey] = $innerValue;
            }

            return $results;
        }
    }

    if (!function_exists('guarded_auth')) {
        /**
         * Since version 5.2 Laravel did change the auth model.
         * Check if we on the new version.
         *
         * @return bool
         */
        function guarded_auth()
        {
            return version_compare(app()->version(), '5.2') >= 0;
        }
    }
}

namespace admin\db {

    use Illuminate\Support\Facades\DB;
    use Illuminate\Database\Eloquent\Model;
    use Terranet\Translatable\Translatable;

    if (!function_exists('scheme')) {
        function scheme()
        {
            return app('scaffold.schema');
        }
    }

    if (!function_exists('table_columns')) {
        function table_columns($model, $withTranslated = true)
        {
            static $data = [];

            $table = $model->getTable();

            if (!array_has($data, $table)) {
                $columns = scheme()->columns($table);

                if ($withTranslated && $model instanceof Translatable && method_exists($model, 'getTranslationModel')) {
                    $related = $model->getTranslationModel()->getTable();
                    $columns = array_merge(
                        $columns,
                        array_except(
                            scheme()->columns($related), array_keys($columns)
                        )
                    );
                }

                array_set($data, $table, $columns);
            }

            return array_get($data, $table, []);
        }
    }

    if (!function_exists('table_indexes')) {
        function table_indexes(Model $model, $withTranslated = true)
        {
            $indexes = scheme()->indexedColumns($model->getTable());

            if ($withTranslated && $model instanceof Translatable && method_exists($model, 'getTranslationModel')) {
                $indexes = array_unique(array_merge(
                    $indexes,
                    scheme()->indexedColumns($model->getTranslationModel()->getTable())
                ));
            }

            return $indexes;
        }
    }

    if (!function_exists('connection')) {
        /**
         * Check if we are on desired connection or get the current connection name
         *
         * @param string $name
         * @return mixed string|boolean
         */
        function connection($name = null)
        {
            if (null === $name) {
                return DB::connection()->getName();
            }

            return (strtolower($name) == strtolower(DB::connection()->getName()));
        }
    }

    if (!function_exists('enum_values')) {
        function enum_values($table, $column)
        {
            $columns = DB::select("SHOW COLUMNS FROM `{$table}` LIKE '{$column}'");
            $values = [];
            if (preg_match('/^enum\((.*)\)$/', $columns[0]->Type, $matches)) {
                foreach (explode(',', $matches[1]) as $value) {
                    $value = trim($value, "'");
                    $values[$value] = title_case($value);
                }
            }

            return $values;
        }
    }
}

namespace admin\helpers {

    use Illuminate\Support\Facades\Route;
    use Coduo\PHPHumanizer\StringHumanizer;
    use Illuminate\Database\Eloquent\Model;
    use Illuminate\Support\Facades\Request;
    use Terranet\Translatable\Translatable;
    use Terranet\Presentable\PresentableInterface;
    use Czim\Paperclip\Contracts\AttachableInterface;
    use Terranet\Administrator\Contracts\Form\HiddenElement;
    use Terranet\Administrator\Contracts\Module\Exportable;

    if (!function_exists('html_list')) {
        /**
         * Fetch key => value pairs from an Eloquent model.
         *
         * @param mixed $model
         * @param string $labelAttribute
         * @param string $keyAttribute
         * @return array
         */
        function html_list($model, $labelAttribute = 'name', $keyAttribute = 'id')
        {
            if (is_string($model)) {
                $model = new $model;
            }

            return $model->pluck($labelAttribute, $keyAttribute)->toArray();
        }
    }

    if (!function_exists('qsRoute')) {
        /**
         * Generate route with query string.
         *
         * @param       $route
         * @param array $params
         * @return string
         */
        function qsRoute($route = null, array $params = [])
        {
            $requestParams = Request::all();

            if (!$route) {
                $current = Route::current();
                $requestParams += $current->parameters();
                $route = $current->getName();
            }

            $params = array_merge($requestParams, $params);

            return route($route, $params);
        }
    }

    if (!function_exists('html_attributes')) {
        function html_attributes(array $attributes = [])
        {
            $out = [];
            foreach ($attributes as $key => $value) {
                // transform
                if (is_bool($value)) {
                    $out[] = "{$key}=\"{$key}\"";
                } else {
                    if (is_numeric($key)) {
                        $out[] = "{$value}=\"{$value}\"";
                    } else {
                        $value = htmlspecialchars($value);
                        $out[] = "{$key}=\"{$value}\"";
                    }
                }
            }

            return implode(' ', $out);
        }
    };

    if (!function_exists('auto_p')) {
        function auto_p($value, $lineBreaks = true)
        {
            if (trim($value) === '') {
                return '';
            }

            $value = $value . "\n"; // just to make things a little easier, pad the end
            $value = preg_replace('|<br />\s*<br />|', "\n\n", $value);

            // Space things out a little
            $allblocks = '(?:table|thead|tfoot|caption|col|colgroup|tbody|tr|td|th|div|dl|dd|dt|ul|ol|li|pre|select|option|form|map|area|blockquote|address|math|style|input|p|h[1-6]|hr|fieldset|legend|section|article|aside|hgroup|header|footer|nav|figure|figcaption|details|menu|summary)';
            $value = preg_replace('!(<' . $allblocks . '[^>]*>)!', "\n$1", $value);
            $value = preg_replace('!(</' . $allblocks . '>)!', "$1\n\n", $value);
            $value = str_replace(["\r\n", "\r"], "\n", $value); // cross-platform newlines

            if (strpos($value, '<object') !== false) {
                $value = preg_replace('|\s*<param([^>]*)>\s*|', '<param$1>', $value); // no pee inside object/embed
                $value = preg_replace('|\s*</embed>\s*|', '</embed>', $value);
            }

            $value = preg_replace("/\n\n+/", "\n\n", $value); // take care of duplicates

            // make paragraphs, including one at the end
            $values = preg_split('/\n\s*\n/', $value, -1, PREG_SPLIT_NO_EMPTY);
            $value = '';

            foreach ($values as $tinkle) {
                $value .= '<p>' . trim($tinkle, "\n") . "</p>\n";
            }

            // under certain strange conditions it could create a P of entirely whitespace
            $value = preg_replace('|<p>\s*</p>|', '', $value);
            $value = preg_replace('!<p>([^<]+)</(div|address|form)>!', '<p>$1</p></$2>', $value);
            $value = preg_replace('!<p>\s*(</?' . $allblocks . '[^>]*>)\s*</p>!', '$1', $value); // don't pee all over a tag
            $value = preg_replace('|<p>(<li.+?)</p>|', '$1', $value); // problem with nested lists
            $value = preg_replace('|<p><blockquote([^>]*)>|i', '<blockquote$1><p>', $value);
            $value = str_replace('</blockquote></p>', '</p></blockquote>', $value);
            $value = preg_replace('!<p>\s*(</?' . $allblocks . '[^>]*>)!', '$1', $value);
            $value = preg_replace('!(</?' . $allblocks . '[^>]*>)\s*</p>!', '$1', $value);

            if ($lineBreaks) {
                $value = preg_replace_callback('/<(script|style).*?<\/\\1>/s', '\admin\helpers\autop_newline_preservation_helper', $value);
                $value = preg_replace('|(?<!<br />)\s*\n|', "<br />\n", $value); // optionally make line breaks
                $value = str_replace('<WPPreserveNewline />', "\n", $value);
            }

            $value = preg_replace('!(</?' . $allblocks . '[^>]*>)\s*<br />!', '$1', $value);
            $value = preg_replace('!<br />(\s*</?(?:p|li|div|dl|dd|dt|th|pre|td|ul|ol)[^>]*>)!', '$1', $value);

            if (strpos($value, '<pre') !== false) {
                $value = preg_replace_callback('!(<pre[^>]*>)(.*?)</pre>!is', '\admin\helpers\clean_pre', $value);
            }
            $value = preg_replace("|\n</p>$|", '</p>', $value);

            return $value;
        }

        /**
         * Accepts matches array from preg_replace_callback in wpautop() or a string.
         * Ensures that the contents of a <<pre>>...<</pre>> HTML block are not
         * converted into paragraphs or line-breaks.
         *
         * @param array|string $matches The array or string
         * @return string The pre block without paragraph/line-break conversion.
         */
        function clean_pre($matches)
        {
            if (is_array($matches)) {
                $text = $matches[1] . $matches[2] . '</pre>';
            } else {
                $text = $matches;
            }

            $text = str_replace(['<br />', '<br/>', '<br>'], ['', '', ''], $text);
            $text = str_replace('<p>', "\n", $text);
            $text = str_replace('</p>', '', $text);

            return $text;
        }

        /**
         * Newline preservation help function for wpautop.
         *
         * @since  3.1.0
         * @param array $matches preg_replace_callback matches array
         * @returns string
         * @return mixed
         */
        function autop_newline_preservation_helper($matches)
        {
            return str_replace("\n", '<PreserveNewline />', $matches[0]);
        }
    }

    if (!function_exists('hidden_element')) {
        function hidden_element($element)
        {
            return $element instanceof HiddenElement;
        }
    }

    if (!function_exists('exportable')) {
        function exportable($module)
        {
            return $module instanceof Exportable;
        }
    }

    if (!function_exists('eloquent_attributes')) {
        function eloquent_attributes(Model $model)
        {
            $fillable = $model->getFillable();

            if (!empty($key = $model->getKeyName())) {
                array_unshift($fillable, $key);
            }

            if ($model instanceof Translatable && method_exists($model, 'getTranslatedAttributes')) {
                $fillable = array_merge($fillable, $model->getTranslatedAttributes());
            }

            $fillable = array_merge($fillable, $model->getDates());

            $fillable = array_filter($fillable, function ($element) use ($model) {
                return !in_array($element, $model->getHidden());
            });

            $data = $model->toArray();

            $out = [];
            foreach ($fillable as $column) {
                $out[$column] = array_get($data, $column);
            }

            return $out;
        }
    }

    if (!function_exists('eloquent_attribute')) {
        function eloquent_attribute(Model $object, $key)
        {
            if ($object instanceof AttachableInterface && array_key_exists($key, $object->getAttachedFiles())) {
                return \admin\output\staplerImage($object->getAttribute($key));
            }

            $value = present($object, $key);

            if (is_array($value)) {
                return !empty($value)
                    ? highlight_string(json_encode($value, JSON_PRETTY_PRINT))
                    : null;
            }

            return $value;
        }

        function present(Model $object, $key, $value = null)
        {
            $value = $value ?: $object->getAttribute($key);

            if ($object instanceof PresentableInterface) {
                if ($adminKey = has_admin_presenter($object, $key)) {
                    return $object->present()->$adminKey($value);
                }

                if ($frontKey = has_presenter($object, $key)) {
                    return $object->present()->$frontKey($value);
                }
            }

            return $value;
        }

        function has_admin_presenter(PresentableInterface $object, $key)
        {
            return method_exists($object->present(), $adminKey = camel_case("admin_{$key}"))
                ? $adminKey
                : null;
        }

        function has_presenter(PresentableInterface $object, $key)
        {
            return method_exists($object->present(), $frontKey = camel_case($key))
                ? $frontKey
                : null;
        }
    }

    if (!function_exists('str_humanize')) {
        function str_humanize($key)
        {
            return StringHumanizer::humanize($key);
        }
    }
}

namespace admin\column {

    function element($title = '', $standalone = false, $output = null)
    {
        return compact('title', 'standalone', 'output');
    }

    function group($label = '', array $elements = [])
    {
        $group = [];

        foreach ($elements as $key => $value) {
            if (is_numeric($key) && is_string($value)) {
                $group[$value] = element($value);
            } else {
                $group[$key] = call_user_func_array('\\admin\\column\\element', $value);
            }
        }

        return [
            'label' => $label,
            'elements' => $group,
        ];
    }
}

namespace admin\output {

    use Closure;

    function label_case($key, $upper = false)
    {
        $key = str_replace('_', ' ', preg_replace('~_id$~si', '', $key));

        return $upper ? mb_strtoupper($key) : title_case($key);
    }

    function boolean($value)
    {
        return $value ? '<i class="fa fa-fw fa-check"></i>' : '';
    }

    function email($value)
    {
        return '<a href="mailto:' . $value . '">' . $value . '</a>';
    }

    function rank($name = 'rank', $value = 1, $key)
    {
        return '<input type="number" style="width: 50px;" value="' . $value . '" name="' . $name . '[' . $key . ']" />';
    }

    /**
     * @param       $image
     * @param array $attributes
     * @return string
     */
    function image($image, array $attributes = [])
    {
        $attributes = \admin\helpers\html_attributes($attributes);

        return $image ? '<img src="' . $image . '" ' . $attributes . ' />' : '';
    }

    /**
     * Output image from Paperclip attachment object
     *
     * @param null $attachment
     * @param null $style
     * @param array $attributes
     * @return null|string
     */
    function staplerImage($attachment = null, $style = null, $attributes = [])
    {
        if ($attachment && $attachment->originalFilename()) {
            $styles = $attachment->variants();

            if (count($styles)) {
                $firstStyle = $style ?: head($styles);
                $origStyle = 'original';

                # in case then style dimensions are less than predefined, adjust width & height to style's
                $aWidth = (int)array_get($attributes, 'width');
                $aHeight = (int)array_get($attributes, 'height');

                if (($aWidth || $aHeight) && $firstStyle) {
                    $size = array_filter($styles, function ($style) use ($firstStyle) {
                        return $style == $firstStyle;
                    });

                    if (($size = array_shift($size))) {
                        $dimensions = array_get($attachment->getConfig(), "variants.{$size}");

                        if (is_array($dimensions)) {
                            $dimensions = array_get($dimensions, 'resize.dimensions');
                        }

                        if ($dimensions && str_contains($dimensions, 'x')) {
                            list($width, $height) = explode('x', $dimensions);

                            if ($aWidth > $width) {
                                $attributes['width'] = $width;
                            }

                            if ($aHeight > $height) {
                                $attributes['height'] = $height;
                            }
                        }
                    }
                }

                return
                    '<a class="fancybox" href="' . url($attachment->url($origStyle)) . '">' .
                    \admin\output\image($attachment->url($firstStyle), $attributes) .
                    '</a>';
            }

            return link_to($attachment->url(), basename($attachment->url()));
        }

        return null;
    }

    function _prepare_collection($items, Closure $callback = null)
    {
        if (is_object($items) && method_exists($items, 'toArray')) {
            $items = $items->toArray();
        }

        if (empty($items)) {
            return '';
        }

        if ($callback) {
            array_walk($items, $callback);
        }

        return $items;
    }

    /**
     * @param array $items
     * @param Closure|null $callback
     * @param array $attributes
     * @return string
     */
    function ul($items = [], Closure $callback = null, array $attributes = [])
    {
        $items = _prepare_collection($items, $callback);

        return '<ul ' . \admin\helpers\html_attributes($attributes) . '>' . '<li>' . implode('</li><li>', $items) . '</li>' . '</ul>';
    }

    /**
     * @param array $items
     * @param Closure|null $callback
     * @param array $attributes
     * @return string
     */
    function ol($items = [], Closure $callback = null, array $attributes = [])
    {
        $items = _prepare_collection($items, $callback);

        return '<ol ' . \admin\helpers\html_attributes($attributes) . '>' . '<li>' . implode('</li><li>', $items) . '</li>' . '</ol>';
    }

    function label($label = '', $class = 'bg-green')
    {
        return '<span class="label ' . $class . '">' . $label . '</span>';
    }

    function badge($label = '', $class = 'bg-green')
    {
        return '<span class="badge ' . $class . '">' . $label . '</span>';
    }
}

namespace admin\filter {

    use Closure;

    function text($label = '', Closure $query = null)
    {
        return [
            'type' => 'text',
            'label' => $label,
            'query' => $query,
        ];
    }

    /**
     * Generate DateRange filter config.
     *
     * @param string $label
     * @param mixed array|callable $options
     * @param callable|Closure $query
     * @param array $attributes
     * @return array
     */
    function select($label = '', $options = [], Closure $query = null, array $attributes = [])
    {
        if (!(is_array($options) || is_callable($options))) {
            trigger_error('Currently only Array or Closure can be provided as $options list', E_USER_ERROR);
        }

        return array_merge([
            'type' => 'select',
            'label' => $label,
            'options' => $options,
            'query' => $query,
        ], $attributes);
    }

    /**
     * Generate DateRange filter config.
     *
     * @param string $label
     * @param callable|Closure $query
     * @return array
     */
    function daterange($label = '', Closure $query = null)
    {
        return [
            'label' => $label,
            'type' => 'daterange',
            'query' => $query,
        ];
    }

    /**
     * Generate Date filter config.
     *
     * @param string $label
     * @param callable|Closure $query
     * @return array
     */
    function date($label = '', Closure $query = null)
    {
        return [
            'label' => $label,
            'type' => 'date',
            'query' => $query,
        ];
    }
}

namespace admin\form {

    function _input($type = 'text', $label = '', array $attributes = [])
    {
        $attributes = [
                'type' => $type,
                'label' => $label,
            ] + $attributes;

        return $attributes;
    }

    function key($label = '')
    {
        return _input('key', $label, []);
    }

    function text($label = '', array $attributes = [])
    {
        return _input('text', $label, $attributes);
    }

    function textarea($label = '', array $attributes = [])
    {
        return _input('textarea', $label, $attributes);
    }

    function tinymce($label = '', array $attributes = [])
    {
        return _input('tinymce', $label, $attributes);
    }

    function ckeditor($label = '', array $attributes = [])
    {
        return _input('ckeditor', $label, $attributes);
    }

    function tel($label = '', array $attributes = [])
    {
        return _input('tel', $label, $attributes);
    }

    function email($label = '', array $attributes = [])
    {
        return _input('email', $label, $attributes);
    }

    function number($label = '', array $attributes = [])
    {
        return _input('number', $label, $attributes);
    }

    /**
     * Generate Select input.
     *
     * @param string $label
     * @param array|callable|string $options - string options should have relation format: table.field
     * @param bool $multiple
     * @param array $attributes
     * @return array
     */
    function select($label = '', $options = [], $multiple = false, array $attributes = [])
    {
        $default = [
            'type' => 'select',
            'label' => $label,
            'options' => $options,
        ];

        if ($multiple) {
            $default['multiple'] = 'multiple';
        }

        return $default + $attributes;
    }

    function livesearch($url = '', $label = '', $options = [], $multiple = false, array $attributes = [])
    {
        $attributes = array_merge([
            'data-url' => $url,
            'data-type' => 'livesearch',
        ], $attributes);

        return select($label, $options, $multiple, $attributes);
    }

    /**
     * Build boolean [checkbox] input.
     *
     * @param string $label
     * @param array $attributes
     * @return array
     */
    function boolean($label = '', array $attributes = [])
    {
        return _input('boolean', $label, $attributes);
    }

    /**
     * Build date field.
     *
     * @param string $label
     * @param array $attributes
     * @return array
     */
    function date($label = '', array $attributes = [])
    {
        return _input('date', $label, $attributes);
    }

    /**
     * Build file input.
     *
     * @param string $label
     * @param array $attributes
     * @return array
     */
    function file($label = '', array $attributes = [])
    {
        return _input('file', $label, $attributes);
    }

    /**
     * Build image input.
     *
     * @param string $label
     * @param array $attributes
     * @return array
     */
    function image($label = '', array $attributes = [])
    {
        return file($label, $attributes);
    }

    /**
     * Set field description [optional].
     *
     * @param string $description
     * @return array
     */
    function description($description = '')
    {
        return ['description' => $description];
    }

    /**
     * Make field translatable.
     *
     * @return array
     */
    function translation()
    {
        return ['translatable' => true];
    }

    /**
     * Set relation to field.
     *
     * @Text   uses relation to fetch value from related table,
     * @example: users && user_detail.phone [user_detail.user_id => users.id]
     * @param $relation
     * @return array
     */
    function relation($relation)
    {
        if (!is_string($relation)) {
            trigger_error('Relation should be string of format <table>.<field>');
        }

        return ['relation' => $relation];
    }
}

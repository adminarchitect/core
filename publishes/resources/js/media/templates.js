angular
    .module('templates', [])
    .run(['$templateCache', function ($templateCache) {
        $templateCache.put('alert.html',
            '<div class="alert">alert</div>'
        );
    }]);

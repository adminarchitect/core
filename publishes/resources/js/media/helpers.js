(function () {
    'use strict';

    /**
     * @param {Scope} scope
     * @param {Function} callback
     */
    angular.safeApply = function (scope, callback) {
        scope[(scope.$$phase || scope.$root.$$phase)
            ? '$eval'
            : '$apply'](callback || function () {
                //
            });
    };

    /**
     * Detects if application is loaded under mobile device
     *
     * @todo: optimize method to increase detecting accuracy.
     */
    angular.isMobile = (function (a) {
        return /((iP([oa]+d|(hone)))|Android|WebOS|BlackBerry|windows (ce|phone))/i.test(a);
    })(navigator.userAgent || navigator.vendor || window.opera);

    /**
     * Detects device's online|offline status
     *
     * @returns {WorkerNavigator|Navigator|boolean}
     */
    angular.isOnline = function isOnline() {
        return (window.navigator && window.navigator.onLine);
    };

    /**
     * Find a parent scope containing property {prop}.
     * @param scope
     * @param prop
     * @returns {*}
     */
    angular.findScopeWithProperty = function (scope, prop) {
        var $parent = scope.$parent;
        do {
            if ($parent && $parent.hasOwnProperty(prop)) {
                break;
            } else if (!$parent) {
                return null;
            }
            $parent = $parent.$parent;
        } while (true);

        return $parent;
    };
})(angular);
Architector.factory('Selection', [
    function() {
        var $selected = [];

        var $factory = {
            all: function() {
                return $selected;
            },
            first: function() {
                return $selected.length ? $selected[0] : null;
            },
            has: function(file) {
                return !!$selected.filter(function(f) {
                    return f.basename === file.basename;
                }).length;
            },
            exists: function() {
                return $selected.length;
            },
            multiple: function() {
                return $selected.length > 1;
            },
            count: function() {
                return $selected.length;
            },
            toggle: function(file) {
                return this.has(file)
                    ? $selected.splice($selected.indexOf(file), 1)
                    : $selected.push(file);
            },
            clean: function() {
                $selected = [];
            },
            info: function(attribute) {
                if ($selected.length !== 1) {
                    return;
                }

                return (attribute && $selected[0].hasOwnProperty(attribute)) ? $selected[0][attribute] : $selected;
            },
        };

        return $factory;
    }]);
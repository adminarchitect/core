Architector.factory('FileStorage', ['$http', function ($http) {
    var factory = {};
    var basedir = window.REQUEST_PATH || '';

    function toFilename(file) {
        return file.basename;
    }

    factory.mkdir = function (name) {
        return $http.post('/admin/media', {
            basedir: basedir,
            name: name
        }).then(function (response) {
            return response;
        });
    };

    factory.move = function (files, target) {
        return $http.post('/admin/media/move', {
            files: files.map(toFilename),
            target: target,
            basedir: basedir
        }).then(function (response) {
            return response;
        });
    };

    factory.rename = function (from, to) {
        return $http.post('/admin/media/rename', {
            from: from.path,
            to: to
        });
    };

    factory.removeSelected = function (files) {
        return $http.post('/admin/media/remove', {
            files: files.filter(function (file) {
                return file.isFile;
            }).map(toFilename),
            directories: files.filter(function (file) {
                return file.isDir;
            }).map(toFilename)
        });
    };

    return factory;
}]);
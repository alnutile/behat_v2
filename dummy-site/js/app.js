var app = angular.module('exampleApp', [
    'ngRoute',
    'restangular',
    'appControllers'
]);

app.config(['$routeProvider',
    function($routeProvider){
        $routeProvider.
            when('/', {
                templateUrl: "templates/stream.html",
                controller: "AppController"
            }).otherwise(
                {
                    redirectTo: '/'
                }
            );
}]);
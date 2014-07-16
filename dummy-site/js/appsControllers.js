angular.module('appControllers', [])
    .controller('AppController', ['$scope', 'Restangular',
        function($scope, Restangular){
            $scope.test = "Testing 1 2 3";

            $scope.response = "Have not run yet :(";

            $scope.run = function() {
                Restangular.one('run_test').get().then(function(response){
                    console.log(response);
                });
            }
}]);
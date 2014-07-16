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

            $scope.streamProcess = function()
            {
                Restangular.one('stream_process').get().then(function(response){
                   $scope.response = response;
                });
            }
}]);
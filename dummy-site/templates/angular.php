<!doctype html>
<html lang="en" ng-app="exampleApp">
<head>
    <meta charset="UTF-8">
    <title>Behat Test Page</title>
    <link rel="stylesheet" href="templates/css/bootstrap.css">
</head>
<body>
    <div class="container">
        <div class="row clearfix">
            <div class="col-md-12 column">
                <ng-view></ng-view>
            </div>
        </div>
    </div>

    <script src="/bower_assets/lodash/dist/lodash.js"></script>
    <script src="/bower_assets/angular/angular.js"></script>
    <script src="/bower_assets/restangular/dist/restangular.js"></script>
    <script src="/bower_assets/angular-route/angular-route.js"></script>
    <script src="/bower_assets/angular-animate/angular-animate.js"></script>
    <script src="/bower_assets/angular-bootstrap/ui-bootstrap-tpls.js"></script>
    <script src="/js/app.js"></script>
    <script src="/js/appsControllers.js"></script>
</body>
</html>
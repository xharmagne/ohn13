// Foundation JavaScript
// Documentation can be found at: http://foundation.zurb.com/docs
$(document).foundation();

function ConfirmedController($scope, $http, $window, $location) {

    var that = this;
    that.scope = $scope;
    that.http = $http;
    that.window = $window;
    that.location = $location;

    that.init = function() {
        that.scope.safeApply(function () {
            that.scope.isBusy = true;
            that.scope.isPaymentSuccessful = false;
            that.scope.isPaymentFailed = false;
        });
        
        var qParams = that.location.search();
        var payerId = qParams.PayerID;
        var paymentId = qParams.token;

        if (!payerId) {
            that.scope.isBusy = false;
            that.scope.isPaymentFailed = true;
            return;
        }

        // Execute payment in Paypal
        var executePayReq = {
            method: 'POST',
            url: 'scripts/p2.php',
            headers: {
                "Content-Type": "application/json",
                "Accept": "application/json"
            },
            data: { "payer_id": payerId }
        }

        $http(executePayReq).success(function (response) {

            // Update payment details - paid
            var paymentDetails = {
                paymentId: paymentId,
                payerId: payerId,
                status: 'Paid'
            };

            var paidReq = {
                method: 'POST',
                url: 'scripts/paid.php',
                headers: {
                    "Content-Type": "application/x-www-form-urlencoded",
                    "Accept": "application/json"
                },
                data: paymentDetails
            }

            $http(paidReq).success(function (response) {
                that.scope.isBusy = false;
                that.scope.isPaymentSuccessful = true;
            }).error(function () {
                that.scope.isBusy = false;
                that.scope.isPaymentFailed = true;
            });


        }).error(function () {

            // Update payment details - failed
            var paymentDetails = {
                paymentId: paymentId,
                status: 'Failed'
            };

            var paidReq = {
                method: 'POST',
                url: 'scripts/paid.php',
                headers: {
                    "Content-Type": "application/x-www-form-urlencoded",
                    "Accept": "application/json"
                },
                data: paymentDetails
            }

            $http(paidReq).success(function (response) {
                that.scope.isBusy = false;
                that.scope.isPaymentSuccessful = true;
            }).error(function () {
                that.scope.isBusy = false;
                that.scope.isPaymentFailed = true;
            });
            
        });

    };

    
    //taken from https://coderwall.com/p/ngisma
    that.scope.safeApply = function (fn) {
        var phase = this.$root.$$phase;
        if (phase == '$apply' || phase == '$digest') {
            if (fn && (typeof (fn) === 'function')) {
                fn();
            }
        } else {
            this.$apply(fn);
        }
    };

    that.init();
}


//init angularJS
var confirmedapp = angular.module('confirmedApp', ['ngAnimate', 'angular-spinkit']).config(
        ['$locationProvider',
            function ($locationProvider) {

                $locationProvider.html5Mode(true);
            }
        ]);

//anuglar controller registration
confirmedapp.controller('ConfirmedController', function ($scope, $http, $window, $location) {
    return new ConfirmedController($scope, $http, $window, $location);
});
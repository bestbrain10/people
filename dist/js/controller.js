angular.module('people',['ngRoute'])
    .config(['$routeProvider','$locationProvider',function($routeProvider,$locationProvider){
        $routeProvider
            .when("/",{

            }).when("/home",{

            })
            .otherwise({redirectTo: '/'});
        //create 404 handler
        //$locationProvider.html5Mode(true);
        $locationProvider.hashPrefix('!');
    }])
    .directive('toApi',['api',function(api){
        return function(scope,element) {
            $(element).attr('action', api.url($(element).attr('to-api')));
        }
    }])
    .directive('ajaxForm',['api',function(api){
        return {
            require: '^form',
            link: function (scope, element, attr,formCtrl) {
                var form = $(element);
                var formButton = form.find("[type='submit']");
                form.submit(function (e) {
                    formButton.attr('disabled','disabled');
                    formText = formButton.html();
                    formButton.html('loading....');
                    e.preventDefault();
                    api.submit({
                        method: form.attr('method'),
                       url: form.attr('action'),
                        header:{'Content-Type' : "multipart/form-data"},
                       data: form.serialize()
                    }).then(function(data){
                        //check for success and call the callback
                        console.log(data.data);
                        scope.$applyAsync(attr.ajaxForm,data.data);
                    },function(data){
                        /*
                         data-toggle="tooltip" data-placement="top" title="" data-original-title="Tooltip on top"
                         */
                        //grab error handler if provided
                        console.error(data.data.error);
                        for(err in data.data.error){
                            formCtrl[err].$setValidity('valid',false);
                        }
                        formCtrl.$setValidity('valid',false);
                    });
                    formButton.html(formText);
                    formButton.removeAttr('disabled');
                });
            }
        }
    }])
    .factory('api',['$http',function($http){
        var api = {};
        api.url =  function(url){
            return "http://localhost/people/api/"+url;
        };
        api.submit = function(config){
            return $http(config);
        };
        api.get = function(url){
            return $http.get(api.url(url));
        };
        api.post = function(url,data){
            return $http({
                method: 'POST',
                url: url,
                data: data
            });

        };
        return api;
    }])
    .factory('user',['$http','api',function($http,api){
        var user = {};

        user.url = function(url){
             return  api.url("user/"+url);;
        };
        user.get = function(id){
          return api.get(user.url(id));
        };
        user.login = function(data){
            localstorage.setItem('user',JSON.stringify(data));
            //deprecated: return api.post(user.url("login"),data);
        };
        user.create = function(data){
            return api.post(user.url("create"),data);
        }
        return user;
    }])
    .controller('login',['$location','user',function($location,user){
        this.login = function(data){
            user.login(data.user);
            //emit login event
            //store user id in sessionStorage
        }
    }]).controller('register',['$location','user',function($location,user){
            this.register = function(){
                console.log("registration galore");
            };
    }]);
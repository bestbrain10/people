angular.module('people',[])
    .factory('api',['$http',function($http){
        var api = {};
        api.url = "http://localhost/people/index.php/api/";
        api.get = function(url){
            $http.get(api.url+""+url).then(function(data){
                return data.data;
            })
        };
        return api;
    }])
    .controller('login',['api',function(api){
        console.log(api.get("user/2"));
    }]).controller('register',function(){
        console.log("registering");
    });
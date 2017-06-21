<div class="col-md-offset-6 col-md-6 container">
    <form method="post" class="jumbotron" name="registerForm" ng-controller="register as r"  to-api="user/create" ajax-form="r.register()">
        <div class="input-group mb" ng-class="{'has-error':registerForm.email.$invalid}">
            <span class="input-group-addon"><span class="glyphicon glyphicon-envelope"></span></span>
            <input type="email" placeholder="email" title="email" class="form-control" pattern="\w*@\w{3,}\.\w{2,}" name="email" ng-model="u.email">
        </div>

        <div class="input-group mb" ng-class="{'has-error':registerForm.name.$invalid}">
            <span class="input-group-addon"><span class="glyphicon glyphicon-user"></span></span>
            <input data-tooltip="Livinus" type="text" placeholder="name" title="name" class="form-control" name="name" ng-model="u.name">
        </div>


        <div class="input-group mb" ng-class="{'has-error':registerForm.password.$invalid}">
            <span class="input-group-addon"><span class="glyphicon glyphicon-lock"></span></span>
            <input type="password" placeholder="password" title="password" class="form-control" name="password" ng-model="u.password">
        </div>

        <div class="input-group mb" ng-class="{'has-error':registerForm.gender.$invalid}">
            <span class="input-group-addon"><span class="glyphicon glyphicon-user"></span></span>
            <select  id="" class="form-control" ng-model="u.gender" name="gender">
                <option value="male">Male</option>
                <option value="female">Female</option>
            </select>
        </div>

        <div class="input-group mb" ng-class="{'has-error':registerForm.birthday.$invalid}">
            <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
            <input type="date" placeholder="birthday" pattern="\d{4}-\d{1,2}-\d{1,2}" name="birthday" ng-model="u.birthday" title="birthday" class="form-control">
        </div>

        <button class="mb btn btn-block btn-primary" ng-disabled="registerForm.$pristine || registerForm.$invalid">Sign Up</button>
    </form>
</div>
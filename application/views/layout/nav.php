<nav class="navbar navbar-default navbar-fixed-top">
    <div class="container-fluid">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="">People</a>
        </div>

        <!-- when logged in-->
        <?php if(false):?>
        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
            <ul class="nav navbar-nav">
                <li class="active"><a href="">Home <span class="sr-only">(current)</span></a></li>
                <li><a href="">Link</a></li>
                <li class="dropdown">
                    <a href="" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Dropdown <span class="caret"></span></a>
                    <ul class="dropdown-menu" role="menu">
                        <li><a href="">Action</a></li>
                        <li><a href="">Another action</a></li>
                        <li><a href="">Something else here</a></li>
                        <li class="divider"></li>
                        <li><a href="">Separated link</a></li>
                        <li class="divider"></li>
                        <li><a href="">One more separated link</a></li>
                    </ul>
                </li>
            </ul>
            <form class="navbar-form navbar-left" role="search">
                <div class="form-group">
                    <input type="text" class="form-control" placeholder="Search">
                </div>
                <button type="submit" class="btn btn-default"><span class="glyphicon glyphicon-search"></span></button>
            </form>
            <ul class="nav navbar-nav navbar-right">
                <li><a href="">Logout  <span class="glyphicon glyphicon-off"></span></a></li>
            </ul>
        </div>

        <!--when logged in-->
        <?php else: ?>
        <!--when not logged in-->

        <div id="bs-example-navbar-collapse-1" class="navbar-collapse collapse">
            <form method="post" class="navbar-form navbar-right" to-api="user/login" ng-controller="login as l" name="loginForm" ajax-form="l.login">
                <div class="input-group" ng-class="{'has-error':loginForm.email.$invalid}">
                    <span class="input-group-addon"><span class="glyphicon glyphicon-user"></span></span>
                    <input type="email" placeholder="Email" class="form-control" ng-model="u.email" name="email" pattern="\w{2,}@\w{3,}\.\w{2,}" required>
                </div>
                <div class="input-group" ng-class="{'has-error': loginForm.password.$invalid}">
                    <span class="input-group-addon"><span class="glyphicon glyphicon-lock"></span></span>
                    <input type="password" placeholder="Password" class="form-control" name="password" ng-model="u.password" pattern=".{6,32}" required>
                </div>
                <button type="submit" class="btn btn-primary" ng-disabled="loginForm.$pristine || loginForm.$invalid">Log in</button>
            </form>
        </div>

        <!--when not logged in-->
        <?php endif; ?>

    </div>
</nav>
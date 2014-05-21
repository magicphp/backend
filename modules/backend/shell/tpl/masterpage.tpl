<!DOCTYPE html>
<html>
<head>
    <title>{$app.title} &middot; Administration</title>
    <meta charset="UTF-8" />
    <meta name="publisher" content="MagicPHP" />
    
    <!-- CSS -->
    <link rel="stylesheet" href="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/themes/smoothness/jquery-ui.css" type="text/css" />
    <link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.0.3/css/bootstrap.min.css" type="text/css" />
    <link rel="stylesheet" href="{$cache.css}" type="text/css" />
    <link rel="icon" type="image/vnd.microsoft.icon" href="{$route.root}favicon.ico" />
    <link rel="apple-touch-icon" href="{$route.root}favicon.ico" />
    <link rel="shortcut icon" href="{$route.root}favicon.ico" />
        
    <!-- JS -->
    <script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js" type="text/javascript"></script>
    <script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/jquery-ui.min.js" type="text/javascript"></script>
    <script src="//netdna.bootstrapcdn.com/bootstrap/3.0.3/js/bootstrap.min.js" type="text/javascript"></script>
    <script type="text/javascript" src="{$route.root}modules/nsave_painel/shell/js/jquery.ui.map.full.min.js"></script>
</head>
<body>
    <div class="baNavbar navbar navbar-inverse">
        <div style="margin-left: 20px; margin-right: 20px">
            <a href="{$route.root}adm"><img src="{$route.root}<LOGO>" class="baBrand" title="{$app.title}" style="width: 96px; height: 29px; margin-top: 10px" /></a>
            <ul class="nav navbar-nav">
                {if {$user.root}}<li><a href="{$route.root}adm-admins"><i class="fa fa-lock"></i> Administrators</a></li>{endif}
            </ul>

            <ul class="nav navbar-nav navbar-right dropdown-caret">
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">{$user.name}</a>
                    <ul class="dropdown-menu">
                        <li><a href="#" data-toggle="modal" data-target="#changepassword">Change Password</a></li>
                        <li><a href="{$route.root}logout">Logout</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
    
    {$template} 
       
    <!-- Change Password -->
    <div class="modal fade" id="changepassword" style="text-align: left">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title">Change Password</h4>
                </div>
                <div class="modal-body">
                    <form method="post">
                        <div style="margin-bottom: 20px;">
                            <div style="margin-right: 20px">Current password:</div>
                            <input type="password" maxlength="150" name="alt" class="form-control" id="changepassord_currentpassword" />
                        </div>
                        <div style="margin-bottom: 20px;">
                            <div style="margin-right: 20px">New password:</div>
                            <input type="password" maxlength="150" name="alt" class="form-control" id="changepassord_newpassword" />
                        </div>
                        <div style="margin-bottom: 20px;">
                            <div style="margin-right: 20px">Repeat password:</div>
                            <input type="password" maxlength="150" name="alt" class="form-control" id="changepassord_renewpassword" />
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary baChangePassawordBtn">Change Password</button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Loading -->
    <div class="modal fade" id="loading">
        <div class="modal-dialog">
            <div class="modal-content">
                <div style="text-align: center; padding: 10px">
                    Loading...
                </div>
            </div>
        </div>
    </div>
    
    <script type="text/javascript">
        $(function(){
            $(".baChangePassawordBtn").click(function(){
                $.put("{$route.root}changepassword", {"currentpassword": $("#changepassord_currentpassword").val(), "newpassword": $("#changepassord_newpassword").val(), "renewpassword": $("#changepassord_renewpassword").val()}, function(mResult){
                    if(!mResult.status){
                        alert(mResult.error);
                    }
                    else{
                        alert("Password changed successfully!");
                        $("#changepassword").modal("hide");
                    }
                });
            });
        });
    </script>
    <!-- End of change password -->
        
    <script src="{$cache.js}" type="text/javascript"></script>
</body>
</html>
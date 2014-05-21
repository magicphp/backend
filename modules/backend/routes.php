<?php
    /**
     * Controlador de rotas
     * 
     * @package     MagicPHP Backend
     * @author      André Ferreira <andrehrf@gmail.com>
     */

    $oDb = Db::backend();
        
    //Base
    maPrivileges::LoadPrivileges();
    Routes::Set("adm", "GET", "maBase::DisplayIndexBackend"); 
    Routes::Set("login", "GET", "maBase::DisplayLogin");
    Routes::Set("login", "POST", "maBase::Login");
    Routes::Set("logout", "GET", "maBase::Logout");
    Routes::Set("changepassword", "PUT", "maBase::ChangePassword");
    
    //Administrators
    Routes::Set("adm-admins", "GET", "maUsers::Display");
    Routes::Set("adm-admins-refreshgrid", "GET", "maUsers::GridRefresh");
    Routes::Set("adm-admins-insert", "POST", "maUsers::Display");
    maBackendController::SetBackendStaticRoutes("adm-admins", "admins", $oDb->administrators);
        
    Routes::SetDynamicRoute(function(){Output::SendHTTPCode(404); });
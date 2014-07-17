<?php
    /**
     * Controller based administrative module
     * 
     * @package     MagicPHP Backend
     * @author      André Ferreira <andrehrf@gmail.com>
     */

    class maBase{        
        /**
         * Function to display logins screen
         * 
         * @static
         * @access public
         * @return void
         */
        public static function DisplayLogin(){              
            if(Session::CheckAuthentication())
                Output::Redirect(Storage::Join("route.root", "adm"));
            
            Output::SetNamespace("login");
            Output::SetTemplate(Storage::Join("dir.shell.backend.tpl", "login.tpl"));
            Output::AppendCSS(Storage::Join("dir.shell.backend.css", "login.css"));
            Output::Send();
        }
        
        /**
         * Function to login
         * 
         * @static
         * @access public
         * @return void
         */
        public static function Login(){
            if(Session::CheckAuthentication())
                Output::Redirect(Storage::Join("route.root", "adm"));
               
            //Verificando administradores
            $oDb = Db::backend();
            $oDb->administrators->Select("id", "email", "name", "privileges")
                                 ->Filter("email", $_POST["login_user"])
                                 ->Filter("password", sha1($_POST["login_password"]))
                                 ->Filter("active", 1)
                                 ->Limit(1)
                                 ->Execute(function($aData, $mError){
                                    if(count($aData) > 0){
                                        $aUser = $aData[0];
                                        Session::Login($aUser["id"], $aUser["email"], $aUser["name"], 28800, true);
                                        Session::Set("user.privileges", $aUser["privileges"]);
                                        maPrivileges::LoadPrivileges();
                                        Output::Redirect(Storage::Join("route.root", "adm"));
                                    }
                                });            
                          
            Output::Redirect(Storage::Join("route.root", "login"));
        }
        
        /**
         * Function for logout session
         * 
         * @static
         * @access public
         * @return void
         */
        public static function Logout(){
            Session::Logout();
            Output::Redirect(Storage::Join("route.root", "login"));        
        }
                
        /**
         * Function to configure information page output backend
         * 
         * @static
         * @access public
         * @param string $sTemplate
         * @return void
         */
        public static function SetBackendOutput($sTemplate){            
            Output::SetNamespace("backend");
            Output::SetTemplate(Storage::Join("dir.shell.backend.tpl", $sTemplate), Storage::Join("dir.shell.backend.tpl", "masterpage.tpl"));
             
            //CSS
            Output::AppendCSS(Storage::Join("dir.shell.backend.css", "font-awesome.css"));
            Output::AppendCSS(Storage::Join("dir.shell.backend.css", "jquery.switchButton.css"));
            Output::AppendCSS(Storage::Join("dir.shell.backend.css", "dataTables.bootstrap.css"));
            Output::AppendCSS(Storage::Join("dir.shell.backend.css", "masterpage.css"));
            
            //Javascript
            Output::AppendJS(Storage::Join("dir.shell.backend.js", "jquery.editinplace.js"));
            Output::AppendJS(Storage::Join("dir.shell.nsave.adm.js", "jquery.switchButton.js"));
            Output::AppendJS(Storage::Join("dir.shell.nsave.adm.js", "jquery.dataTables.js"));
            Output::AppendJS(Storage::Join("dir.shell.nsave.adm.js", "jquery.mask.min.js"));
            Output::AppendJS(Storage::Join("dir.shell.nsave.adm.js", "jquery.confirm.js"));
            Output::AppendJS(Storage::Join("dir.shell.nsave.adm.js", "dataTables.bootstrap.js"));
            Output::AppendJS(Storage::Join("dir.shell.backend.js", "masterpage.js"));
        }
        
        /**
         * Function to display the main screen of the administration
         * 
         * @static
         * @access public
         * @return void
         */
        public static function DisplayIndexBackend(){
            if(!Session::CheckAuthentication())
                Output::Redirect(Storage::Join("route.root", "login"));
            
           maBase::SetBackendOutput("index.tpl");
           Output::Send();
        }
        
        /**
         * Function to change password
         * 
         * @static
         * @access public
         * @return void
         */
        public static function ChangePassword(){
            if(Session::CheckAuthentication()){ 
                $sCurrentPassword = Storage::Get("put.currentpassword");    
                $sNewPassword = Storage::Get("put.newpassword");    
                $sReNewPassword = Storage::Get("put.renewpassword");    
                
                if(empty($sCurrentPassword) || empty($sNewPassword)){
                    ioBackendController::ReturnAjax(false, "Senha atual ou nova senha inválidas.");
                }
                else if($sNewPassword != $sReNewPassword){
                    ioBackendController::ReturnAjax(false, "A nova senha não é igual a sua repetição, por favor digite novamente.");
                }
                else{                    
                    $oDb = Db::backend();
                    $oDb->administrators->Select("id")
                                         ->Filter("email", Storage::Get("user.username"))
                                         ->Filter("password", sha1($sCurrentPassword))
                                         ->Filter("active", 1)
                                         ->Limit(1)
                                         ->Execute(function($aData, $mError){
                                            if(is_array($aData)){
                                                if(count($aData) > 0){
                                                  $oDb = Db::backend();
                                                  $oDb->administrators->Update(array("password" => sha1(Storage::Get("put.newpassword"))), array("id" => $aData[0]["id"]), 1, function($mData, $mError){
                                                      if($mData)
                                                        maBackendController::ReturnAjax(true);
                                                      else
                                                        maBackendController::ReturnAjax(false, $mError);
                                                  });
                                                }
                                                else{
                                                    maBackendController::ReturnAjax(false, "A senha atual não é válida.");
                                                }
                                            }
                                            else{
                                                maBackendController::ReturnAjax(false, "A senha atual não é válida.");
                                            }
                                        });
                }
            }
            else{
                ioBackendController::ReturnAjax(false, Storage::Get("lng.session.expired"));
            }
        }
        
        /**
         * Function for sending mail via SMTP
         * 
         * @static
         * @access public
         * @param string $sTo
         * @param string $sToName
         * @param string $sSubject
         * @param string $sBody
         * @return boolean
         */
        public static function SendEmail($sTo, $sToName, $sSubject, $sBody){
            $oMail = new PHPMailer();
            
            $oMail->isSMTP();
            $oMail->SMTPDebug  = 0;
            $oMail->Host = Storage::Get("smtp.hostname");
            $oMail->SMTPAuth = true;
            $oMail->Username = Storage::Get("smtp.username");                 
            $oMail->Password = Storage::Get("smtp.password");                          
            $oMail->SMTPSecure = Storage::Get("smtp.secure");    
            $oMail->Port = Storage::Get("smtp.port");
            $oMail->Helo = Storage::Get("smtp.hostname"); 
            
            $oMail->From = Storage::Get("smtp.username");
            $oMail->FromName = Storage::Get("smtp.fromname");
            $oMail->addAddress($sTo, $sToName);    
            
            $oMail->isHTML(true); 
            $oMail->Subject = $sSubject;
            $oMail->Body = $sBody;
            $oMail->AltBody = strip_tags($sBody);
            
            return $oMail->send();
        }
    }
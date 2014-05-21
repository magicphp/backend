<?php
    /**
     * Controller User screen
     * 
     * @package     MagicPHP Backend
     * @author      André Ferreira <andrehrf@gmail.com>
     */

    class maUsers extends maBackendController{
        /**
         * Function to display the screen
         * 
         * @static
         * @access public
         * @return void
         */
        public static function Display() {
            if(maPrivileges::AuthorizedAccess("users", "view")){
                maBase::SetBackendOutput("users.tpl");

                //Listando usuários
                $oDb = Db::backend();
                $oDb->administrators->Select("id", "email", "name")
                                     ->Execute(function($aData, $mError){
                                          Output::ReplaceList("users", $aData);
                                     });
                              
                //Listando privilêgios
                $aPrivileges = array(array("description" => "Administrators", "usercase" => "admins", "menu" => true));
                Output::ReplaceList("privileges", $aPrivileges);
                Output::Send();
            }
        }
        
        /**
         * Function to update the grid 
         * 
         * @static
         * @access public
         * @return void
         */
        public static function GridRefresh(){
            if(maPrivileges::AuthorizedAccess("users", "view")){
                maBase::SetBackendOutput("users.tpl");
                
                 //Listando privilêgios
                $aPrivileges = array(array("description" => "Administrators", "usercase" => "admins", "menu" => true));
                Output::ReplaceList("privileges", $aPrivileges);
                
                $oDb = Db::backend();
                $oDb->administrators->Select("id", "username", "name", "email")
                                     ->Execute(function($aData, $mError){
                                          Output::ExtractList("users");
                                          Output::ReplaceList("users", $aData);
                                          Output::Send();
                                     });
            }
        }
        
        /**
         * Function to generate random passwords
         * 
         * @see http://www.devmedia.com.br/post-17497-Gerando-Senhas-Seguras-com-PHP.html
         * @access public
         * @param integer $iSize Password length
         * @param boolean $bUppercase Sets the password should contain uppercase
         * @param boolean $bLowercase Sets the password should contain lowercase letters
         * @param boolean $bNumbers Sets the password should contain numbers
         * @param boolean $bSymbols Sets the password should contain special characters
         * @return string
         */
        public static function GenerateSecurePassword($iSize, $bUppercase = true, $bLowercase = true, $bNumbers = true, $bSymbols = true){
            $sUppercase = "ABCDEFGHIJKLMNOPQRSTUVYXWZ"; 
            $sLowercase = "abcdefghijklmnopqrstuvyxwz"; 
            $sNumbers = "0123456789"; 
            $sSymbols = "!@#$%&*_+=";
            $sPasswordMap = "";

            if($bUppercase)
                $sPasswordMap .= str_shuffle($sUppercase);

            if($bLowercase)
                $sPasswordMap .= str_shuffle($sLowercase);

            if($bNumbers)
                $sPasswordMap .= str_shuffle($sNumbers);

            if($bSymbols)
                $sPasswordMap .= str_shuffle($sSymbols);

            return trim(substr(str_shuffle($sPasswordMap), 0, $iSize));
        }
    }

<?php
    /**
     * Controlador de acesso do backend
     * 
     * @package     MagicPHP Backend
     * @author      André Ferreira <andrehrf@gmail.com>
     */

    class maPrivileges{
        /**
         * Função para verificar autorização de acesso 
         * 
         * @static
         * @access public
         * @param string $sUsercase
         * @param string $sType
         * @return boolean
         */
        public static function AuthorizedAccess($sUsercase, $sType){
            if(Session::CheckAuthentication()){
                return (Storage::Get("user.root", false)) ? true : Storage::Get("privilege.{$sType}.{$sUsercase}", false);
            }
            else{
                Output::Redirect(Storage::Join("route.root", "login"));
                return false;
            }
        }
        
        /**
         * Função para salvar os privilégios
         * 
         * @static
         * @access public
         * @param integer $iID
         * @return void
         */
        public static function SavePrivileges($iID){
            $oDb = Db::backend();
            $iID = intval($iID);
            $aStorage = Storage::GetList();
            $aData = array();

            foreach($aStorage as $sKey => $mValue){
                if(substr($sKey, 0, 4) == "put.")
                    $aData[str_replace("put.", "", $sKey)] = $mValue;
            }
            
            if(count($aData) > 0 && $iID > 0)
                $oDb->administrators->Update(array("privilegios" => json_encode($aData)), array("id" => $iID), 1);
                
            ioBackendController::ReturnAjax(true);
        }
        
        /**
         * Função para retornar lista de privilgégios de um usuário
         * 
         * @static
         * @access public
         * @param integer $iID
         * @return void
         */
        public static function GetPrivileges($iID){
            $iID = intval($iID);
            
            if($iID){
                $oDb = Db::backend();
                $oDb->administrators->Select("privilegios")
                                    ->Filter("id", $iID)
                                    ->Execute(function($aData, $mError){ 
                                        $aPrivileges = json_decode($aData[0]["privilegios"], true);

                                        if(count($aPrivileges) > 0)
                                          foreach($aPrivileges as $iKey => $mItem)
                                            $aPrivileges[$iKey] = ($mItem == "true");

                                        $aPrivileges = (empty($aPrivileges)) ? json_encode(array()) : json_encode($aPrivileges);
                                        die($aPrivileges);
                                    });  
            }
            else{
                die(json_encode(array()));
            }
        }
        
        /**
         * Função para converter privilegios em JSON para Storage
         * 
         * @static
         * @access public
         * @return void
         */
        public static function LoadPrivileges(){
            //if(!empty(Session::Get("user.privileges", ""))){
                $aPrivileges = json_decode(Session::Get("user.privileges", array()), true);
               
                if($aPrivileges != null)
                    foreach($aPrivileges as $sKey => $sValue)
                        Storage::Set("privilege.".str_replace("_", ".", $sKey), ($sValue == "true"));
            //}
        }
    }

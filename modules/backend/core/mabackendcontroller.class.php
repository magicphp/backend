<?php
    /**
     * Parent controller class backend
     * 
     * @package     MagicPHP Backend
     * @author      André Ferreira <andrehrf@gmail.com>
     */

    class maBackendController{
        /**
         * Function to configure basic features of the routes backend
         * 
         * @static
         * @access protected
         * @param string $sPrefix
         * @param string $sUsercase
         * @param array $aParams
         * @return void
         */
        public static function SetBackendStaticRoutes($sPrefix, $sUsercase, $oDb){                                        
            if(maPrivileges::AuthorizedAccess($sUsercase, "insert"))
                Routes::Set($sPrefix."-insert", "POST", array("ioBackendController::Insert", $oDb)); 
                    
            if(maPrivileges::AuthorizedAccess($sUsercase, "edit")){
                Routes::Set($sPrefix."-switchbuttom", "PUT", array("ioBackendController::SwitchButtom", $oDb));
                Routes::Set($sPrefix."-editinplace", "PUT", array("ioBackendController::EditInPlace", $oDb));
                Routes::Set($sPrefix."-edit", "PUT", array("ioBackendController::Edit", $oDb));
                Routes::Set($sPrefix."-order", "PUT", array("ioBackendController::Order", $oDb));
            }
            
            if(maPrivileges::AuthorizedAccess($sUsercase, "delete"))
                Routes::Set($sPrefix."-remove/{id}", "DELETE", array("ioBackendController::Remove", $oDb));
        }
        
        /**
         * Response function to an ajax request
         * 
         * @access public
         * @return void
         */        
        public static function ReturnAjax($bStatus = true, $mError = null){
            if($bStatus)
                die(json_encode(array("status" => true)));
            else
                die(json_encode(array("status" => false, "error" => $mError)));
        }
        
        /**
         * Function to insert new record
         * 
         * @static
         * @access public
         * @return void
         */
        public static function Insert($oDb){
            if(Session::CheckAuthentication()){
                $aData = $_POST;
                
                if(is_object($oDb) && count($aData) > 0){                    
                    //Fix para checkbox
                    foreach($aData as $sKey => $mValue)
                        if($mValue == "true" || $mValue == "false")
                            $aData[$sKey] = ($mValue == "true") ? "1" : "0";
                        
                    if(Events::Has(Storage::Get("route") . "_BeforeInserting"))
                        $aData = Events::Call(Storage::Get("route") . "_BeforeInserting", $aData);
                    
                    $oDb->Insert($aData, function($mData, $mError){
                        if(Events::Has(Storage::Get("route") . "_AfterInserting"))
                            Events::Call(Storage::Get("route") . "_AfterInserting");
                        
                        if($mData)
                            ioBackendController::ReturnAjax(true);
                        else
                            ioBackendController::ReturnAjax(false, $mError);
                    });
                }
                else{
                    self::ReturnAjax(false, Storage::Get("lng.insert.generic.error"));
                }  
            }
            else{
                self::ReturnAjax(false, Storage::Get("lng.session.expired"));
            }
        }

        /**
         * Function to enable / disable boolean fields
         * 
         * @static
         * @access public
         * @param object $oDb Table object for communication with the bank
         * @return void
         */
        public static function SwitchButtom($oDb){
            if(Session::CheckAuthentication()){
                $sField = @preg_replace("/[^a-zA-Z0-9]/", "", Storage::Get("put.rel"));
                $iId = @intval(Storage::Get("put.id"));
                $bValue = (Storage::Get("put.value") == "true") ? "1" : "0";
                
                if(is_object($oDb) && !empty($sField) && $iId > 0){
                    if(Events::Has(Storage::Get("route") . "_BeforeSwitchButtom"))
                        Events::Call(Storage::Get("route") . "_BeforeSwitchButtom");
                    
                    $sIdField = Storage::Get("field.id", "id");
                    
                    $oDb->Update(array($sField => $bValue), array($sIdField => $iId), 1, function($mData, $mError){
                        if($mData)
                            ioBackendController::ReturnAjax(true);
                        else
                            ioBackendController::ReturnAjax(false, $mError);
                    });
                }
                else{
                    self::ReturnAjax(false, Storage::Get("lng.edit.generic.error"));
                }   
            }
            else{
                self::ReturnAjax(false, Storage::Get("lng.session.expired"));
            }
        }
        
        /**
         * Function to change text without complete edition
         * 
         * @static
         * @access public
         * @param object $oDb Table object for communication with the bank
         * @return void
         */
        public static function EditInPlace($oDb){
            if(Session::CheckAuthentication()){                
                $sField = @preg_replace("/[^a-zA-Z0-9]/", "", Storage::Get("put.rel"));
                $iId = @intval(Storage::Get("put.id"));
                $sValue = Storage::Get("put.value");

                if(is_object($oDb) && !empty($sField) && !empty($sValue)){
                    if(Events::Has(Storage::Get("route") . "_BeforeEditInPlace"))
                        Events::Call(Storage::Get("route") . "_BeforeEditInPlace");
                                        
                    $sIdField = Storage::Get("field.id", "id");
                    
                    $oDb->Update(array($sField => $sValue), array($sIdField => $iId), 1, function($mData, $mError){
                        if(Events::Has("AfterEditInPlace"))
                            Events::Call("AfterEditInPlace");
                        
                        if($mData)
                            ioBackendController::ReturnAjax(true);
                        else
                            ioBackendController::ReturnAjax(false, $mError);
                    });
                }
                else{
                    self::ReturnAjax(false, Storage::Get("lng.edit.generic.error"));
                }   
            }
            else{
                self::ReturnAjax(false, Storage::Get("lng.session.expired"));
            }
        }
                
        /**
         * Function to change registry
         * 
         * @static
         * @access public
         * @param object $oDb Table object for communication with the bank
         * @return void
         */
        public static function Edit($oDb){
            if(Session::CheckAuthentication()){ 
                if(is_object($oDb)){
                    $aStorage = Storage::GetList();
                    $aData = array();

                    foreach($aStorage as $sKey => $mValue){
                        if(substr($sKey, 0, 4) == "put.")
                            $aData[str_replace("put.", "", $sKey)] = $mValue;
                    }
                    
                    if(Events::Has(Storage::Get("route") . "_BeforeEditing"))
                        $aData = Events::Call(Storage::Get("route") . "_BeforeEditing", $aData);

                    $sIdField = Storage::Get("field.id", "id");
                    $iId = intval($aData[$sIdField]);
                    unset($aData[$sIdField]);
                    
                    if($iId > 0 && count($aData) > 0){
                        foreach($aData as $sKey => $mValue)
                            if($mValue == "true" || $mValue == "false")
                                $aData[$sKey] = ($mValue == "true") ? "1" : "0"; 

                        $oDb->Update($aData, array($sIdField => $iId), 1, function($mData, $mError){
                            if(Events::Has(Storage::Get("route") . "_AfterEditing"))
                                Events::Call(Storage::Get("route") . "_AfterEditing");

                            if($mData)
                                ioBackendController::ReturnAjax(true);
                            else
                                ioBackendController::ReturnAjax(false, $mError);
                        });
                    }
                    else{
                        self::ReturnAjax(false, Storage::Get("lng.edit.generic.error"));
                    } 
                }
                else{
                    self::ReturnAjax(false, Storage::Get("lng.edit.generic.error"));
                }   
            }
            else{
                self::ReturnAjax(false, Storage::Get("lng.session.expired"));
            }
        }
        
        /**
         * Function to change record order
         * 
         * @static
         * @access public
         * @param object $oDb Table object for communication with the bank
         * @return void
         */
        public static function Order($oDb){
            if(Session::CheckAuthentication()){ 
                $sSets = Storage::Get("put.sets");
                
                
                if(is_object($oDb) && !empty($sSets)){
                    $sSets = substr($sSets, 0, -1);//Bugfix
                    $aSets = explode(",", $sSets);
                    
                    if(count($aSets) > 0){
                        foreach($aSets as $sItem){
                            list($iID, $iPosition) = explode("-", $sItem);
                            $iID = intval($iID);
                            $iPosition = intval($iPosition);

                            if($iID > 0 && $iPosition > 0)
                                $oDb->Update(array("posicao" => $iPosition), array("id" => $iID), 1, function(){});
                        }
                        
                        ioBackendController::ReturnAjax(true);
                    }
                    else{
                        self::ReturnAjax(false, Storage::Get("lng.edit.generic.error"));
                    } 
                }
                else{
                    self::ReturnAjax(false, Storage::Get("lng.edit.generic.error"));
                }  
            }
            else{
                self::ReturnAjax(false, Storage::Get("lng.session.expired"));
            }
        }
        
        /**
         * Function to unregister
         * 
         * @static
         * @access public
         * @param object $oDb Table object for communication with the bank
         * @param integer $iID ID of the record to be removed
         * @return void
         */
        public static function Remove($oDb, $iID){
            if(Session::CheckAuthentication()){     
                $iID = intval($iID);
                               
                if(is_object($oDb) && $iID > 0){
                    if(Events::Has(Storage::Get("route") . "_BeforeRemoving"))
                        Events::Call(Storage::Get("route") . "_BeforeRemoving", $iID);
                    
                    $sIdField = Storage::Get("field.id", "id");//Bugfix para tabelas cuja chave primária não se chama ID
                   
                    $oDb->Delete(array($sIdField => $iID), 1, function($mData, $mError){
                        if(Events::Has(Storage::Get("route") . "_AfterRemoving"))
                            Events::Call(Storage::Get("route") . "_AfterRemoving", $iId);
                        
                        if($mData)
                            ioBackendController::ReturnAjax(true);
                        else
                            ioBackendController::ReturnAjax(false, $mError);
                    });
                }
                else{
                    self::ReturnAjax(false, Storage::Get("lng.edit.generic.error"));
                } 
            }
            else{
                self::ReturnAjax(false, Storage::Get("lng.session.expired"));
            }
        }
    }
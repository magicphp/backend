<?php
    /**
     * Event Tracker
     * 
     * @package     MagicPHP Backend
     * @author      André Ferreira <andrehrf@gmail.com>
     */
    
    /**
     * ADMINISTRATORS
     */
    Events::SetPerRoute("BeforeInserting", "adm-admins-insert", "POST", function($aData) {
        $sPassword = maUsers::GenerateSecurePassword(5, true, true, true, false);
        $aData["senha"] = sha1($sPassword);
        
        $mResult = maBase::SendEmail($aData["email"], $aData["name"], "administrative password", "Your password for administrative access: ".$sPassword);

        if($mResult)
            return $aData;
        else
            die(json_encode(array("status" => false)));
    });
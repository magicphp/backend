<?php
    /**
     * Settings
     * 
     * @package     MagicPHP Backend
     * @author      André Ferreira <andrehrf@gmail.com>
     */

    Session::Start("<SESSION NAME>", Storage::Join("dir.cache", "sessions"));
    
    //Configurações base
    Storage::Set("app.title", "MagicPHP Backend");
          
    //Diretório de template
    Storage::Set("dir.shell.backend", __DIR__ . SP . "shell" . SP);
    Storage::Set("dir.shell.backend.css", Storage::Join("dir.shell.backend", "css") . SP);
    Storage::Set("dir.shell.backend.js", Storage::Join("dir.shell.backend", "js") . SP);
    Storage::Set("dir.shell.backend.img", Storage::Join("dir.shell.backend", "img") . SP);
    Storage::Set("dir.shell.backend.tpl", Storage::Join("dir.shell.backend", "tpl"). SP);
    
    //Conexão com o banco de dados 
    Db::CreateConnection("backend", "mysql", "<HOSTNAME>", "<USERNAME>", "<PASSWORD>", "<SCHEMA>");
    Db::SetCharset("backend", "UTF8");
    
    //Confiurações de SMTP
    Storage::Set("smtp.hostname", "<SMTP HOSTNAME>");
    Storage::Set("smtp.fromname", "<SMTP FROM NAME>");
    Storage::Set("smtp.username", "<SMTP USERNAME>");
    Storage::Set("smtp.password", "<SMTP PASSWORD>");
    Storage::Set("smtp.secure", "tls");//Optional
    Storage::Set("smtp.port", 587);//Optional
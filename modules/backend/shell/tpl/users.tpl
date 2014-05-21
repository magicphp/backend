<div class="ioContainer">
    <div class="well" style="position: relative">
        <h3><i class="fa fa-lock"></i>&nbsp;&nbsp;Administrators</h3>
        
        <div style="position: absolute; top: 10px; right: 10px">
            <button type="button" class="btn btn-default btn-lg" data-toggle="modal" data-target="#insert">Register</button>
            <div class="modal fade baInsertModal" id="insert" style="text-align: left">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                            <h4 class="modal-title">Inserir</h4>
                        </div>
                        <div class="modal-body">
                            <form class="baInsert" method="post">
                                <div class="alert alert-warning">
                                    The password will be created automatically by the system and sent by email.
                                </div>
                                <div style="margin-bottom: 20px;">
                                    <div style="margin-right: 20px">Name:</div>
                                    <input type="text" maxlength="100" name="name" class="form-control" />
                                </div>
                                <div style="margin-bottom: 20px;">
                                    <div style="margin-right: 20px">Email:</div>
                                    <input type="text" maxlength="150" name="email" class="form-control"  />
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-primary baInsertModalSaveButton">Insert new record</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div style="text-align: right; margin-bottom: 20px;">
        <div class="modal fade baInsertModal" id="privileges" style="text-align: left">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h4 class="modal-title">Privilégios</h4>
                    </div>
                    <div class="modal-body">
                        <table class="table table-striped table-bordered" cellpadding="0" cellspacing="0" border="0">
                            <thead>
                                <tr>
                                    <td>Descrição</td>
                                    <td><i class="glyphicon glyphicon-eye-open"></i></td>
                                    <td><i class="glyphicon glyphicon-pencil"></i></td>
                                    <td><i class="glyphicon glyphicon-remove"></i></td>
                                </tr>
                            </thead>
                            <tbody>
                                {list:privileges}
                                <tr>
                                    <td><span>{list.privileges.description}</span></td>
                                    <td><input type="checkbox" class="baSwitchButton baSwitchButtonForm" id="view_{list.privileges.usercase}" name="view_{list.privileges.usercase}"></td>
                                    <td><input type="checkbox" class="baSwitchButton baSwitchButtonForm" id="edit_{list.privileges.usercase}" name="edit_{list.privileges.usercase}"></td>
                                    <td><input type="checkbox" class="baSwitchButton baSwitchButtonForm" id="remove_{list.privileges.usercase}" name="remove_{list.privileges.usercase}"></td>
                                </tr>
                                {end}
                            </tbody>
                        </table>
                        <input type="hidden" id="id" />
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
                        <button type="button" class="btn btn-primary baPrivilegesSaveButton">Salvar</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <table class="table table-striped table-bordered baDataTable" cellpadding="0" cellspacing="0" border="0">
        <thead>
            <tr>
                <td>Name</td>
                <td>E-mail</td>
                <td>&nbsp;</td>
            </tr>
        </thead>
        <tbody>
        {list:users}
            <tr>
                <td><span>{list.users.name}</span></td>     
                <td><span>{list.users.email}</span></td>     
                <td style="text-align: right">
                    <button title="Remover" class="btn btn-danger baGridDelete" id="{list.users.id}" rel="{list.users.nome}"><i class="glyphicon glyphicon-remove" style="color: #FFF"></i> Remover</button>
                </td>
            </tr>
        {end}
        </tbody>
    </table>
    <script type="text/javascript">
        $(".baPrivileges").click(function(){
            $("#id").val($(this).attr("id"));

            $.get(window.location.href + "-get-privileges/"+$(this).attr("id"), null, function(aData){
                aData = JSON.parse(aData);

                $("#privileges input[type=checkbox]").each(function(){
                    $(this).parent().find(".off").css("display", "block");
                    $(this).parent().find(".on").css("display", "none");
                    $(this).parent().find(".switch-button-background").removeClass("checked");
                    $(this).attr("checked", false);
                    $(this).prop("checked", false);
                });

                for(var iKey in aData){
                    console.log(iKey+": "+aData[iKey]);

                    if(aData[iKey]){
                        $("#"+iKey).parent().find(".off").css("display", "none");
                        $("#"+iKey).parent().find(".on").css("display", "block");
                        $("#"+iKey).parent().find(".switch-button-background").addClass("checked");
                        $("#"+iKey).attr("checked", true);
                    }
                    else{
                        $("#"+iKey).parent().find(".off").css("display", "block");
                        $("#"+iKey).parent().find(".on").css("display", "none");
                        $("#"+iKey).parent().find(".switch-button-background").removeClass("checked");
                        $("#"+iKey).attr("checked", false);
                    }
                }

                $("#privileges").modal("show");
            })
        });

        $(".baPrivilegesSaveButton").click(function(){
            var aData = $("#privileges").serializeObject();

            $.put(window.location.href + "-save-privileges/"+$("#id").val(), aData, function(){

            });

            $("#privileges").modal("hide");
        });
    </script>
</div>
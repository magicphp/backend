$(function() {
    BindDataTable();
    BindEditInPlace();
    BindEdit();
    BindInsert();
    BindDragOrder();
    BindSwitchButton();
});

/**
 * REST functions for jQuery
 * 
 * @see http://stackoverflow.com/questions/2153917/how-to-send-a-put-delete-request-in-jquery
 */
function _ajax_request(url, data, callback, type, method) {
    if (jQuery.isFunction(data)) {
        callback = data;
        data = {};
    }
    return jQuery.ajax({
        type: method,
        url: url,
        data: data,
        success: callback,
        dataType: type
    });
}

jQuery.extend({
    put: function(url, data, callback) {
        return _ajax_request(url, data, callback, "json", "PUT");
    },
    delete_: function(url, data, callback) {
        return _ajax_request(url, data, callback, "json", "DELETE");
    }
});

/**
 * Serialization function to form
 * 
 * @see http://stackoverflow.com/questions/11338774/serialize-form-data-to-json
 */
$.fn.serializeObject = function() {
    var o = {};

    //    var a = this.serializeArray();
    $(this).find('input[type="hidden"], input[type="text"], input[type="password"], input[type="checkbox"]:checked, input[type="radio"]:checked, select').each(function() {
        if ($(this).attr('type') == 'hidden') { //if checkbox is checked do not take the hidden field
            var $parent = $(this).parent();
            var $chb = $parent.find('input[type="checkbox"][name="' + this.name.replace(/\[/g, '\[').replace(/\]/g, '\]') + '"]');
            if ($chb != null) {
                if ($chb.prop('checked'))
                    return;
            }
        }
        if (this.name === null || this.name === undefined || this.name === '')
            return;
        var elemValue = null;
        if ($(this).is('select'))
            elemValue = $(this).find('option:selected').val();
        else
            elemValue = this.value;
        if (o[this.name] !== undefined) {
            if (!o[this.name].push) {
                o[this.name] = [o[this.name]];
            }
            o[this.name].push(elemValue || '');
        } else {
            o[this.name] = elemValue || '';
        }
    });

    $('input[type="checkbox"]', this).each(function() {
        o[$(this).attr("name")] = $(this).prop("checked");
    });

    $('input[type="hidden"]', this).each(function() {
        o[$(this).attr("name")] = $(this).val();
    });

    return o;
}

/**
 * Function to perform serial processing in
 * 
 * @see http://book.mixu.net/ch7.html
 * @param array aCallbacks
 * @param function fLast
 * @return void
 */
function Serial(aCallbacks, fLast) {
    var aResults = [];

    function Next() {
        var fCallback = aCallbacks.shift();

        if (fCallback) {
            fCallback(function() {
                aResults.push(Array.prototype.slice.call(arguments));
                Next();
            });
        }
        else {
            fLast(aResults);
        }
    }

    Next();
}
;

/**
 * Function to enable Switch Button
 * 
 * @access public
 * @return void
 */
function BindSwitchButton() {
    $(".SwitchButton").each(function() {
        if (!$(this).hasClass("SwitchButtonActivated")) {
            $(this).addClass("SwitchButtonActivated");

            $(this).switchButton({
                on_label: 'On',
                off_label: 'Off',
                labels_placement: "left"
            });

            if (!$(this).hasClass("SwitchButtonForm"))
                $(this).change(function() {
                    $.put(window.location.href + "-switchbuttom", {"rel": $(this).attr("rel"), "id": $(this).attr("id"), "value": $(this).prop("checked")}, function(mResult) {
                        if (!mResult.status)
                            alert(mResult.error);
                    });
                });
        }
    });
}

/**
 * Function to enable EditInPlace
 * 
 * @access public
 * @return void
 */
function BindEditInPlace() {
    $(".GridEditable").editInPlace({
        default_text: "<span style='color: #bfbfbf'>[ Empty ]</span>",
        callback: function(original_element, html, original) {
            $.put(window.location.href + "-editinplace", {"rel": $(this).attr("rel"), "id": $(this).attr("id"), "value": html}, function(mResult) {
                if (!mResult.status)
                    alert(mResult.error);
            });

            return(html);
        },
    });
}

/**
 * Confirm function to enable
 * 
 * @access public
 * @return void
 */
function BindConfirm() {
    $(".GridDelete").each(function() {
        if (!$(this).hasClass("GridDeleteActivated")) {
            $(this).addClass("GridDeleteActivated");
            var iID = $(this).attr("id");
            var sRel = $(this).attr("rel");

            $(this).confirm({
                text: "Really delete the record '" + sRel + "' ?",
                title: "Unregister",
                confirm: function(button) {
                    $("#loading").modal("show");

                    $.delete_(window.location.href + "-remove/" + iID, {}, function(mResult) {
                        $("#loading").modal("hide");
                        RefreshGrid();

                        if (!mResult.status) {
                            alert(mResult.error);
                        }
                        else {
                            $($this).parent().parent().remove();
                            alert("Registration successfully removed!");
                        }
                    });
                },
                confirmButton: "Yes",
                cancelButton: "No",
                post: false
            });
        }
    });
}

/**
 * Function to enable function insert
 * 
 * @access public
 * @return void
 */
function BindInsert() {
    $(".InsertModal").submit(function(e) {
        e.preventDefault();
    });
    $(".InsertModalSaveButton").click(function() {
        $(this).addClass("disabled");

        var oThis = $(this);
        var aData = $(this).parent().parent().find("form").serializeObject();
        aData["id"] = $(this).parent().parent().find("form").attr("id");
        setTimeout(function(oThis) {
            $.post(window.location.href + "-insert", aData, function(mResult) {
                mResult = JSON.parse(mResult);

                oThis.removeClass("disabled");
                $("#insert").modal("hide");
                RefreshGrid();
                ClearForm($(".baInsert"));

                if (!mResult.status)
                    alert(mResult.error);
                else
                    alert("Insert completed successfully!");
            });
        }, 1, oThis);
    });
}

/**
 * Function to enable editing functions
 * 
 * @access public
 * @return void
 */
function BindEdit() {
    $(".GridEdit").click(function() {
        $("#loading").modal("show");

        $.get(window.location.href + "-edit/" + $(this).attr("id"), null, function(sData) {
            aData = JSON.parse(sData);
            ClearForm($("#edit"));

            for (var sKey in aData) {
                $("#edit *[name=" + sKey + "]").each(function() {
                    //if($(this).attr("type")){
                    switch ($(this).get(0).tagName.toLowerCase()) {
                        case"input":
                            switch ($(this).attr("type").toLowerCase()) {
                                case "text":
                                case "password":
                                case "hidden":
                                    $(this).val(aData[sKey]);
                                    break;
                                case "checkbox":
                                case "radio":
                                    if (aData[sKey] == "1") {
                                        $(this).parent().find(".off").css("display", "none");
                                        $(this).parent().find(".on").css("display", "block");
                                        $(this).parent().find(".switch-button-background").addClass("checked");
                                        $(this).attr("checked", true);
                                    }
                                    else {
                                        $(this).parent().find(".off").css("display", "block");
                                        $(this).parent().find(".on").css("display", "none");
                                        $(this).parent().find(".switch-button-background").removeClass("checked");
                                        $(this).attr("checked", false);
                                    }
                                    break;
                            }
                            break;
                        case"select":
                            $("option", this).each(function() {
                                if ($(this).val() == aData[sKey])
                                    $(this).attr('selected', 'selected');
                            });
                            break;
                        case"textarea":
                            $(this).val(aData[sKey]);
                            break;
                    }
                    //}
                });
            }

            if (typeof AfterEdit == 'function')
                AfterEdit();

            $("#loading").modal("hide");
            $("#edit").modal("show");
        });
    });

    $(".baEdit").submit(function(e) {
        e.preventDefault();
    });

    $(".EditModalSaveButton").each(function() {
        if (!$(".EditModalSaveButton").hasClass("EditModalSaveButtonActivated")) {
            $(".EditModalSaveButton").addClass("EditModalSaveButtonActivated");

            $(this).click(function() {
                $(this).addClass("disabled");

                var oThis = $(this);
                var aData = $(this).parent().parent().find("form").serializeObject();

                setTimeout(function(oThis) {
                    $.put(window.location.href + "-edit", aData, function(mResult) {
                        oThis.removeClass("disabled");
                        $(".EditModal").modal("hide");
                        RefreshGrid();
                        ClearForm($(".baEdit"));

                        if (!mResult.status)
                            alert(mResult.error);
                        else
                            alert("Editing completed successfully!");
                    });
                }, 1, oThis);
            });
        }
    });

}

/**
 * Function to enable grid
 * 
 * @access public
 * @return void
 */
function BindDataTable() {
    $('.DataTable').dataTable({
        "iDisplayLength": 25,
        "sPaginationType": "full_numbers",
        "sDom": '<"top"pf<"clear">>rt<"bottom"lp<"clear">>',
        "oLanguage": {
            "oPaginate": {
                "sFirst": "primeira",
                "sLast": "última",
                "sNext": "próximo",
                "sPrevious": "anterior",
            },
            "sLengthMenu": "_MENU_",
            "sInfo": "View _START_ to _END_ of total _TOTAL_",
            "sEmptyTable": "There are no records in the table",
            "sSearch": "Filter: _INPUT_"
        },
        "fnDrawCallback": function(oSettings) {
            BindEditInPlace();
            BindConfirm();
            BindSwitchButton();
            BindEdit();
            BindMasks();
        }
    });
}

/**
 * Function to enable input masks
 * 
 * @access public
 * @return void
 */
function BindMasks() {
    $(".Phone").mask('(00) 0000-0000');
    $(".CellPhone").mask('(00) 00000-0000');
    $(".Date").mask('00/00/0000');
}

/**
 * Function to enable / disable sorting via drag
 * 
 * @access public
 * @return void
 */
function BindDragOrder() {
    var posicao = [];

    $('.DragOrder tr').each(function(i) {
        if (i) {
            var id = $(this).attr('id');
            posicao[id] = i;
        }
    });

    $(".DragOrder tbody").sortable({
        helper: function(e, tr) {
            var $originals = tr.children();
            var $helper = tr.clone();

            $helper.children().each(function(index) {
                $(this).width($originals.eq(index).width())
            });

            return $helper;
        },
        stop: function(e, ui) {
            var alteradas = '';

            $('.DragOrder tr').each(function(i) {
                if (i) {
                    var id = $(this).attr('id');

                    if (posicao[id] != i) {
                        //console.log(id + '/' + i);
                        alteradas += id + '-' + i + ',';
                        posicao[id] = i;
                    }
                }
            });

            if (alteradas.length > 0)
                $.put(window.location.href + "-order", {"sets": alteradas}, function() {

                });
        }
    }).disableSelection();
}

/**
 * Function to update grid
 * 
 * @access public
 * @return void
 */
function RefreshGrid() {
    $.get(window.location.href + "-refreshgrid", function(mResult) {
        $(".baDataTable tbody").remove();
        $('.baDataTable').dataTable().fnDestroy();
        $(".baDataTable thead").after("<tbody>" + mResult + "</tbody>");
        BindDataTable();
        BindDragOrder();
        BindEditInPlace();
        BindConfirm();
        BindSwitchButton();
        BindEdit();
        BindMasks();
    });
}

/**
 * Function to clear form
 * 
 * @
 * @access public
 * @param {type} oForm
 * @returns {undefined}
 */
function ClearForm(oForm) {
    $("input", oForm).each(function() {
        switch ($(this).attr("type").toLowerCase()) {
            case "text":
            case "password":
            case "hidden":
                $(this).val("");
                break;
            case "checkbox":
            case "radio":
                $(this).parent().find(".off").css("display", "block");
                $(this).parent().find(".on").css("display", "none");
                $(this).parent().find(".switch-button-background").removeClass("checked");
                $(this).attr("checked", false);
                break;
        }
    });

    $("select", oForm).each(function() {
        $("option:first", this).attr("selected", "selected");
    });

    $("textarea", oForm).val(" ");
}

/**
 * Functions PHPJS
 * 
 * @see http://phpjs.org/
 */

function implode(glue, pieces) {
    var i = '',
            retVal = '',
            tGlue = '';
    if (arguments.length === 1) {
        pieces = glue;
        glue = '';
    }
    if (typeof pieces === 'object') {
        if (Object.prototype.toString.call(pieces) === '[object Array]') {
            return pieces.join(glue);
        }
        for (i in pieces) {
            retVal += tGlue + pieces[i];
            tGlue = glue;
        }
        return retVal;
    }
    return pieces;
}

function call_user_func_array(cb, parameters) {
    var func;

    if (typeof cb === 'string') {
        func = (typeof this[cb] === 'function') ? this[cb] : func = (new Function(null, 'return ' + cb))();
    } else if (Object.prototype.toString.call(cb) === '[object Array]') {
        func = (typeof cb[0] === 'string') ? eval(cb[0] + "['" + cb[1] + "']") : func = cb[0][cb[1]];
    } else if (typeof cb === 'function') {
        func = cb;
    }

    if (typeof func !== 'function') {
        throw new Error(func + ' is not a valid function');
    }

    return (typeof cb[0] === 'string') ? func.apply(eval(cb[0]), parameters) : (typeof cb[0] !== 'object') ? func.apply(
            null, parameters) : func.apply(cb[0], parameters);
}
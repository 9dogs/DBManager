$(document).ready(function(){

    var statusBar = new Object();
    statusBar.setStatusLoad = function(){
        $('#response').html('<span class="load"><img alt="Загрузка" title="Загрузка" src="images/load.gif" />Загрузка</span></li>');
    }
    statusBar.setStatusReady = function(){
        $('#response').html('<span class="wait"><img alt="Ожидание" title="Ожидание" src="images/bullet_yellow.png" />Ожидание ввода</span></li>');
    }
    statusBar.setStatus = function(response){
        $('#response').html(response);
        setTimeout(statusBar.setStatusReady, 5000);
    }

    function moveStatusBar() {
        var offset = $(document).scrollTop()+"px";
        $('#statusBar').css({
            'top' : offset
        });
    }
    function toggleAdd(buttonId, elementId, name) {

        return function()
        {
            $(elementId).toggle('fast');
            var options;
            if ($(buttonId).text() !== name) {
                options = {
                    label: name,
                    icons: {
                        primary: 'ui-icon-triangle-1-s',
                        secondary: 'ui-icon-triangle-1-s'


                    }
                };
            }else {
                options = {
                    label: 'Спрятать',
                    icons: {
                        primary: 'ui-icon-triangle-1-n',
                        secondary: 'ui-icon-triangle-1-n'
                    }
                };
            }
            $(buttonId).button('option', options);
            setTimeout(moveStatusBar, 400);
        };
    }
    function refreshField(field, request) {
        return function(){
            $(field).html('');
            $.ajax({
                complete: function(){
                    statusBar.setStatusReady();
                },
                url: "ajax.php",
                data: 'q='+request,
                cache: false,
                dataType: "html",
                success: function(data) {
                    $(field).html(data);
                }
            });
        }
    }
    function showWrap(form) {
        return function(){
            $(form).css({
                'display':"block",
                'z-index':"50"
            });
            $(form).animate({
                opacity:1
            });
        }
    }

    function showResponse(responseText, statusText) {
        // for normal html responses, the first argument to the success callback
        // is the XMLHttpRequest object's responseText property

        // if the ajaxForm method was passed an Options Object with the dataType
        // property set to 'xml' then the first argument to the success callback
        // is the XMLHttpRequest object's responseXML property

        // if the ajaxForm method was passed an Options Object with the dataType
        // property set to 'json' then the first argument to the success callback
        // is the json data object returned by the server
        $('#response').html('');
        $('#response').html(responseText);
        setTimeout(statusBar.setStatusReady, 5000);
    }

    function toggleLock() {
        if (this.alt == "Разблокировать"){
            this.alt = "Заблокировать";
            this.title="Заблокировать"
            $("#personSelect").removeAttr('disabled');
            this.src = "images/unlock.png";
        }
        else {
            this.alt = "Разблокировать";
            this.title="Разблокировать"
            $("#personSelect").attr('disabled', 'disabled');
            this.src = "images/lock.png";
        }
    }

    $("#personSelect").attr('disabled', 'disabled');
    $("#lock").click(toggleLock);
    
    $(window).scroll(moveStatusBar);

    //Подсветка нечетных дивов
    //$('form div:odd').css( {
    //    'background-color' : '#F7F7F7'
    //});
    //$('form div.clear').css( {
    //    'background-color' : '#F1F1F1'
    //});

    //AJAX Setup
    $.ajaxSetup( {
        beforeSend: function(){
            statusBar.setStatusLoad();
        },
        success: function(data){
            statusBar.setStatus(data);
            setTimeout(statusBar.setStatusReady(), 5000);
        }
    });



    $("#showadd").button( {
        icons: {
            primary: 'ui-icon-triangle-1-s',
            secondary: 'ui-icon-triangle-1-s'
        }
    });

    $("#showcont").button( {
        icons: {
            primary: 'ui-icon-triangle-1-s',
            secondary: 'ui-icon-triangle-1-s'
        }
    });

    $('.close').click(function() {
        $(this).parent().animate({
            opacity:0
        }, function(){
            $(this).css({
                display:"none"
            });
        })
    });

    $('#pshow').click(showWrap('#pwrap'));
    $('#dshow').click(showWrap('#dwrap'));
    $('#lshow').click(showWrap('#lwrap'));

    $(".draggable").draggable();

    $('#refdep').click(refreshField('#departmentId','department'));
    $('#reflab').click(refreshField('#labId','lab'));
    $('#refpos').click(refreshField('#posId','position'));
    $('#refchief').click(refreshField('#chiefId','person'));
    $('#refaschief').click(refreshField('#asChiefId','person'));
    $('#reflchief').click(refreshField('#lchiefId','person'));
    $('#refldep').click(refreshField('#ldepartmentId','department'));

    $("#form").ajaxForm({
        beforeSubmit : statusBar.setStatusLoad,
        dataType: 'script',
        success : showResponse,
        resetForm : false
    });
    $("#pform").ajaxForm({
        beforeSubmit : statusBar.setStatusLoad,
        dataType: 'script',
        success : showResponse,
        resetForm : false
    });
    $("#dform").ajaxForm({
        beforeSubmit : statusBar.setStatusLoad,
        dataType: 'script',
        success : showResponse,
        resetForm : false
    });
    $("#lform").ajaxForm({
        beforeSubmit : statusBar.setStatusLoad,
        dataType: 'script',
        success : showResponse,
        resetForm : false
    });

    //Кнопка выхода
    $("button").button();
    $("button").css( {
        'font-size' : '8pt'
    } );
    $("#exit").click( function() {
        $.cookie("id", null, {
            path: '/',
            expires: 10
        } );
        $.cookie("hash", null, {
            path: '/db',
            expires: 10
        } );
        window.location.replace("login.php");
    });

    $("#tabs").tabs();

});
 $( document ).ready(function() {

    $(".dismiss").click(function(){
             $("#notification").fadeOut("slow");
             $('.printlabel').css('color', '#333');
    });

    $(".proceed").click(function(){
        var orderNumber =  $(this).attr("data-orderNumber");
        var url = '/portal/proceed';

        $.ajax({
            type: "POST",
            url: url,
            data: {'orderNumber': orderNumber},
            dataType: "text",
            success: function(resultData) { 
                if (resultData == 1) 
                {
                    $('#'+orderNumber).remove()
                }
            }
        });
    });

    $(".printAll").click(function(){
        
        $("#notification").fadeOut("slow")
        $('.printlabel').css('color', '#333');
        var onBarcode = [];
        var onPicklist = [];
        $('.printCheckBarcode:checkbox:checked').each(function () {
            onBarcode.push($(this).attr("data-orderNumber"));
        });
        $('.printCheckPicklist:checkbox:checked').each(function () {
            onPicklist.push($(this).attr("data-orderNumber"));
        });
        if (onBarcode.length === 0 && onPicklist.length === 0) {
            $('.printlabel').css('color', 'red');
            $("#notification").fadeIn("slow");
            return;
        }
        $("body").addClass("loading");
        var url = '/portal/printpick';
         $.ajax({
            type: "POST",
            url: url,
            data: {'onPicklist': onPicklist,'onBarcode': onBarcode,'print':0},
            dataType: "text",
            success: function(resultData) { 
                    $.each( onPicklist, function( key, value ) {
                        $("#printed_"+value).html("YES");
                    });
                    $.each( onBarcode, function( key, value ) {
                        $("#printed_"+value).html("YES");
                    });
                    $("body").removeClass("loading");
                    window.open('/pdf/orderNumbers_'+resultData+'.pdf', '_blank');

            }
        });
    });

    $(".printOne").click(function(){        
        var orderNumber = $(this).attr("data-orderNumber");
        var onBarcode = [];
        var onPicklist = [];
        $("#notification").fadeOut("slow")
        $('.printlabel').css('color', '#333');

        $('.printCheckBarcode:checkbox:checked').each(function () {
            if ($(this).attr("data-orderNumber") == orderNumber) {
                onBarcode.push($(this).attr("data-orderNumber"));
            }
        });

        $('.printCheckPicklist:checkbox:checked').each(function () {
            if ($(this).attr("data-orderNumber") == orderNumber) {    
                onPicklist.push($(this).attr("data-orderNumber"));
            }
        });
        if (onBarcode.length === 0 && onPicklist.length === 0) {
            $('.printlabel').css('color', 'red');
            $("#notification").fadeIn("slow");
            return;
        }
        $("body").addClass("loading");
        var url = '/portal/printpick';
         $.ajax({
            type: "POST",
            url: url,
            data: {'onPicklist': onPicklist,'onBarcode': onBarcode,'print':0},
            dataType: "text",
            success: function(resultData) {     
                $("body").removeClass("loading");
                $("#printed_"+orderNumber).text("YES");
                window.open('/pdf/orderNumbers_'+resultData+'.pdf', '_blank');
            }
        });
    });

    $( "#start_date,#end_date,#support_date,#receipt_date,#Order_date").datepicker(); 
    

 });
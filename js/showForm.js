var lasttime;

function getCurrentTime() {
    return Math.round(((new Date()).getTime()-Date.UTC(1970,0,1))/1000);
}

$(document).ready(function() {
    lasttime = getCurrentTime();
    $("input#dice").focus();
    $("form#dice").submit(function() {
        $.ajax({
            url: "?action=roll&ajax=1",
            data: {
                dice: $("input#dice").val()
            },
            type: "post",
            dataType: "json",
            success: function(data) {
                if (data.success) {
                    lasttime = data.time;
                    $("tbody#rollresults").prepend(getResultRow(data));
                } else {
                    alert(data.error);
                }
            }
        });
        $("input#dice").select();
        return false; 
    });
    $("input.standardroll").click(function() {
        $("input#dice").val($(this).val());
    });
    
    $("div#link").css("display", "none");
    
    $("#getLink").click(function() {
        if ($("div#link").is(":hidden")) {
            $("div#link").slideDown("fast");
            $("div#link input").select();
        } else {
            $("div#link").slideUp("fast");
        }
    });
    $("#closeLink").click(function() {
        $("div#link").slideUp("fast");
    });
$("#clear").click(function() {
        $.ajax({
                url: "?action=clear&ajax=1",
                type: "get",
                success: function(data) {
$("tbody#rollresults").empty();
                                    }
            });

    });

    
    if (refreshPage) {
        window.setInterval(function() {
            
            $.ajax({
                url: "?action=getRolls&ajax=1",
                type: "post",
                data: {
                    lastTime: lasttime
                },
                dataType: "json",
                success: function(data) {
                    if (data != null) {
                        var newData = '';
                        lasttime = data[0].time
                        for (var currentIndex in data) {
                            if (data[currentIndex].success) {   
                                newData += getResultRow(data[currentIndex]);
                                
                            }
                        }
                        $("tbody#rollresults").html(newData);
                    }
                }
            });
        }, 1000
        );
    }
    
});

function getResultRow(data) {
var date = new Date(data.time * 1000);
var dateString = pad(date.getHours(), 2) + ":" + pad(date.getMinutes(), 2) + ":" + pad(date.getSeconds(), 2);
    return '<tr><td class="dice" >' + data.dice + '</td><td class="result">' + data.result + '</td><td class="time">' + dateString + "</td></tr>";
}

function pad(number, length) {
   
    var str = '' + number;
    while (str.length < length) {
        str = '0' + str;
    }
   
    return str;

}
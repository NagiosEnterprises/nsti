$(document).ready(function(event){
    var tog = false; // or true if they are checked on load
    $('.bigone').click(function() {
        $('.selectors').attr('checked',!tog);
        tog = !tog;
    });
    var vlu = $('#state').val();
    var mini = 'min';
    var init = 'ini';
    if (vlu == mini) {
        $('#minimize').show();
        $('#initiate').hide();
        $('#searchrow').hide();
    }
    else {
        $('#minimize').hide();
        $('#initiate').show();
        $('#searchrow').show();
    }
    $('#minimize').click(function() {
        $('#minimize').hide();
        $('#initiate').show();
        $('#searchrow').show();
        $.post('./index.php',{'state':init});
    });
    $('#initiate').click(function() {
        $('#minimize').show();
        $('#initiate').hide();
        $('#searchrow').hide();
        $.post('./index.php',{'state':mini});
    });
    $('.timepicker').AnyTime_picker(
        { format: "%a %b %d %T %Y" });
});

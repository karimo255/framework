$(document).ready(function () {

    var wert = 0, sliderWidth = 0, width = 0;
    $('#slider ul li').each(function () {
        width = width + $(this).width();
    });
    width = width / 2;
    sliderWidth = (width / 960) * 100;   //(body,#slider,ul,li) width  ist 960px       
    $('#slider ul').width(width); //setzen der breite des ul elementes anhand der anzahl der li elemente  

    //vorwärts
    $('#slider .fa.fa-angle-right').click(function () {
        var sliderWert = -(sliderWidth - 100);
        if (wert != sliderWert) {
            wert = wert - 100;
            $('#slider i').show();
        }
        if (wert == sliderWert) {
            $(this).hide();
        }
        var value = wert + '%';
        indicate(value);
    });
    //rückwärts
    $('#slider .fa.fa-angle-left').click(function () {
        if (wert != 0) {
            wert = wert + 100;
            $('#slider i').show();
        }
        if (wert == 0) {
            $(this).hide();
        }
        var value = wert + '%';
        indicate(value);
    });

    $('#indcator  li').click(function () {//noch nicht aktif
        var value = '-200';
        indicate(value);
    });

    function indicate(value) {
        var valuesArray = {'0': 0, '-100': 1, '-200': 2, '-300': 3, '-400': 4, '-500': 5, '-600': 6, '-700': 7};
        //aply
        $('#slider > ul').css('left', value);
        //indicator
        $('#indcator li').css('background', 'white');
        $('#indcator li').eq(valuesArray[value]).css('background', '#0088cc');
    }
    indicate(0);

});
$(function () {

    $('.radio label').each(function () {
        var e = $(this);
        e.click(function () {
            e.closest('.radio').find("label").removeClass("active");
            e.addClass("active");
        });
    });
    $('.checkbox label').each(function () {
        var e = $(this);
        e.click(function () {
            if (e.find('input').is(':checked')) {
                e.addClass("active");
            } else {
                e.removeClass("active");
            }
            ;
        });
    });

    $('.icon-navicon').each(function () {
        var e = $(this);
        var target = e.attr("data-target");
        e.click(function () {
            $(target).slideToggle().toggleClass("nav-navicon");
        });
    });

    window._bd_share_config = {"common": {"bdSnsKey": {}, "bdText": "", "bdMini": "2", "bdMiniList": false, "bdPic": "", "bdStyle": "0", "bdSize": "116"}, "share": {}};
    with (document)
        0[(getElementsByTagName('head')[0] || body).appendChild(createElement('script')).src = 'http://bdimg.share.baidu.com/static/api/js/share.js?v=89860593.js?cdnversion=' + ~(-new Date() / 36e5)];
});
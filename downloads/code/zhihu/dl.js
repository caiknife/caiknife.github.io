/**
 * Created by caiknife on 16/9/9.
 */
$(function () {
    $('<div id="showImg"></div>').prependTo($("body"));
    $('img.origin_image.zh-lightbox-thumb.lazy').each(function () {
        $("#showImg").append($(this).data('original') + "<br/>");
    });
});

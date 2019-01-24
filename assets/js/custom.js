function openCharm() {
    toggleMetroCharm("#right-charm"), $(".fancybox-overlay").removeClass("hide-div"), $(".fancybox-overlay").addClass("show-div")
}

function addFocused(e) {
    $(".textbox-wrap").removeClass("focused"), $(e).parent(".input-control").parent(".textbox-wrap").addClass("focused")
}

function stringValidate(e) {
    var a = $(e);
    a.val(a.val().replace(/[^a-zA-Z ÀÁÂÃÈÉÊÌÍÒÓÔÕÙÚĂĐĨŨƠàáâãèéêìíòóôõùúăđĩũơƯĂẠẢẤẦẨẪẬẮẰẲẴẶẸẺẼỀỀỂưăạảấầẩẫậắằẳẵặẹẻẽềềểỄỆỈỊỌỎỐỒỔỖỘỚỜỞỠỢỤỦỨỪễệỉịọỏốồổỗộớờởỡợụủứừỬỮỰỲỴÝỶỸửữựỳỵỷỹ]/g, function(e) {
        return ""
    }))
}

function phoneValidate(e) {
    var a = $(e);
    a.val(a.val().replace(/[^0-9 ]/g, function(e) {
        return ""
    }))
}

function fileNameValidate(e) {
    var a = $(e);
    a.val(a.val().replace("/", ""))
}

function showRoom(e) {
    window.location.href = "#/detail-room/" + e
}

function toggleFullScreen() {
    document.fullscreenElement || document.mozFullScreenElement || document.webkitFullscreenElement ? (document.cancelFullScreen ? document.cancelFullScreen() : document.mozCancelFullScreen ? document.mozCancelFullScreen() : document.webkitCancelFullScreen && document.webkitCancelFullScreen(), $(".slimScrollDiv").css("height", browser_size), $(".hr-content").css("height", browser_size)) : (document.documentElement.requestFullscreen ? document.documentElement.requestFullscreen() : document.documentElement.mozRequestFullScreen ? document.documentElement.mozRequestFullScreen() : document.documentElement.webkitRequestFullscreen && document.documentElement.webkitRequestFullscreen(Element.ALLOW_KEYBOARD_INPUT), $(".slimScrollDiv").css("height", screen_size), $(".hr-content").css("height", screen_size))
}

function setActive() {
    var e = $(location).attr("href").split("/");
    switch (e[4]) {
        case "info":
        case "auth":
        case "account":
        case "nghi-phep":
        case "thong-bao":
            $(".tabs").removeClass("active"), $(".tab-panel").css("display", "none"), $("#system").addClass("active"), $("#tab_system").css("display", "block");
            break;
        case "members":
        case "new-member":
        case "view-member":
        case "edit-member":
        case "update-member":
        case "ktkl":
        case "new-ktkl":
        case "new-hoatdong":
        case "update-ktkl":
        case "update-hoatdong":
        case "cong-tac":
        case "ds-hop-dong":
        case "show-hop-dong":
            $(".tabs").removeClass("active"), $(".tab-panel").css("display", "none"), $("#file").addClass("active"), $("#tab_file").css("display", "block");
            break;
        case "ds-nghi-phep":
        case "list-rooms":
        case "detail-room":
        case "chuc-danh":
        case "chuc-vu":
        case "chuyen-mon":
        case "du-lieu":
            $(".tabs").removeClass("active"), $(".tab-panel").css("display", "none"), $("#manage").addClass("active"), $("#tab_manage").css("display", "block");
            break;
        case "ki-hieu-cham-cong":
        case "bang-cham-cong":
        case "thiet-lap-dinh-muc":
        case "bang-tinh-luong":
        case "thiet-lap-luong":
        case "phu-cap":
        case "bang-luong-thang":
            $(".tabs").removeClass("active"), $(".tab-panel").css("display", "none"), $("#wage").addClass("active"), $("#tab_wage").css("display", "block");
            break;
        case "tk-chuc-danh":
        case "tk-do-tuoi":
        case "tk-ton-giao":
        case "tk-dan-toc":
        case "tk-trinh-do":
        case "tk-gioi-tinh":
        case "tk-ngay-nghi":
            $(".tabs").removeClass("active"), $(".tab-panel").css("display", "none"), $("#statistic").addClass("active"), $("#tab_statistic").css("display", "block");
            break;
        case "gioi-thieu":
        case "huong-dan":
        case "phan-hoi":
            $(".tabs").removeClass("active"), $(".tab-panel").css("display", "none"), $("#help").addClass("active"), $("#tab_help").css("display", "block")
    }
}
$(document).ready(function() {
    $(".fancybox").fancybox({
        fitToView: !0,
        width: "40%",
        height: "60%",
        autoSize: !1,
        closeBtn: !1
    })
});
var screen_size = screen.height - 180,
    browser_size = $(window).height() - 180;
document.addEventListener("keydown", function(e) {
    122 == e.keyCode && toggleFullScreen()
}, !1);
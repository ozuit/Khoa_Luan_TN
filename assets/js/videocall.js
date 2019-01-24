var VIDEO_FRAME_RATE = 1000 / 30;
var promise;
var connection;
var lastSelectedFile;
var chunk_size = 60 * 1000;
var requestAnimationFrame =
    window.requestAnimationFrame ||
    window.mozRequestAnimationFrame ||
    window.msRequestAnimationFrame ||
    window.webkitRequestAnimationFrame;

function openWindow(e) {
    if (window.innerWidth <= 640) {
        var n = document.createElement("a");
        n.setAttribute("href", e), n.setAttribute("target", "_blank");
        var t = document.createEvent("HTMLEvents");
        t.initEvent("click", !0, !0), n.dispatchEvent(t)
    } else {
        var o = window.innerWidth,
            i = window.innerHeight;
        window.open(e, "window", "width=" + o + ", height=" + i + ", top=0, left=0")
    }
    return !1
}

function startAudioCall() {
    $("#audioCall").trigger("play")
}

function pauseAudioCall() {
    $("#audioCall").trigger("pause")
}

function stopAudioCall() {
    pauseAudioCall(), $("#audioCall").prop("currentTime", 0)
}

function openVideoCall(e) {
    e || (e = connection.token());
    var n = "https://" + window.location.host + "/#/video-call?roomid=" + e;
    return openWindow(n)
}

function setMainVideo(video) {
    var canvas = document.getElementById('canvas');
    var ctx = canvas.getContext('2d');
    ctx.clearRect(0, 0, canvas.width, canvas.height);

    function stopCurrentAnimation() {
        if (promise) {
          clearInterval(promise);
        }
    }

    stopCurrentAnimation();

    promise = setInterval(function() {
        requestAnimationFrame(function() {
          // ctx.drawImage(video, 0, 0, 860, 680);
          ctx.drawImage(video, 0, 0, 640, 480);
        });
    }, VIDEO_FRAME_RATE, 0, false);
}

function setupWebRTCConnection() {
    if (connection) {
        return;
    }
    connection = new RTCMultiConnection;
    connection.fileReceived = {};
    connection.socketURL = "https://192.168.159.1:9001/";
    connection.chunkSize = chunk_size;
    connection.sdpConstraints.mandatory = {
        OfferToReceiveAudio: !0,
        OfferToReceiveVideo: !0
    };
    connection.enableFileSharing = true;
    connection.session = {
        audio: !0,
        video: !0,
        data: !0
    };

    connection.connectedWith = {};

    connection.onmessage = function(event) {
        if(event.data.doYouWannaReceiveThisFile) {
            if(!connection.fileReceived[event.data.fileName]) {
                connection.send({
                    yesIWannaReceive:true,
                    fileName: event.data.fileName
                });
            }
        }

        if(event.data.yesIWannaReceive && !!lastSelectedFile) {
            connection.shareFile(lastSelectedFile, event.userid);
        }
    };

    connection.onopen = function(e) {
        try {
            chrome.power.requestKeepAwake('display');
        }
        catch(e) {}

        if (connection.connectedWith[e.userid]) return;
        connection.connectedWith[e.userid] = true;

        if (!lastSelectedFile) return;

        var file = lastSelectedFile;
        setTimeout(function() {
            connection.send({
                doYouWannaReceiveThisFile: true,
                fileName: file.size + file.name
            });
        }, 500);
    };

    window.connection = connection;
}

function initVideoCall() {
    connection.videosContainer = document.getElementById("user-online"), connection.onstream = function(e) {
        t = getMediaElement(e.mediaElement, {
            title: e.userid,
            buttons: ['mute-audio', 'mute-video', 'full-screen'],
            showOnMouseEnter: !1
        });

        connection.videosContainer.appendChild(t);

        var o = e.extra;
        var overlay = document.createElement('div');
        overlay.className = 'overlay-video';
        var video = document.getElementById(e.streamid);
        var mediaBox = video.parentNode;
        mediaBox.setAttribute('title', o.fullname);
        mediaBox.addEventListener('mouseenter', function() {
            mediaBox.appendChild(overlay);
        });
        mediaBox.addEventListener('mouseleave', function() {
            mediaBox.removeChild(overlay);
        });

        setMainVideo(video);

        setTimeout(function() {
            t.media.play()
        }, 5e3), t.id = e.streamid;

    }, connection.onstreamended = function(e) {
        var n = document.getElementById(e.streamid);
        n && n.parentNode.removeChild(n);
        var t = document.getElementById("avatar_" + e.streamid);
        t && t.parentNode.removeChild(t)
    };

    connection.onmute = function(e) {
       if(e.session.audio && !e.session.video) {
           e.mediaElement.muted = true;
           return;
       }
       var o = e.extra;
       e.mediaElement.src = null;
       e.mediaElement.pause();
       e.mediaElement.setAttribute('poster', window.location.origin + o.avatar);
    };

    connection.onunmute = function(e) {
       if(e.session.audio && !e.session.video) {
            e.mediaElement.muted = false;
            return;
       }

       e.mediaElement.removeAttribute('poster');
       e.mediaElement.src = (window.URL ? URL : webkitURL).createObjectURL(e.stream);
       e.mediaElement.play();
    };

    var progressHelper = {};
    connection.filesContainer = logsDiv = document.getElementById('logs');

    connection.onFileStart = function(file) {
        if (connection.fileReceived[file.size + file.name]) return;

        var div = document.createElement('div');
        div.style.borderBottom = '1px solid black';
        div.style.padding = '2px 4px';
        div.id = file.uuid;

        var message = '';
        message += '<br><b>' + file.name + '</b>.';
        message += '<br>Size: <b>' + bytesToSize(file.size) + '</b>';
        message += '<br><label>0%</label> <progress></progress>';

        if(file.userid !== connection.userid) {
            message += '<br><button id="resend">Receive Again?</button>';
        }

        div.innerHTML = message;

        connection.filesContainer.insertBefore(div, connection.filesContainer.firstChild);

        if(file.userid !== connection.userid && div.querySelector('#resend')) {
            div.querySelector('#resend').onclick = function(e) {
                e.preventDefault();
                this.onclick = function() {};

                if(connection.fileReceived[file.size + file.name]) {
                    delete connection.fileReceived[file.size + file.name];
                }
                connection.send({
                    yesIWannaReceive: true,
                    fileName: file.name
                }, file.userid);

                div.parentNode.removeChild(div);
            };
        }

        if (!file.remoteUserId) {
            progressHelper[file.uuid] = {
                div: div,
                progress: div.querySelector('progress'),
                label: div.querySelector('label')
            };
            progressHelper[file.uuid].progress.max = file.maxChunks;
            return;
        }

        if (!progressHelper[file.uuid]) {
            progressHelper[file.uuid] = {};
        }

        progressHelper[file.uuid][file.remoteUserId] = {
            div: div,
            progress: div.querySelector('progress'),
            label: div.querySelector('label')
        };
        progressHelper[file.uuid][file.remoteUserId].progress.max = file.maxChunks;
    };

    connection.onFileProgress = function(chunk) {
        if (connection.fileReceived[chunk.size + chunk.name]) return;

        var helper = progressHelper[chunk.uuid];
        if (!helper) {
            return;
        }
        if (chunk.remoteUserId) {
            helper = progressHelper[chunk.uuid][chunk.remoteUserId];
            if (!helper) {
                return;
            }
        }

        helper.progress.value = chunk.currentPosition || chunk.maxChunks || helper.progress.max;
        updateLabel(helper.progress, helper.label);
    };

    connection.onFileEnd = function(file) {
        if (connection.fileReceived[file.size + file.name]) return;

        var div = document.getElementById(file.uuid);
        if (div) {
            div.parentNode.removeChild(div);
        }

        if (file.remoteUserId === connection.userid) {

            connection.fileReceived[file.size + file.name] = file;

            var message = ($.cookie('lang') == 'en_US') ? 'Successfully received file:' : 'Đã nhận file:';
            message += '<br><a href="' + file.url + '" target="_blank" download="' + file.name + '"><b>' + file.name + '</b> (' + bytesToSize(file.size) + ')</a>';
            var div = appendLog(message);
            return;
        }

        var message = ($.cookie('lang') == 'en_US') ? 'Successfully shared file:' : 'Đã chia sẻ file:';
        message += '<br><b>' + file.name + '</b> (' + bytesToSize(file.size) + ')';
        appendLog(message);
    };

    var e = location.href,
    n = e.split("=")[1];
    connection.openOrJoin(n);
    localStorage.setItem("roomid", n);
    $("#icon-micro").click(function(e) {
        var n = connection.streamEvents.selectFirst().streamid;
        if ( $("#toggleAudio").val() === "true" ) {
            connection.streamEvents[n].stream.unmute('audio');
        } else {
            connection.streamEvents[n].stream.mute('audio');
        }
    });
    $("#icon-webcam").click(function() {
        var e = connection.streamEvents.selectFirst().streamid;
        if ( $("#toggleVideo").val() === "true" ) {
            connection.streamEvents[e].stream.unmute('video');
        } else {
            connection.streamEvents[e].stream.mute('video');
        }
    });
    $(self).bind("beforeunload", function(e) {
        localStorage.removeItem("videocall"), localStorage.removeItem("roomid")
    });
};

function updateLabel(progress, label) {
    if (progress.position === -1) {
        return;
    }

    var position = +progress.position.toFixed(2).split('.')[1] || 100;
    label.innerHTML = position + '%';
}

function bytesToSize(bytes) {
    var k = 1000;
    var sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
    if (bytes === 0) {
        return '0 Bytes';
    }
    var i = parseInt(Math.floor(Math.log(bytes) / Math.log(k)), 10);
    return (bytes / Math.pow(k, i)).toPrecision(3) + ' ' + sizes[i];
}

function onFileSelected(file) {
    lastSelectedFile = file;

    if (connection) {
        connection.send({
            doYouWannaReceiveThisFile: true,
            fileName: file.size + file.name
        });
    }
}

function appendLog(html, color) {
    var div = document.createElement('div');
    div.innerHTML = '<p>' + html + '</p>';
    logsDiv.insertBefore(div, logsDiv.firstChild);

    if(color) {
      div.style.color = color;
    }

    return div;
}

setupWebRTCConnection();
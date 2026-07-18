@if(!empty($videoExportScript))
<div id="cb-video-export-status" role="status" style="position:fixed;bottom:1.25rem;left:50%;transform:translateX(-50%);padding:0.75rem 1.25rem;background:#1a3646;color:#f8fafc;border-radius:0.75rem;font:500 0.875rem system-ui,-apple-system,sans-serif;z-index:9999;box-shadow:0 10px 40px rgba(0,0,0,.25);max-width:min(90vw,24rem);text-align:center;line-height:1.4">
    Building your video… this can take up to a minute. Keep this tab open.
</div>
<script src="https://cdn.jsdelivr.net/npm/html2canvas@1.4.1/dist/html2canvas.min.js" crossorigin="anonymous"></script>
<script>
(function () {
    var baseName = @json($videoExportBasename ?? 'invitation');
    var durationSec = Math.max(5, Math.min(120, parseInt(@json($videoExportDurationSec ?? 30), 10) || 30));
    var statusEl = document.getElementById('cb-video-export-status');
    function setStatus(text, isError) {
        if (!statusEl) return;
        statusEl.textContent = text;
        if (isError) {
            statusEl.style.background = '#fff7ed';
            statusEl.style.color = '#9a3412';
            statusEl.style.border = '1px solid #fdba74';
        }
    }
    function waitForImages(root) {
        var imgs = root.querySelectorAll('img');
        return Promise.all(Array.prototype.map.call(imgs, function (img) {
            if (img.complete && img.naturalWidth > 0) {
                return Promise.resolve();
            }
            return new Promise(function (resolve) {
                img.addEventListener('load', resolve, { once: true });
                img.addEventListener('error', resolve, { once: true });
            });
        }));
    }
    function pickMime() {
        if (typeof MediaRecorder === 'undefined') {
            return null;
        }
        var candidates = [
            'video/webm;codecs=vp9',
            'video/webm;codecs=vp8',
            'video/webm',
            'video/mp4',
        ];
        for (var i = 0; i < candidates.length; i++) {
            if (MediaRecorder.isTypeSupported(candidates[i])) {
                return candidates[i];
            }
        }
        return null;
    }
    function run() {
        var mimeType = pickMime();
        if (!mimeType) {
            setStatus('Video export needs a desktop browser (Chrome, Edge, or Firefox). Try PNG or screen-record the Open tab.', true);
            return;
        }
        var el = document.querySelector('.capture-root');
        if (!el || typeof html2canvas !== 'function') {
            setStatus('Could not find the card on this page.', true);
            return;
        }
        waitForImages(el).then(function () {
            return new Promise(function (r) {
                requestAnimationFrame(function () { requestAnimationFrame(r); });
            });
        }).then(function () {
            return html2canvas(el, {
                useCORS: true,
                allowTaint: false,
                scale: 2,
                logging: false,
                backgroundColor: null,
                imageTimeout: 20000,
            });
        }).then(function (sourceCanvas) {
            var w = sourceCanvas.width;
            var h = sourceCanvas.height;
            var out = document.createElement('canvas');
            out.width = w;
            out.height = h;
            var ctx = out.getContext('2d');
            var fps = 30;
            var totalFrames = Math.round(durationSec * fps);
            var zoomTotal = 0.1;
            function drawAt(t) {
                t = Math.min(1, Math.max(0, t));
                var ease = 0.5 - 0.5 * Math.cos(Math.PI * t);
                var scale = 1 + zoomTotal * ease;
                var sw = w / scale;
                var sh = h / scale;
                var sx = (w - sw) * 0.5;
                var sy = (h - sh) * 0.5;
                ctx.drawImage(sourceCanvas, sx, sy, sw, sh, 0, 0, w, h);
            }
            drawAt(0);
            var stream = out.captureStream(fps);
            var recorder = new MediaRecorder(stream, { mimeType: mimeType, videoBitsPerSecond: 2200000 });
            var chunks = [];
            recorder.ondataavailable = function (e) {
                if (e.data && e.data.size) {
                    chunks.push(e.data);
                }
            };
            recorder.onstop = function () {
                if (statusEl) {
                    statusEl.remove();
                }
                try {
                    var blob = new Blob(chunks, { type: (mimeType || 'video/webm').split(';')[0] });
                    var ext = (blob.type && blob.type.indexOf('mp4') !== -1) ? 'mp4' : 'webm';
                    var url = URL.createObjectURL(blob);
                    var a = document.createElement('a');
                    a.download = baseName + '.' + ext;
                    a.href = url;
                    a.rel = 'noopener';
                    document.body.appendChild(a);
                    a.click();
                    a.remove();
                    URL.revokeObjectURL(url);
                } catch (err) {
                    var p = document.createElement('p');
                    p.style.cssText = 'padding:1rem;margin:1rem;font-family:system-ui,sans-serif;font-size:14px;color:#333;max-width:28rem;';
                    p.textContent = 'Could not save the video file. Try PNG or Open and screen record instead.';
                    document.body.appendChild(p);
                }
            };
            recorder.start(100);
            var frame = 0;
            var interval = setInterval(function () {
                frame += 1;
                var t = frame / Math.max(1, totalFrames - 1);
                drawAt(Math.min(1, t));
                if (frame >= totalFrames) {
                    clearInterval(interval);
                    setTimeout(function () {
                        try {
                            recorder.stop();
                        } catch (e) {
                            setStatus('Video recording failed. Use PNG or Open instead.', true);
                        }
                    }, 180);
                }
            }, 1000 / fps);
        }).catch(function () {
            setStatus('Could not create the video. Open the card and use PNG or print instead.', true);
        });
    }
    if (document.fonts && document.fonts.ready) {
        document.fonts.ready.then(function () { setTimeout(run, 320); });
    } else {
        setTimeout(run, 650);
    }
})();
</script>
@endif

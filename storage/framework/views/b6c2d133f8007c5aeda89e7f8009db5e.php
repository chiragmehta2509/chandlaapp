<?php if(!empty($pngExportScript)): ?>
<script src="https://cdn.jsdelivr.net/npm/html2canvas@1.4.1/dist/html2canvas.min.js" crossorigin="anonymous"></script>
<script>
(function () {
    var filename = <?php echo json_encode($pngExportFilename ?? 'invitation.png', 15, 512) ?>;
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
    function capture() {
        var el = document.querySelector('.capture-root');
        if (!el || typeof html2canvas !== 'function') {
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
        }).then(function (canvas) {
            try {
                var a = document.createElement('a');
                a.download = filename;
                a.href = canvas.toDataURL('image/png');
                a.rel = 'noopener';
                document.body.appendChild(a);
                a.click();
                a.remove();
            } catch (e) {
                var p = document.createElement('p');
                p.style.cssText = 'padding:1rem;margin:1rem;font-family:system-ui,sans-serif;font-size:14px;color:#333;max-width:28rem;';
                p.textContent = 'Could not save the image from the browser. Go back, open “View or print”, then use your browser’s share or screenshot.';
                document.body.appendChild(p);
            }
        }).catch(function () {
            var p = document.createElement('p');
            p.style.cssText = 'padding:1rem;margin:1rem;font-family:system-ui,sans-serif;font-size:14px;color:#333;max-width:28rem;';
            p.textContent = 'Could not create the image. Open “View or print” and save from the browser instead.';
            document.body.appendChild(p);
        });
    }
    if (document.fonts && document.fonts.ready) {
        document.fonts.ready.then(function () { setTimeout(capture, 300); });
    } else {
        setTimeout(capture, 600);
    }
})();
</script>
<?php endif; ?>
<?php /**PATH C:\Users\Chirag\Desktop\New folder\ChandlaBook\resources\views/client/marriage-invitations/partials/export-png-script.blade.php ENDPATH**/ ?>
<?php if(!empty($demoThumbIframe)): ?>
<script>
(function () {
    function ensureViewport(root) {
        var vp = document.getElementById('cb-demo-fit-viewport');
        if (vp && root.parentNode === vp) {
            return vp;
        }
        if (vp && !document.body.contains(vp)) {
            vp = null;
        }
        vp = document.createElement('div');
        vp.id = 'cb-demo-fit-viewport';
        root.parentNode.insertBefore(vp, root);
        vp.appendChild(root);
        return vp;
    }

    function fitCbDemoThumb() {
        var root = document.getElementById('cb-demo-fit-root');
        if (!root) return;
        var vp = ensureViewport(root);
        var pad = 24;
        var vw = Math.max(1, window.innerWidth - pad);
        var vh = Math.max(1, window.innerHeight - pad);

        /* Measure at natural dimensions; never reuse a narrowed viewport width for scrollWidth */
        vp.style.width = '';
        vp.style.height = '';
        root.style.transform = '';
        root.style.transformOrigin = '';
        void root.offsetHeight;

        var sw = Math.max(root.scrollWidth, root.offsetWidth, root.getBoundingClientRect().width);
        var sh = Math.max(root.scrollHeight, root.offsetHeight, root.getBoundingClientRect().height);
        if (!sw || !sh) return;

        var scale = Math.min(vw / sw, vh / sh, 1) * 0.985;
        var bw = Math.ceil(sw * scale);
        var bh = Math.ceil(sh * scale);

        vp.style.width = bw + 'px';
        vp.style.height = bh + 'px';

        root.style.transformOrigin = 'top left';
        root.style.transform = 'scale(' + scale + ')';
    }

    function bindImgReflow(root) {
        if (!root || !root.querySelectorAll) return;
        var imgs = root.querySelectorAll('img');
        for (var i = 0; i < imgs.length; i++) {
            (function (img) {
                if (img.complete && img.naturalWidth > 0) return;
                img.addEventListener('load', function () { fitCbDemoThumb(); }, { once: true });
                img.addEventListener('error', function () { fitCbDemoThumb(); }, { once: true });
            })(imgs[i]);
        }
    }

    function run() {
        var root = document.getElementById('cb-demo-fit-root');
        bindImgReflow(root);
        fitCbDemoThumb();
        requestAnimationFrame(fitCbDemoThumb);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', run);
    } else {
        run();
    }
    window.addEventListener('resize', fitCbDemoThumb);
    if (document.fonts && document.fonts.ready) {
        document.fonts.ready.then(function () { setTimeout(fitCbDemoThumb, 50); });
    }
    window.addEventListener('load', function () { setTimeout(fitCbDemoThumb, 150); });
})();
</script>
<?php endif; ?>
<?php /**PATH C:\Users\Chirag\Desktop\New folder\ChandlaBook\resources\views/client/marriage-invitations/partials/demo-thumb-fit-script.blade.php ENDPATH**/ ?>
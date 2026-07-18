@if(!empty($demoThumbIframe))
<style id="cb-demo-thumb-fit">
    html.cb-demo-thumb-scope,
    html.cb-demo-thumb-scope body.cb-demo-thumb-fit {
        height: 100%;
        margin: 0;
        overflow: hidden !important;
    }
    body.cb-demo-thumb-fit {
        min-height: 0 !important;
        padding: 10px !important;
        box-sizing: border-box !important;
        background-attachment: scroll !important;
        display: flex !important;
        justify-content: center !important;
        align-items: flex-start !important;
    }
    /* Viewport clips to scaled pixel size; inner #cb-demo-fit-root uses transform (layout box stays full until painted). */
    body.cb-demo-thumb-fit #cb-demo-fit-viewport {
        overflow: hidden;
        flex-shrink: 0;
        box-sizing: border-box;
        margin-left: auto;
        margin-right: auto;
    }
    body.cb-demo-thumb-fit #cb-demo-fit-root {
        flex-shrink: 0;
        will-change: transform;
        /* Shrink-wrap card width so scrollWidth stays ~560px; avoids collapse after viewport is narrowed */
        width: max-content;
        max-width: none;
        box-sizing: border-box;
    }
</style>
@endif

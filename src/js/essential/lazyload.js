import EntryTrigger from "../helpers/entry-trigger"

function init() {
    let srcsets = [].slice.call(document.querySelectorAll('[data-srcset]:not(.loaded)'))
    if (srcsets) {
        srcsets.forEach(el => {
            new EntryTrigger(el.parentElement.querySelector('img'),() => {
                el.addEventListener('load', () => el.classList.add('loaded'),false)
                el.srcset = el.dataset.srcset
            })
        })
    }
    let srcs = [].slice.call(document.querySelectorAll('img[data-src]:not(.loaded)'))
    if (srcs) {
        srcs.forEach(el => {
            new EntryTrigger(el,() => {
                el.addEventListener('load', () => el.classList.add('loaded'),false)
                el.src = el.dataset.src
            })
        })
    }

    let videoSrcs = [].slice.call(document.querySelectorAll('video:not(.loaded) source[data-src]'))
    if (videoSrcs) {
        videoSrcs.forEach(el => {
            new EntryTrigger(el.parentElement,() => {
                el.addEventListener('load', () => {
                    el.parentElement.classList.add('loaded')
                },false)
                el.src = el.dataset.src
                el.parentElement.load()
            })
        })
    }

    let cssSrcs = [].slice.call(document.querySelectorAll('link.preload-css:not(.loaded)'))
    if (cssSrcs) {
        cssSrcs.forEach(el => {
            requestAnimationFrame(() => {
                el.rel = 'stylesheet'
                el.classList.add('loaded')
            })
        })
    }
}

window.addEventListener('load', init, false)
window.loadImages = () => init()
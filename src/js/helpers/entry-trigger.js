export default class EntryTrigger {
    constructor(element,callback,options = {
        disconnect: false
    }) {
        if (element === 'undefined' || !element.nodeType || typeof callback !== 'function') {
            return
        }
        this.options = options
        this.element = element
        this.callback = callback
        this.observer = new IntersectionObserver((e,o) => this._callback(e,o))
        this.observer.observe(this.element)     
    }

    _callback(entries,observer) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                console.log('intersecting!');
                this.callback()
                if (this.options.disconnect) {
                    observer.disconnect(this.element)
                }
            }
        })
    }
}
import Glide from '@glidejs/glide'

var glide = new Glide('.glide', {
    type: 'carousel',
    perView: 6,
    focusAt: 'center',
    breakpoints: {
      800: {
        perView: 2
      },
      480: {
        perView: 1
      }
    }
})
glide.mount()
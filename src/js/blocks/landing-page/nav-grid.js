;(function(){

    var gridItems = document.querySelectorAll("[data-id]")

    gridItems.forEach(item => {
        
        item.addEventListener('mouseover', (event => {
            item.classList.add('dehover-shrink')
            var list = item.querySelector("li")
            var image = list.querySelector("img")
            image.classList.add('dehover-image')
        }));
    })
})();


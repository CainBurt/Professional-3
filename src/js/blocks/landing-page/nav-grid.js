;(function(){

    var gridItems = document.querySelectorAll("#grid-item")

    gridItems.forEach(item => {
        
        item.addEventListener('mouseover', (event => {
            item.classList.add('dehover-shrink')
            var list = item.querySelector("li")
            var icon = list.querySelector("#icon")
            icon.classList.add('dehover-image')
        }));
    })
})();


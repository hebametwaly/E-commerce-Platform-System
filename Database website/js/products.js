let products_2 = null;

fetch('../products_2.json')
.then(response => response.json())
.then (data => {
    products_2 = data;
    addDataToHTML();
});

function addDataToHTML () {
    let listProductHTML = document.getElementById('cont-products');
    listProductHTML.innerHTML = "";

    if (products_2 != null) {
        products_2.forEach(product => {
            let newProduct = document.createElement('article');
            newProduct.classList.add('product');
            newProduct.innerHTML = `<div class="product-container">
                <img
                    src="${product.image}"
                    class="product-img img"
                    alt="product"
                />

                <div class="product-icons">
                <a class="product-icon" onclick ="showProductDetails(${product.id})">
                    <i class="fas fa-search" title="View Product"></i>
                </a>
                <button class="product-cart-btn product-icon" onclick="addCart(${product.id})">
                    <i class="fas fa-shopping-cart" title="Purchase"></i>
                </button>
                </div>
            </div>
            <footer>
                <h5 class="product-name">${product.title}</h5>
                <span class="product-price">$${product.price}</span>
            </footer>`;
            listProductHTML.appendChild(newProduct);
        });
    }
}

function showProductDetails (productId) {
    window.location.href = 'single-product.html?id= ' + productId;
}
addDataToHTML();
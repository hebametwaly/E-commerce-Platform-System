let products = null;

fetch('../products.json')
.then(response => response.json())
.then (data => {
    products = data;
    addDataToHTML();
});

addDataToHTML();
function addDataToHTML () {
    let listProductHTML = document.getElementById('cont-products');
    // listProductHTML.innerHTML = "";

    if (products != null) {
        products.forEach(product => {
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

/* -------------------------------------------------------------------------- */
/*                                AddDataToCart                               */
/* -------------------------------------------------------------------------- */
let listCart =[];


function addCart($idProduct) {
    let productCopy;

    if (products.some(product => product.id == $idProduct)) {
        productCopy = JSON.parse(JSON.stringify(products));
    } 
    else if (products_2.some(product => product.id == $idProduct)) {
        productCopy = JSON.parse(JSON.stringify(products_2));
    } else {
        console.error("Product not found");
        return;
    }
    if (!listCart[$idProduct]) {
        let dataProduct = productCopy.filter(
            product => product.id == $idProduct
        )[0];
        listCart[$idProduct] = dataProduct;
        listCart[$idProduct].quantity = 1;
    } else {
        listCart[$idProduct].quantity++;
    }
    addCartToHTML();
    }

addCartToHTML();
function addCartToHTML() {
    let listCartHTML = document.querySelector('.cart-items');
    listCartHTML.innerHTML = '';

    let totalHTML = document.querySelector('.cart-item-count');
    let totalQuantity = 0;

    let totalPriceHTML = document.getElementById('total');
    totalPrice = 0;


    if (listCart) {
        listCart.forEach(product => {
            if (product) {
                let newCart = document.createElement('article');
                newCart.classList.add('cart-item');
                newCart.innerHTML = `    

                        <img
                        src="${product.image}"
                        class="cart-item-img"
                        alt="product"
                        />
                        <div class="cart-item-info">
                        <h5 class="cart-item-name">${product.title}</h5>
                        <span class="cart-item-price">${product.price}</span>
                        <button class="cart-item-remove-btn">remove</button>
                        </div>

                        <div>
                        <button class="cart-item-increase-btn" onclick="changeQuantity(${product.id}, '+')">
                            <i class="fas fa-chevron-up"></i>
                        </button>

                        <span class="cart-item-amount">${product.quantity}</span>

                        <button class="cart-item-decrease-btn" onclick="changeQuantity(${product.id}, '-')">
                            <i class="fas fa-chevron-down"></i>
                        </button>
                        </div>`;
                listCartHTML.appendChild(newCart);
                totalQuantity = totalQuantity + product.quantity;
                totalPrice = totalPrice + (product.price * product.quantity);
            }
        });
        totalHTML.innerHTML = totalQuantity;
        totalPriceHTML.innerHTML = "$" + totalPrice ;
        
    }
}




function changeQuantity ($idProduct, $type){
    switch ($type) {
        case '-':
            listCart[$idProduct].quantity--;
            if (listCart[$idProduct].quantity <= 0) {
                delete listCart[$idProduct];
            }
            break;
        case '+':
            listCart[$idProduct].quantity++;
            break;
        default:
            break;
    }
    addCartToHTML();
}
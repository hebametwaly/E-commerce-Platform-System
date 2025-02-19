
const toggleNav = document.querySelector('.toggle-nav');
const sidebarOverlay = document.querySelector('.sidebar-overlay');
const closeBtn = document.querySelector('.sidebar-close');

/* -------------------------------------------------------------------------- */
/*                                  showNav                                   */
/* -------------------------------------------------------------------------- */
toggleNav.addEventListener('click', () => {
  sidebarOverlay.classList.add('show');
});
closeBtn.addEventListener('click', () => {
  sidebarOverlay.classList.remove('show');
});


/* -------------------------------------------------------------------------- */
/*                                  showCart                                  */
/* -------------------------------------------------------------------------- */
const cartOverlay = document.querySelector('.cart-overlay');
const closeCartBtn = document.querySelector('.cart-close');
const toggleCartBtn = document.querySelector('.toggle-cart');
// const productCartBtnList = [...document.querySelectorAll('.product-cart-btn')];

toggleCartBtn.addEventListener('click', () => {
  cartOverlay.classList.add('show');
});
closeCartBtn.addEventListener('click', () => {
  cartOverlay.classList.remove('show');
});
// productCartBtnList.forEach((btn) => {
//   btn.addEventListener('click', () => {
//     cartOverlay.classList.add('show');
//   });
// });


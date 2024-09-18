document.addEventListener("DOMContentLoaded", function () {
  const productContainer = document.querySelector(".pro-container");

  // Fetch products from localStorage
  const products = JSON.parse(localStorage.getItem("products")) || [];

  // Iterate through products and add them to the DOM
  products.forEach((product) => {
    const productHTML = `
            <div class="pro">
                <img src="${product.image}" alt="${product.name}">
                <div class="des">
                    <span>New Product</span>
                    <h5>${product.name}</h5>
                    <p>${product.description}</p>
                    <div class="star">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                    </div>
                    <h4>$${product.price}</h4>
                </div>
                <a href="#"><i class="fas fa-shopping-cart cart"></i></a>
            </div>
        `;

    // Insert the new product into the product container
    productContainer.insertAdjacentHTML("beforeend", productHTML);
  });
});

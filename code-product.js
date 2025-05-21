
let allProducts = [];
let selectedCategory = 'all';  

function loadFilteredProducts() {
  const categoryFilter = document.getElementById('categoryFilter');
  const productsContainer = document.getElementById('productsContainer');

  fetch('get_products.php')
    .then(response => response.json())
    .then(products => {
      allProducts = products;

       
      function displayFilteredProducts() {
        const filteredProducts = selectedCategory === 'all' 
          ? allProducts 
          : allProducts.filter(p => p.category_id === selectedCategory);

        productsContainer.innerHTML = '';
        filteredProducts.forEach(p => {
          productsContainer.appendChild(createProductCard(p));
        });
      }

     
      categoryFilter.addEventListener('change', (e) => {
        selectedCategory = e.target.value;
        displayFilteredProducts();
      });

      openBtn.onclick = () => {
        search.style.display = 'flex';
        selectedCategory = 'all'; 
        categoryFilter.value = 'all'; 
        displayFilteredProducts();  
        searchBar.focus();
      };

      closeBtn.onclick = () => {
        search.style.display = 'none';
      };

     
      displayFilteredProducts();
    })
    .catch(error => console.error("Error:", error));
}

 
window.onload = loadFilteredProducts;


// دالة إنشاء كارد المنتج 
function createProductCard(product) {
  const productCard = document.createElement('div');
  productCard.className = 'productCard';
  productCard.id = `product-${product.product_id}`;

  const imagePath = `uploads/${product.product_id}.jpg`;

  productCard.innerHTML = `
      <div> <img class="product-image" src="${imagePath}" alt="${product.name}"> </div>
      <h2 class="product-name">${product.name}</h2>
      <p class="product-price">${product.price} EGP</p>
      <div>
      <button class="add-to-cart">+</button>
      <button><a href="product-details.html?product_id=${product.product_id}">Details</a></button>
      </div>
  `;
  
  // ----------------------------------------------------------------------------------
  
  
  // إضافة حدث النقر على زر الإضافة
  const addButton = productCard.querySelector('.add-to-cart');
  addButton.addEventListener('click', () => {
    addToCart(product);
  });
  
  return productCard;
}

function addToCart(product) {

  fetch('check_auth.php')
    .then(response => response.json())
    .then(data => {
      if (!data.authenticated) {
        alert('please login')
        window.location.href = 'login_signup.php';
        return;
      }
    })


  fetch('add_to_cart.php', {
      method: 'POST',
      headers: {
          'Content-Type': 'application/json',
      },
      body: JSON.stringify({
          product_id: product.product_id
      })
  })
  .then(response => response.json())
  .then(data => {
      if (data.success) {
          alert('تمت الإضافة للسلة بنجاح');
      } else {
          alert(data.error || 'حدث خطأ ما');
      }
  })
  .catch(error => {
      console.error('Error:', error);
      alert('فشل في الإتصال بالخادم');
  });
}

// ----------------------------------------------------------------------------------

// البحث أثناء الكتابة
const searchBar = document.getElementById('search-bar');
const searchResults = document.getElementById('searchResults');

searchBar.addEventListener('input', function() {
  const searchTerm = this.value.toUpperCase();
  searchResults.innerHTML = '';

  if (searchTerm.length > 0) {
    const filtered = allProducts.filter(product => 
      product.name.toUpperCase().includes(searchTerm)
    );

      if (filtered.length > 0) {
      searchResults.style.display = 'block';
      searchOverlay.style.display = 'block';
    } else {
      closeSearch();
    }

    filtered.forEach(product => {
      const div = document.createElement('div');
      div.className = 'search-item';
      div.textContent = product.name;
      div.onclick = () => scrollToProduct(product.product_id);
      searchResults.appendChild(div);
    });

    searchResults.style.display = filtered.length ? 'block' : 'none';
  } else {
    searchResults.style.display = 'none';
  }
});

function closeSearch() {
  searchResults.style.display = 'none';
  searchOverlay.style.display = 'none';
}

// إغلاق عند النقر على الخلفية
searchOverlay.addEventListener('click', closeSearch);

// إغلاق عند اختيار نتيجة
function scrollToProduct(productId) {
  closeSearch(); 
}

// التمرير إلى المنتج
function scrollToProduct(productId) {
  const productElement = document.getElementById(`product-${productId}`);
  if (productElement) {
    productElement.scrollIntoView({ behavior: 'smooth' });
    productElement.classList.add('highlight');
    setTimeout(() => productElement.classList.remove('highlight'), 2000);
  }
  searchResults.style.display = 'none';
  searchOverlay.style.display = 'none';
}

// إغلاق القائمة عند النقر خارجها
document.addEventListener('click', (e) => {
  if (!e.target.closest('#search')) {
    searchResults.style.display = 'none';
  }
});

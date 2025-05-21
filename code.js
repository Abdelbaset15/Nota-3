document.addEventListener('DOMContentLoaded', function() {
    // التحقق من وجود جلسة مستخدم (PHPSESSID)
    if (!document.cookie.includes('PHPSESSID')) {
        // عرض رسالة التنبيه
        document.getElementById('loginAlert').style.display = 'block';
        
        // إخفاء جميع المنتجات (اضف class="product" لكل قسم منتجات)
        const products = document.querySelectorAll('.product');
        products.forEach(product => {
            product.style.display = 'none';
        });
    }
});
// ----------------------------------------------------------------------------------

const products = [
    {
        image: "imgs/amplifers.jpg"
    },
    {
        image: "imgs/drums.jpg"
    },
    {
        image: "imgs/audio.jpg"
    },
    {
        image: "imgs/guitars.jpg"
    },
    {
        image: "imgs/organs.jpg"
    },
    {
        image: "imgs/piano.jpg"
    },
    {
        image: "imgs/strings.jpg"
    },
    {
        image: "imgs/synthesizers.jpg"
    },
    {
        image: "imgs/traditional percussion.jpg"
    },
    {
        image: "imgs/violins.jpg"
    },
    {
        image: "imgs/western instrments.jpg"
    },
];

products.forEach(product => {
  const img = new Image();
  img.src = product.image;
});

const productDiv = document.getElementById("product");

let currentIndex = 0;

function displayProduct() {
    const product = products[currentIndex];
    productDiv.innerHTML = `
        <img src="${product.image}" alt="${product.name}">
    `;

    currentIndex = (currentIndex + 1) % products.length;
}

setInterval(displayProduct, 5000);

displayProduct();


// ---------------------------------------------------------------------------

const links = document.querySelectorAll('.header-links div a');

links.forEach(link => {
    link.addEventListener('mouseover', () => {
        links.forEach(otherLink => {
            if (otherLink !== link) {
                otherLink.style.filter = 'blur(4px)';
                otherLink.style.opacity = '0.6'; 
            }
        });
    });

    link.addEventListener('mouseout', () => {
        links.forEach(otherLink => {
            otherLink.style.filter = 'none';
            otherLink.style.opacity = '1'; 
        });
    });
});

// -------------------------------------------------------------------------

document.addEventListener("DOMContentLoaded", function () {
  let backToTopBtn = document.getElementById("backToTop");
  
  window.onscroll = function () {
      if (window.scrollY > 300) { 
          backToTopBtn.style.display = "flex";
      } else {
          backToTopBtn.style.display = "none";
      }
  };

  backToTopBtn.addEventListener("click", function () {
      window.scrollTo({
          top: 0,
          behavior: "smooth"
      });
  });
});

// -------------------------------------------------------------------------


function loadProducts() {
  fetch('get_products.php')
      .then(response => response.json())
      .then(products => {

          const guitarContainer = document.getElementById('guitarContainer');
          const drumsContainer = document.getElementById('drumsContainer');
          const keyboardsContainer = document.getElementById('keyboardsContainer');

          // مسح المحتوى القديم (إذا وجد)
          guitarContainer.innerHTML = '';
          drumsContainer.innerHTML = '';
          keyboardsContainer.innerHTML = '';

          // تصنيف المنتجات
          const guitarProducts = [];
          const drumsProducts = [];
          const keyboardsProducts = [];

          products.forEach(product => {
              if (product.category_id === '1') {
                  guitarProducts.push(product); 
              } else if (product.category_id === '2') {
                  drumsProducts.push(product);
              } else if (product.category_id === '3') {
                  keyboardsProducts.push(product); 
              }
          });

          // عرض 4 منتجات فقط في كل قسم
          displayProducts(guitarProducts.slice(0, 4), guitarContainer); // ← عرض 4 منتجات فقط
          displayProducts(drumsProducts.slice(0, 4), drumsContainer); // عرض 4 منتجات فقط
          displayProducts(keyboardsProducts.slice(0, 4), keyboardsContainer); // عرض 4 منتجات فقط
      })
      .catch(error => console.error("Error loading products:", error));
}

// دالة لعرض المنتجات في حاوية معينة
function displayProducts(products, container) {
  products.forEach(product => {
      const card = createProductCard(product); // إنشاء كارد المنتج
      container.appendChild(card);
    });
}

// دالة مساعدة لإنشاء كارد المنتج
function createProductCard(product) {
  const card = document.createElement('div');
  card.className = 'card';

  // مسار الصورة بناءً على product_id
  const imagePath = `uploads/${product.product_id}.jpg`;
  
  // محتوى الكارد
  card.innerHTML = `
  <img class="product-image" src="${imagePath}" alt="${product.name}">
  <h2 class="product-name">${product.name}</h2>
      <p class="product-price">${product.price} EGP</p>
      <div>
      <button class="add-to-cart">+</button>
      <button><a href="product-details.html?product_id=${product.product_id}">Details</a></button>
      </div>
  `;

  // اضافه المنتج لصفحه الكارت
  const addButton = card.querySelector('.add-to-cart');
  addButton.addEventListener('click', () => {
      addToCart(product);
  });

  return card;
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
          alert('Added to cart successfully');
      } else {
          alert(data.error || 'Something went wrong');
      }
  })
  .catch(error => {
      console.error('Error:', error);
      alert('Failed to add to cart');
  });
}


window.onload = loadProducts;


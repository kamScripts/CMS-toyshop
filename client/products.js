const filtersForm = document.querySelector('#filtersForm');
// filter buttons
const applyButton = document.querySelector('#applyFilters');
const resetButton = document.querySelector('#resetFilters');
//filter inputs
const filterCollection = document.querySelector('#filterCollection');
const filterBrand = document.querySelector('#filterBrand');
const filterScale = document.querySelector('#filterScale');

const productCount = document.querySelector('#productCount');
const productGrid = document.querySelector('#productsGrid');
const loading = document.querySelector('#loading');
const URL = 'http://localhost/CMS-toyshop/server/carModels'


const fetchScale = async () => {
    const path = `${URL}/scale`;
    const response = await fetch(path);
    if (!response.ok) {
        throw new Error(`HTTP error! Status: ${response.status}`);
    }
    const res = await response.json();
    res.data.forEach((item) => {
        const { scale_id, scale_name } = item;
        const option = document.createElement('option');
        option.text = scale_name;
        option.value = scale_id;
        filterScale.appendChild(option);
    })
}
const fetchCollection = async () => {
    const path = `${URL}/collection`;
    const response = await fetch(path);
    if (!response.ok) {
        throw new Error(`HTTP error! Status: ${response.status}`);
    }
    const res = await response.json();
    res.data.forEach((item) => {
        const { collection_id, category_name } = item;
        const option = document.createElement('option');
        option.text = category_name;
        option.value = collection_id;
        filterCollection.appendChild(option);
    })
}
const fetchBrand = async () => {
    const path = `${URL}/brand`;
    const response = await fetch(path);
    if (!response.ok) {
        throw new Error(`HTTP error! Status: ${response.status}`);
    }
    const res = await response.json();
    res.data.forEach((item) => {
        const { brand_id, brand_name } = item;
        const option = document.createElement('option');
        option.text = brand_name;
        option.value = brand_id;
        filterBrand.appendChild(option);
    })
}

const fetchData = async (url) => {
    try {
        loading.classList.remove('inactive');   // show loading

        const response = await fetch(url);

        if (!response.ok) {
            throw new Error(`HTTP error! Status: ${response.status}`);
        }

        const data = await response.json();

        showProducts(data);

    } catch (err) {
        console.error('Fetch error:', err);
        productGrid.innerHTML = `
            <p>
                Failed to load products. Please try again later.
            </p>`;
    } finally {
        loading.classList.add('inactive');   // hide loading in any case
    }
};

const showProducts = (data) => {
    const { products } = data || {};

    // Clear previous content
    productGrid.innerHTML = '';

    if (!products || products.length === 0) {
        productGrid.innerHTML = '<p class="noResults">No products found.</p>';
        return;
    }

    products.forEach(product => {
        const {
            model_id,
            model_name,
            variant,
            description,
            price,
            stock,
            imagepath,
            brand_id,
            brand_name,
            scale_id,
            scale_name,
            collection_id,
            collection_name
        } = product;
        // Create card and children
        const card = document.createElement('article');
        card.classList.add('productCard');
        card.setAttribute('data-collection', collection_id)
        card.setAttribute('data-scale', scale_id);
        card.setAttribute('data-collection', collection_id);

        const img = document.createElement('img');

        img.src = imagepath && imagepath.trim() !== ''
            ? `assets/products/${imagepath}`
            : 'assets/products/placeholder.png';
        // Fallback if the image doesn't exist or fails to load
        img.onerror = function() {
            this.onerror = null;           // prevent infinite loop if placeholder also fails
            this.src = 'assets/products/placeholder.png';
        };

        img.alt = `${brand_name} ${model_name} ${variant || 'image placeholder'}`;
        img.loading = "lazy";
        img.classList.add('cardImage');

        const title = document.createElement('h3');
        title.textContent = `${brand_name} ${model_name} ${variant || ''}`.trim();

        const desc = document.createElement('p');
        desc.textContent = description || 'No description available';

        const priceEl = document.createElement('p');
        priceEl.classList.add('productPrice');
        priceEl.textContent = `$${parseFloat(price).toFixed(2)}`;

        const btnContainer = document.createElement('div');
        btnContainer.classList.add('productActions');

        const addBtn = document.createElement('button');
        addBtn.textContent = 'Add to trolley';
        addBtn.classList.add('primaryBttn');

        const viewBtn = document.createElement('button');
        viewBtn.textContent = 'View details';
        viewBtn.classList.add('secondaryBttn');

        btnContainer.append(addBtn, viewBtn);

        card.append(img, title, desc, priceEl, btnContainer);

        productGrid.appendChild(card);
    });

    if (productCount) {
        productCount.textContent = products.length;
    }
};
applyButton.addEventListener('click', (e)=>{
    e.preventDefault();
    const formData = new FormData(filtersForm);

});

fetchData(URL);
fetchScale();
fetchCollection();
fetchBrand();

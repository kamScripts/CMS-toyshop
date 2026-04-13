const filtersForm = document.querySelector('#filtersForm');
// filter buttons
const applyButton = document.querySelector('#applyFilters');
const resetButton = document.querySelector('#resetFilters');
//filter inputs
const filterCollection = document.querySelector('#filterCollection');
const filterBrand = document.querySelector('#filterBrand');
const filterScale = document.querySelector('#filterScale');
const searchModel = document.querySelector('#searchModel');
const minPriceInput = document.querySelector('#minPrice');
const maxPriceInput = document.querySelector('#maxPrice');

const productCount = document.querySelector('#productCount');
const productGrid = document.querySelector('#productsGrid');
const loading = document.querySelector('#loading');
const URL = 'http://localhost/CMS-toyshop/server/carModels';

let allProducts = [];

const getUrlParams = () => {
    const params = new URLSearchParams(window.location.search);
    return {
        search: params.get('search') || ''
    };
};

const initProducts = async () => {
    const params = getUrlParams();

    // Fetch all products first
    await fetchData(URL);

    // If there is a search term in URL, apply it automatically
    if (params.search) {
        if (searchModel) {
            searchModel.value = params.search;// fill the filter search box
        }
        applyFiltersFromUrl(params.search);
    }
};

const applyFiltersFromUrl = (searchTerm) => {
    //Trigger the same filtering logic as the Apply button
    const selectedCollection = filterCollection ? filterCollection.value : '';
    const selectedBrand = filterBrand ? filterBrand.value : '';
    const selectedScale = filterScale ? filterScale.value : '';

    const filtered = allProducts.filter(product => {
        return !searchTerm ||
            (product.model_name && product.model_name.toLowerCase().includes(searchTerm.toLowerCase())) ||
            (product.variant && product.variant.toLowerCase().includes(searchTerm.toLowerCase())) ||
            (product.brand_name && product.brand_name.toLowerCase().includes(searchTerm.toLowerCase()));

    });

    renderProducts(filtered);
};

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
    allProducts = products || [];
    renderProducts(allProducts);
};

const renderProducts = (productsToShow) => {
    //Clear the container
    productGrid.innerHTML = '';
    //Handle 0 matches
    if (!productsToShow || productsToShow.length === 0) {
        productGrid.innerHTML = '<p class="noResults">No products found matching your filters.</p>';
        productCount.textContent = '0';
        return;
    }

    productsToShow.forEach(product => {
        const {
            model_id, model_name, variant, description, price, imagepath,
            brand_id, brand_name, scale_id, scale_name, collection_id, collection_name
        } = product;

        const card = document.createElement('article');
        card.classList.add('productCard');


        const img = document.createElement('img');
        img.src = imagepath && imagepath.trim() !== ''
            ? `assets/products/${imagepath}`
            : 'assets/products/placeholder.png';

        img.onerror = function() {
            this.onerror = null;
            this.src = 'assets/products/placeholder.png';
        };
        img.alt = `${brand_name} ${model_name} ${variant || ''}`;
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

    productCount.textContent = productsToShow.length;
};

applyButton.addEventListener('click', (e) => {
    e.preventDefault();

    const selectedCollection = filterCollection.value;
    const selectedBrand = filterBrand.value;
    const selectedScale = filterScale.value;
    const searchTerm = searchModel ? searchModel.value.toLowerCase().trim() : '';

    const minPrice = minPriceInput ? parseFloat(minPriceInput.value) || 0 : 0;
    const maxPrice = maxPriceInput ? parseFloat(maxPriceInput.value) || Infinity : Infinity;

    const filtered = allProducts.filter(product => {
        const matchCollection = !selectedCollection || product.collection_id == selectedCollection;
        const matchBrand = !selectedBrand || product.brand_id == selectedBrand;
        const matchScale = !selectedScale || product.scale_id == selectedScale;

        const matchSearch = !searchTerm ||
            (product.model_name && product.model_name.toLowerCase().includes(searchTerm)) ||
            (product.variant && product.variant.toLowerCase().includes(searchTerm)) ||
            (product.brand_name && product.brand_name.toLowerCase().includes(searchTerm));

        const productPrice = parseFloat(product.price) || 0;
        const matchPrice = productPrice >= minPrice && productPrice <= maxPrice;

        return matchCollection && matchBrand && matchScale && matchSearch && matchPrice;
    });

    renderProducts(filtered);
});


resetButton.addEventListener('click', () => {
    filtersForm.reset();
    renderProducts(allProducts);
});


initProducts();
fetchScale();
fetchCollection();
fetchBrand();

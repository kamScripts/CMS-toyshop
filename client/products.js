const filtersForm = document.querySelector('#filtersForm');
const applyButton = document.querySelector('#applyFilters');
const resetButton = document.querySelector('#resetFilters');
const productCount = document.querySelector('#productCount');
const productGrid = document.querySelector('#productGrid');
const URL = 'http://localhost/CMS-toyshop/server/carModels'
const fetchData = async (url) => {
    try {
        const response = await fetch(url);
        const data = await response.json();
        console.log(data.products);
        return data;
    } catch (e) {
        console.log(e);
    }
}
fetchData();
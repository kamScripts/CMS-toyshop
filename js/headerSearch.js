const searchForm = document.querySelector('.horizontalForm');
const searchInput = document.querySelector('#searchBar');

searchForm.addEventListener('submit', (e) => {
    e.preventDefault();
    const query = searchInput.value.trim();
    if (query === '') {//Redirect to products page on empty input
        window.location.href = 'products.html';
        return;
    }
    // Redirect to products page with search query as URL parameter
    window.location.href = `products.html?search=${encodeURIComponent(query)}`;
});

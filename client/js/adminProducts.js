import { checkAuth } from './auth.js';
const addModalBtn  = document.getElementById('addProductBtn');
const addModal = document.getElementById('addProductModal');
const addProductForm = document.getElementById('addProductForm')
let allProducts = [];

document.addEventListener('DOMContentLoaded', async () => {
    const user = await checkAuth();
    if (!user) {
        alert("You must be logged in.");
        window.location.href = "login.html";
        return;
    }
    // Load brands and scales for dropdowns
    await loadLookupData();
    loadAllProducts();
});
async function loadLookupData() {
    try {
        // Load Brands
        const brandRes = await fetch('http://localhost/CMS-toyshop/server/carModels/brand');
        const brandData = await brandRes.json();
        const brandSelect = document.getElementById('productBrand');
        brandData.data.forEach(b => {
            const opt = document.createElement('option');
            opt.value = b.brand_id;
            opt.textContent = b.brand_name;
            brandSelect.appendChild(opt);
        });

        // Load Scales
        const scaleRes = await fetch('http://localhost/CMS-toyshop/server/carModels/scale');
        const scaleData = await scaleRes.json();
        const scaleSelect = document.getElementById('productScale');
        scaleData.data.forEach(s => {
            const opt = document.createElement('option');
            opt.value = s.scale_id;
            opt.textContent = s.scale_name;
            scaleSelect.appendChild(opt);
        });

        const models = await fetch('http://localhost/CMS-toyshop/server/carModels/model');
        const modelData = await models.json();
        const modelSelect = document.getElementById('productModel');
        modelData.data.forEach(s => {
            const opt = document.createElement('option');
            opt.value = s.model_id;
            opt.textContent = s.model_name;
            modelSelect.appendChild(opt);
        });

    } catch (e) {
        console.error("Failed to load lookup data", e);
    }
}

async function loadAllProducts() {
    try {
        const res = await fetch('http://localhost/CMS-toyshop/server/carModels');
        const data = await res.json();
        allProducts = data.products || [];
        renderProducts(allProducts);
    } catch (e) {
        console.error(e);
    }
}

function renderProducts(products) {
    const tbody = document.getElementById('productsBody');
    tbody.innerHTML = '';

    products.forEach(p => {
        const row = document.createElement('tr');

        const fields = [
            p.variant_id,
            p.brand_name || '-',
            p.model_name,
            p.variant || '-',
            p.scale_name || '-',
            `$${parseFloat(p.price || 0).toFixed(2)}`,
            p.stock || 0
        ];

        fields.forEach(value => {
            const td = document.createElement('td');
            td.textContent = value;   // SAFE
            row.appendChild(td);
        });

        const actionsTd = document.createElement('td');

        const editBtn = document.createElement('button');
        editBtn.className = 'editBtn';
        editBtn.dataset.id = p.variant_id;
        editBtn.textContent = 'Edit';

        const deleteBtn = document.createElement('button');
        deleteBtn.className = 'deleteBtn';
        deleteBtn.dataset.id = p.variant_id;
        deleteBtn.textContent = 'Delete';

        actionsTd.appendChild(editBtn);
        actionsTd.appendChild(deleteBtn);
        actionsTd.classList.add('tdBtns');
        row.appendChild(actionsTd);
        tbody.appendChild(row);
    });
    addActionListeners();
}

// Show Add-Product Modal
function showAddModal(modal) {

    modal.classList.remove('hidden');
    modal.removeAttribute('aria-hidden');
    document.getElementById('addProductForm').reset();
    document.getElementById('cancelAddBtn').addEventListener('click', ()=> {
        modal.classList.add('hidden');
        modal.ariaHidden = 'true';
    });
}
// Show Add-Product Modal
addModalBtn.addEventListener('click', ()=>showAddModal(addModal));
// Handle Add-Product Form submission
addProductForm.addEventListener('submit', async (e) => {
    e.preventDefault();

    const brandId = document.getElementById('productBrand').value;
    const modelId = document.getElementById('productModel').value;
    const variant = document.getElementById('productVariant').value.trim();
    const scaleId = document.getElementById('productScale').value;
    const price = document.getElementById('productPrice').value;
    const stock = document.getElementById('productStock').value;

    if (!brandId || !modelId || !scaleId || !price) {
        alert("Please fill all required fields.");
        return;
    }

    const saveBtn = document.getElementById('saveAddBtn');
    saveBtn.disabled = true;
    saveBtn.textContent = "Adding...";

    try {
        const response = await fetch('http://localhost/CMS-toyshop/server/carModels/variant', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                brand_id: parseInt(brandId),
                model_id: modelId,
                variant: variant || null,
                scale_id: parseInt(scaleId),
                price: parseFloat(price),
                stock: parseInt(stock)
            }),
            credentials: 'include'
        });

        const result = await response.json();

        if (response.ok) {
            alert("Product added successfully!");
            document.getElementById('addProductModal').classList.add('hidden');
            loadAllProducts(); // refresh list
        } else {
            alert(result.message || "Failed to add product.");
        }
    } catch (error) {
        console.error(error);
        alert("Error connecting to server.");
    } finally {
        saveBtn.disabled = false;
        saveBtn.textContent = "Add Product";
    }
});
function addActionListeners() {
    // Delete buttons
    document.querySelectorAll('.deleteBtn').forEach(btn => {
        btn.addEventListener('click', async () => {
            if (!confirm('Delete this product?')) return;

            const id = btn.dataset.id;
            try {
                const res = await fetch(`http://localhost/CMS-toyshop/server/carModels/variant/${id}`, {
                    method: 'DELETE',
                    credentials: 'include'
                });

                if (res.ok) {
                    alert("Product deleted successfully.");
                    loadAllProducts(); // refresh table
                } else {
                    alert("Failed to delete product.");
                }
            } catch (e) {
                console.error(e);
                alert("Error deleting product.");
            }
        });
    });
    document.querySelectorAll('.editBtn').forEach(btn => {
        btn.addEventListener('click', () => {
            const id = parseInt(btn.dataset.id);
            const product = allProducts.find(p => p.variant_id === id);

            if (product) {
                showEditModal(product);
            } else {
                alert("Product data not found.");
            }
        });
    });
}
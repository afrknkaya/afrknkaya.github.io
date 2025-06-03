<?php
session_start(); // Oturumu başlat

// Kullanıcı giriş yapmamışsa login.php'ye yönlendir
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: login.php?error=2'); // Giriş yapılması gerektiğini belirten bir hata koduyla
    exit;
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TANYERİ - Yönetim Paneli</title>
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/5/w3.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Lato">
    <style>
        :root {
            --primary-color: #5D2B2C;
            --secondary-color: #C89B4F;
            --accent-color: #A88755;
            --text-color-dark: #333333;
            --background-color-light: #FDFBF5;
            --danger-color: #dc3545;
            --success-color: #28a745; /* Kategori kaydet butonu için */
        }
        body { font-family: "Lato", sans-serif; background-color: var(--background-color-light); padding: 20px; color:var(--text-color-dark); }
        .container { max-width: 900px; margin: auto; background-color: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 0 15px rgba(0,0,0,0.1); }
        h1, h2 { color: var(--primary-color); text-align: center; margin-bottom: 25px; }
        hr { border: 0; height: 1px; background: #ddd; margin: 30px 0; }
        label { display: block; margin-top: 15px; margin-bottom: 5px; font-weight: bold; }
        input[type="text"], input[type="number"], textarea, select {
            width: 100%; padding: 12px; margin-bottom: 15px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; font-size: 1em;
        }
        textarea { min-height: 100px; }
        .button-group { margin-top:20px; margin-bottom: 20px; }
        .button-group button, .action-button {
            padding: 12px 22px; border: none; border-radius: 4px; cursor: pointer; color: white; margin-right: 10px; transition: opacity 0.2s; font-size: 1em;
        }
        .button-group button:hover, .action-button:hover { opacity: 0.85; }

        .save-btn { background-color: var(--primary-color); }
        .update-btn { background-color: var(--accent-color); }
        .clear-btn { background-color: var(--secondary-color); color:var(--primary-color)!important;}
        .delete-all-btn { background-color: var(--danger-color); margin-top: 20px; }

        #productListTable { width: 100%; margin-top: 15px; border-collapse: collapse; font-size: 0.95em; }
        #productListTable th, #productListTable td { border: 1px solid #ddd; padding: 10px; text-align: left; vertical-align: middle; }
        #productListTable th { background-color: var(--primary-color); color: white; font-weight: bold; }
        #productListTable img { max-width: 60px; max-height:40px; object-fit:cover; height: auto; border-radius: 4px; }
        .edit-btn { background-color: var(--accent-color); color:white; padding: 6px 12px; font-size:0.9em; margin-right:5px; }
        .delete-btn { background-color: var(--danger-color); color:white; padding: 6px 12px; font-size:0.9em;}

        .message-area { padding: 12px; margin-top: 20px; margin-bottom: 20px; border-radius: 4px; text-align: center; font-weight: bold; display: none; }
        .message-area.success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb;}
        .message-area.error { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb;}

        .admin-header-controls { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap;}
        .back-to-menu { color: var(--primary-color); text-decoration: none; font-weight: bold; font-size: 1.1em;}
        .back-to-menu:hover { text-decoration: underline; }
        .logout-section span { margin-right: 10px; }
        .logout-section a { text-decoration: none; }


        #dropZone { border: 2px dashed #ccc; padding: 20px; text-align: center; margin-bottom: 15px; background-color: #f9f9f9; cursor:pointer; }
        #dropZone.dragover { background-color: #e0e0e0; border-color: #aaa; }
        #imagePreview { max-width: 200px; max-height: 200px; margin-top: 10px; display: none; border:1px solid #ddd; padding:5px;}

        #categoryOrderListContainer .category-order-item { display: flex; align-items: center; margin-bottom: 10px; padding: 8px; background-color: #f9f9f9; border-radius: 4px;}
        #categoryOrderListContainer .category-order-item input[type="number"] { width: 70px; margin-right: 15px; margin-bottom:0; text-align: center;}
        #categoryOrderListContainer .category-order-item label { font-weight: normal; margin:0; }

        .filter-controls { display: flex; gap: 20px; margin-bottom: 20px; flex-wrap: wrap; }
        .filter-controls > div { flex: 1; min-width: 250px;}
        .filter-controls label { margin-top:0; }

        /* Kategori Yönetimi Stilleri */
        #categoryManagementListContainer .category-management-item { display: flex; justify-content: space-between; align-items: center; padding: 10px; border: 1px solid #eee; margin-bottom: 8px; border-radius: 4px; background-color: #f9f9f9; }
        #categoryManagementListContainer .category-name-display,
        #categoryManagementListContainer .category-name-edit { flex-grow: 1; margin-right: 10px; font-size: 1em; }
        #categoryManagementListContainer .category-name-edit { padding: 8px; border: 1px solid #ccc; border-radius: 4px; }
        #categoryManagementListContainer .category-actions button { margin-left: 5px; padding: 5px 10px; font-size: 0.85em; }
        .save-category-btn { background-color: var(--success-color) !important; }
    </style>
</head>
<body>
    <div class="container">
        <div class="admin-header-controls">
            <a href="index.html" class="back-to-menu">&larr; Ana Menüye Dön</a>
            <div class="logout-section">
                <span>Hoş geldin, <?php echo isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : 'Admin'; ?>!</span>
                <a href="logout.php" class="w3-button w3-small" style="background-color: var(--danger-color); color:white;">Çıkış Yap</a>
            </div>
        </div>
        <h1>TANYERİ - Menü Yönetim Paneli</h1>
        <div id="messageArea" class="message-area"></div>

        <form id="menuItemForm">
            <input type="hidden" id="itemId">
            <h2>Ürün Ekle / Düzenle</h2>
            <label for="itemName">Ürün Adı:</label>
            <input type="text" id="itemName" required>

            <label for="itemCategorySelect">Kategori Seçin:</label>
            <select id="itemCategorySelect" name="itemCategorySelect">
                <option value="">--- Kategori Seçiniz ---</option>
            </select>

            <label for="itemNewCategory">Veya Yeni Kategori Adı (isteğe bağlı):</label>
            <input type="text" id="itemNewCategory" placeholder="Yeni kategori ise buraya yazın">

            <label for="itemPrice">Fiyat (Örn: 75 TL):</label>
            <input type="text" id="itemPrice" required>

            <label for="itemDescription">Açıklama (Popup'ta görünecek):</label>
            <textarea id="itemDescription"></textarea>

            <div style="margin-top: 20px; margin-bottom: 20px; padding: 15px; border: 1px dashed var(--secondary-color); border-radius: 8px; background-color: #fff8f0;">
                <input type="checkbox" id="isAiRecommendation" style="width: auto; margin-right: 10px; vertical-align: middle;">
                <label for="isAiRecommendation" style="display: inline; font-weight: bold; color: var(--primary-color);">Yapay Zeka Önerisi Olarak İşaretle</label>
                <p style="font-size: 0.9em; color: #666; margin-top: 5px;">(Sadece bir ürün yapay zeka önerisi olarak işaretlenebilir. Yeni bir ürün işaretlendiğinde eski öneri kaldırılır.)</p>
            </div>
            <label for="itemImageFile">Ürün Resmi Yükle:</label>
            <div id="dropZone">Resmi buraya sürükleyip bırakın veya tıklayıp seçin</div>
            <input type="file" id="itemImageFile" accept="image/*" style="display: none;">
            <img id="imagePreview" src="#" alt="Resim Önizlemesi"/>
            <input type="hidden" id="itemImagePath">

            <div class="button-group">
                <button type="submit" class="save-btn action-button">Ürün Ekle</button>
                <button type="button" id="updateButton" class="update-btn action-button" style="display:none;">Ürünü Güncelle</button>
                <button type="button" id="clearFormButton" class="clear-btn action-button">Formu Temizle</button>
            </div>
        </form>
        <hr>
        <h2>Genel Ayarlar</h2>
        <div style="padding: 20px; border: 1px solid #ddd; border-radius: 8px; background-color: #f9f9f9; margin-bottom: 30px;">
            <label for="aiRecommendationMessageTemplate">Yapay Zeka Önerisi Mesaj Şablonu:</label>
            <textarea id="aiRecommendationMessageTemplate" placeholder="Örn: Bugün size özel olarak &quot;{urunAdi}&quot; ürünümüzü öneririm! Fiyatı: {fiyat}. {aciklama}"></textarea>
            <p style="font-size: 0.85em; color: #777; margin-top: -10px; margin-bottom: 15px;">Kullanabileceğiniz yer tutucular: `{urunAdi}`, `{fiyat}`, `{aciklama}`</p>
            <button type="button" id="saveGeneralSettingsButton" class="action-button save-btn">Ayarları Kaydet</button>
        </div>
        <hr>
        <h2>Kategori Yönetimi</h2>
        <div class="w3-row-padding" style="margin-bottom: 20px;">
            <div class="w3-threequarter">
                <label for="newCategoryNameInput" style="margin-top:0;">Yeni Kategori Adı:</label>
                <input type="text" id="newCategoryNameInput" placeholder="Eklenecek kategori adını yazın...">
            </div>
            <div class="w3-quarter" style="display:flex; align-items:flex-end; margin-bottom:15px;">
                <button type="button" id="addCategoryButton" class="action-button save-btn" style="width:100%;">Kategori Ekle</button>
            </div>
        </div>
        <div id="categoryManagementListContainer">
            </div>
        <hr>
        <h2>Kategori Sıralaması</h2>
        <p>Kategorilerin menü sayfasında görünmesini istediğiniz sırayı belirlemek için sayılar girin (örn: 1, 2, 3...). Aynı sayıyı birden fazla kategoriye verirseniz, kendi aralarında alfabetik olarak dizilirler. Boş bırakılanlar veya 0 girilenler sona eklenir.</p>
        <div id="categoryOrderListContainer"></div>
        <div class="button-group" style="margin-top: 15px;">
            <button type="button" id="saveCategoryOrderButton" class="action-button save-btn">Kategori Sırasını Kaydet</button>
        </div>
        <hr>
        <h2>Mevcut Ürünler</h2>
        <div class="filter-controls">
            <div>
                <label for="searchInputAdmin">Ürün Adında Ara:</label>
                <input type="text" id="searchInputAdmin" placeholder="Aramak için yazın...">
            </div>
            <div>
                <label for="categoryFilterAdmin">Kategoriye Göre Filtrele:</label>
                <select id="categoryFilterAdmin">
                    <option value="">Tüm Kategoriler</option>
                </select>
            </div>
        </div>
        <div style="overflow-x:auto;">
            <table id="productListTable">
                <thead>
                    <tr><th>Resim</th><th>Ad</th><th>Kategori</th><th>Fiyat</th><th>Yapay Zeka Önerisi</th><th>İşlemler</th></tr>
                </thead>
                <tbody id="productList"></tbody>
            </table>
        </div>
           <button type="button" id="deleteAllItemsButton" class="delete-all-btn action-button">Tüm Menüyü Sil (Test)</button>
        <hr>
        <h2>Veri Yönetimi</h2>
        <p>Mevcut menü ürünlerinizi ve kategori sıralamanızı bir JSON dosyası olarak yedekleyebilir veya daha önce yedeklediğiniz bir dosyadan geri yükleyebilirsiniz.</p>
        <div class="button-group">
            <button type="button" id="exportDataButton" class="action-button save-btn" style="background-color: var(--accent-color);">Verileri Dışa Aktar (JSON)</button>
        </div>
        <div style="margin-top: 15px;">
            <label for="importFile" style="margin-top:0; display:inline-block; margin-right:10px;">Verileri İçe Aktar (JSON):</label>
            <input type="file" id="importFile" accept=".json" style="display:inline-block; margin-bottom:10px;">
            <button type="button" id="importDataButton" class="action-button save-btn">İçe Aktar</button>
        </div>
        <hr style="margin-top:30px; margin-bottom:30px;">
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // DOM Elementleri
        const menuItemForm = document.getElementById('menuItemForm');
        const itemIdInput = document.getElementById('itemId');
        const itemNameInput = document.getElementById('itemName');
        const itemCategorySelect = document.getElementById('itemCategorySelect');
        const itemNewCategoryInput = document.getElementById('itemNewCategory');
        const itemPriceInput = document.getElementById('itemPrice');
        const itemDescriptionInput = document.getElementById('itemDescription');
        const isAiRecommendationCheckbox = document.getElementById('isAiRecommendation'); // Yeni AI Önerisi Checkbox
        const productListTableBody = document.getElementById('productList');
        const updateButton = document.getElementById('updateButton');
        const clearFormButton = document.getElementById('clearFormButton');
        const deleteAllItemsButton = document.getElementById('deleteAllItemsButton');
        const messageArea = document.getElementById('messageArea');
        const dropZone = document.getElementById('dropZone');
        const itemImageFileInput = document.getElementById('itemImageFile');
        const imagePreview = document.getElementById('imagePreview');
        const itemImagePathInput = document.getElementById('itemImagePath');
        const categoryOrderListContainer = document.getElementById('categoryOrderListContainer');
        const saveCategoryOrderButton = document.getElementById('saveCategoryOrderButton');
        const exportDataButton = document.getElementById('exportDataButton');
        const importFileInput = document.getElementById('importFile');
        const importDataButton = document.getElementById('importDataButton');
        const searchInputAdmin = document.getElementById('searchInputAdmin');
        const categoryFilterAdmin = document.getElementById('categoryFilterAdmin');
        const newCategoryNameInput = document.getElementById('newCategoryNameInput');
        const addCategoryButton = document.getElementById('addCategoryButton');
        const categoryManagementListContainer = document.getElementById('categoryManagementListContainer');
        
        // Yeni Genel Ayarlar DOM Elementleri
        const aiRecommendationMessageTemplateInput = document.getElementById('aiRecommendationMessageTemplate');
        const saveGeneralSettingsButton = document.getElementById('saveGeneralSettingsButton');


        // localStorage Anahtarları ve Global Değişkenler
        const localStorageKey = 'cafeMenuItems_Tanyeri';
        const orderLocalStorageKey = 'cafeCategoryOrder_Tanyeri';
        const managedCategoriesLocalStorageKey = 'cafeAllCategories_Tanyeri';
        const aiMessageTemplateLocalStorageKey = 'aiRecommendationMessageTemplate_Tanyeri'; // Yeni anahtar

        let menuItems = [];
        let managedCategories = [];
        let aiRecommendationMessageTemplate = ''; // Yeni değişken
        let editingItemId = null;
        let currentFileToUpload = null;

        // --- GENEL AYARLAR FONKSİYONLARI ---
        function loadGeneralSettings() {
            const storedTemplate = localStorage.getItem(aiMessageTemplateLocalStorageKey);
            if (storedTemplate) {
                aiRecommendationMessageTemplate = storedTemplate;
                aiRecommendationMessageTemplateInput.value = storedTemplate;
            } else {
                // Varsayılan mesaj şablonu
                aiRecommendationMessageTemplate = 'Bugün size özel olarak "{urunAdi}" ürünümüzü öneririm! Fiyatı: {fiyat}. {aciklama}';
                aiRecommendationMessageTemplateInput.value = aiRecommendationMessageTemplate;
            }
        }

        function saveGeneralSettings() {
            aiRecommendationMessageTemplate = aiRecommendationMessageTemplateInput.value.trim();
            localStorage.setItem(aiMessageTemplateLocalStorageKey, aiRecommendationMessageTemplate);
            showMessage('Genel ayarlar kaydedildi!', 'success');
        }

        saveGeneralSettingsButton.addEventListener('click', saveGeneralSettings);

        // --- KATEGORİ YÖNETİMİ FONKSİYONLARI ---
        function loadManagedCategories() {
            const storedCategories = localStorage.getItem(managedCategoriesLocalStorageKey);
            if (storedCategories) {
                try { managedCategories = JSON.parse(storedCategories); }
                catch(e) { console.error("Yönetilen kategoriler yüklenirken hata: ", e); managedCategories = []; }
            } else {
                // Eğer managedCategories henüz kaydedilmemişse, menüdeki mevcut kategorilerden oluştur
                if (menuItems && menuItems.length > 0) {
                    managedCategories = [...new Set(menuItems.map(item => item.category).filter(Boolean))].sort((a,b) => a.localeCompare(b, 'tr'));
                } else {
                    managedCategories = [];
                }
            }
            renderCategoryManagementList();
        }

        function saveManagedCategories() {
            managedCategories.sort((a,b) => a.localeCompare(b, 'tr'));
            localStorage.setItem(managedCategoriesLocalStorageKey, JSON.stringify(managedCategories));
            populateCategoryDropdown();
            populateAdminCategoryFilter();
            displayCategoriesForOrdering();
            renderCategoryManagementList(); // Ensure category management list is also updated
        }

        function renderCategoryManagementList() {
            categoryManagementListContainer.innerHTML = '';
            if (managedCategories.length === 0) {
                categoryManagementListContainer.innerHTML = '<p>Henüz yönetilecek kategori eklenmemiş.</p>'; return;
            }
            managedCategories.forEach(categoryName => {
                const itemDiv = document.createElement('div'); itemDiv.className = 'category-management-item';
                itemDiv.setAttribute('data-category', categoryName);
                const nameSpan = document.createElement('span'); nameSpan.className = 'category-name-display'; nameSpan.textContent = categoryName;
                const nameInput = document.createElement('input'); nameInput.type = 'text'; nameInput.className = 'category-name-edit';
                nameInput.value = categoryName; nameInput.style.display = 'none';
                const actionsDiv = document.createElement('div'); actionsDiv.className = 'category-actions';
                const editBtn = document.createElement('button'); editBtn.textContent = 'Düzenle'; editBtn.className = 'action-button edit-btn edit-category-btn';
                const saveBtn = document.createElement('button'); saveBtn.textContent = 'Kaydet'; saveBtn.className = 'action-button save-category-btn';
                saveBtn.style.display = 'none';
                const deleteBtn = document.createElement('button'); deleteBtn.textContent = 'Sil'; deleteBtn.className = 'action-button delete-btn delete-category-btn';
                actionsDiv.appendChild(editBtn); actionsDiv.appendChild(saveBtn); actionsDiv.appendChild(deleteBtn);
                itemDiv.appendChild(nameSpan); itemDiv.appendChild(nameInput); itemDiv.appendChild(actionsDiv);
                categoryManagementListContainer.appendChild(itemDiv);
            });
            attachCategoryActionListeners();
        }

        addCategoryButton.addEventListener('click', function() {
            const newCategoryName = newCategoryNameInput.value.trim();
            if (!newCategoryName) { showMessage('Lütfen geçerli bir kategori adı girin.', 'error'); return; }
            if (managedCategories.map(c => c.toLowerCase()).includes(newCategoryName.toLowerCase())) {
                showMessage(`"${newCategoryName}" adında bir kategori zaten mevcut.`, 'error'); return;
            }
            managedCategories.push(newCategoryName);
            saveManagedCategories();
            newCategoryNameInput.value = '';
            showMessage(`"${newCategoryName}" kategorisi eklendi.`, 'success');
        });

        function attachCategoryActionListeners() {
            categoryManagementListContainer.querySelectorAll('.edit-category-btn').forEach(btn => {
                const newBtn = btn.cloneNode(true); btn.parentNode.replaceChild(newBtn, btn);
                newBtn.addEventListener('click', function() { handleEditCategory(this.closest('.category-management-item')); });
            });
            categoryManagementListContainer.querySelectorAll('.save-category-btn').forEach(btn => {
                const newBtn = btn.cloneNode(true); btn.parentNode.replaceChild(newBtn, btn);
                newBtn.addEventListener('click', function() { handleSaveCategory(this.closest('.category-management-item')); });
            });
            categoryManagementListContainer.querySelectorAll('.delete-category-btn').forEach(btn => {
                const newBtn = btn.cloneNode(true); btn.parentNode.replaceChild(newBtn, btn);
                newBtn.addEventListener('click', function() { handleDeleteCategory(this.closest('.category-management-item')); });
            });
        }

        function handleEditCategory(itemDiv) {
            categoryManagementListContainer.querySelectorAll('.category-management-item').forEach(div => {
                if (div !== itemDiv) {
                    div.querySelector('.category-name-display').style.display = 'inline-block';
                    div.querySelector('.category-name-edit').style.display = 'none';
                    div.querySelector('.edit-category-btn').style.display = 'inline-block';
                    div.querySelector('.save-category-btn').style.display = 'none';
                    div.querySelector('.delete-category-btn').style.display = 'inline-block';
                }
            });
            itemDiv.querySelector('.category-name-display').style.display = 'none';
            const nameInput = itemDiv.querySelector('.category-name-edit');
            nameInput.style.display = 'inline-block'; nameInput.focus(); nameInput.select();
            itemDiv.querySelector('.edit-category-btn').style.display = 'none';
            itemDiv.querySelector('.save-category-btn').style.display = 'inline-block';
            itemDiv.querySelector('.delete-category-btn').style.display = 'none';
        }

        function handleSaveCategory(itemDiv) {
            const oldCategoryName = itemDiv.getAttribute('data-category');
            const newCategoryNameInput = itemDiv.querySelector('.category-name-edit');
            const newCategoryName = newCategoryNameInput.value.trim();

            if (!newCategoryName) {
                showMessage('Kategori adı boş olamaz.', 'error'); newCategoryNameInput.value = oldCategoryName;
                renderCategoryManagementList(); return;
            }
            if (newCategoryName.toLowerCase() !== oldCategoryName.toLowerCase() && 
                managedCategories.some(cat => cat.toLowerCase() === newCategoryName.toLowerCase())) {
                showMessage(`"${newCategoryName}" adında bir kategori zaten mevcut.`, 'error');
                renderCategoryManagementList(); return;
            }
            if (newCategoryName === oldCategoryName) { renderCategoryManagementList(); return; }

            let itemsUpdatedCount = 0;
            menuItems.forEach(item => { if (item.category === oldCategoryName) { item.category = newCategoryName; itemsUpdatedCount++; } });
            if (itemsUpdatedCount > 0) { localStorage.setItem(localStorageKey, JSON.stringify(menuItems)); }

            let currentOrder = JSON.parse(localStorage.getItem(orderLocalStorageKey) || '[]');
            const orderIndex = currentOrder.indexOf(oldCategoryName);
            if (orderIndex > -1) { currentOrder[orderIndex] = newCategoryName; localStorage.setItem(orderLocalStorageKey, JSON.stringify(currentOrder)); }

            const catIndexInManaged = managedCategories.indexOf(oldCategoryName);
            if (catIndexInManaged > -1) { managedCategories[catIndexInManaged] = newCategoryName; }
            else { managedCategories.push(newCategoryName); }

            saveManagedCategories();
            if (itemsUpdatedCount > 0) applyFiltersAndRender();
            showMessage(`"${oldCategoryName}" kategorisi "${newCategoryName}" olarak güncellendi.`, 'success');
        }

        async function handleDeleteCategory(itemDiv) {
            const categoryNameToDelete = itemDiv.getAttribute('data-category');
            const productsInCategory = menuItems.filter(item => item.category === categoryNameToDelete);
            let confirmationMessage = `"${categoryNameToDelete}" kategorisini silmek istediğinizden emin misiniz?`;
            if (productsInCategory.length > 0) {
                confirmationMessage += `\n\nUYARI: Bu kategoriye ait ${productsInCategory.length} ürün bulunmaktadır. Bu ürünlerin kategorisi "Kategorisiz" olarak ayarlanacaktır.`;
            }
            if (confirm(confirmationMessage)) { 
                let itemsUpdated = false;
                menuItems.forEach(item => { 
                    if (item.category === categoryNameToDelete) { 
                        item.category = ""; // Kategori silindiğinde ürünün kategorisini boşalt
                        itemsUpdated = true; 
                    } 
                });
                if (itemsUpdated) { localStorage.setItem(localStorageKey, JSON.stringify(menuItems)); }
                
                // managedCategories'i doğrudan filtrele
                managedCategories = managedCategories.filter(cat => cat !== categoryNameToDelete);
                
                let currentOrder = JSON.parse(localStorage.getItem(orderLocalStorageKey) || '[]');
                currentOrder = currentOrder.filter(cat => cat !== categoryNameToDelete);
                localStorage.setItem(orderLocalStorageKey, JSON.stringify(currentOrder));
                
                saveManagedCategories(); // Güncellenmiş managedCategories'i kaydet ve UI'ı yenile
                if (itemsUpdated) applyFiltersAndRender();
                showMessage(`"${categoryNameToDelete}" kategorisi silindi.`, 'success');
            }
        }

        // --- DİĞER FONKSİYONLAR ---
        function populateAdminCategoryFilter() {
            const currentFilterValue = categoryFilterAdmin.value;
            categoryFilterAdmin.innerHTML = '<option value="">Tüm Kategoriler</option>';
            managedCategories.forEach(cat => {
                const option = document.createElement('option'); option.value = cat; option.textContent = cat;
                categoryFilterAdmin.appendChild(option);
            });
            if (managedCategories.includes(currentFilterValue)) categoryFilterAdmin.value = currentFilterValue;
            else if (currentFilterValue !== "") categoryFilterAdmin.value = "";
        }

        function applyFiltersAndRender() {
            const searchTerm = searchInputAdmin.value.toLowerCase().trim();
            const selectedCategory = categoryFilterAdmin.value;
            let filteredItems = menuItems;
            if (searchTerm) filteredItems = filteredItems.filter(item => item.name.toLowerCase().includes(searchTerm));
            if (selectedCategory) filteredItems = filteredItems.filter(item => item.category === selectedCategory);
            renderProductList(filteredItems);
        }
        searchInputAdmin.addEventListener('input', applyFiltersAndRender);
        categoryFilterAdmin.addEventListener('change', applyFiltersAndRender);

        function displayCategoriesForOrdering() {
            categoryOrderListContainer.innerHTML = '';
            let savedOrderArray = []; const storedOrder = localStorage.getItem(orderLocalStorageKey);
            if (storedOrder) { try { savedOrderArray = JSON.parse(storedOrder); } catch(e){ console.error(e); savedOrderArray = []; } }
            let orderedDisplayCategories = [];
            // Kaydedilmiş sıradaki kategorileri ekle
            savedOrderArray.forEach(catName => { if (managedCategories.includes(catName)) orderedDisplayCategories.push(catName); });
            // ManagedCategories'de olup kaydedilmiş sırada olmayanları ekle (sona)
            managedCategories.forEach(catName => { if (!orderedDisplayCategories.includes(catName)) orderedDisplayCategories.push(catName); });
            
            if (orderedDisplayCategories.length === 0) { categoryOrderListContainer.innerHTML = '<p>Sıralanacak kategori bulunmamaktadır.</p>'; return; }
            
            orderedDisplayCategories.forEach((catName, index) => {
                const div = document.createElement('div'); div.className = 'category-order-item';
                const input = document.createElement('input'); input.type = 'number'; input.min = '1';
                input.value = index + 1; // Varsayılan olarak mevcut sırayı göster
                input.setAttribute('data-category-name', catName); input.className = 'category-order-input';
                const label = document.createElement('label'); label.textContent = catName;
                div.appendChild(input); div.appendChild(label); categoryOrderListContainer.appendChild(div);
            });
        }
        saveCategoryOrderButton.addEventListener('click', function() {
            const inputs = categoryOrderListContainer.querySelectorAll('.category-order-input'); let categoriesToSave = [];
            inputs.forEach(input => { 
                const orderValue = parseInt(input.value, 10); 
                categoriesToSave.push({ 
                    name: input.getAttribute('data-category-name'), 
                    order: (orderValue && orderValue > 0) ? orderValue : 999 // Geçersiz veya boş değerler için sona at
                }); 
            });
            categoriesToSave.sort((a, b) => { 
                if (a.order === b.order) return a.name.localeCompare(b.name, 'tr'); // Sıra aynıysa alfabetik
                return a.order - b.order; 
            });
            const orderedCategoryNames = categoriesToSave.map(cat => cat.name);
            localStorage.setItem(orderLocalStorageKey, JSON.stringify(orderedCategoryNames));
            showMessage('Kategori sıralaması kaydedildi!', 'success');
            displayCategoriesForOrdering(); // Sıralamayı yeniden yükle ve göster
        });

        dropZone.addEventListener('click', () => itemImageFileInput.click());
        dropZone.addEventListener('dragover', (event) => { event.preventDefault(); dropZone.classList.add('dragover'); });
        dropZone.addEventListener('dragleave', () => dropZone.classList.remove('dragover'));
        dropZone.addEventListener('drop', (event) => { event.preventDefault(); dropZone.classList.remove('dragover'); if (event.dataTransfer.files.length > 0) previewFile(event.dataTransfer.files[0]); });
        itemImageFileInput.addEventListener('change', (event) => { if (event.target.files.length > 0) previewFile(event.target.files[0]); });
        function previewFile(file) {
            if (!file.type.startsWith('image/')) { showMessage('Lütfen bir resim dosyası seçin.', 'error'); currentFileToUpload = null; return; }
            const maxSizeInBytes = 5 * 1024 * 1024;
            if (file.size > maxSizeInBytes) { showMessage(`Resim boyutu çok büyük (Maks: ${maxSizeInBytes / (1024*1024)}MB).`, 'error'); currentFileToUpload = null; itemImageFileInput.value = ''; return; }
            const reader = new FileReader();
            reader.onloadend = () => { imagePreview.src = reader.result; imagePreview.style.display = 'block'; dropZone.textContent = file.name; }
            reader.readAsDataURL(file); currentFileToUpload = file; itemImagePathInput.value = '';
        }

        function populateCategoryDropdown() {
            const currentSelectedValue = itemCategorySelect.value;
            itemCategorySelect.innerHTML = '<option value="">--- Kategori Seçiniz ---</option>';
            managedCategories.forEach(cat => {
                const option = document.createElement('option'); option.value = cat; option.textContent = cat;
                itemCategorySelect.appendChild(option);
            });
            if (managedCategories.includes(currentSelectedValue)) itemCategorySelect.value = currentSelectedValue;
            else itemCategorySelect.value = "";
        }

        function loadItemsFromLocalStorage() {
            const storedItems = localStorage.getItem(localStorageKey);
            menuItems = storedItems ? JSON.parse(storedItems) : [];
            loadManagedCategories(); // Bu, managedCategories'i yükler ve renderCategoryManagementList'i çağırır
            populateCategoryDropdown();
            populateAdminCategoryFilter();
            displayCategoriesForOrdering();
            applyFiltersAndRender();
            loadGeneralSettings(); // Genel ayarları da yükle
        }

        function renderProductList(itemsToDisplay) {
            productListTableBody.innerHTML = ''; const items = itemsToDisplay;
            if (items.length === 0) {
                const row = productListTableBody.insertRow(); row.insertCell().colSpan = 6; // Sütun sayısı 6 oldu
                if (menuItems.length > 0 && (searchInputAdmin.value || categoryFilterAdmin.value)) { row.cells[0].textContent = 'Arama kriterlerinize uygun ürün bulunamadı.'; }
                else if (menuItems.length === 0) { row.cells[0].textContent = 'Henüz menüye ürün eklenmemiş.'; }
                else { row.cells[0].textContent = 'Gösterilecek ürün yok.'; }
                row.cells[0].style.textAlign = 'center'; row.cells[0].style.padding = '20px'; return;
            }
            items.forEach(item => {
                const row = productListTableBody.insertRow();
                row.insertCell().innerHTML = `<img src="${item.image || 'https://via.placeholder.com/60x40/E0E0E0/BDBDBD?Text=RsmYok'}" alt="${item.name || 'resim'}">`;
                row.insertCell().textContent = item.name; 
                row.insertCell().textContent = item.category || 'Kategorisiz'; // Kategori boşsa "Kategorisiz" göster
                row.insertCell().textContent = item.price;
                // Yapay Zeka Önerisi sütunu
                const aiRecCell = row.insertCell();
                aiRecCell.textContent = item.isAiRecommendation ? 'Evet' : 'Hayır';
                aiRecCell.style.fontWeight = item.isAiRecommendation ? 'bold' : 'normal';
                // CSS değişkenleri yerine doğrudan hex kodları kullanıldı
                aiRecCell.style.color = item.isAiRecommendation ? '#5D2B2C' : '#333333';


                const actionsCell = row.insertCell();
                const editBtn = document.createElement('button'); editBtn.textContent = 'Düzenle'; editBtn.className = 'action-button edit-btn'; editBtn.onclick = () => loadItemForEdit(item.id); actionsCell.appendChild(editBtn);
                const deleteBtn = document.createElement('button'); deleteBtn.textContent = 'Sil'; deleteBtn.className = 'action-button delete-btn'; deleteBtn.onclick = () => deleteItem(item.id); actionsCell.appendChild(deleteBtn);
            });
        }

        function saveItemsToLocalStorage() {
            localStorage.setItem(localStorageKey, JSON.stringify(menuItems));

            // Mevcut ürünlerden kategorileri al
            const categoriesFromItems = [...new Set(menuItems.map(item => item.category).filter(Boolean))];
            
            // Daha önce yönetilen kategorileri yükle (manuel eklenenleri korumak için)
            let currentManaged = JSON.parse(localStorage.getItem(managedCategoriesLocalStorageKey) || '[]');
            
            // Ürünlerdeki kategorilerle, mevcut yönetilen kategorileri birleştir
            // Bu, ürünlerde olan kategorilerin her zaman managedCategories'de olmasını sağlar
            // ve manuel eklenen kategorilerin de korunmasına yardımcı olur.
            let updatedManagedCategoriesSet = new Set([...categoriesFromItems, ...currentManaged]);
            
            // ManagedCategories'i yeniden oluştur ve sırala
            managedCategories = Array.from(updatedManagedCategoriesSet).sort((a,b) => a.localeCompare(b, 'tr'));
            
            // Güncellenmiş managedCategories'i kaydet ve ilgili UI elemanlarını yenile
            saveManagedCategories(); 
            
            // Tüm filtreleri ve listeyi yeniden uygula ve render et
            applyFiltersAndRender(); 
        }
        
        function clearForm(focusOnName = true) {
            menuItemForm.reset(); itemIdInput.value = ''; itemNewCategoryInput.value = ''; itemCategorySelect.value = '';
            itemImagePathInput.value = ''; imagePreview.style.display = 'none'; imagePreview.src = '#';
            itemImageFileInput.value = ''; dropZone.textContent = 'Resmi buraya sürükleyip bırakın veya tıklayıp seçin';
            currentFileToUpload = null; editingItemId = null;
            isAiRecommendationCheckbox.checked = false; // AI Önerisi checkbox'ını temizle
            updateButton.style.display = 'none'; menuItemForm.querySelector('.save-btn').style.display = 'inline-block';
            if(focusOnName) itemNameInput.focus();
        }
        function showMessage(message, type = 'success') {
            messageArea.textContent = message; messageArea.className = `message-area ${type}`;
            messageArea.style.display = 'block'; setTimeout(() => { messageArea.style.display = 'none'; }, 3000);
        }

        // Yeni: isAiRecommendationCheckbox için change event listener
        isAiRecommendationCheckbox.addEventListener('change', function() {
            // Eğer checkbox işaretlendiyse ve yeni bir ürün ekleniyorsa (düzenleme yoksa)
            if (this.checked && !editingItemId) {
                // Mevcut tüm ürünlerin isAiRecommendation özelliğini false yap
                menuItems.forEach(item => item.isAiRecommendation = false);
                // NOT: Bu değişiklik henüz localStorage'a kaydedilmez.
                // Sadece formdaki yeni ürünün AI önerisi olarak işaretlenmesine izin verirken,
                // diğer ürünlerin bu özelliğini dahili olarak sıfırlarız.
                // Kayıt işlemi submit handler'da gerçekleşecektir.
            }
            // Eğer checkbox'ın işareti kaldırıldıysa, veya mevcut bir ürün düzenleniyorsa,
            // submit handler zaten doğru mantığı uygulayacaktır.
        });


        menuItemForm.addEventListener('submit', async function(event) {
            event.preventDefault();
            const name = itemNameInput.value.trim(); 
            let category = itemNewCategoryInput.value.trim() || itemCategorySelect.value;
            const price = itemPriceInput.value.trim(); 
            const description = itemDescriptionInput.value.trim();
            const isAiRecommended = isAiRecommendationCheckbox.checked; // AI Önerisi durumunu al
            let imageFinalPath = itemImagePathInput.value;

            if (!name || !category || !price) { showMessage('Ürün Adı, Kategori ve Fiyat zorunludur!', 'error'); return; }

            if (currentFileToUpload) {
                const formData = new FormData(); formData.append('itemImageFile', currentFileToUpload);
                try {
                    const uploadResponse = await fetch('upload.php', { method: 'POST', body: formData });
                    const result = await uploadResponse.json();
                    if (result.success) { imageFinalPath = result.filePath; } 
                    else { showMessage('Resim YÜKLENEMEDİ: ' + result.message, 'error'); return; }
                } catch (error) { showMessage('Resim yükleme hatası: ' + error, 'error'); console.error('Upload error:', error); return; }
            }

            let itemData = { name, category, price, description, image: imageFinalPath };

            // Yapay Zeka Önerisi mantığı: Sadece bir ürün öneri olabilir
            if (isAiRecommended) {
                // Tüm ürünlerin AI önerisi işaretini kaldır
                menuItems.forEach(item => item.isAiRecommendation = false);
                itemData.isAiRecommendation = true; // Mevcut ürünü AI önerisi olarak işaretle
            } else {
                itemData.isAiRecommendation = false; // İşaretli değilse false yap
            }

            if (editingItemId) {
                const itemIndex = menuItems.findIndex(item => item.id === editingItemId);
                if (itemIndex > -1) { 
                    // Eğer güncellenen ürün AI önerisi ise ve checkbox kaldırıldıysa, AI önerisi kalmaz
                    // Ancak, eğer başka bir ürün işaretlendiyse, bu zaten yukarıdaki forEach ile false yapılmıştır.
                    // Bu kontrol, sadece mevcut ürünün işaretinin kaldırılması durumunda gereklidir.
                    if (menuItems[itemIndex].isAiRecommendation && !isAiRecommended) {
                         itemData.isAiRecommendation = false; // AI önerisi kaldırıldı
                    }
                    menuItems[itemIndex] = { ...menuItems[itemIndex], ...itemData, id: editingItemId }; 
                    showMessage('Ürün güncellendi!', 'success');
                }
            } else { 
                itemData.id = Date.now(); 
                menuItems.push(itemData); 
                showMessage('Ürün eklendi!', 'success'); 
            }
            saveItemsToLocalStorage(); 
            // applyFiltersAndRender() saveItemsToLocalStorage içinde çağrılıyor.
            clearForm();
        });

        function loadItemForEdit(id) {
            const itemToEdit = menuItems.find(item => item.id === id);
            if (itemToEdit) {
                itemIdInput.value = itemToEdit.id; itemNameInput.value = itemToEdit.name;
                itemPriceInput.value = itemToEdit.price; itemDescriptionInput.value = itemToEdit.description || '';
                itemNewCategoryInput.value = '';
                let catExistsInManaged = managedCategories.includes(itemToEdit.category);
                itemCategorySelect.value = catExistsInManaged ? itemToEdit.category : "";
                
                isAiRecommendationCheckbox.checked = itemToEdit.isAiRecommendation || false; // AI Önerisi checkbox'ını ayarla

                if (itemToEdit.image && itemToEdit.image.trim() !== '') {
                    imagePreview.src = itemToEdit.image; imagePreview.style.display = 'block';
                    itemImagePathInput.value = itemToEdit.image; dropZone.textContent = "Yeni resim seçin (" + itemToEdit.image.split('/').pop() + ")";
                } else {
                    imagePreview.style.display = 'none'; imagePreview.src = '#'; itemImagePathInput.value = '';
                    dropZone.textContent = 'Resmi buraya sürükleyip bırakın veya tıklayıp seçin';
                }
                itemImageFileInput.value = ''; currentFileToUpload = null;
                editingItemId = id; updateButton.style.display = 'inline-block';
                menuItemForm.querySelector('.save-btn').style.display = 'none';
                window.scrollTo(0, menuItemForm.offsetTop - 20); itemNameInput.focus();
            }
        }
        updateButton.addEventListener('click', () => {
             if (typeof menuItemForm.requestSubmit === 'function') menuItemForm.requestSubmit(); else menuItemForm.dispatchEvent(new Event('submit', {cancelable: true}));
        });

        async function deleteItem(id) {
            const itemToDelete = menuItems.find(item => item.id === id);
            if (!itemToDelete) return;
            if (confirm(`"${itemToDelete.name}" adlı ürünü silmek istediğinizden emin misiniz?`)) { 
                let imagePathToDelete = itemToDelete.image;
                if (imagePathToDelete && imagePathToDelete.trim() !== '' && !imagePathToDelete.startsWith('http') && !imagePathToDelete.startsWith('data:image')) {
                    try {
                        const deleteResponse = await fetch('delete_image.php', {
                            method: 'POST', headers: {'Content-Type': 'application/json'},
                            body: JSON.stringify({ filePath: imagePathToDelete })
                        });
                        const result = await deleteResponse.json();
                        if (result.success) { showMessage('Resim dosyası sunucudan silindi.', 'success'); } 
                        else { showMessage('Ürün verisi silindi ancak sunucudaki resim dosyası silinemedi: ' + result.message, 'error'); console.warn('Resim silinemedi:', result.message); }
                    } catch (error) { showMessage('Resim silme servisine ulaşılamadı.', 'error'); console.error('delete_image.php fetch error:', error); }
                }
                menuItems = menuItems.filter(item => item.id !== id);
                saveItemsToLocalStorage(); 
                // applyFiltersAndRender() saveItemsToLocalStorage içinde çağrılıyor.
                setTimeout(() => showMessage(`"${itemToDelete.name}" ürünü başarıyla silindi.`, 'success'), 100);

                if (editingItemId === id) clearForm(false);
            }
        }
        clearFormButton.addEventListener('click', () => clearForm());
        deleteAllItemsButton.addEventListener('click', function() {
            if (confirm('UYARI: Tüm menü ürünlerini, kategorileri ve kayıtlı kategori sırasını silmek istediğinizden emin misiniz? (Yüklenmiş resim dosyaları sunucudan SİLİNMEYECEKTİR.)')) {
                menuItems = []; localStorage.removeItem(localStorageKey);
                managedCategories = []; localStorage.removeItem(managedCategoriesLocalStorageKey);
                localStorage.removeItem(orderLocalStorageKey);
                localStorage.removeItem(aiMessageTemplateLocalStorageKey); // AI mesaj şablonunu da sil
                saveManagedCategories(); // Bu, UI'ı ve ilgili dropdown'ları günceller
                applyFiltersAndRender(); // Ürün listesini boşaltır
                clearForm(false);
                loadGeneralSettings(); // Genel ayarları varsayılana döndür
                showMessage('Tüm menü verileri ve kategoriler silindi! (Resimler sunucuda kaldı)', 'success');
            }
        });

        exportDataButton.addEventListener('click', function() {
            const categoryOrderToExport = JSON.parse(localStorage.getItem(orderLocalStorageKey) || '[]');
            const managedCategoriesToExport = JSON.parse(localStorage.getItem(managedCategoriesLocalStorageKey) || '[]');
            const aiMessageTemplateToExport = localStorage.getItem(aiMessageTemplateLocalStorageKey) || ''; // AI mesaj şablonunu al
            
            if (menuItems.length === 0 && categoryOrderToExport.length === 0 && managedCategoriesToExport.length === 0 && !aiMessageTemplateToExport) {
                showMessage('Dışa aktarılacak veri bulunmamaktadır.', 'error'); return;
            }
            const dataToExport = { 
                menuItems: menuItems, 
                categoryOrder: categoryOrderToExport, 
                managedCategories: managedCategoriesToExport,
                aiMessageTemplate: aiMessageTemplateToExport // AI mesaj şablonunu ekle
            };
            const jsonData = JSON.stringify(dataToExport, null, 2); const blob = new Blob([jsonData], { type: 'application/json' });
            const url = URL.createObjectURL(blob); const a = document.createElement('a'); a.href = url;
            const date = new Date(); const dateString = `${date.getFullYear()}${(date.getMonth()+1).toString().padStart(2,'0')}${date.getDate().toString().padStart(2,'0')}`;
            a.download = `tanyeri_menu_yedek_${dateString}.json`; document.body.appendChild(a); a.click(); document.body.removeChild(a); URL.revokeObjectURL(url);
            showMessage('Veriler dışa aktarıldı!', 'success');
        });
        importDataButton.addEventListener('click', function() {
            const file = importFileInput.files[0];
            if (!file) { showMessage('Lütfen bir JSON dosyası seçin.', 'error'); return; }
            if (file.type !== "application/json") { showMessage('Lütfen geçerli bir JSON dosyası seçin.', 'error'); importFileInput.value = ''; return; }
            const reader = new FileReader();
            reader.onload = function(event) {
                try {
                    const importedData = JSON.parse(event.target.result);
                    if (importedData && typeof importedData === 'object' && Array.isArray(importedData.menuItems) && Array.isArray(importedData.categoryOrder) && (importedData.managedCategories === undefined || Array.isArray(importedData.managedCategories))) {
                        if (confirm('Mevcut tüm veriler bu dosyadan gelenlerle değiştirilecek. Emin misiniz?')) { 
                            menuItems = importedData.menuItems || [];
                            const importedCategoryOrder = importedData.categoryOrder || [];
                            if (importedData.managedCategories && importedData.managedCategories.length > 0) {
                                managedCategories = importedData.managedCategories;
                            } else {
                                // Eğer içe aktarılan veride managedCategories yoksa, ürünlerden türet
                                managedCategories = [...new Set(menuItems.map(item => item.category).filter(Boolean))].sort((a,b)=>a.localeCompare(b,'tr'));
                            }
                            // AI mesaj şablonunu içe aktar
                            if (importedData.aiMessageTemplate !== undefined) {
                                localStorage.setItem(aiMessageTemplateLocalStorageKey, importedData.aiMessageTemplate);
                            } else {
                                localStorage.removeItem(aiMessageTemplateLocalStorageKey); // Yoksa temizle
                            }

                            localStorage.setItem(localStorageKey, JSON.stringify(menuItems));
                            localStorage.setItem(orderLocalStorageKey, JSON.stringify(importedCategoryOrder));
                            localStorage.setItem(managedCategoriesLocalStorageKey, JSON.stringify(managedCategories));
                            loadItemsFromLocalStorage(); clearForm(false);
                            showMessage('Veriler içe aktarıldı!', 'success');
                        }
                    } else { showMessage('İçe aktarılan JSON dosyasının yapısı geçersiz.', 'error'); }
                } catch (e) { showMessage('JSON dosyası işlenirken hata: ' + e.message, 'error'); console.error("JSON Parse Error:", e); }
                finally { importFileInput.value = ''; }
            };
            reader.onerror = () => { showMessage('Dosya okunurken hata.', 'error'); importFileInput.value = ''; };
            reader.readAsText(file);
        });

        // Başlangıç
        loadItemsFromLocalStorage();
    });
    </script>
</body>
</html>

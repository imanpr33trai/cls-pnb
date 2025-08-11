document.addEventListener("DOMContentLoaded", function () {
    // --- 1. CACHE STATIC DOM ELEMENTS ---
    const sidebar = document.getElementById("sidebar");
    const openSidebarBtn = document.getElementById("open-sidebar");
    const closeSidebarBtn = document.getElementById("close-sidebar");
    const contentArea = document.getElementById("content-area");
    const mainContent = document.getElementById("main-content");
    const menuToggles = document.querySelectorAll(".menu-toggle");
    const activeClass = "bg-gray-700";

    // --- Modal elements ---
    const editAdModal = document.getElementById("edit-ad-modal");
    const deleteAdModal = document.getElementById("delete-ad-modal");
    const editCategoryModal = document.getElementById("edit-category-modal");
    
    let itemToDelete = { id: null, type: null };

    // --- 2. CORE FUNCTIONS ---

    function openSidebar() { sidebar.classList.remove("-translate-x-full"); }
    function closeSidebar() { sidebar.classList.add("-translate-x-full"); }

    function loadPage(page, pushState = true) {
        contentArea.innerHTML = '<div class="flex justify-center items-center h-32"><div class="animate-spin rounded-full h-8 w-8 border-b-2 border-gray-900"></div></div>';
        
        const pageName = page.split('&')[0];
        const fetchUrl = `/admin/pages/${pageName}`;

        fetch(fetchUrl, { headers: { "X-Requested-With": "XMLHttpRequest" } })
            .then(response => {
                if (!response.ok) throw new Error("Page not found or server error.");
                return response.text();
            })
            .then(data => {
                contentArea.innerHTML = data;
                if (pushState) {
                    const newUrl = pageName === "dashboard" ? "/admin" : `/admin/${pageName}`;
                    history.pushState({ page: pageName }, "", newUrl);
                }
                updateActiveLink(pageName);
            })
            .catch(error => {
                contentArea.innerHTML = `<div class="bg-red-100 border-red-400 text-red-700 px-4 py-3 rounded" role="alert">${error.message}</div>`;
                console.error("Error loading page:", error);
            });
    }

    function updateActiveLink(page) {
        const pageName = page.split('&')[0];
        document.querySelectorAll(".tab-link").forEach(l => l.classList.remove(activeClass));
        const activeLink = document.querySelector(`.tab-link[data-page^="${pageName}"]`);
        if (activeLink) {
            activeLink.classList.add(activeClass);
            const parentSubmenu = activeLink.closest(".submenu");
            if (parentSubmenu && !parentSubmenu.classList.contains("open")) {
                const toggle = parentSubmenu.previousElementSibling;
                if(toggle && toggle.matches('.menu-toggle')) {
                    toggle.click();
                }
            }
        }
    }

    // --- 3. MODAL FUNCTIONS ---

    function openModal(modal) {
        if (modal) {
            modal.classList.remove("hidden");
            mainContent.classList.add("blur");
        }
    }

    function closeModal(modal) {
        if (modal) {
            modal.classList.add("hidden");
            mainContent.classList.remove("blur");
        }
    }

    async function openEditModal(modal, contentDiv, url) {
        contentDiv.innerHTML = "Loading...";
        openModal(modal);
        try {
            const response = await fetch(url);
            if (!response.ok) throw new Error('Failed to load form.');
            contentDiv.innerHTML = await response.text();
        } catch (error) {
            contentDiv.innerHTML = `<p class="text-red-500">${error.message}</p>`;
        }
    }

    function openDeleteConfirmationModal(id, type) {
        itemToDelete = { id, type };
        const modalTitle = deleteAdModal.querySelector('h3');
        if(modalTitle) {
            modalTitle.textContent = `Delete ${type.charAt(0).toUpperCase() + type.slice(1)}`;
        }
        openModal(deleteAdModal);
    }

    // --- 4. FORM SUBMISSION & DELETE ---

    async function submitForm(formElement, actionUrl, modalElement) {
        const formData = new FormData(formElement);
        try {
            const response = await fetch(actionUrl, { method: "POST", body: formData });
            const result = await response.json();
            if (result.success) {
                closeModal(modalElement);
                const pageToLoad = actionUrl.includes('category') ? 'category' : 'view-ads';
                loadPage(pageToLoad);
            } else {
                throw new Error(result.message || "An unknown error occurred.");
            }
        } catch (error) {
            alert(`Error: ${error.message}`);
        }
    }

    async function confirmDelete() {
        if (!itemToDelete.id || !itemToDelete.type) return;
        
        const url = `/admin/util/delete_${itemToDelete.type}.php`;
        const formData = new FormData();
        formData.append(`${itemToDelete.type}_id`, itemToDelete.id);

        try {
            const response = await fetch(url, { method: 'POST', body: formData });
            const result = await response.json();
            if (result.success) {
                closeModal(deleteAdModal);
                loadPage(itemToDelete.type === 'ad' ? 'view-ads' : 'category');
            } else {
                throw new Error(result.message);
            }
        } catch (error) {
            alert(`Error: ${error.message}`);
        }
    }
    
    // --- 5. EVENT LISTENERS ---

    // Static listeners
    if (openSidebarBtn) openSidebarBtn.addEventListener("click", openSidebar);
    if (closeSidebarBtn) closeSidebarBtn.addEventListener("click", closeSidebar);

    menuToggles.forEach(toggle => {
        toggle.addEventListener("click", () => {
            const submenu = toggle.nextElementSibling;
            submenu.classList.toggle("open");
            toggle.querySelector("svg").classList.toggle("rotate-180");
        });
    });

    sidebar.addEventListener('click', (e) => {
        const link = e.target.closest('.tab-link');
        if (link && link.dataset.page) {
            e.preventDefault();
            loadPage(link.dataset.page);
            if (window.innerWidth < 768) closeSidebar();
        }
    });

    window.addEventListener("popstate", (e) => {
        loadPage(e.state?.page || "dashboard", false);
    });

    // Modal close buttons
    document.getElementById("close-edit-modal")?.addEventListener("click", () => closeModal(editAdModal));
    document.getElementById("close-edit-category-modal")?.addEventListener("click", () => closeModal(editCategoryModal));
    document.getElementById("cancel-delete-btn")?.addEventListener("click", () => closeModal(deleteAdModal));
    
    // Modal confirm button
    document.getElementById("confirm-delete-btn")?.addEventListener("click", confirmDelete);

    // --- EVENT DELEGATION for dynamic content ---
    mainContent.addEventListener('click', function(event) {
        const target = event.target;
        
        const editAdButton = target.closest('.open-edit-modal');
        if (editAdButton) {
            const adId = editAdButton.dataset.adId;
            const contentDiv = editAdModal.querySelector("#edit-ad-modal-content");
            openEditModal(editAdModal, contentDiv, `/admin/util/get_ad_form.php?ad_id=${adId}`);
        }

        const editCategoryButton = target.closest('.open-edit-category-modal');
        if (editCategoryButton) {
            const categoryId = editCategoryButton.dataset.categoryId;
            const contentDiv = editCategoryModal.querySelector("#edit-category-modal-content");
            openEditModal(editCategoryModal, contentDiv, `/admin/util/get_category_form.php?category_id=${categoryId}`);
        }

        const deleteAdButton = target.closest('.open-delete-modal');
        if (deleteAdButton) {
            openDeleteConfirmationModal(deleteAdButton.dataset.adId, 'ad');
        }
        
        const deleteCategoryButton = target.closest('.open-delete-category-modal');
        if (deleteCategoryButton) {
            openDeleteConfirmationModal(deleteCategoryButton.dataset.categoryId, 'category');
        }

        const editBlogButton = target.closest('.open-edit-blog-modal');
        if (editBlogButton) {
            const blogId = editBlogButton.dataset.blogId;
            const contentDiv = document.getElementById("edit-blog-modal-content");
            openEditModal(document.getElementById("edit-blog-modal"), contentDiv, `/admin/util/get_blog_form.php?blog_id=${blogId}`);
        }

        const deleteBlogButton = target.closest('.open-delete-blog-modal');
        if (deleteBlogButton) {
            openDeleteConfirmationModal(deleteBlogButton.dataset.blogId, 'blog');
        }
    });

    // Event delegation for form submission
    document.body.addEventListener('submit', function(event) {
        if (event.target.matches('#edit-ad-form')) {
            event.preventDefault();
            submitForm(event.target, '/admin/util/edit-ad.php', editAdModal);
        }
        if (event.target.matches('#edit-category-form')) {
            event.preventDefault();
            submitForm(event.target, '/admin/util/update_category.php', editCategoryModal);
        }
        if (event.target.matches('#edit-blog-form')) {
            event.preventDefault();
            submitForm(event.target, '/admin/util/update_blog.php', document.getElementById('edit-blog-modal'));
        }
    });

    // --- 6. INITIAL PAGE LOAD ---
    function getCurrentPage() {
        const path = window.location.pathname;
        const page = path.split('/admin/')[1] || 'dashboard';
        return page === '' ? 'dashboard' : page;
    }
    const initialPage = getCurrentPage();
    updateActiveLink(initialPage);
});
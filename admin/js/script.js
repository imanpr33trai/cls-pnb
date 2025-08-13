document.addEventListener("DOMContentLoaded", function () {
  const sidebar = document.getElementById("sidebar");
  const openSidebarBtn = document.getElementById("open-sidebar");
  const closeSidebarBtn = document.getElementById("close-sidebar");
  const contentArea = document.getElementById("content-area");
  const mainContent = document.getElementById("main-content");
  const menuToggles = document.querySelectorAll(".menu-toggle");
  const activeClass = "bg-gray-700";

  const editAdModal = document.getElementById("edit-ad-modal");
  const deleteAdModal = document.getElementById("delete-ad-modal");
  const editCategoryModal = document.getElementById("edit-category-modal");
  const editBlogModal = document.getElementById("edit-blog-modal");
  const editBlogCatModal = document.getElementById("edit-blog-cat-modal");

  let itemToDelete = { id: null, type: null };

  function openSidebar() {
    sidebar.classList.remove("-translate-x-full");
  }
  function closeSidebar() {
    sidebar.classList.add("-translate-x-full");
  }

  function loadPage(page, pushState = true) {
    contentArea.innerHTML =
      '<div class="flex justify-center items-center h-32"><div class="animate-spin rounded-full h-8 w-8 border-b-2 border-gray-900"></div></div>';

    const pageName = page.split("&")[0];
    const fetchUrl = `/admin/pages/${pageName}`;

    fetch(fetchUrl, { headers: { "X-Requested-With": "XMLHttpRequest" } })
      .then((response) => {
        if (!response.ok) throw new Error("Page not found or server error.");
        return response.text();
      })
      .then((data) => {
        contentArea.innerHTML = data;
        if (pushState) {
          const newUrl =
            pageName === "dashboard" ? "/admin" : `/admin/${pageName}`;
          history.pushState({ page: pageName }, "", newUrl);
        }
        updateActiveLink(pageName);
      })
      .catch((error) => {
        contentArea.innerHTML = `<div class="bg-red-100 border-red-400 text-red-700 px-4 py-3 rounded" role="alert">${error.message}</div>`;
        console.error("Error loading page:", error);
      });
  }

  function updateActiveLink(page) {
    const pageName = page.split("&")[0];
    document
      .querySelectorAll(".tab-link")
      .forEach((l) => l.classList.remove(activeClass));
    const activeLink = document.querySelector(
      `.tab-link[data-page^="${pageName}"]`
    );
    if (activeLink) {
      activeLink.classList.add(activeClass);
      const parentSubmenu = activeLink.closest(".submenu");
      if (parentSubmenu && !parentSubmenu.classList.contains("open")) {
        const toggle = parentSubmenu.previousElementSibling;
        if (toggle && toggle.matches(".menu-toggle")) {
          toggle.click();
        }
      }
    }
  }

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
      if (!response.ok) throw new Error("Failed to load form.");
      contentDiv.innerHTML = await response.text();
    } catch (error) {
      contentDiv.innerHTML = `<p class="text-red-500">${error.message}</p>`;
    }
  }

  function openDeleteConfirmationModal(id, type) {
    itemToDelete = { id, type };
    const modalTitle = deleteAdModal.querySelector("h3");
    if (modalTitle) {
      let typeName = type.replace(/[-_]/g, " ");
      typeName = typeName
        .split(" ")
        .map((word) => word.charAt(0).toUpperCase() + word.slice(1))
        .join(" ");
      modalTitle.textContent = `Delete ${typeName}`;
    }
    openModal(deleteAdModal);
  }

  async function submitForm(formElement, actionUrl, modalElement) {
    const formData = new FormData(formElement);
    try {
      const response = await fetch(actionUrl, {
        method: "POST",
        body: formData,
      });
      const result = await response.json();
      if (result.success) {
        closeModal(modalElement);
        let pageToLoad;
        if (actionUrl.includes("blog_category")) {
          pageToLoad = "blog-cat";
        } else if (actionUrl.includes("category")) {
          pageToLoad = "category";
        } else if (actionUrl.includes("blog")) {
          pageToLoad = "view-blogs";
        } else {
          pageToLoad = "view-ads";
        }
        loadPage(pageToLoad);
      } else {
        throw new Error(result.message || "An unknown error occurred.");
      }
    } catch (error) {
      alert(`Error: ${error.message}`);
    }
  }

  async function toggleUserStatus(userId) {
    try {
      const response = await fetch("/admin/util/toggle_user_status.php", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          Accept: "application/json",
        },
        body: JSON.stringify({ user_id: userId }),
      });
      const result = await response.json();
      if (result.success) {
        loadPage("view_users");
      } else {
        throw new Error(result.message);
      }
    } catch (error) {
      alert(`Error toggling user status: ${error.message}`);
    }
  }

  async function confirmDelete() {
    if (!itemToDelete.id || !itemToDelete.type) return;

    const url = `/admin/util/delete_${itemToDelete.type.replace("-", "_")}.php`;
    const formData = new FormData();
    const idKey =
      itemToDelete.type === "user"
        ? "user_id"
        : `${itemToDelete.type.replace("-", "_")}_id`;
    formData.append(idKey, itemToDelete.id);

    try {
      const response = await fetch(url, { method: "POST", body: formData });
      const result = await response.json();
      if (result.success) {
        closeModal(deleteAdModal);
        let pageToLoad;
        switch (itemToDelete.type) {
          case "ad":
            pageToLoad = "view-ads";
            break;
          case "blog":
            pageToLoad = "view-blogs";
            break;
          case "category":
            pageToLoad = "category";
            break;
          case "blog-cat":
            pageToLoad = "blog-cat";
            break;
          case "user":
            pageToLoad = "view_users";
            break;
          case "subscriber":
            pageToLoad = "view-subscribers";
            break;
          default:
            pageToLoad = "dashboard";
        }
        loadPage(pageToLoad);
      } else {
        throw new Error(result.message);
      }
    } catch (error) {
      alert(`Error: ${error.message}`);
    }
  }

  if (openSidebarBtn) openSidebarBtn.addEventListener("click", openSidebar);
  if (closeSidebarBtn) closeSidebarBtn.addEventListener("click", closeSidebar);

  menuToggles.forEach((clickedToggle) => {
    clickedToggle.addEventListener("click", () => {
      const submenuToToggle = clickedToggle.nextElementSibling;
      const isCurrentlyOpen = submenuToToggle.classList.contains("open");

      menuToggles.forEach((toggle) => {
        toggle.nextElementSibling.classList.remove("open");
        toggle.querySelector("svg")?.classList.remove("rotate-180");
      });

      if (!isCurrentlyOpen) {
        submenuToToggle.classList.add("open");
        clickedToggle.querySelector("svg")?.classList.add("rotate-180");
      }
    });
  });

  sidebar.addEventListener("click", (e) => {
    const link = e.target.closest(".tab-link");
    if (link && link.dataset.page) {
      e.preventDefault();
      loadPage(link.dataset.page);
      if (window.innerWidth < 768) closeSidebar();
    }
  });

  window.addEventListener("popstate", (e) => {
    loadPage(e.state?.page || "dashboard", false);
  });

  document
    .getElementById("confirm-delete-btn")
    ?.addEventListener("click", confirmDelete);

  mainContent.addEventListener("click", function (event) {
    const target = event.target;

    const editAdButton = target.closest(".open-edit-modal");
    if (editAdButton) {
      const adId = editAdButton.dataset.adId;
      const contentDiv = editAdModal.querySelector("#edit-ad-modal-content");
      openEditModal(
        editAdModal,
        contentDiv,
        `/admin/util/get_ad_form.php?ad_id=${adId}`
      );
    }

    const editCategoryButton = target.closest(".open-edit-category-modal");
    if (editCategoryButton) {
      const categoryId = editCategoryButton.dataset.categoryId;
      const contentDiv = editCategoryModal.querySelector(
        "#edit-category-modal-content"
      );
      openEditModal(
        editCategoryModal,
        contentDiv,
        `/admin/util/get_category_form.php?category_id=${categoryId}`
      );
    }

    const editBlogCatButton = target.closest(".open-edit-blog-cat-modal");
    if (editBlogCatButton) {
      const blogCategoryId = editBlogCatButton.dataset.blogCategoryId;
      const contentDiv = editBlogCatModal.querySelector(
        "#edit-blog-cat-modal-content"
      );
      openEditModal(
        editBlogCatModal,
        contentDiv,
        `/admin/util/get_blog_cat_form.php?blog_category_id=${blogCategoryId}`
      );
    }

    const editBlogButton = target.closest(".open-edit-blog-modal");
    if (editBlogButton) {
      const blogId = editBlogButton.dataset.blogId;
      const contentDiv = document.getElementById("edit-blog-modal-content");
      openEditModal(
        document.getElementById("edit-blog-modal"),
        contentDiv,
        `/admin/util/get_blog_form.php?blog_id=${blogId}`
      );
    }

    const deleteAdButton = target.closest(".open-delete-modal");
    if (deleteAdButton) {
      openDeleteConfirmationModal(deleteAdButton.dataset.adId, "ad");
    }

    const deleteCategoryButton = target.closest(".open-delete-category-modal");
    if (deleteCategoryButton) {
      openDeleteConfirmationModal(
        deleteCategoryButton.dataset.categoryId,
        "category"
      );
    }

    const deleteBlogCatButton = target.closest(".open-delete-blog-cat-modal");
    if (deleteBlogCatButton) {
      openDeleteConfirmationModal(
        deleteBlogCatButton.dataset.blogCategoryId,
        "blog-cat"
      );
    }

    const deleteBlogButton = target.closest(".open-delete-blog-modal");
    if (deleteBlogButton) {
      openDeleteConfirmationModal(deleteBlogButton.dataset.blogId, "blog");
    }

    const deleteUserButton = target.closest(".open-delete-user-modal");
    if (deleteUserButton) {
      openDeleteConfirmationModal(deleteUserButton.dataset.userId, "user");
    }

    const deleteSubscriberButton = target.closest(
      ".open-delete-subscriber-modal"
    );
    if (deleteSubscriberButton) {
      openDeleteConfirmationModal(
        deleteSubscriberButton.dataset.subscriberId,
        "subscriber"
      );
    }

    const toggleUserStatusButton = target.closest(".toggle-user-status-btn");
    if (toggleUserStatusButton) {
      toggleUserStatus(toggleUserStatusButton.dataset.userId);
    }

    const dashboardRedirectButton = target.closest(".dashboard-redirect");
    if (dashboardRedirectButton) {
      const page = dashboardRedirectButton.dataset.page;
      if (page) {
        loadPage(page);
      }
    }
  });

  document.body.addEventListener("click", function (event) {
    const target = event.target;

    const closeButton = target.closest(".close-edit-modal");
    if (closeButton) {
      const modal = closeButton.closest(".fixed.inset-0");
      if (modal) {
        closeModal(modal);
      }
    }

    const cancelButton = target.closest("#cancel-delete-btn");
    if (cancelButton) {
      closeModal(deleteAdModal);
    }
  });

  document.body.addEventListener("submit", function (event) {
    if (event.target.matches("#edit-ad-form")) {
      event.preventDefault();
      submitForm(event.target, "/admin/util/edit-ad.php", editAdModal);
    }
    if (event.target.matches("#edit-category-form")) {
      event.preventDefault();
      submitForm(
        event.target,
        "/admin/util/update_category.php",
        editCategoryModal
      );
    }
    if (event.target.matches("#edit-blog-form")) {
      event.preventDefault();
      submitForm(event.target, "/admin/util/update_blog.php", editBlogModal);
    }
    if (event.target.matches("#edit-blog-cat-form")) {
      event.preventDefault();
      submitForm(
        event.target,
        "/admin/util/update_blog_category.php",
        editBlogCatModal
      );
    }
  });

  function getCurrentPage() {
    const path = window.location.pathname;
    const page = path.split("/admin/")[1] || "dashboard";
    return page === "" ? "dashboard" : page;
  }
  const initialPage = getCurrentPage();
  updateActiveLink(initialPage);
});

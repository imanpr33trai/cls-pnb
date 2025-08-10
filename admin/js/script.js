document.addEventListener("DOMContentLoaded", function () {
  const sidebar = document.getElementById("sidebar");
  const openSidebarBtn = document.getElementById("open-sidebar");
  const closeSidebarBtn = document.getElementById("close-sidebar");
  const contentArea = document.getElementById("content-area");
  const links = document.querySelectorAll(".tab-link");
  const menuToggles = document.querySelectorAll(".menu-toggle");
  const activeClass = "bg-gray-700";
  const mainContent = document.getElementById("main-content");

  // Edit Modal
  const editAdModal = document.getElementById("edit-ad-modal");
  const editAdModalContent = document.getElementById("edit-ad-modal-content");
  const closeEditModalBtn = document.getElementById("close-edit-modal");

  // Delete Modal
  const deleteAdModal = document.getElementById("delete-ad-modal");
  const confirmDeleteBtn = document.getElementById("confirm-delete-btn");
  const cancelDeleteBtn = document.getElementById("cancel-delete-btn");
  let adToDeleteId = null;

  function openSidebar() {
    sidebar.classList.remove("-translate-x-full");
  }
  function closeSidebar() {
    sidebar.classList.add("-translate-x-full");
  }

  if (openSidebarBtn) openSidebarBtn.addEventListener("click", openSidebar);
  if (closeSidebarBtn) closeSidebarBtn.addEventListener("click", closeSidebar);

  // Handle menu toggle clicks
  menuToggles.forEach((toggle) => {
    toggle.addEventListener("click", () => {
      const submenu = toggle.nextElementSibling;
      const icon = toggle.querySelector("svg");

      document.querySelectorAll(".submenu.open").forEach((openSubmenu) => {
        if (openSubmenu !== submenu) {
          openSubmenu.classList.remove("open");
          openSubmenu.previousElementSibling
            .querySelector("svg")
            .classList.remove("rotate-180");
        }
      });

      submenu.classList.toggle("open");
      icon.classList.toggle("rotate-180");
    });
  });

  function loadPage(page, pushState = true, url = null) {
    contentArea.innerHTML =
      '<div class="flex justify-center items-center h-32"><div class="animate-spin rounded-full h-8 w-8 border-b-2 border-gray-900"></div></div>';

    fetch(`/admin?page=${page}`, {
      method: "GET",
      headers: {
        "X-Requested-With": "XMLHttpRequest",
      },
    })
      .then((response) => {
        if (!response.ok) {
          throw new Error(
            response.status === 404
              ? "Page not found"
              : "Network response was not ok"
          );
        }
        return response.text();
      })
      .then((data) => {
        contentArea.innerHTML = data;
        if (pushState) {
          const newUrl =
            url ||
            (page === "dashboard" || page === "" ? "/admin" : `/admin/${page}`);
          history.pushState({ page: page, url: newUrl }, "", newUrl);
        }
        updateActiveLink(page);
        attachEventListeners();
      })
      .catch((error) => {
        contentArea.innerHTML = `
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <strong class="font-bold">Error!</strong>
                    <span class="block sm:inline"> ${error.message}</span>
                </div>`;
        console.error("Error loading page:", error);
      });
  }

  function updateActiveLink(page) {
    links.forEach((l) => l.classList.remove(activeClass));
    const activeLink = document.querySelector(`.tab-link[data-page="${page}"]`);
    if (activeLink) {
      activeLink.classList.add(activeClass);
      const parentSubmenu = activeLink.closest(".submenu");
      if (parentSubmenu && !parentSubmenu.classList.contains("open")) {
        parentSubmenu.previousElementSibling.click();
      }
    }
  }

  function attachEventListeners() {
    document.querySelectorAll(".open-edit-modal").forEach((button) => {
      button.addEventListener("click", function () {
        openEditModal(this.getAttribute("data-ad-id"));
      });
    });
    document.querySelectorAll(".open-delete-modal").forEach((button) => {
      button.addEventListener("click", function () {
        openDeleteModal(this.getAttribute("data-ad-id"));
      });
    });
  }

  // Modal Functions
  function openModal(modal) {
    modal.classList.remove("hidden");
    mainContent.classList.add("blur");
    document.body.classList.add("modal-open");
  }

  function closeModal(modal) {
    modal.classList.add("hidden");
    mainContent.classList.remove("blur");
    document.body.classList.remove("modal-open");
  }

  // Edit Modal Functions
  function openEditModal(adId) {
    editAdModalContent.innerHTML = "Loading...";
    openModal(editAdModal);
    fetch(`/admin/util/get_ad_form.php?ad_id=${adId}`)
      .then((res) => res.text())
      .then((data) => {
        editAdModalContent.innerHTML = data;
        attachFormSubmission();
      })
      .catch((error) => console.error("Error:", error));
  }

  function closeEditModal() {
    closeModal(editAdModal);
  }
  if (closeEditModalBtn)
    closeEditModalBtn.addEventListener("click", closeEditModal);

  // Delete Modal Functions
  function openDeleteModal(adId) {
    adToDeleteId = adId;
    openModal(deleteAdModal);
  }

  function closeDeleteModal() {
    adToDeleteId = null;
    closeModal(deleteAdModal);
  }

  if (cancelDeleteBtn)
    cancelDeleteBtn.addEventListener("click", closeDeleteModal);
  if (confirmDeleteBtn) {
    confirmDeleteBtn.addEventListener("click", () => {
      if (adToDeleteId) {
        fetch("/admin/util/delete_ad.php", {
          method: "POST",
          headers: { "Content-Type": "application/x-www-form-urlencoded" },
          body: `ad_id=${adToDeleteId}`,
        })
          .then((res) => res.json())
          .then((data) => {
            if (data.success) {
              closeDeleteModal();
              loadPage(getCurrentPage()); // Refresh
            } else {
              alert("Error: " + data.message);
            }
          })
          .catch((error) => console.error("Error:", error));
      }
    });
  }

  // Click outside to close
  window.addEventListener("click", function (event) {
    if (event.target == editAdModal) {
      closeEditModal();
    }
    if (event.target == deleteAdModal) {
      closeDeleteModal();
    }
  });

  function attachFormSubmission() {
    const editForm = document.getElementById("edit-ad-form");
    if (editForm) {
      editForm.addEventListener("submit", function (e) {
        e.preventDefault();
        const formData = new FormData(this);
        fetch("/admin/util/edit-ad.php", {
          method: "POST",
          body: formData,
        })
          .then((res) => res.json())
          .then((data) => {
            if (data.success) {
              closeEditModal();
              loadPage(getCurrentPage()); // Refresh
            } else {
              alert("Error: " + data.message);
            }
          })
          .catch((error) => console.error("Error:", error));
      });
    }
  }

  function getCurrentPage() {
    const path = window.location.pathname;
    let page = path.startsWith("/admin/") ? path.substring(7) : "dashboard";
    return page === "" ? "dashboard" : page;
  }

  // Link click handlers
  links.forEach((link) => {
    link.addEventListener("click", function (e) {
      e.preventDefault();
      const page = this.getAttribute("data-page");
      if (page) loadPage(page);
      if (window.innerWidth < 768) closeSidebar();
    });
  });

  // History handling
  window.addEventListener("popstate", function (e) {
    if (e.state && e.state.page) {
      loadPage(e.state.page, false, e.state.url);
    } else {
      initialPageLoad();
    }
  });

  function initialPageLoad() {
    const path = window.location.pathname;
    let page = path.startsWith("/admin/") ? path.substring(7) : "dashboard";
    if (page === "" || page === "index.php") page = "dashboard";

    const validPages = Array.from(links).map((l) =>
      l.getAttribute("data-page")
    );
    if (validPages.includes(page)) {
      loadPage(page, false, path);
    } else {
      loadPage("dashboard", false, "/admin/dashboard");
    }
  }

  initialPageLoad();
});

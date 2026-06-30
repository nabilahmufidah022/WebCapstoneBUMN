const sidebar = document.querySelector(".sidebar");
const submenuItems = document.querySelectorAll(".submenu_item");
const sidebarOpen = document.querySelector("#sidebarOpen");
const sidebarClose = document.querySelector(".collapse_sidebar");
const sidebarExpand = document.querySelector(".expand_sidebar");

// =========================
// Sidebar
// =========================

if (sidebarOpen) {
  sidebarOpen.addEventListener("click", () => {
    sidebar.classList.toggle("close");
  });
}

if (sidebarClose) {
  sidebarClose.addEventListener("click", () => {
    sidebar.classList.add("close", "hoverable");
  });
}

if (sidebarExpand) {
  sidebarExpand.addEventListener("click", () => {
    sidebar.classList.remove("close", "hoverable");
  });
}

if (sidebar) {
  sidebar.addEventListener("mouseenter", () => {
    if (sidebar.classList.contains("hoverable")) {
      sidebar.classList.remove("close");
    }
  });

  sidebar.addEventListener("mouseleave", () => {
    if (sidebar.classList.contains("hoverable")) {
      sidebar.classList.add("close");
    }
  });
}

// =========================
// Submenu Partnership
// =========================

submenuItems.forEach((item, index) => {
  item.addEventListener("click", () => {
    item.classList.toggle("show_submenu");

    submenuItems.forEach((item2, index2) => {
      if (index !== index2) {
        item2.classList.remove("show_submenu");
      }
    });
  });
});

// =========================
// Responsive Sidebar
// =========================

function adjustSidebarOnResize() {
  if (!sidebar) return;

  if (window.innerWidth < 768) {
    sidebar.classList.add("close");
  } else {
    sidebar.classList.remove("close");
  }
}

adjustSidebarOnResize();

window.addEventListener("resize", adjustSidebarOnResize);

// =========================
// Add Account Modal
// =========================

document.addEventListener("DOMContentLoaded", function () {
  const addAccountBtn = document.getElementById("addAccountBtn");
  const addAccountModalElement = document.getElementById("addAccountModal");

  if (addAccountModalElement) {
    const addAccountModal = new bootstrap.Modal(addAccountModalElement);
    const saveAccountBtn = document.getElementById("saveAccountBtn");
    const addAccountForm = document.getElementById("addAccountForm");

    if (addAccountBtn) {
      addAccountBtn.addEventListener("click", function () {
        addAccountModal.show();
      });
    }

    if (saveAccountBtn && addAccountForm) {
      saveAccountBtn.addEventListener("click", function () {
        const formData = new FormData(addAccountForm);

        fetch("/account", {
          method: "POST",
          body: formData,
          headers: {
            "X-Requested-With": "XMLHttpRequest",
            Accept: "application/json",
          },
        })
          .then((response) => response.json())
          .then((data) => {
            if (data.success) {
              addAccountModal.hide();
              addAccountForm.reset();
              location.reload();
            } else {
              alert("Error: " + (data.message || "Failed to create account"));
            }
          })
          .catch((error) => {
            console.error(error);
            alert("An error occurred while creating the account");
          });
      });
    }
  }
});

// =========================
// Edit Account Modal
// =========================

document.addEventListener("DOMContentLoaded", function () {
  const editButtons = document.querySelectorAll(".edit-btn");
  const editAccountModalElement = document.getElementById("editAccountModal");

  if (editAccountModalElement) {
    const editAccountModal = new bootstrap.Modal(editAccountModalElement);
    const updateAccountBtn = document.getElementById("updateAccountBtn");
    const editAccountForm = document.getElementById("editAccountForm");

    editButtons.forEach((button) => {
      button.addEventListener("click", function () {
        document.getElementById("edit_user_id").value =
          this.getAttribute("data-id");
        document.getElementById("edit_name").value =
          this.getAttribute("data-name");
        document.getElementById("edit_email").value =
          this.getAttribute("data-email");
        document.getElementById("edit_usertype").value =
          this.getAttribute("data-usertype");

        editAccountModal.show();
      });
    });

    if (updateAccountBtn && editAccountForm) {
      updateAccountBtn.addEventListener("click", function () {
        const userId = document.getElementById("edit_user_id").value;
        const formData = new FormData(editAccountForm);

        fetch(`/account/${userId}`, {
          method: "POST",
          body: formData,
          headers: {
            "X-Requested-With": "XMLHttpRequest",
            Accept: "application/json",
            "X-HTTP-Method-Override": "PUT",
          },
        })
          .then((response) => response.json())
          .then((data) => {
            if (data.success) {
              editAccountModal.hide();
              editAccountForm.reset();
              location.reload();
            } else {
              alert("Error: " + (data.message || "Failed to update account"));
            }
          })
          .catch((error) => {
            console.error(error);
            alert("An error occurred while updating the account");
          });
      });
    }
  }
});

// =========================
// Delete Account
// =========================

document.addEventListener("DOMContentLoaded", function () {
  const deleteButtons = document.querySelectorAll(".delete-btn");

  deleteButtons.forEach((button) => {
    button.addEventListener("click", function () {
      const userId = this.getAttribute("data-id");
      const isActive = this.getAttribute("data-active") === "1";

      const confirmMessage = isActive
        ? "Are you sure you want to deactivate this account?"
        : "Are you sure you want to activate this account?";

      if (confirm(confirmMessage)) {
        fetch(`/account/${userId}`, {
          method: "DELETE",
          headers: {
            "X-Requested-With": "XMLHttpRequest",
            Accept: "application/json",
            "X-CSRF-TOKEN": document
              .querySelector('meta[name="csrf-token"]')
              .getAttribute("content"),
          },
        })
          .then((response) => response.json())
          .then((data) => {
            if (data.success) {
              location.reload();
            } else {
              alert("Error: " + (data.message || "Failed to update account"));
            }
          })
          .catch((error) => {
            console.error(error);
            alert("An error occurred while updating the account");
          });
      }
    });
  });
});
// =========================
// Add Participation Modal
// =========================

document.addEventListener("DOMContentLoaded", function () {
  const addParticipationBtn = document.getElementById("addParticipationBtn");
  const addParticipationModalElement = document.getElementById("addParticipationModal");

  if (!addParticipationModalElement) return;

  const addParticipationModal = new bootstrap.Modal(addParticipationModalElement);

  const saveParticipationBtn = document.getElementById("saveParticipationBtn");
  const addParticipationForm = document.getElementById("addParticipationForm");

  if (addParticipationBtn) {
    addParticipationBtn.addEventListener("click", function () {
      addParticipationModal.show();
    });
  }

  if (saveParticipationBtn && addParticipationForm) {
    saveParticipationBtn.addEventListener("click", function () {

      const formData = new FormData(addParticipationForm);

      fetch("/mitra/participation", {
        method: "POST",
        body: formData,
        headers: {
          "X-Requested-With": "XMLHttpRequest",
          "Accept": "application/json"
        }
      })
      .then(response => response.json())
      .then(data => {

        if (data.success) {
          addParticipationModal.hide();
          addParticipationForm.reset();
          location.reload();
        } else {
          alert("Error: " + (data.message || "Failed to create participation"));
        }

      })
      .catch(error => {
        console.error(error);
        alert("An error occurred while creating the participation");
      });

    });
  }
});

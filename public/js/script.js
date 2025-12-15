const body = document.querySelector("body");
const darkLight = document.querySelector("#darkLight");
const sidebar = document.querySelector(".sidebar");
const submenuItems = document.querySelectorAll(".submenu_item");
const sidebarOpen = document.querySelector("#sidebarOpen");
const sidebarClose = document.querySelector(".collapse_sidebar");
const sidebarExpand = document.querySelector(".expand_sidebar");
sidebarOpen.addEventListener("click", () => sidebar.classList.toggle("close"));

sidebarClose.addEventListener("click", () => {
  sidebar.classList.add("close", "hoverable");
});
sidebarExpand.addEventListener("click", () => {
  sidebar.classList.remove("close", "hoverable");
});

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

darkLight.addEventListener("click", () => {
  body.classList.toggle("dark");
  if (body.classList.contains("dark")) {
    darkLight.classList.replace("bx-sun", "bx-moon");
  } else {
    darkLight.classList.replace("bx-moon", "bx-sun");
  }
});

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

function adjustSidebarOnResize() {
  if (window.innerWidth < 768) {
    sidebar.classList.add("close");
  } else {
    sidebar.classList.remove("close");
  }
}

adjustSidebarOnResize(); // Initial check

window.addEventListener('resize', adjustSidebarOnResize);

// Add Account Modal Functionality
document.addEventListener('DOMContentLoaded', function() {
  const addAccountBtn = document.getElementById('addAccountBtn');
  const addAccountModal = new bootstrap.Modal(document.getElementById('addAccountModal'));
  const saveAccountBtn = document.getElementById('saveAccountBtn');
  const addAccountForm = document.getElementById('addAccountForm');

  if (addAccountBtn) {
    addAccountBtn.addEventListener('click', function() {
      addAccountModal.show();
    });
  }

  if (saveAccountBtn && addAccountForm) {
    saveAccountBtn.addEventListener('click', function() {
      const formData = new FormData(addAccountForm);

      fetch('/account', {
        method: 'POST',
        body: formData,
        headers: {
          'X-Requested-With': 'XMLHttpRequest',
          'Accept': 'application/json'
        }
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          addAccountModal.hide();
          addAccountForm.reset();
          // Reload the page to show the new account in the table
          location.reload();
        } else {
          alert('Error: ' + (data.message || 'Failed to create account'));
        }
      })
      .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while creating the account');
      });
    });
  }
});

// Edit Account Modal Functionality
document.addEventListener('DOMContentLoaded', function() {
  const editButtons = document.querySelectorAll('.edit-btn');
  const editAccountModal = new bootstrap.Modal(document.getElementById('editAccountModal'));
  const updateAccountBtn = document.getElementById('updateAccountBtn');
  const editAccountForm = document.getElementById('editAccountForm');

  editButtons.forEach(button => {
    button.addEventListener('click', function() {
      const userId = this.getAttribute('data-id');
      const userName = this.getAttribute('data-name');
      const userEmail = this.getAttribute('data-email');
      const userType = this.getAttribute('data-usertype');

      // Populate the edit form
      document.getElementById('edit_user_id').value = userId;
      document.getElementById('edit_name').value = userName;
      document.getElementById('edit_email').value = userEmail;
      document.getElementById('edit_usertype').value = userType;

      editAccountModal.show();
    });
  });

  if (updateAccountBtn && editAccountForm) {
    updateAccountBtn.addEventListener('click', function() {
      const userId = document.getElementById('edit_user_id').value;
      const formData = new FormData(editAccountForm);

      fetch(`/account/${userId}`, {
        method: 'POST',
        body: formData,
        headers: {
          'X-Requested-With': 'XMLHttpRequest',
          'Accept': 'application/json',
          'X-HTTP-Method-Override': 'PUT'
        }
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          editAccountModal.hide();
          editAccountForm.reset();
          // Reload the page to show the updated account in the table
          location.reload();
        } else {
          alert('Error: ' + (data.message || 'Failed to update account'));
        }
      })
      .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while updating the account');
      });
    });
  }
});

// Delete Account Functionality
document.addEventListener('DOMContentLoaded', function() {
  const deleteButtons = document.querySelectorAll('.delete-btn');

  deleteButtons.forEach(button => {
    button.addEventListener('click', function() {
      const userId = this.getAttribute('data-id');
      const isActive = this.getAttribute('data-active') === '1';

      const confirmMessage = isActive
        ? 'Are you sure you want to deactivate this account?'
        : 'Are you sure you want to activate this account?';

      if (confirm(confirmMessage)) {
        fetch(`/account/${userId}`, {
          method: 'DELETE',
          headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
          }
        })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            // Reload the page to show the updated table
            location.reload();
          } else {
            alert('Error: ' + (data.message || 'Failed to update account'));
          }
        })
        .catch(error => {
          console.error('Error:', error);
          alert('An error occurred while updating the account');
        });
      }
    });
  });
});

// Add Participation Modal Functionality
document.addEventListener('DOMContentLoaded', function() {
  const addParticipationBtn = document.getElementById('addParticipationBtn');
  const addParticipationModal = new bootstrap.Modal(document.getElementById('addParticipationModal'));
  const saveParticipationBtn = document.getElementById('saveParticipationBtn');
  const addParticipationForm = document.getElementById('addParticipationForm');

  if (addParticipationBtn) {
    addParticipationBtn.addEventListener('click', function() {
      addParticipationModal.show();
    });
  }

  if (saveParticipationBtn && addParticipationForm) {
    saveParticipationBtn.addEventListener('click', function() {
      const formData = new FormData(addParticipationForm);

      fetch('/mitra/participation', {
        method: 'POST',
        body: formData,
        headers: {
          'X-Requested-With': 'XMLHttpRequest',
          'Accept': 'application/json'
        }
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          addParticipationModal.hide();
          addParticipationForm.reset();
          // Reload the page to show the new participation in the table
          location.reload();
        } else {
          alert('Error: ' + (data.message || 'Failed to create participation'));
        }
      })
      .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while creating the participation');
      });
    });
  }
});

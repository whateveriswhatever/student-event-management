document.addEventListener("DOMContentLoaded", () => {
  const openBtn = document.getElementById("open-friends-modal");
  const closeBtn = document.getElementById("close-friends-modal");
  const overlay = document.getElementById("friends-modal-overlay");

  if (openBtn && overlay) {
    // Toggles display view open on grid element card clicks
    openBtn.addEventListener("click", () => {
      console.log(`Clicked to see friends list!`);
      overlay.classList.add("active");
      document.body.style.overflow = "hidden"; // Freeze background window scrolls
    });

    // Hide overlay upon clicking the X mark icon close button
    if (closeBtn) {
      closeBtn.addEventListener("click", () => {
        overlay.classList.remove("active");
        document.body.style.overflow = ""; // Re-enable viewport scrolling
      });
    }

    // Hide overlay upon user clicks onto external blurry layout boundaries
    overlay.addEventListener("click", (e) => {
      if (e.target === overlay) {
        overlay.classList.remove("active");
        document.body.style.overflow = "";
      }
    });
  }
});

// document.addEventListener("DOMContentLoaded", () => {
//   const openBtn = document.getElementById("open-friends-modal");
//   const closeBtn = document.getElementById("close-friends-modal");
//   const overlay = document.getElementById("friends-modal-overlay");
//   const filterInput = document.getElementById("friend-filter-input");
//   const friendRows = document.querySelectorAll(".friend-row");
//   const emptyMsg = document.getElementById("friend-filter-empty");

//   function closeModal() {
//     overlay.classList.remove("active");
//     document.body.style.overflow = "";

//     // Reset filter
//     if (filterInput) {
//       filterInput.value = "";
//       filterFriends(""); // Show all again
//     }
//   }

//   function filterFriends(searchTerm) {
//     searchTerm = searchTerm.toLowerCase().trim();
//     let hasVisible = false;

//     friendRows.forEach((row) => {
//       const name = row.getAttribute("data-name") || "";
//       if (name.includes(searchTerm)) {
//         row.style.display = "";
//         hasVisible = true;
//       } else {
//         row.style.display = "none";
//       }
//     });

//     if (emptyMsg) {
//       emptyMsg.style.display = hasVisible ? "none" : "block";
//     }
//   }

//   if (openBtn && overlay) {
//     openBtn.addEventListener("click", () => {
//       console.log("Opening friends modal");
//       overlay.classList.add("active");
//       document.body.style.overflow = "hidden";

//       if (filterInput) {
//         filterInput.focus();
//         filterInput.dispatchEvent(new Event("input")); // Trigger initial filter
//       }
//     });

//     if (closeBtn) {
//       closeBtn.addEventListener("click", closeModal);
//     }

//     // Close on backdrop click
//     overlay.addEventListener("click", (e) => {
//       if (e.target === overlay) closeModal();
//     });

//     // Search filter
//     if (filterInput) {
//       filterInput.addEventListener("input", (e) => {
//         filterFriends(e.target.value);
//       });
//     }
//   }
// });

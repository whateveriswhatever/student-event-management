document.addEventListener("DOMContentLoaded", () => {
  const folderPath = "/final-project/infrastructure";
  const modal = document.getElementById("members-modal");
  const modalClubName = document.getElementById("modal-club-name");
  const modalMembersList = document.getElementById("modal-members-list");
  const closeModalBtn = document.getElementById("close-modal-btn");
  const memberBadges = document.querySelectorAll(".member-count-badge");

  // Triggered when clicking on any club's member span tag
  memberBadges.forEach((badge) => {
    badge.addEventListener("click", () => {
      const clubID = badge.getAttribute("data-club-id");
      const clubName = badge.getAttribute("data-club-name");

      modalClubName.textContent = `Members of ${clubName}`;
      modalMembersList.innerHTML = `<p style="text-align: center; color: #64748b;">Loading members...</p>`;

      // Open the overlay structure and apply animation
      modal.style.display = "flex";
      setTimeout(() => modal.classList.add("show"), 10);

      // Dynamically fetch from our infrastructure backend route
      fetch(`${folderPath}/memberships/all-members?club_ID=${clubID}`)
        .then((response) => response.json())
        .then((data) => {
          if (data.error) {
            modalMembersList.innerHTML = `<p style="color:#ef4444;">${escapeHtml(data.error)}</p>`;
            return;
          }

          if (!data["members"] || data["members"].length === 0) {
            modalMembersList.innerHTML =
              '<p style="color:#64748b;">No approved members found.</p>';
            return;
          }

          // Rendering dynamic member list
          let htmlContent =
            "<div style='display:flex; flex-direction:column;'>";
          data["members"].forEach((m) => {
            htmlContent += `
                            <div class="member-item">
                                <span><strong>${escapeHtml(m.firstname)} ${escapeHtml(m.lastname)}</strong></span>
                                <span class="badge" style="background:#f1f5f9; color:#475569; font-size:0.8rem;">${escapeHtml(m.role)}</span>
                            </div>`;
          });
          htmlContent += "</div>";
          modalMembersList.innerHTML = htmlContent;
        })
        .catch(() => {
          modalMembersList.innerHTML =
            '<p style="color:#ef4444;">Failed to communicate with Server.</p>';
        });
    });
  });

  // Closing handlers
  const hideModal = () => {
    modal.classList.remove("show");
    setTimeout(() => {
      modal.style.display = "none";
    }, 250);
  };

  closeModalBtn.addEventListener("click", hideModal);
  modal.addEventListener("click", (e) => {
    if (e.target === modal) {
      hideModal();
    }
  });

  function escapeHtml(text) {
    return String(text).replace(
      /[&<>""']/g,
      (m) =>
        ({
          "&": "&amp;",
          "<": "&lt;",
          ">": "&gt;",
          '"': "&quot;",
          "'": "&#39;",
        })[m],
    );
  }
});

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
      const isExecutive = badge.getAttribute("data-is-exec") === "true";

      modalClubName.textContent = `Members of ${clubName}`;
      modalMembersList.innerHTML = `<p style="text-align: center; color: #64748b;">Loading members...</p>`;

      // Open the overlay structure and apply animation
      modal.style.display = "flex";
      setTimeout(() => modal.classList.add("show"), 10);
      console.log(`Fetching the API...`);
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
          // console.log(`Data: ${data["members"]}`);
          data["members"].forEach((m) => {
            // console.log(`Processing member: ${m}`);
            const roleTitle = escapeHtml(m.role).toLowerCase();
            // console.log(`Role: ${roleTitle}`);
            const studentID = m["studentID"];
            // console.log(`Student ID: ${studentID}`);
            htmlContent += `
                            <div class="member-item" style="display: flex; justify-content: space-between; align-items: center; border: 1px solid #e2e8f0; padding: 12px; border-radius: 8px; background: #f8fafc;">
                              <div style="display: flex; flex-direction: column; justify-content: center; align-items: center;">
                                <span><strong>${escapeHtml(m.firstname)} ${escapeHtml(m.lastname)}</strong></span>
                                <span class="badge" style="background:#f1f5f9; color:#475569; font-size:0.8rem;">${escapeHtml(m.role)}</span>
                              </div>
                            `;
            if (
              isExecutive &&
              roleTitle !== "president" &&
              roleTitle !== "vice president"
            ) {
              htmlContent += `
              <form action="${folderPath}/clubs/member-kick" method="POST" style="margin: 0;" onsubmit="return confirm('Are you sure you want to kick ${escapeHtml(m.firstname)} ${escapeHtml(m.lastname)}? This action cannot be undone.');">
                <input type="hidden" name="club_ID" value="${escapeHtml(clubID)}">
                <input type="hidden" name="student_ID" value="${studentID}">
                <button type="submit" style="background: #ef4444; color: white; border: none; border-radius: 50%; width: 28px; height: 28px; display: flex; align-items: center; justify-content: center; cursor: pointer; font-weight: bold; font-size: 1.2rem; transition: background 0.2s;" title="Kick Member" onmouseover="this.style.backgroundColor='#dc2626'" onmouseout="this.style.backgroundColor='#ef4444'">&minus;</button>
              </form>
            `;
            }
            htmlContent += `</div>`;
            // console.log("Done!");
          });

          htmlContent += "</div>";
          modalMembersList.innerHTML = htmlContent;
        })
        .catch((error) => {
          console.error(`JavaScript Error Details: ${error}`);
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

  /* Create Event Modal Logic  */
  const createEventBtn = document.getElementById("open-create-event-btn");
  const createEventModal = document.getElementById("create-event-modal");
  const closeEventModalBtn = document.getElementById("close-event-modal-btn");
  console.log("Searching for buttons...");
  if (createEventBtn && createEventModal && closeEventModalBtn) {
    console.log("Found all buttons!");
    // Opening the modal
    createEventBtn.addEventListener("click", () => {
      createEventModal.style.display = "flex";
      setTimeout(() => createEventModal.classList.add("show"), 10);
    });

    // Closing the modal
    const hideEventModal = () => {
      createEventModal.classList.remove("show");
      setTimeout(() => {
        createEventModal.style.display = "none";
      }, 250);
    };

    closeEventModalBtn.addEventListener("click", hideEventModal);
    createEventModal.addEventListener("click", (e) => {
      if (e.target === createEventModal) {
        hideEventModal();
      }
    });
  }
});

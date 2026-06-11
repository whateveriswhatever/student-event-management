document.addEventListener("DOMContentLoaded", () => {
  // 1. Toggle Login / Registration Form
  const showRegisterBtn = document.getElementById("show-register");
  const showLoginBtn = document.getElementById("show-login");
  const loginSection = document.getElementById("login-section");
  const registerSection = document.getElementById("register-section");

  if (showRegisterBtn && showLoginBtn) {
    showRegisterBtn.addEventListener("click", (e) => {
      e.preventDefault();
      loginSection.style.display = "none";
      registerSection.style.display = "block";
    });

    showLoginBtn.addEventListener("click", (e) => {
      e.preventDefault();
      registerSection.style.display = "none";
      loginSection.style.display = "block";
    });
  }

  // 2. Real-time Vanilla JS Club Search Filter
  const searchInput = document.getElementById("club-search");
  const clubCards = document.querySelectorAll(".club-card");

  if (searchInput) {
    searchInput.addEventListener("input", function (e) {
      const query = e.target.value.toLowerCase().trim();

      clubCards.forEach((card) => {
        const clubName = card.getAttribute("data-club-name");
        const clubId = card.getAttribute("data-club-id");

        // Show card if query matches name OR id, else hide
        if (clubName.includes(query) || clubId.includes(query)) {
          card.style.display = "block";
        } else {
          card.style.display = "none";
        }
      });
    });
  }
});

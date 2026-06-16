document.addEventListener("DOMContentLoaded", () => {
  const searchInput = document.getElementById("club-search");
  const clubCards = document.querySelector(".club-card");

  if (searchInput) {
    searchInput.addEventListener("input", (e) => {
      const query = e.target.value.toLowerCase().trim();

      clubCards.forEach((card) => {
        const clubName = card.getAttribute("data-club-name") || "";
        const clubID = card.getAttribute("data-club-id") || "";

        if (clubName.includes(query) || clubID.includes(query)) {
          card.style.display = "flex";
        } else {
          card.style.display = "none";
        }
      });
    });
  }
});

const filterButtons = document.querySelectorAll("[data-filter]");
const eventCards = document.querySelectorAll(".event-card");
const searchInput = document.querySelector("#eventSearch");

let activeFilter = "all";

function normalize(value) {
    return value
        .toLowerCase()
        .normalize("NFD")
        .replace(/[\u0300-\u036f]/g, "");
}

function applyEventFilters() {
    const query = normalize(searchInput?.value || "");

    eventCards.forEach((card) => {
        const status = card.dataset.status;
        const haystack = normalize(card.dataset.search || "");
        const matchesStatus = activeFilter === "all" || status === activeFilter;
        const matchesSearch = !query || haystack.includes(query);

        card.classList.toggle("is-hidden", !(matchesStatus && matchesSearch));
    });
}

filterButtons.forEach((button) => {
    button.addEventListener("click", () => {
        activeFilter = button.dataset.filter || "all";
        filterButtons.forEach((item) => item.classList.remove("active"));
        button.classList.add("active");
        applyEventFilters();
    });
});

searchInput?.addEventListener("input", applyEventFilters);

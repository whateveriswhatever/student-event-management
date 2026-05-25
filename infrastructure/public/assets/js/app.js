const viewTriggers = document.querySelectorAll("[data-view-link]");
const navLinks = document.querySelectorAll(".nav-list [data-view-link]");
const viewPanels = document.querySelectorAll("[data-view-panel]");
const pageEyebrow = document.querySelector("#pageEyebrow");
const pageTitle = document.querySelector("#pageTitle");
const pageSubtitle = document.querySelector("#pageSubtitle");

const globalSearchInput = document.querySelector("#globalSearch");
const overviewFilterButtons = document.querySelectorAll("[data-overview-filter]");
const overviewCards = document.querySelectorAll("[data-overview-card]");

const eventFilterButtons = document.querySelectorAll("[data-event-filter]");
const eventRows = document.querySelectorAll("[data-event-row]");
const eventManagerSearch = document.querySelector("#eventManagerSearch");
const clubFilter = document.querySelector("#clubFilter");
const eventEmptyState = document.querySelector("#eventEmptyState");
const eventDetailPanel = document.querySelector("#eventDetailPanel");

const clubSearch = document.querySelector("#clubSearch");
const clubFilterButtons = document.querySelectorAll("[data-club-filter]");
const clubCards = document.querySelectorAll("[data-club-card]");
const clubEmptyState = document.querySelector("#clubEmptyState");
const adminProfileButton = document.querySelector("#adminProfileButton");
const adminProfilePanel = document.querySelector("#adminProfilePanel");

const statusLabels = {
    open: "Đang mở",
    pending: "Chờ duyệt",
    closed: "Đã đủ chỗ",
    void: "Tạm dừng",
    success: "Đã duyệt",
    rejected: "Từ chối",
};

let activeOverviewFilter = "all";
let activeEventFilter = "all";
let activeClubFilter = "all";

function normalize(value) {
    return value
        .toLowerCase()
        .normalize("NFD")
        .replace(/[\u0300-\u036f]/g, "");
}

function datasetText(node, key) {
    return node?.dataset?.[key] || "";
}

function setActiveView(viewName) {
    const targetPanel = document.querySelector(`[data-view-panel="${viewName}"]`);

    if (!targetPanel) {
        return;
    }

    viewPanels.forEach((panel) => {
        panel.classList.toggle("active", panel.dataset.viewPanel === viewName);
    });

    navLinks.forEach((link) => {
        link.classList.toggle("active", link.dataset.viewLink === viewName);
    });

    const metaKey = viewName;
    if (datasetText(pageEyebrow, metaKey)) {
        pageEyebrow.textContent = datasetText(pageEyebrow, metaKey);
    }

    if (datasetText(pageTitle, metaKey)) {
        pageTitle.textContent = datasetText(pageTitle, metaKey);
    }

    if (datasetText(pageSubtitle, metaKey)) {
        pageSubtitle.textContent = datasetText(pageSubtitle, metaKey);
    }

    if (window.location.hash !== `#${viewName}`) {
        window.history.replaceState(null, "", `#${viewName}`);
    }

    applyOverviewFilters();
    applyEventTableFilters();
    applyClubFilters();
}

function globalQuery() {
    return normalize(globalSearchInput?.value || "");
}

function applyOverviewFilters() {
    const query = globalQuery();

    overviewCards.forEach((card) => {
        const status = card.dataset.status;
        const haystack = normalize(card.dataset.search || "");
        const matchesStatus = activeOverviewFilter === "all" || status === activeOverviewFilter;
        const matchesSearch = !query || haystack.includes(query);

        card.classList.toggle("is-hidden", !(matchesStatus && matchesSearch));
    });
}

function applyEventTableFilters() {
    const query = normalize(eventManagerSearch?.value || "") || globalQuery();
    const selectedClub = clubFilter?.value || "all";
    let visibleCount = 0;
    let firstVisibleRow = null;

    eventRows.forEach((row) => {
        const status = row.dataset.status;
        const club = row.dataset.club;
        const haystack = normalize(row.dataset.search || "");
        const matchesStatus = activeEventFilter === "all" || status === activeEventFilter;
        const matchesClub = selectedClub === "all" || club === selectedClub;
        const matchesSearch = !query || haystack.includes(query);
        const isVisible = matchesStatus && matchesClub && matchesSearch;

        row.classList.toggle("is-hidden", !isVisible);

        if (isVisible) {
            visibleCount += 1;
            firstVisibleRow ||= row;
        }
    });

    eventEmptyState?.classList.toggle("visible", visibleCount === 0);

    const selectedRow = document.querySelector("[data-event-row].selected:not(.is-hidden)");
    if (!selectedRow && firstVisibleRow) {
        selectEventRow(firstVisibleRow);
    }
}

function applyClubFilters() {
    const query = normalize(clubSearch?.value || "") || globalQuery();
    let visibleCount = 0;

    clubCards.forEach((card) => {
        const status = card.dataset.status;
        const haystack = normalize(card.dataset.search || "");
        const matchesStatus = activeClubFilter === "all" || status === activeClubFilter;
        const matchesSearch = !query || haystack.includes(query);
        const isVisible = matchesStatus && matchesSearch;

        card.classList.toggle("is-hidden", !isVisible);
        if (isVisible) {
            visibleCount += 1;
        }
    });

    clubEmptyState?.classList.toggle("visible", visibleCount === 0);
}

function setDetailText(selector, value) {
    const node = eventDetailPanel?.querySelector(selector);

    if (node) {
        node.textContent = value;
    }
}

function closeAdminProfile() {
    adminProfilePanel?.setAttribute("hidden", "");
    adminProfileButton?.setAttribute("aria-expanded", "false");
}

function toggleAdminProfile() {
    if (!adminProfilePanel || !adminProfileButton) {
        return;
    }

    const shouldOpen = adminProfilePanel.hasAttribute("hidden");
    adminProfilePanel.toggleAttribute("hidden", !shouldOpen);
    adminProfileButton.setAttribute("aria-expanded", String(shouldOpen));
}

function selectEventRow(row) {
    eventRows.forEach((item) => item.classList.remove("selected"));
    row.classList.add("selected");

    const status = row.dataset.status || "void";
    const statusBadge = eventDetailPanel?.querySelector("[data-detail-status]");
    const image = eventDetailPanel?.querySelector("[data-detail-image]");
    const progress = eventDetailPanel?.querySelector("[data-detail-progress]");

    if (image) {
        image.src = row.dataset.image || "";
    }

    if (statusBadge) {
        statusBadge.className = `status-badge ${status}`;
        statusBadge.textContent = statusLabels[status] || "Không rõ";
    }

    if (progress) {
        progress.style.width = `${row.dataset.progress || 0}%`;
    }

    setDetailText("[data-detail-title]", row.dataset.title || "");
    setDetailText("[data-detail-description]", row.dataset.description || "");
    setDetailText("[data-detail-club]", row.dataset.club || "");
    setDetailText("[data-detail-time]", `${row.dataset.date || ""} · ${row.dataset.time || ""}`);
    setDetailText("[data-detail-location]", row.dataset.location || "");
    setDetailText("[data-detail-owner]", row.dataset.owner || "");
    setDetailText("[data-detail-registered]", row.dataset.registered || "0");
    setDetailText("[data-detail-capacity]", `/ ${row.dataset.capacity || "0"} đăng ký`);
}

viewTriggers.forEach((trigger) => {
    trigger.addEventListener("click", (event) => {
        const viewName = trigger.dataset.viewLink;

        if (!document.querySelector(`[data-view-panel="${viewName}"]`)) {
            return;
        }

        event.preventDefault();
        setActiveView(viewName);
    });
});

overviewFilterButtons.forEach((button) => {
    button.addEventListener("click", () => {
        activeOverviewFilter = button.dataset.overviewFilter || "all";
        overviewFilterButtons.forEach((item) => item.classList.remove("active"));
        button.classList.add("active");
        applyOverviewFilters();
    });
});

eventFilterButtons.forEach((button) => {
    button.addEventListener("click", () => {
        activeEventFilter = button.dataset.eventFilter || "all";
        eventFilterButtons.forEach((item) => item.classList.remove("active"));
        button.classList.add("active");
        applyEventTableFilters();
    });
});

clubFilterButtons.forEach((button) => {
    button.addEventListener("click", () => {
        activeClubFilter = button.dataset.clubFilter || "all";
        clubFilterButtons.forEach((item) => item.classList.remove("active"));
        button.classList.add("active");
        applyClubFilters();
    });
});

adminProfileButton?.addEventListener("click", (event) => {
    event.stopPropagation();
    toggleAdminProfile();
});

adminProfilePanel?.addEventListener("click", (event) => {
    event.stopPropagation();
});

document.addEventListener("click", closeAdminProfile);
document.addEventListener("keydown", (event) => {
    if (event.key === "Escape") {
        closeAdminProfile();
    }
});

eventRows.forEach((row) => {
    row.addEventListener("click", () => selectEventRow(row));
});

globalSearchInput?.addEventListener("input", () => {
    applyOverviewFilters();
    applyEventTableFilters();
    applyClubFilters();
});

eventManagerSearch?.addEventListener("input", applyEventTableFilters);
clubFilter?.addEventListener("change", applyEventTableFilters);
clubSearch?.addEventListener("input", applyClubFilters);

const initialHash = window.location.hash.replace("#", "");
const knownViews = ["overview", "events", "clubs"];
setActiveView(knownViews.includes(initialHash) ? initialHash : "overview");

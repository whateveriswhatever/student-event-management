(function () {
  // ── Helpers ────────────────────────────────────────────────────────────

  /** Escape a value for safe inclusion in a CSV cell */
  function csvCell(value) {
    if (value === null || value === undefined) return "";
    const str = String(value);
    // Wrap in quotes if the value contains a comma, quote, or newline
    if (str.includes(",") || str.includes('"') || str.includes("\n")) {
      return '"' + str.replace(/"/g, '""') + '"';
    }
    return str;
  }

  /** Convert a 2-D array (rows of cells) to a CSV string */
  function buildCSV(rows) {
    return rows.map((row) => row.map(csvCell).join(",")).join("\n");
  }

  /** Trigger a CSV file download in the browser */
  function downloadCSV(csvString, filename) {
    const blob = new Blob([csvString], { type: "text/csv;charset=utf-8;" });
    const url = URL.createObjectURL(blob);
    const link = document.createElement("a");
    link.href = url;
    link.download = filename;
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    URL.revokeObjectURL(url);
  }

  // ── CSV builder ────────────────────────────────────────────────────────

  /**
   * Build the CSV rows from the cached admin-stats JSON.
   * The global `window.__adminDashboardData` is expected to be set by
   * ClubAdminDashboard.js once the API call resolves.
   */
  function buildDashboardCSV(data, clubName) {
    const rows = [];
    const now = new Date().toLocaleString();

    // ── Section 1: Summary ──────────────────────────────────────────
    rows.push(["Club Admin Dashboard Export"]);
    rows.push(["Club", clubName]);
    rows.push(["Exported at", now]);
    rows.push([]);

    // ── Section 2: Membership Overview ─────────────────────────────
    rows.push(["=== Membership Overview ==="]);
    rows.push(["Status", "Count"]);

    const statusLabels = {
      active: "Active",
      pending: "Pending",
      rejected: "Rejected",
      banned: "Banned",
      left: "Left",
    };
    const counts = data.statusCounts || {};
    for (const [key, label] of Object.entries(statusLabels)) {
      rows.push([label, counts[key] ?? 0]);
    }
    rows.push(["Total Members (Active)", data.totalMembers ?? 0]);
    rows.push([]);

    // ── Section 3: Event Stats ──────────────────────────────────────
    rows.push(["=== Event Statistics ==="]);
    rows.push(["Total Events", data.totalEvents ?? 0]);
    rows.push([]);

    // ── Section 4: Peak Hosting Hours ──────────────────────────────
    rows.push(["=== Peak Event Hosting Hours ==="]);
    rows.push(["Hour (24h)", "Events Scheduled"]);
    const peakHours = data.peakHours || [];
    peakHours.forEach((count, hour) => {
      const label = String(hour).padStart(2, "0") + ":00";
      rows.push([label, count]);
    });

    return buildCSV(rows);
  }

  // ── Wire up the Export button ──────────────────────────────────────────

  document.addEventListener("DOMContentLoaded", function () {
    const exportBtn = document.getElementById("export-dashboard-csv-btn");
    if (!exportBtn) return;

    exportBtn.addEventListener("click", function () {
      const data = window.__adminDashboardData;
      if (!data) {
        alert(
          "Dashboard data is not loaded yet. Please wait a moment and try again.",
        );
        return;
      }

      const modal = document.getElementById("admin-stats-modal");
      const clubName = modal ? modal.dataset.clubName || "Club" : "Club";

      const csvString = buildDashboardCSV(data, clubName);
      const safeName = clubName.replace(/[^a-z0-9]/gi, "_").toLowerCase();
      const filename = "dashboard_" + safeName + "_" + Date.now() + ".csv";

      downloadCSV(csvString, filename);
    });
  });

  // ── Show/hide the Export button together with the modal ───────────────

  // Observe when the admin-stats-modal visibility changes so we can
  // show the Export button only after data has loaded.
  const adminModal = document.getElementById("admin-stats-modal");
  if (adminModal) {
    // Use a MutationObserver to react to display-style changes made by
    // ClubAdminDashboard.js (it sets style.display = 'flex' / 'none').
    const observer = new MutationObserver(function () {
      const exportBtn = document.getElementById("export-dashboard-csv-btn");
      if (!exportBtn) return;

      const isVisible = adminModal.style.display !== "none";
      if (!isVisible) {
        // Hide the button when the modal closes
        exportBtn.style.display = "none";
      }
      // The button is revealed by the data-load hook below.
    });

    observer.observe(adminModal, {
      attributes: true,
      attributeFilter: ["style"],
    });
  }

  // ── Hook into ClubAdminDashboard.js data load ─────────────────────────
  //
  // ClubAdminDashboard.js renders stats into #admin-stats-content once the
  // fetch resolves. We intercept by patching window.__setAdminDashboardData
  // (a convention) OR by watching #admin-stats-content for DOM changes.

  const statsContent = document.getElementById("admin-stats-content");
  if (statsContent) {
    const contentObserver = new MutationObserver(function () {
      const exportBtn = document.getElementById("export-dashboard-csv-btn");
      if (!exportBtn) return;

      // Show the button only when real data (not the loading placeholder)
      // has been rendered AND the cached data object is available.
      const hasData =
        window.__adminDashboardData &&
        !statsContent.textContent.includes("Loading stats");
      exportBtn.style.display = hasData ? "inline-flex" : "none";
    });

    contentObserver.observe(statsContent, { childList: true, subtree: true });
  }
})();

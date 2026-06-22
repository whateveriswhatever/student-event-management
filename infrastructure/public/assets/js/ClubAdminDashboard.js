(function () {
  //   console.log(`Hi`);
  const openBtn = document.getElementById("open-admin-dashboard-btn");
  const closeBtn = document.getElementById("close-admin-modal-btn");
  const modal = document.getElementById("admin-stats-modal");
  const content = document.getElementById("admin-stats-content");

  //   console.log(openBtn);
  //   console.log(closeBtn);
  // console.log(modal);
  // console.log(content);

  if (!openBtn) {
    console.log(`Don't see admin dashboard button!`);
    return;
  }

  // Read PHP-injected values from data attributes instead of inline JS
  const clubID = modal.dataset.clubId;
  const basePath = modal.dataset.basePath;

  openBtn.addEventListener("click", () => {
    console.log(`Clicked on to open the admin dashboard`);
    modal.classList.add("active");
    loadStats();
  });

  closeBtn.addEventListener("click", () => modal.classList.remove("active"));
  modal.addEventListener("click", (e) => {
    if (e.target === modal) modal.classList.remove("active");
  });

  function loadStats() {
    content.innerHTML =
      '<p style="color:#64748b;text-align:center;">Loading...</p>';

    fetch(`${basePath}/clubs/admin-stats?id=${clubID}`)
      .then((r) => r.text())
      .then((raw) => {
        console.log(`Raw response: ${raw}`);
        return JSON.parse(raw);
      })
      .then((data) => {
        if (data.error) {
          console.log(`Some error occured! -> ${data.error}`);
          content.innerHTML = `<p style="color:red;">${data.error}</p>`;
          return;
        }
        console.log(`No error => loading statistics...`);
        window.__adminDashboardData = data;
        renderStats(data);
      })
      .catch((e) => {
        console.log(`Error: ${e}`);
        content.innerHTML = '<p style="color:red;">Failed to load stats.</p>';
      });
  }

  function renderStats(data) {
    console.log(`Rendering the statistics...`);
    const { totalMembers, statusCounts, peakHours, totalEvents } = data;

    const total = Object.values(statusCounts).reduce((a, b) => a + b, 0);
    const pct = (k) =>
      total > 0 ? Math.round((statusCounts[k] / total) * 100) : 0;

    const peakHour = peakHours.indexOf(Math.max(...peakHours));
    const peakLabel =
      peakHour === 0
        ? "12 AM"
        : peakHour < 12
          ? `${peakHour} AM`
          : peakHour === 12
            ? "12 PM"
            : `${peakHour - 12} PM`;

    const chartHours = Array.from({ length: 16 }, (_, i) => i + 7);
    const maxVal = Math.max(...peakHours) || 1;
    const bars = chartHours
      .map((h) => {
        const val = peakHours[h];
        const heightPct = Math.round((val / maxVal) * 100);
        const label =
          h === 0 ? "12A" : h < 12 ? `${h}A` : h === 12 ? "12P" : `${h - 12}P`;
        return `
                <div style="display:flex;flex-direction:column;align-items:center;gap:4px;flex:1;">
                    <span style="font-size:0.7rem;color:#64748b;">${val > 0 ? val : ""}</span>
                    <div style="width:100%;background:#e2e8f0;border-radius:4px;height:80px;display:flex;align-items:flex-end;">
                        <div style="width:100%;height:${heightPct}%;background:${val === maxVal ? "#7c3aed" : "#a78bfa"};border-radius:4px;transition:height 0.3s;"></div>
                    </div>
                    <span style="font-size:0.7rem;color:#64748b;">${label}</span>
                </div>`;
      })
      .join("");

    const pill = (label, value, color, bg) =>
      `<div style="background:${bg};border-radius:10px;padding:16px 20px;display:flex;flex-direction:column;gap:4px;">
                <span style="font-size:1.75rem;font-weight:700;color:${color};">${value}</span>
                <span style="font-size:0.85rem;color:#64748b;">${label}</span>
            </div>`;

    content.innerHTML = `
            <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:12px;margin-bottom:24px;">
                ${pill("Active Members", totalMembers, "#10b981", "#f0fdf4")}
                ${pill("Total Events", totalEvents, "#3b82f6", "#eff6ff")}
                ${pill("Acceptance Rate", pct("active") + "%", "#7c3aed", "#f5f3ff")}
            </div>

            <div style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:10px;padding:16px;margin-bottom:24px;">
                <h4 style="margin:0 0 12px 0;color:#1e293b;font-size:0.95rem;">Join Request Breakdown</h4>
                <div style="display:flex;flex-direction:column;gap:8px;">
                    ${[
                      ["Accepted", "active", "#10b981", "#dcfce7"],
                      ["Pending", "pending", "#f59e0b", "#fef9c3"],
                      ["Rejected", "rejected", "#ef4444", "#fee2e2"],
                      ["Banned", "banned", "#6b7280", "#f1f5f9"],
                      ["Left", "left", "#8b5cf6", "#f5f3ff"],
                    ]
                      .map(([label, key, color]) => {
                        const p = pct(key);
                        return `
                        <div>
                            <div style="display:flex;justify-content:space-between;margin-bottom:4px;">
                                <span style="font-size:0.85rem;font-weight:500;color:#334155;">${label}</span>
                                <span style="font-size:0.85rem;color:#64748b;">${statusCounts[key]} (${p}%)</span>
                            </div>
                            <div style="background:#e2e8f0;border-radius:999px;height:8px;">
                                <div style="width:${p}%;background:${color};border-radius:999px;height:100%;transition:width 0.4s;"></div>
                            </div>
                        </div>`;
                      })
                      .join("")}
                </div>
            </div>

            <div style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:10px;padding:16px;">
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px;">
                    <h4 style="margin:0;color:#1e293b;font-size:0.95rem;">Peak Event Hosting Hours</h4>
                    <span style="font-size:0.8rem;background:#ede9fe;color:#7c3aed;padding:4px 10px;border-radius:999px;font-weight:600;">
                        Peak: ${peakLabel}
                    </span>
                </div>
                <div style="display:flex;gap:4px;align-items:flex-end;">${bars}</div>
            </div>
        `;
  }
})();

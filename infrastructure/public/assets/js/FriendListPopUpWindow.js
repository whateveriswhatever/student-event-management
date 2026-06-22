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
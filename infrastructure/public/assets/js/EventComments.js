document.addEventListener("DOMContentLoaded", () => {
  const folderPath = "/final-project/infrastructure";

  const commentsModal = document.getElementById("comments-modal");
  const closeCommentsBtn = document.getElementById("close-comments-btn");
  const commentsList = document.getElementById("comments-list");
  const commentForm = document.getElementById("comment-form");
  const commentEventIdInput = document.getElementById("comment-event-id");
  const commentInput = document.getElementById("comment-input");
  const commentModalTitle = document.getElementById("comments-modal-title");

  // Helper function to safely render HTML
  const escapeHtml = (text) => {
    return String(text).replace(
      /[&<>"']/g,
      (m) =>
        ({
          "&": "&amp;",
          "<": "&lt;",
          ">": "&gt;",
          '"': "&quot;",
          "'": "&#39;",
        })[m],
    );
  };

  const loadComments = (eventID) => {
    commentsList.innerHTML = `<div style="text-align: center; padding: 20px; color: #64748b;">
            Loading comments...
        </div>`;

    const currUserID = document.getElementById("current-user-id")?.value || 0;
    console.log(`Current user ID: ${currUserID}`);

    fetch(`${folderPath}/events/comments?event_ID=${eventID}`)
      .then((res) => {
        return res.json();
      })
      .then((data) => {
        console.log(`Data: ${JSON.stringify(data)}`);
        if (data.error) {
          commentsList.innerHTML = `<div 
                    style="color: #ef4444; text-align: center;">${escapeHtml(data.error)}</div>`;
          return;
        }

        if (!data.comments || data.comments.length === 0) {
          commentsList.innerHTML = `<div
                    style="text-align: center; padding: 40px 20px; color: #94a3b8;">No comments yet. Be the first to start the discussion!</div>`;
          return;
        }

        commentsList.innerHTML = "";

        data.comments.forEach((c) => {
          console.log(`Current comment: ${JSON.stringify(c)}`);
          const commentSenderID = String(c["senderID"]);
          const isMe =
            commentSenderID === String(currUserID) && currUserID !== "0";
          console.log(`Is current comment belonged to me: ${isMe}`);

          const alignmentClass = isMe
            ? "align-self: flex-end;"
            : "align-self: flex-start;";
          const itemAlignment = isMe
            ? "align-items: flex-end;"
            : "align-items: flex-start;";

          const chatBubbleSytles = isMe
            ? "background-color: #3b82f6; color: #fff; border-radius: 16px 16px 0px 16px;" // Blue bubble, white text
            : "background-color: #f1f5f9; color: #000; border-radius: 16px 16px 0px 16px;"; // Gray bubble, black text

          const nameLabel = isMe
            ? `${c["senderName"]} (Me)`
            : escapeHtml(`${c["senderName"]}`);
          const subtitleColor = isMe ? "color: #dbeafe;" : "color: #64748b;"; // Soft tint for meta-details

          const commentHtml = `
            <div style="display: flex; flex-direction: column; max-width: 75%; ${alignmentClass} ${itemAlignment}">
                <span style="font-size: 0.75rem; font-weight: 600; color: #64748b; padding: 0 4px;">
                    ${nameLabel}
                </span>

                <div style="padding: 10px 14px; box-shadow: 0 1px 2px rgba(0, 0, 0, 0, 0.5); word-break: break-word; ${chatBubbleSytles}">
                    <p style="margin: 0; font-size: 0.925rem; line-height: 1.4;">${escapeHtml(c["content"])}</p>
                </div>

                <span style="font-size: 0.7rem; color: #94a3b8; padding: 0 4px;">
                    ${c["timeAgo"] || "Just now"}
                </span>
            </div>
          `;

          commentsList.insertAdjacentHTML("beforeend", commentHtml);
        });
        commentsList.scrollTop = commentsList.scrollHeight;
      })
      .catch((err) => {
        console.error(err);
        commentsList.innerHTML = `<div style="color: #ef4444;">Failed to load comments.</div>`;
      });
  };

  // Opening modal
  document.querySelectorAll(".open-comments-btn").forEach((btn) => {
    btn.addEventListener("click", () => {
      const eventID = btn.getAttribute("data-event-id");
      const title = btn.getAttribute("data-event-title");

      commentModalTitle.textContent = `Comments on ${title}`;
      commentEventIdInput.value = eventID;

      commentsModal.style.display = "flex";
      setTimeout(() => (commentsModal.style.opacity = "1"), 10);

      loadComments(eventID);
    });
  });

  // Closing modal
  const hideCommentsModal = () => {
    commentsModal.style.opacity = "0";
    setTimeout(() => (commentsModal.style.display = "none"), 200);
  };

  if (closeCommentsBtn)
    closeCommentsBtn.addEventListener("click", hideCommentsModal);
  if (commentsModal) {
    commentsModal.addEventListener("click", (e) => {
      if (e.target === commentsModal) hideCommentsModal();
    });
  }

  // Submit new comment
  if (commentForm) {
    commentForm.addEventListener("submit", (e) => {
      e.preventDefault();
      const eventID = commentEventIdInput.value;
      const content = commentInput.value;
      const submitBtn = commentForm.querySelector("button[type=submit]");
      console.log(`Content: ${content}`);

      submitBtn.disabled = true;
      submitBtn.textContent = "Posting...";

      fetch(`${folderPath}/events/comments`, {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify({
          event_ID: eventID,
          content: content,
        }),
      })
        .then((res) => {
          console.log(`Processing... ${res}`);
          return res.json();
        })
        .then((data) => {
          submitBtn.disabled = false;
          submitBtn.textContent = "Post";
          console.log(`Response data from backend: ${data}`);
          if (data && data.success) {
            commentInput.value = "";
            loadComments(eventID);
          } else {
            alert(data.error || "Failed to post comment!");
          }
        })
        .catch((err) => {
          console.error(`Failed to send data to the API: ${err}`);
          submitBtn.disabled = false;
          submitBtn.textContent = "Post";
          alert("A network error occured!");
        });
    });
  }
});

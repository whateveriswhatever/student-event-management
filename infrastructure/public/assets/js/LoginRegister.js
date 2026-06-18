document.addEventListener("DOMContentLoaded", () => {
  const loginSection = document.getElementById("login-section");
  const registerSection = document.getElementById("register-section");
  const toggleToRegister = document.getElementById("toggle-to-register");
  const toggleToLogin = document.getElementById("toggle-to-login");

  // Switching to register form
  toggleToRegister.addEventListener("click", () => {
    console.log("Clicked to display the register section!");
    loginSection.classList.add("hidden");
    registerSection.classList.remove("hidden");
    document.title = "Register - Club&Event Seeker";
  });

  // Switching back to login form
  toggleToLogin.addEventListener("click", () => {
    registerSection.classList.add("hidden");
    loginSection.classList.remove("hidden");
    document.title = "Sign In - Club&Event Seeker";
    console.log("Clicked to display the login section!");
  });

  // Auto-opening register tab if URL contains #register (e.g: from the nav-bar button)
  if (window.location.hash === "#register") {
    toggleToRegister.click();
  }
});

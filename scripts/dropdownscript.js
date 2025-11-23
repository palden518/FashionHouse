
document.addEventListener("DOMContentLoaded", () => {
    const accountBtn = document.querySelector(".account-btn");
    const dropdown = document.querySelector(".account-dropdown");

    accountBtn.addEventListener("click", () => {
        dropdown.style.display =
            dropdown.style.display === "block" ? "none" : "block";
    });

    // Close when clicking outside
    document.addEventListener("click", (e) => {
        if (!document.querySelector(".account-dropdown-container").contains(e.target)) {
            dropdown.style.display = "none";
        }
    });
});


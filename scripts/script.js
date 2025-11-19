/**
 * ExamPro - Global Scripts
 * Handles mobile menu, dropdowns, and other global UI interactions.
 */

document.addEventListener("DOMContentLoaded", function () {
  initializeMobileMenu();
  initializeMobileDropdowns();
  initializeUserDropdown();
});

/**
 * Initialize Mobile Menu Toggle
 */
function initializeMobileMenu() {
  const mobileMenuToggle = document.querySelector(".mobile-menu-toggle");
  const mainNav = document.getElementById("mainNav");

  if (mobileMenuToggle && mainNav) {
    mobileMenuToggle.addEventListener("click", function (e) {
      e.stopPropagation();
      mainNav.classList.toggle("active");

      // Toggle icon
      const icon = mobileMenuToggle.querySelector("i");
      if (icon) {
        if (mainNav.classList.contains("active")) {
          icon.classList.remove("fa-bars");
          icon.classList.add("fa-times");
        } else {
          icon.classList.remove("fa-times");
          icon.classList.add("fa-bars");
        }
      }
    });

    // Close menu when clicking outside
    document.addEventListener("click", function (event) {
      if (
        !mainNav.contains(event.target) &&
        !mobileMenuToggle.contains(event.target)
      ) {
        mainNav.classList.remove("active");
        const icon = mobileMenuToggle.querySelector("i");
        if (icon) {
          icon.classList.remove("fa-times");
          icon.classList.add("fa-bars");
        }
      }
    });
  }
}

/**
 * Initialize Mobile Dropdowns
 * Allows clicking on parent items to toggle submenus on mobile
 */
function initializeMobileDropdowns() {
  const navItems = document.querySelectorAll(".nav-item");

  navItems.forEach((item) => {
    const link = item.querySelector(".nav-link");
    const dropdown = item.querySelector(".dropdown-menu");

    if (link && dropdown) {
      link.addEventListener("click", function (e) {
        // Only apply on mobile/tablet screens
        if (window.innerWidth <= 968) {
          // If the link is just a toggle (href="#"), prevent default
          if (
            link.getAttribute("href") === "#" ||
            link.getAttribute("href") === ""
          ) {
            e.preventDefault();
          }

          // Toggle the dropdown
          const isVisible = dropdown.style.display === "block";

          // Close other dropdowns
          document.querySelectorAll(".dropdown-menu").forEach((d) => {
            if (d !== dropdown) d.style.display = "none";
          });

          dropdown.style.display = isVisible ? "none" : "block";
        }
      });
    }
  });

  // Reset styles on window resize
  window.addEventListener("resize", function () {
    if (window.innerWidth > 968) {
      document.querySelectorAll(".dropdown-menu").forEach((d) => {
        d.style.display = ""; // Reset to CSS hover behavior
      });
      const mainNav = document.getElementById("mainNav");
      if (mainNav) mainNav.classList.remove("active");
    }
  });
}

/**
 * Initialize User Profile Dropdown (Mobile & Desktop click support)
 */
function initializeUserDropdown() {
  const userProfile = document.querySelector(".user-profile");
  const userButton = document.querySelector(".user-button");
  const userDropdown = document.querySelector(".user-dropdown");

  if (userProfile && userButton && userDropdown) {
    // On mobile, we might want click instead of hover
    userButton.addEventListener("click", function (e) {
      e.stopPropagation();
      userDropdown.classList.toggle("show");
    });

    // Close when clicking outside
    document.addEventListener("click", function (event) {
      if (!userProfile.contains(event.target)) {
        userDropdown.classList.remove("show");
      }
    });
  }
}

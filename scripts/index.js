/**
 * ExamPro - Index Page JavaScript
 * Modern Professional Blue-Slate Theme
 */

document.addEventListener("DOMContentLoaded", function () {
  initializeSearchFeature();
  initializeSmoothScroll();
  initializeAnimations();
  initializeExamFilters();
});

/**
 * Initialize search functionality (if search bar exists)
 */
function initializeSearchFeature() {
  const searchInput = document.getElementById("searchBar");
  if (!searchInput) return;

  searchInput.addEventListener(
    "input",
    debounce(function (e) {
      const searchTerm = e.target.value.toLowerCase().trim();
      filterExams(searchTerm);
    }, 300)
  );
}

/**
 * Filter exams based on search term
 */
function filterExams(searchTerm) {
  const examCards = document.querySelectorAll(".exam-card");

  examCards.forEach((card) => {
    const examTitle =
      card.querySelector(".exam-title")?.textContent.toLowerCase() || "";
    const examMeta =
      card.querySelector(".exam-meta")?.textContent.toLowerCase() || "";

    const matches =
      examTitle.includes(searchTerm) || examMeta.includes(searchTerm);

    if (matches || searchTerm === "") {
      card.style.display = "";
      card.style.animation = "fadeIn 0.3s ease-in-out";
    } else {
      card.style.display = "none";
    }
  });

  // Show empty state if no results
  updateEmptyState(searchTerm);
}

/**
 * Update empty state message
 */
function updateEmptyState(searchTerm) {
  const sections = document.querySelectorAll(".exams-section");

  sections.forEach((section) => {
    const visibleCards = section.querySelectorAll(
      '.exam-card:not([style*="display: none"])'
    );
    let emptyState = section.querySelector(".search-empty-state");

    if (searchTerm && visibleCards.length === 0) {
      if (!emptyState) {
        emptyState = document.createElement("div");
        emptyState.className = "empty-state search-empty-state";
        emptyState.innerHTML = `
                    <i class="fas fa-search"></i>
                    <h3>No exams found</h3>
                    <p>No exams match your search for "${searchTerm}". Try different keywords.</p>
                `;
        section.querySelector(".exams-grid")?.appendChild(emptyState);
      }
    } else if (emptyState) {
      emptyState.remove();
    }
  });
}

/**
 * Initialize smooth scrolling for anchor links
 */
function initializeSmoothScroll() {
  document.querySelectorAll('a[href^="#"]').forEach((anchor) => {
    anchor.addEventListener("click", function (e) {
      const href = this.getAttribute("href");
      if (href === "#") return;

      e.preventDefault();
      const target = document.querySelector(href);

      if (target) {
        target.scrollIntoView({
          behavior: "smooth",
          block: "start",
        });
      }
    });
  });
}

/**
 * Initialize scroll animations
 */
function initializeAnimations() {
  // Add fade-in animation to elements when they come into view
  const observerOptions = {
    threshold: 0.1,
    rootMargin: "0px 0px -100px 0px",
  };

  const observer = new IntersectionObserver(function (entries) {
    entries.forEach((entry) => {
      if (entry.isIntersecting) {
        entry.target.classList.add("animate-fade-in");
        observer.unobserve(entry.target);
      }
    });
  }, observerOptions);

  // Observe stat cards, exam cards, and feature cards
  const animateElements = document.querySelectorAll(
    ".stat-card, .exam-card, .feature-card, .quick-link-card"
  );

  animateElements.forEach((el) => {
    el.style.opacity = "0";
    el.style.transform = "translateY(20px)";
    observer.observe(el);
  });
}

/**
 * Initialize exam filters (category tabs if needed in future)
 */
function initializeExamFilters() {
  // Add click handlers for potential filter buttons
  const filterButtons = document.querySelectorAll("[data-filter]");

  filterButtons.forEach((button) => {
    button.addEventListener("click", function () {
      const filter = this.getAttribute("data-filter");

      // Remove active class from all buttons
      filterButtons.forEach((btn) => btn.classList.remove("active"));

      // Add active class to clicked button
      this.classList.add("active");

      // Filter exams by category
      filterByCategory(filter);
    });
  });
}

/**
 * Filter exams by category
 */
function filterByCategory(category) {
  const examCards = document.querySelectorAll(".exam-card");

  examCards.forEach((card) => {
    if (category === "all") {
      card.style.display = "";
    } else {
      const cardCategory = card.getAttribute("data-category");
      card.style.display = cardCategory === category ? "" : "none";
    }
  });
}

/**
 * Debounce function to limit rate of function calls
 */
function debounce(func, wait) {
  let timeout;
  return function executedFunction(...args) {
    const later = () => {
      clearTimeout(timeout);
      func(...args);
    };
    clearTimeout(timeout);
    timeout = setTimeout(later, wait);
  };
}

/**
 * Show notification/toast message
 */
function showNotification(message, type = "info") {
  const notification = document.createElement("div");
  notification.className = `alert alert-${type}`;
  notification.style.position = "fixed";
  notification.style.top = "20px";
  notification.style.right = "20px";
  notification.style.zIndex = "10000";
  notification.style.minWidth = "300px";
  notification.style.animation = "slideInRight 0.3s ease-out";
  notification.textContent = message;

  document.body.appendChild(notification);

  setTimeout(() => {
    notification.style.animation = "slideOutRight 0.3s ease-in";
    setTimeout(() => notification.remove(), 300);
  }, 3000);
}

/**
 * Handle exam registration click
 */
function handleExamRegistration(examId, examName) {
  // You can add confirmation dialog here
  const confirmed = confirm(`Do you want to register for "${examName}"?`);

  if (confirmed) {
    // Redirect to registration page with exam ID
    window.location.href = `exams/registerExam.php?id=${examId}`;
  }
}

/**
 * Handle view exam details
 */
function viewExamDetails(examId) {
  window.location.href = `exams/attemptExam.php?id=${examId}`;
}

/**
 * Handle view result
 */
function viewExamResult(examId) {
  window.location.href = `exams/result.php?id=${examId}`;
}

// Add CSS animations dynamically
const style = document.createElement("style");
style.textContent = `
    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .animate-fade-in {
        animation: fadeIn 0.5s ease-out forwards;
    }

    @keyframes slideInRight {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }

    @keyframes slideOutRight {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(100%);
            opacity: 0;
        }
    }
`;
document.head.appendChild(style);

// Export functions for use in inline handlers if needed
window.handleExamRegistration = handleExamRegistration;
window.viewExamDetails = viewExamDetails;
window.viewExamResult = viewExamResult;
window.showNotification = showNotification;

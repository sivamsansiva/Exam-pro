document.addEventListener("DOMContentLoaded", () => {
  const passwordToggles = document.querySelectorAll("[data-toggle-password]");

  passwordToggles.forEach((toggle) => {
    const wrapper = toggle.closest(".input-wrapper");
    if (!wrapper) {
      return;
    }

    const input = wrapper.querySelector("[data-password-field]");
    if (!input) {
      return;
    }

    toggle.addEventListener("click", () => {
      const isVisible = input.getAttribute("type") === "text";
      input.setAttribute("type", isVisible ? "password" : "text");
      toggle.setAttribute("aria-pressed", String(!isVisible));

      const toggleText = toggle.querySelector(".toggle-text");
      if (toggleText) {
        toggleText.textContent = isVisible ? "Show" : "Hide";
      }
    });
  });

  const forms = document.querySelectorAll(".auth-form");
  const passwordPattern = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/;

  forms.forEach((form) => {
    const submitButton = form.querySelector('button[type="submit"]');
    const passwordInput = form.querySelector("#password");
    const confirmInput = form.querySelector("#confirmPassword");
    const confirmTargetSelector =
      confirmInput?.getAttribute("data-match-target");
    const confirmTarget = confirmTargetSelector
      ? form.querySelector(confirmTargetSelector)
      : null;

    const setSubmittingState = (isSubmitting) => {
      if (!submitButton) {
        return;
      }

      if (isSubmitting) {
        if (!submitButton.dataset.originalLabel) {
          submitButton.dataset.originalLabel = submitButton.textContent.trim();
        }
        submitButton.textContent = "Processing...";
        submitButton.disabled = true;
      } else if (submitButton.disabled) {
        submitButton.textContent =
          submitButton.dataset.originalLabel || "Submit";
        submitButton.disabled = false;
      }
    };

    const validatePasswordStrength = () => {
      if (!passwordInput) {
        return;
      }

      if (!passwordInput.value) {
        passwordInput.setCustomValidity("");
        return;
      }

      if (!passwordPattern.test(passwordInput.value)) {
        passwordInput.setCustomValidity(
          "Use 8+ characters with uppercase, lowercase, and a number."
        );
      } else {
        passwordInput.setCustomValidity("");
      }
    };

    const validatePasswordMatch = () => {
      if (!confirmInput || !confirmTarget) {
        return;
      }

      if (!confirmInput.value) {
        confirmInput.setCustomValidity("");
        return;
      }

      if (confirmInput.value !== confirmTarget.value) {
        confirmInput.setCustomValidity("Passwords do not match.");
      } else {
        confirmInput.setCustomValidity("");
      }
    };

    if (passwordInput) {
      passwordInput.addEventListener("input", () => {
        validatePasswordStrength();
        validatePasswordMatch();
      });
    }

    if (confirmInput) {
      confirmInput.addEventListener("input", validatePasswordMatch);
    }

    form.addEventListener("submit", (event) => {
      validatePasswordStrength();
      validatePasswordMatch();

      if (!form.checkValidity()) {
        event.preventDefault();
        form.reportValidity();
        setSubmittingState(false);
        return;
      }

      setSubmittingState(true);
    });

    form.addEventListener("input", () => {
      if (submitButton?.disabled) {
        setSubmittingState(false);
      }
    });
  });
});

function setCookie(name, value, days = 365) {
  const expires = new Date();
  expires.setTime(expires.getTime() + days * 24 * 60 * 60 * 1000);
  document.cookie = `${name}=${value}; expires=${expires.toUTCString()}; path=/; SameSite=Lax`;
}

function getCookie(name) {
  const nameEQ = name + "="; // Use the 'name' parameter
  const ca = document.cookie.split(";");
  for (let i = 0; i < ca.length; i++) {
    let c = ca[i].trim();
    if (c.indexOf(nameEQ) === 0) {
      return c.substring(nameEQ.length, c.length);
    }
  }
  return null;
}

document.addEventListener("DOMContentLoaded", function () {
  const settings = exitIntentPopupSettings || {};
  const enablePopup = settings.enablePopup === "1";

  if (!enablePopup) {
    return;
  }

  function showExitIntentPopup() {
    if (!getCookie("emg_exitPopup")) {
      const popup = document.getElementById("exitIntentPopup");
      if (!popup) {
        return;
      }
      popup.style.display = "block";
      setCookie("emg_exitPopup", "true", 365);
    }
  }

  function closeModal() {
    const popup = document.getElementById("exitIntentPopup");
    popup.style.display = "none";
  }

  document.addEventListener("mouseleave", function (e) {
    if (e.clientY < 10) {
      showExitIntentPopup();
    }
  });
  const closeButton = document.querySelector(
    "#exitIntentPopup .modal-chose-button"
  );
  if (closeButton) {
    closeButton.addEventListener("click", closeModal);
  }

  const triggerButton = document.querySelector(".trigger-emg-popup-plugin");
  if (triggerButton) {
    triggerButton.addEventListener("click", function (event) {
      event.preventDefault();

      const form = document.getElementById("address_placeholder_popup");
      form.classList.add("popup_working");

      const offerAutocomplete = document.getElementById(
        "offer_autocomplete_popup"
      ).value;
      if (offerAutocomplete.length > 0) {
        setTimeout(function () {
          form.classList.remove("popup_working");
          form.submit();
        }, 200);
      } else {
        document
          .getElementById("offer_autocomplete_popup")
          .classList.add("field_validation_below");
      }
    });
  }
});

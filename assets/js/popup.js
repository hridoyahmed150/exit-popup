function eipSetCookie(name, value, days = 365) {
  const expires = new Date();
  expires.setTime(expires.getTime() + days * 24 * 60 * 60 * 1000);
  document.cookie = `${name}=${value}; expires=${expires.toUTCString()}; path=/; SameSite=Lax`;
}

function eipGetCookie(name) {
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
  const settings = exitIntentPopupData || {};
  const enablePopup = settings.enablePopup === "1";

  if (!enablePopup) {
    return;
  }

  function showExitIntentPopup() {
    if (!eipGetCookie("emgExitIntentPopup")) {
      const popup = document.getElementById("emgExitIntentPopup");
      if (!popup) {
        return;
      }
      popup.style.display = "block";
      eipSetCookie("emgExitIntentPopup", "true", 365);
    }
  }

  function closeModal() {
    const popup = document.getElementById("emgExitIntentPopup");
    if (!popup) {
      return;
    }
    popup.style.display = "none";
  }

  document.addEventListener("mouseleave", function (e) {
    if (e.clientY < 10) {
      showExitIntentPopup();
    }
  });
  const closeButton = document.querySelector(
    "#emgExitIntentPopup .modal-close-button"
  );
  if (closeButton) {
    closeButton.addEventListener("click", closeModal);
  }
});

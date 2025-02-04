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
  return null; // Return null if the cookie is not found
}

// Delete a cookie
function deleteCookie(name) {
  document.cookie = `${name}=; expires=Thu, 01 Jan 1970 00:00:00 GMT; path=/`;
}

document.addEventListener("DOMContentLoaded", function () {
  // Initialize Google Maps autocomplete functionality'
  const settings = exitIntentPopupSettings || {};
  const enablePopup = settings.enablePopup === "1";

  if (!enablePopup) {
    return; // Exit if popup is disabled
  }
  let autocomplete2;

  var componentForm = {
    street_number: "short_name",
    route: "short_name", // street name
    locality: "short_name", // city
    administrative_area_level_1: "short_name", // state
    country: "long_name",
    postal_code: "short_name",
  };

  function initAutocomplete() {
    autocomplete = new google.maps.places.Autocomplete(
      document.getElementById("offer_autocomplete_popup"),
      { types: ["geocode"] }
    );
    autocomplete.setFields(["address_component"]);
    autocomplete.addListener("place_changed", fillInAddress);
  }
  function fillInAddress() {
    var place = autocomplete.getPlace();

    for (var component in componentForm) {
      document.getElementById(component).value = "";
      document.getElementById(component).disabled = false;
    }

    for (var i = 0; i < place.address_components.length; i++) {
      var addressType = place.address_components[i].types[0]; // street_name, route, etc...
      if (componentForm[addressType]) {
        var val = place.address_components[i][componentForm[addressType]];
        document.getElementById(addressType).value = val;
      }
    }
  }

  function showExitIntentPopup() {
    if (!getCookie("emg_exitPopup")) {
      const popup = document.getElementById("exitIntentPopup");
      if (!popup) {
        return;
      }
      popup.style.display = "block";
      deleteCookie("exitIntentModal");
      setCookie("emg_exitPopup", "true", 365);
    }
  }

  // Close the modal popup
  function closeModal() {
    const popup = document.getElementById("exitIntentPopup");
    popup.style.display = "none";
  }

  document.addEventListener("mouseleave", function (e) {
    if (e.clientY < 10) {
      showExitIntentPopup();
    }
  });

  // Close button event
  const closeButton = document.querySelector(
    "#exitIntentPopup .modal-chose-button"
  );
  if (closeButton) {
    closeButton.addEventListener("click", closeModal);
  }

  // Trigger form submission
  // Trigger form submission
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
          // submit form here
          form.submit();
        }, 200);
      } else {
        document
          .getElementById("offer_autocomplete_popup")
          .classList.add("field_validation_below");
      }
    });
  }

  // Initialize Google Maps Autocomplete
  if (typeof google !== "undefined" && google.maps) {
    initAutocomplete();
  }
});

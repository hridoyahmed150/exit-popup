function initAutocomplete() {
  console.log("Google Maps API loaded successfully!");

  autocomplete2 = new google.maps.places.Autocomplete(
    document.getElementById("offer_autocomplete_popup"),
    { types: ["geocode"] }
  );

  autocomplete2.setFields(["address_component"]);
  autocomplete2.addListener("place_changed", fillInAddress);
}

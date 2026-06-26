// BM2R — chargement des photos.
// Valeur = chemin local (commence par "assets/") => vraie photo BM2R.
// Valeur = identifiant Unsplash => placeholder temporaire (sujets pas encore shootés : élec, clim).
(function () {
  var MAP = {
    // --- Vraies photos BM2R (chantiers du client) ---
    accueil:      "assets/photos/loft.webp",
    renovation:   "assets/photos/reno-apres.webp",
    renovation2:  "assets/photos/loft.webp",
    interior:     "assets/photos/loft.webp",
    interior2:    "assets/photos/peinture.webp",
    living:       "assets/photos/loft.webp",
    bathroom:     "assets/photos/sdb1.webp",
    bathroom2:    "assets/photos/sdb2.webp",
    bathroom3:    "assets/photos/sdb1.webp",
    bathroom_dark:"assets/photos/sdb2.webp",
    shower:       "assets/photos/sdb1.webp",
    paint:        "assets/photos/peinture.webp",
    wood:         "assets/photos/fenetre-apres.webp",
    window:       "assets/photos/fenetre-apres.webp",
    drywall:      "assets/photos/combles-apres.webp",
    kitchen:      "assets/photos/cuisine.webp",
    kitchen2:     "assets/photos/cuisine.webp",
    tools:        "assets/photos/sol-avant.webp",
    chauffe_eau:  "assets/photos/chauffe-eau.webp",

    // --- Paires avant / après dédiées (sliders Réalisations) ---
    ba_combles_avant: "assets/photos/combles-avant.webp",
    ba_combles_apres: "assets/photos/combles-apres.webp",
    ba_sol_avant:     "assets/photos/sol-avant.webp",
    ba_sol_apres:     "assets/photos/sol-apres.webp",
    ba_reno_avant:    "assets/photos/reno-avant.webp",
    ba_reno_apres:    "assets/photos/reno-apres.webp",
    ba_fenetre_avant: "assets/photos/fenetre-avant.webp",
    ba_fenetre_apres: "assets/photos/fenetre-apres.webp",

    // --- Électricité & climatisation (vraies photos BM2R, plus aucun Unsplash) ---
    elec:        "assets/photos/elec-accueil.webp",
    thermostat:  "assets/photos/clim-accueil.webp",
    radiator:    "assets/photos/clim-accueil.webp"
  };
  function url(id, w) {
    if (id.indexOf("assets/") === 0) return id;          // photo locale
    return "https://images.unsplash.com/photo-" + id + "?auto=format&fit=crop&w=" + (w || 1100) + "&q=72";
  }
  function fill(el) {
    var key = el.getAttribute("data-photo");
    if (!key || !MAP[key]) return;
    var img = new Image();
    img.className = "ph-photo";
    img.alt = el.getAttribute("data-alt") || "";
    img.onload = function () { el.classList.add("has-photo"); };
    img.onerror = function () { img.remove(); };
    img.src = url(MAP[key], el.getAttribute("data-w") || 1100);
    el.appendChild(img);
  }
  function init() {
    var els = document.querySelectorAll(".ph[data-photo]");
    for (var i = 0; i < els.length; i++) fill(els[i]);
  }
  if (document.readyState === "loading") document.addEventListener("DOMContentLoaded", init);
  else init();
})();

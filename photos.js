// BM2R — chargement des photos (libres de droit, Unsplash) avec repli sur le placeholder
(function () {
  var MAP = {
    renovation:  "1503387762-592deb58ef4e",
    renovation2: "1504307651254-35680f356dfd",
    interior:    "1513694203232-719a280e022f",
    interior2:   "1600210492486-724fe5c67fb0",
    living:      "1493809842364-78817add7ffb",
    elec:        "1621905251918-48416bd8575a",
    bathroom:    "1620626011761-996317b8d101",
    bathroom2:   "1584622650111-993a426fbf0a",
    bathroom3:   "1552321554-5fefe8c9ef14",
    bathroom_dark:"1600566752355-35792bedcfea",
    paint:       "1562259949-e8e7689d7828",
    wood:        "1589939705384-5185137a7f0f",
    drywall:     "1607400201889-565b1ee75f8e",
    kitchen:     "1556912173-3bb406ef7e77",
    kitchen2:    "1600585152220-90363fe7e115",
    thermostat:  "1545259741-2ea3ebf61fa3",
    tools:       "1581578731548-c64695cc6952",
    window:      "1600566752355-35792bedcfea",
    shower:      "1552321554-5fefe8c9ef14",
    radiator:    "1545259741-2ea3ebf61fa3"
  };
  function url(id, w) { return "https://images.unsplash.com/photo-" + id + "?auto=format&fit=crop&w=" + (w || 1100) + "&q=72"; }
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

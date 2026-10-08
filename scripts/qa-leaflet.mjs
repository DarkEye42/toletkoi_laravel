/**
 * Does Leaflet actually initialise a map on the search/map page?
 *
 * Reading `typeof window.L` is unreliable through this driver, so assert on
 * the DOM Leaflet produces: a `.leaflet-container` with tile <img> children
 * only exists if L.map()/L.tileLayer() really ran.
 */
export default async function run(page, ui) {
    const out = { url: page.url() };

    // Give the deferred Leaflet script + the Alpine x-init poll time to finish.
    await page.waitForTimeout(3000);

    out.leaflet = await page.evaluate(() => {
        const container = document.querySelector(".leaflet-container");
        const mapEl = document.getElementById("map");
        const loading = document.getElementById("map-loading");

        return {
            containerExists: !!container,
            containerClass: mapEl ? mapEl.className : "no #map element",
            tileImages: document.querySelectorAll(".leaflet-tile").length,
            zoomControls: document.querySelectorAll(".leaflet-control-zoom")
                .length,
            attribution: document.querySelectorAll(
                ".leaflet-control-attribution",
            ).length,
            // If Leaflet failed to load, the view writes this message.
            loadingText: loading
                ? loading.textContent.trim().slice(0, 80)
                : null,
            loadingHidden: loading
                ? getComputedStyle(loading).display === "none"
                : null,
        };
    });

    return out;
}

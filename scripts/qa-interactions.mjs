/**
 * Verify the reported-broken interactions by BEHAVIOUR, not by reading
 * window.Alpine / window.L (which the headless driver reports unreliably).
 *
 * A component that actually reacts to a click is proof Alpine booted.
 */
export default async function run(page, ui) {
    const out = {};

    // 1. The search modal is x-cloak'd — it must start hidden.
    out.modalHiddenOnLoad = await page.evaluate(() => {
        const m = document.querySelector('[x-show="isSearchOpen"]');
        return m ? getComputedStyle(m).display === "none" : "modal not found";
    });

    // 2. Find the search trigger and click it.
    const snapshot = await ui.snapshot();
    // The accessible name is the icon ligature plus the label, e.g.
    // `button "search Search"`.
    const trigger = snapshot.match(/@(e\d+) button "[^"]*Search"/)?.[1];

    if (!trigger) {
        out.error = "search trigger button not in snapshot";
        out.snapshot = snapshot;
        return out;
    }

    await ui.click(trigger);
    await page.waitForTimeout(700);

    // 3. If Alpine booted, x-show flipped and x-cloak released -> visible.
    out.modalVisibleAfterClick = await page.evaluate(() => {
        const m = document.querySelector('[x-show="isSearchOpen"]');
        if (!m) return "modal not found";
        const cs = getComputedStyle(m);
        return {
            display: cs.display,
            visibility: cs.visibility,
            opacity: cs.opacity,
        };
    });

    // 4. The close button must be reachable and must close the modal again.
    const afterOpen = await ui.snapshot();
    out.closeButtonHasAccessibleName = /button "Close search"/.test(afterOpen);

    const close = afterOpen.match(/@(e\d+) button "Close search"/)?.[1];
    if (close) {
        await ui.click(close);
        await page.waitForTimeout(700);
        out.modalHiddenAfterClose = await page.evaluate(() => {
            const m = document.querySelector('[x-show="isSearchOpen"]');
            return m
                ? getComputedStyle(m).display === "none"
                : "modal not found";
        });
    }

    return out;
}

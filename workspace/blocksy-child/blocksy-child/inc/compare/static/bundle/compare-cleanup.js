document.addEventListener('DOMContentLoaded', () => {
    // Blocksy Premium Comparison and our Child Theme Comparison might both render.
    // Let's clean up duplicates.

    // 1. Remove duplicate compare bars
    const originalCompareBars = document.querySelectorAll('.ct-compare-bar:not(.qcfw-side-pop-wrapper)');
    originalCompareBars.forEach(bar => bar.remove());

    // 2. Remove duplicate compare tables (if placed on page)
    const tables = document.querySelectorAll('.ct-compare-table');
    if (tables.length > 1) {
        // Keep only the last one (which is usually ours since we appended it later)
        for (let i = 0; i < tables.length - 1; i++) {
            tables[i].remove();
        }
    }

    // 3. Remove duplicate 'Add to Compare' buttons on single products
    const addButtons = document.querySelectorAll('.ct-compare-button-single');
    if (addButtons.length > 1) {
        for (let i = 0; i < addButtons.length - 1; i++) {
            addButtons[i].remove();
        }
    }

    // 4. Remove duplicate 'Add to Compare' tooltips on archive products
    const archiveButtons = document.querySelectorAll('.ct-compare-button-archive');
    // Group them by product ID
    const productBtns = {};
    archiveButtons.forEach(btn => {
        // Find nearest product wrapper or use data attr
        const wrapper = btn.closest('.product');
        if (wrapper) {
            if (!productBtns[wrapper]) {
                productBtns[wrapper] = [];
            }
            productBtns[wrapper].push(btn);
        }
    });

    for (let wrapper in productBtns) {
        if (productBtns[wrapper].length > 1) {
            for (let i = 0; i < productBtns[wrapper].length - 1; i++) {
                productBtns[wrapper][i].remove();
            }
        }
    }
});

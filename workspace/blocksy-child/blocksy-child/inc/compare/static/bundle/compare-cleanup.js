function cleanupDuplicateCompareElements() {
    // 1. Remove duplicate compare bars (Keep only our side pop wrapper)
    const originalCompareBars = document.querySelectorAll('.ct-compare-bar:not(.qcfw-side-pop-wrapper)');
    originalCompareBars.forEach(bar => bar.remove());

    // 2. Remove duplicate compare tables (if placed on page)
    const tables = document.querySelectorAll('.ct-compare-table');
    if (tables.length > 1) {
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

    // 4. Remove duplicate archive buttons per product
    const archiveButtons = document.querySelectorAll('.ct-compare-button-archive');
    const productBtns = new Map();
    archiveButtons.forEach(btn => {
        const wrapper = btn.closest('.product') || btn.closest('.product-card') || btn.parentElement;
        if (wrapper) {
            if (!productBtns.has(wrapper)) {
                productBtns.set(wrapper, []);
            }
            productBtns.get(wrapper).push(btn);
        }
    });

    productBtns.forEach(btns => {
        if (btns.length > 1) {
            for (let i = 0; i < btns.length - 1; i++) {
                btns[i].remove();
            }
        }
    });
}

// Run immediately and also set up an observer for dynamically loaded products/AJAX
document.addEventListener('DOMContentLoaded', () => {
    cleanupDuplicateCompareElements();

    // Catch dynamic injects
    setTimeout(cleanupDuplicateCompareElements, 500);
    setTimeout(cleanupDuplicateCompareElements, 2000);

    const observer = new MutationObserver((mutations) => {
        let shouldRun = false;
        mutations.forEach(mut => {
            if (mut.addedNodes.length) {
                shouldRun = true;
            }
        });
        if (shouldRun) {
            cleanupDuplicateCompareElements();
        }
    });

    observer.observe(document.body, { childList: true, subtree: true });
});

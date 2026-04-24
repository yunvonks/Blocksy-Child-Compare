document.addEventListener('DOMContentLoaded', () => {
    const opener = document.querySelector('.qcfw-m-side-pop-opener');
    const content = document.querySelector('.qcfw-m-side-pop-content');

    if (opener && content) {
        // Initial state hidden
        content.style.display = 'none';
        content.style.position = 'absolute';
        content.style.right = '0';

        opener.addEventListener('click', (e) => {
            e.preventDefault();
            if (content.style.display === 'none') {
                content.style.display = 'flex';
                opener.style.display = 'none'; // optional depending on layout
            } else {
                content.style.display = 'none';
            }
        });

        // Hide content if clicking outside
        document.addEventListener('click', (e) => {
            if (!opener.contains(e.target) && !content.contains(e.target)) {
                content.style.display = 'none';
                opener.style.display = 'flex';
            }
        });
    }
});

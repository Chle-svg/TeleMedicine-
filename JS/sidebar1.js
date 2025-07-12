document.addEventListener('DOMContentLoaded', () => {
    const sidebar = document.querySelector('.sidebar');
    const toggleBtn = document.querySelector('.sidebar-toggle');
    const dropdowns = document.querySelectorAll('.sidebar ul li.dropdown > a');

    toggleBtn.addEventListener('click', () => {
        sidebar.classList.toggle('collapsed');
    });

    dropdowns.forEach(dropdownToggle => {
        dropdownToggle.addEventListener('click', (e) => {
            e.preventDefault();
            const parentLi = dropdownToggle.parentElement;
            parentLi.classList.toggle('open');
        });
    });
});

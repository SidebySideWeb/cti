const searchInput = document.getElementById("gz-features-tabs-search");

searchInput.addEventListener("input", function () {
	const searchTerm = searchInput.value.toLowerCase();

	const items = document.querySelectorAll(".gz-features-tab-item");

	items.forEach(item => {
        const classList = Array.from(item.classList);
        const textContent = item.textContent.trim().toLowerCase();

        const match = classList.some(cls => cls.toLowerCase().includes(searchTerm)) || textContent.includes(searchTerm);
        item.classList.toggle("hidden", !match);
    });
});
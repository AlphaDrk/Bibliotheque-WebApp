// Site interactions: sidebar toggle, theme toggle (light/dark), search placeholder rotation
document.addEventListener('DOMContentLoaded', function () {
	// Mobile sidebar toggle
	const menuToggle = document.querySelector('.navbar-toggler');
	const sidebar = document.querySelector('.sidebar');
	if (menuToggle && sidebar) {
		menuToggle.addEventListener('click', function () {
			sidebar.classList.toggle('mobile-open');
		});
	}

	// Theme (light/dark) toggle
	const themeToggle = document.getElementById('theme-toggle');
	const root = document.documentElement;
	const THEME_KEY = 'site_theme';

	function applyTheme(theme) {
		if (theme === 'dark') {
			root.classList.add('dark');
			document.body.style.backgroundColor = '#0f1724';
		} else {
			root.classList.remove('dark');
			document.body.style.backgroundColor = '';
		}
	}

	// Initialize from localStorage
	try {
		const stored = localStorage.getItem(THEME_KEY) || 'light';
		applyTheme(stored);
	} catch (e) { /* ignore */ }

	if (themeToggle) {
		themeToggle.addEventListener('click', function () {
			const isDark = root.classList.contains('dark');
			const next = isDark ? 'light' : 'dark';
			applyTheme(next);
			try { localStorage.setItem(THEME_KEY, next); } catch (e) {}
		});
	}

	// Search placeholder rotation
	const searchInput = document.getElementById('global-search-input');
	if (searchInput) {
		const placeholders = [
			'Rechercher un livre...',
			'Rechercher un auteur...',
			'Rechercher un éditeur...'
		];
		let idx = 0;
		setInterval(() => {
			idx = (idx + 1) % placeholders.length;
			// only change if input is empty and not focused
			if (document.activeElement !== searchInput && !searchInput.value) {
				searchInput.setAttribute('placeholder', placeholders[idx]);
			}
		}, 3000);
	}

	// Floating add button behavior (open quick modal / focus search)
	const fab = document.getElementById('fab-add');
	if (fab) {
		fab.addEventListener('click', () => {
			// If you have a modal, open it here. Default: focus on search
			if (searchInput) searchInput.focus();
		});
	}
});

document.addEventListener('DOMContentLoaded', async () => {
	const grid = document.getElementById('blogGrid');
	if (!grid) return;

	// Ensure blog section is visible
	const blogSection = grid.closest('.blog, #blog, .blog-feed');
	if (blogSection) {
		blogSection.classList.add('is-visible');
		// Also ensure it's visible in case observer doesn't trigger
		setTimeout(() => {
			blogSection.classList.add('is-visible');
			blogSection.style.opacity = '1';
		}, 100);
	}

	// Store original content as fallback
	const originalContent = grid.innerHTML;
	const hasOriginalContent = originalContent.trim().length > 0;

	try {
		const res = await fetch('data/blog_posts.json', { cache: 'no-store' });
		if (!res.ok) throw new Error('Failed to load blog posts');
		const posts = await res.json();

		const published = (Array.isArray(posts) ? posts : [])
			.filter(p => (p.status || '').toLowerCase() === 'published')
			.sort((a, b) => new Date(b.published_at || b.created_at || 0) - new Date(a.published_at || a.created_at || 0));

		if (published.length === 0) {
			// If no published posts and no original content, show message
			if (!hasOriginalContent) {
				grid.innerHTML = '<p style="text-align: center; padding: 40px; color: #666;">No articles yet. Please check back soon.</p>';
			}
			// Otherwise keep original content
			return;
		}

		// Clear grid only if we have posts to show
		grid.innerHTML = '';
		published.forEach(post => {
			const card = document.createElement('article');
			card.className = 'blog-card';
			
			// Process image path: convert escaped slashes to regular slashes
			let imgSrc = 'assets/images/pallavi-logo.png'; // default fallback
			if (post.featured_image && post.featured_image.trim()) {
				// Convert escaped slashes (\/) to regular slashes (/)
				imgSrc = post.featured_image.replace(/\\\//g, '/');
				// Ensure path starts correctly - if it's just a filename, prepend assets/images/
				if (!imgSrc.includes('/')) {
					imgSrc = 'assets/images/' + imgSrc;
				}
				// If path doesn't start with assets/ or http, assume it's relative to assets/images/
				if (!imgSrc.startsWith('assets/') && !imgSrc.startsWith('/') && !imgSrc.startsWith('http')) {
					imgSrc = 'assets/images/' + imgSrc;
				}
			}
			
			const safeTitle = (post.title || 'Untitled');
			const excerpt = (post.excerpt || (post.content || '').slice(0, 140) + '...');
			const slug = (post.slug || 'post-' + post.id);
			const category = (post.categories && post.categories.length > 0) ? post.categories[0] : 'General';
			const date = post.published_at || post.created_at || '';
			const formattedDate = date ? new Date(date).toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' }) : '';

			card.innerHTML = `
				<div class="blog-image">
					<img src="${imgSrc}" alt="${safeTitle}" class="blog-img" onerror="this.src='assets/images/pallavi-logo.png'; this.onerror=null;">
				</div>
				<div class="blog-content">
					${date ? `<div class="blog-meta">
						<span class="blog-category">${category}</span>
						${formattedDate ? `<span class="blog-date">${formattedDate}</span>` : ''}
					</div>` : ''}
					<h3>${safeTitle}</h3>
					<p>${excerpt}</p>
					<a href="blog.html?post=${encodeURIComponent(slug)}" class="read-more">Read More <i class="fas fa-arrow-right"></i></a>
				</div>
			`;
			grid.appendChild(card);
		});

		// Ensure cards are visible after loading
		setTimeout(() => {
			grid.querySelectorAll('.blog-card').forEach(card => {
				card.classList.add('is-visible');
				card.style.opacity = '1';
			});
		}, 50);
	} catch (err) {
		console.error('Error loading blogs:', err);
		// If there was original content, restore it; otherwise show error
		if (hasOriginalContent) {
			grid.innerHTML = originalContent;
			// Ensure restored cards are visible
			setTimeout(() => {
				grid.querySelectorAll('.blog-card').forEach(card => {
					card.classList.add('is-visible');
					card.style.opacity = '1';
				});
			}, 50);
		} else {
			grid.innerHTML = '<p style="text-align: center; padding: 40px; color: #666;">Unable to load articles right now. Please try again later.</p>';
		}
	}
});


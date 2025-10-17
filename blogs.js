// document.addEventListener('DOMContentLoaded', async () => {
	const grid = document.getElementById('blogGrid');
	if (!grid) return;

	try {
		const res = await fetch('data/blog_posts.json', { cache: 'no-store' });
		if (!res.ok) throw new Error('Failed to load blog posts');
		const posts = await res.json();

		const published = (Array.isArray(posts) ? posts : [])
			.filter(p => (p.status || '').toLowerCase() === 'published')
			.sort((a, b) => new Date(b.published_at || b.created_at || 0) - new Date(a.published_at || a.created_at || 0));

		if (published.length === 0) {
			grid.innerHTML = '<p>No articles yet. Please check back soon.</p>';
			return;
		}

		grid.innerHTML = '';
		published.slice(0, 6).forEach(post => {
			const card = document.createElement('article');
			card.className = 'blog-card';
			const imgSrc = (post.featured_image && post.featured_image.trim()) ? post.featured_image : 'assets/images/pallavi-logo.png';
			const safeTitle = (post.title || 'Untitled');
			const excerpt = (post.excerpt || (post.content || '').slice(0, 140) + '...');
			const slug = (post.slug || 'post-' + post.id);

			card.innerHTML = `
				<div class="blog-image">
					<img src="${imgSrc}" alt="${safeTitle}">
				</div>
				<div class="blog-content">
					<h3>${safeTitle}</h3>
					<p>${excerpt}</p>
					<a href="blog.html?post=${encodeURIComponent(slug)}" class="read-more">Read More <i class="fas fa-arrow-right"></i></a>
				</div>
			`;
			grid.appendChild(card);
		});
	} catch (err) {
		console.error('Error loading blogs:', err);
		grid.innerHTML = '<p>Unable to load articles right now.</p>';
	}
// });



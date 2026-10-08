/**
 * ToLetKoi - Interactive Components
 */

// ==============================
// Cascading Dropdowns (Division → District → Area)
// ==============================
document.addEventListener('DOMContentLoaded', function () {
    const divisionSelect = document.getElementById('division_id');
    const districtSelect = document.getElementById('district_id');
    const areaSelect = document.getElementById('area_id');

    if (divisionSelect && districtSelect) {
        divisionSelect.addEventListener('change', function () {
            const divisionId = this.value;
            districtSelect.innerHTML = '<option value="">Loading districts...</option>';
            areaSelect.innerHTML = '<option value="">-- Select Area --</option>';
            areaSelect.disabled = true;

            if (!divisionId) {
                districtSelect.innerHTML = '<option value="">-- Select District --</option>';
                districtSelect.disabled = true;
                return;
            }

            fetch(`/api/districts/${divisionId}`)
                .then(r => r.json())
                .then(data => {
                    districtSelect.innerHTML = '<option value="">-- Select District --</option>';
                    districtSelect.disabled = false;
                    data.forEach(d => {
                        const opt = new Option(d.name, d.id);
                        districtSelect.appendChild(opt);
                    });
                })
                .catch(() => {
                    districtSelect.innerHTML = '<option value="">Error loading districts</option>';
                });
        });
    }

    if (districtSelect && areaSelect) {
        districtSelect.addEventListener('change', function () {
            const districtId = this.value;
            areaSelect.innerHTML = '<option value="">Loading areas...</option>';

            if (!districtId) {
                areaSelect.innerHTML = '<option value="">-- Select Area --</option>';
                areaSelect.disabled = true;
                return;
            }

            fetch(`/api/areas/${districtId}`)
                .then(r => r.json())
                .then(data => {
                    areaSelect.innerHTML = '<option value="">-- Select Area --</option>';
                    areaSelect.disabled = false;
                    data.forEach(a => {
                        const opt = new Option(a.name, a.id);
                        areaSelect.appendChild(opt);
                    });
                })
                .catch(() => {
                    areaSelect.innerHTML = '<option value="">Error loading areas</option>';
                });
        });
    }

    // ==============================
    // Favorite Toggle (AJAX)
    // ==============================
    document.querySelectorAll('[data-favorite-toggle]').forEach(btn => {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            e.stopPropagation();
            const listingId = this.dataset.listingId;

            fetch('/api/favorites/' + listingId, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
                    'Accept': 'application/json'
                }
            })
            .then(r => r.json())
            .then(data => {
                if (data.favorited) {
                    this.innerHTML = '❤️';
                    this.classList.add('text-red-500');
                    this.classList.remove('text-gray-400');
                } else {
                    this.innerHTML = '🤍';
                    this.classList.add('text-gray-400');
                    this.classList.remove('text-red-500');
                }
                if (data.count !== undefined) {
                    const countEl = this.querySelector('.fav-count');
                    if (countEl) countEl.textContent = data.count;
                }
            })
            .catch(() => {});
        });
    });

    // ==============================
    // Contact Unlock (AJAX)
    // ==============================
    document.querySelectorAll('[data-unlock-contact]').forEach(btn => {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            const listingId = this.dataset.listingId;
            const balanceEl = document.getElementById('credit-balance');
            const credits = parseInt(balanceEl?.textContent || '0');

            if (credits < 5 && !this.dataset.forceUnlock) {
                if (!confirm('You need 5 credits to unlock. Continue?')) return;
            }

            fetch('/api/listings/' + listingId + '/unlock-contact', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({})
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('contact-phone').textContent = data.phone;
                    document.getElementById('contact-email').textContent = data.email;
                    document.getElementById('contact-info-hidden').classList.add('hidden');
                    document.getElementById('contact-info-visible').classList.remove('hidden');
                    if (balanceEl && data.credits_remaining !== undefined) {
                        balanceEl.textContent = data.credits_remaining;
                    }
                } else {
                    alert(data.message || 'Failed to unlock contact.');
                }
            })
            .catch(() => alert('Network error. Please try again.'));
        });
    });
});

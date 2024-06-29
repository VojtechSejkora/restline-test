// dynamically change values base after select base on https://blog.nette.org/en/dependent-selectboxes-elegantly-in-nette-and-pure-js
// find all child selectboxes on the page
document.addEventListener('DOMContentLoaded', registerDataDepends);

function registerDataDepends() {
	document.querySelectorAll('select[data-depends]').forEach((childSelect) => {
		let parentSelect = childSelect.form[childSelect.dataset.depends]; // parent <select>
		let url = childSelect.dataset.url; // attribute data-url
		let items = JSON.parse(childSelect.dataset.items || 'null'); // attribute data-items

		// when the user changes the selected item in the parent selection...
		parentSelect.addEventListener('change', () => {
			// if the data-items attribute exists...
			if (items) {
				// load new items directly into the child selectbox
				updateSelectbox(childSelect, items[parentSelect.value]);
			}

			// if the data-url attribute exists...
			if (url) {
				// we make AJAX request to the endpoint with the selected item instead of placeholder
				fetch(url.replace(encodeURIComponent('#'), encodeURIComponent(parentSelect.value)))
					.then((response) => response.json())
					// and load new items into the child selectbox
					.then((data) => updateSelectbox(childSelect, data));
			}
		});
	});
}

// replaces <options> in <select>
function updateSelectbox(select, items) {

	let promp = select.querySelector('option[disabled=""]');
	select.innerHTML = ''; // remove all

	if (promp) {

		select.appendChild(promp); // add prompt
		promp.selected = true;
		// select.value = promp['value'];
	}


	for (let id in items) { // insert new
		let el = document.createElement('option');
		el.setAttribute('value', id);
		el.innerText = items[id];
		select.appendChild(el);
	}
}


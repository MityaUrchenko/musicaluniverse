$(document).ready(function () {
	const container = $('#items-container');
	const btnGrid = $('#view-grid');
	const btnList = $('#view-list');

	// Восстанавливаем предыдущий вид
	const savedView = localStorage.getItem('viewMode') || 'grid';
	container.removeClass('view-grid view-list').addClass('view-' + savedView);
	if (savedView === 'list') {
		btnList.addClass('active');
		btnGrid.removeClass('active');
	}

	btnGrid.on('click', function () {
		container.removeClass('view-list').addClass('view-grid');
		btnGrid.addClass('active');
		btnList.removeClass('active');
		localStorage.setItem('viewMode', 'grid');
	});

	btnList.on('click', function () {
		container.removeClass('view-grid').addClass('view-list');
		btnList.addClass('active');
		btnGrid.removeClass('active');
		localStorage.setItem('viewMode', 'list');
	});
});
$(document).ready( function () {

	$.datepicker.regional['ru'] = { 
		closeText: 'Закрыть', 
		prevText: '&#x3c;Пред', 
		nextText: 'След&#x3e;', 
		currentText: 'Сегодня', 
		monthNames: ['Январь','Февраль','Март','Апрель','Май','Июнь', 
		'Июль','Август','Сентябрь','Октябрь','Ноябрь','Декабрь'], 
		monthNamesShort: ['Янв','Фев','Мар','Апр','Май','Июн', 
		'Июл','Авг','Сен','Окт','Ноя','Дек'], 
		dayNames: ['воскресенье','понедельник','вторник','среда','четверг','пятница','суббота'], 
		dayNamesShort: ['вск','пнд','втр','срд','чтв','птн','сбт'], 
		dayNamesMin: ['Вс','Пн','Вт','Ср','Чт','Пт','Сб'], 
		dateFormat: 'yy-mm-dd', 
		firstDay: 1, 
		isRTL: false 
		}; 
	$.datepicker.setDefaults($.datepicker.regional['ru']); 
	
	$("#dpick1").datepicker();
	$("#dpick2").datepicker();
	$("#dpick3").datepicker();
	$("#dpick4").datepicker();
	$("#dpick5").datepicker();

	$('#AddNewPage').click(function () {
		$('#NewPageForm').submit();
	});

	$('#EditPageConfirm').click(function () {
		$('#EditPageForm').submit();
	});

	$('#EditPageDelete').click(function () {
		if( confirm("Вы действительно хотите удалить эту страницу?") )
			$('#DeletePageForm').submit();
	});

	$('#AddNewNews').click(function () {
		$('#NewNewsForm').submit();
	});

	$('#EditNewsConfirm').click(function () {
		$('#EditNewsForm').submit();
	});

	$('#EditNewsDelete').click(function () {
		if( confirm("Вы действительно хотите удалить эту страницу?") )
			$('#DeleteNewsForm').submit();
	});

	$('#Public').datepicker();

	// MENU
	function onClickEvent(element) {
		atb = element.target;
		form = $('#AddAndEditMenuForm');
		var id = atb.getAttribute('ids'); 
		var name = atb.getAttribute('names');
		var link = atb.getAttribute('link');
		var parent = atb.getAttribute('parent'); 
		var priority = atb.getAttribute('priority');

		priority = (priority == null) ? 0 : priority;
		parent = (parent == null) ? "NULL" : parent;
		parent = (parent == 0) ? "NULL" : parent;

		form.find('#id').val(id); console.log(id);
		form.find('#name').val(name);
		form.find('#link').val(link);
		form.find('#parent').val(parent);
		form.find('#priority').val(priority);

		$('#editMenuModal').modal();

	};

	$('.editMenuButton').click(onClickEvent);
	$('.deleteMenuButton').click(function(element){
		var id = element.target.getAttribute('ids');
		if( confirm("Вы действительно хотите удалить этот элемент меню?") )
		{
			$('<form action="admin.php?page=menu_editor&operation=delete&" method="POST"><input type=input name="id" value="' + id + '"></form>' ).submit();
		}
	});


});
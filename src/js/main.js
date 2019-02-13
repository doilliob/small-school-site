$(document).ready(function(){


	//********************************************
	//   НАПИШИ ВОПРОС ДИРЕКТОРУ - МОДАЛЬНОЕ ОКНО
	//********************************************
	var paneContent = null;

	function SendDirektor() {
		var pane = $("#ModalDialog");
		pane.find('#ModalTitle').html("Отправить письмо директору");
		if( paneContent == null )
		{ 
			$.get( "templates/director.html", function( data ) {
				paneContent = data;
				$('#ModalBody').html(data);
			});
		}else{
			$('#ModalBody').html(paneContent);
		}
		pane.modal();

	}; // SendDirektor


	$('#SendToDirektor').click(function() {
		SendDirektor();
	})

	//********************************************
	//   НАПИШИ ВОПРОС ДИРЕКТОРУ - АНИМАЦИЯ
	//********************************************

	$('.Question').each(function(){
		$(this).click(function (){
			var answer = $(this).parent().find('.Answer');
			var span = $(this).find('span');
			var classes = span.attr('class');
			if( classes == "glyphicon glyphicon-plus")
			{
				span.attr("class","glyphicon glyphicon-minus");
				answer.slideDown();
			}else{
				span.attr("class","glyphicon glyphicon-plus");
				answer.slideUp();
			}

		});
	});

});
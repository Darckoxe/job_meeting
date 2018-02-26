$(document).ready(function(){
			$('#ville').autocomplete({
				source : function(request,response){
				$.ajax({

					type: "GET",
					dataType: "json",
					url: "http://infoweb/~E149769S/Job-Meeting/Gitlab/JobMeetAdmin/codePostalComplete.php",
					data: 'commune=' + $('#ville').val() + '&maxRows=10',
					success: function(data){
						response($.map(data, function(item){
							return{
								value : item.Ville,
								codePostal : item.CodePostal,
								label : item.Ville + " " + item.CodePostal,
							}
						}));
					},
				});
				},
		minLength : 3,

				select : function(event, ui){
					$('#cp').attr('value', ui.item.codePostal);
				}
		});
});

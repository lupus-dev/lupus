/* 
 * Lupus in Tabula
 * ...un progetto di Edoardo Morassutto
 * Contributors:
 * - 2014 Edoardo Morassutto <edoardo.morassutto@gmail.com>
 */


function checkRoomName() {
	var room_name = $("#room-name").val();
	$.ajax({
		url: APIdir + "/checkRoomName",
		type: 'GET',
		dataType: 'json',
		data: {
			room_name: room_name
		},
		success: function(data) {
			var status = data.status;
			var container = $("#room-name").parent();
			if (status) {
				container.addClass("has-success").removeClass("has-error");
				$("#room-name-icon").addClass("glyphicon-ok").removeClass("glyphicon-remove");
			} else {
				container.removeClass("has-success").addClass("has-error");
				$("#room-name-icon").removeClass("glyphicon-ok").addClass("glyphicon-remove");
			}
		},
		error: function() {
			var container = $("#room-name").parent();
			container.removeClass("has-success").addClass("has-error");
			$("#room-name-icon").removeClass("glyphicon-ok").addClass("glyphicon-remove");
		}
	});
}
function checkRoomDescr() {
	var room_descr = $("#room-desc").val();
	$.ajax({
		url: APIdir + "/checkRoomDescr",
		type: 'GET',
		dataType: 'json',
		data: {
			room_descr: room_descr
		},
		success: function(data) {
			var status = data.status;
			var container = $("#room-desc").parent();
			if (status) {
				container.addClass("has-success").removeClass("has-error");
				$("#room-desc-icon").addClass("glyphicon-ok").removeClass("glyphicon-remove");
			} else {
				container.removeClass("has-success").addClass("has-error");
				$("#room-desc-icon").removeClass("glyphicon-ok").addClass("glyphicon-remove");
			}
		},
		error: function() {
			var container = $("#room-name").parent();
			container.removeClass("has-success").addClass("has-error");
			$("#room-desc-icon").removeClass("glyphicon-ok").addClass("glyphicon-remove");
		}
	});
}

function newRoom() {
	if ($(".has-error").length > 0) {
		alert(":(");
		return;
	}
	var room_name = $("#room-name").val();
	var room_descr = $("#room-desc").val();
	
	data = {
		descr: room_descr
	};
	if ($("#private").length > 0 && $("#private").prop("checked"))
		data.private = true;		
	
	$.ajax({
		url: APIdir + "/new_room/"+room_name,
		type: 'GET',
		dataType: 'json',
		data: data,
		success: function() {
			document.location.href = room_name;
		},
		error: function(jqXHR) {
			console.error(jqXHR);
		}
	});
}
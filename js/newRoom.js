/* 
 * Lupus in Tabula
 * ...un progetto di Edoardo Morassutto
 * Contributors:
 * - 2014 Edoardo Morassutto <edoardo.morassutto@gmail.com>
 */

var name_ok = false;
var descr_ok = false;

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
				$("#room-name").popover("hide");
				name_ok = true;
			} else {
				container.removeClass("has-success").addClass("has-error");
				$("#room-name-icon").removeClass("glyphicon-ok").addClass("glyphicon-remove");
				$("#room-name").popover("show");
				name_ok = false;
			}
			if (name_ok && descr_ok)
				$("#create").removeClass("disabled");
			else
				$("#create").addClass("disabled");
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
				$("#room-desc").popover("hide");
				descr_ok = true;
			} else {
				container.removeClass("has-success").addClass("has-error");
				$("#room-desc-icon").removeClass("glyphicon-ok").addClass("glyphicon-remove");
				$("#room-desc").popover("show");
				descr_ok = false;
			}
			if (name_ok && descr_ok)
				$("#create").removeClass("disabled");
			else
				$("#create").addClass("disabled");
		},
		error: function() {
			var container = $("#room-name").parent();
			container.removeClass("has-success").addClass("has-error");
			$("#room-desc-icon").removeClass("glyphicon-ok").addClass("glyphicon-remove");
		}
	});
}

function newRoom() {
	if (!(name_ok && descr_ok))	
		return;
	var room_name = $("#room-name").val();
	var room_descr = $("#room-desc").val();
	var private = $("input[name=private]:checked").val();

	$.ajax({
		url: APIdir + "/new_room/" + room_name,
		type: 'GET',
		dataType: 'json',
		data: {
			descr: room_descr,
			private: private
		},
		success: function() {
			document.location.href = room_name;
		},
		error: function(error) {
			console.error(error);
			showError(getErrorMessage(error));
		}
	});
}

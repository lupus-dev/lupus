/* 
 * Lupus in Tabula
 * ...un progetto di Edoardo Morassutto
 * Contributors:
 * - 2014 Edoardo Morassutto <edoardo.morassutto@gmail.com>
 */

function checkGameDescr() {
	var descr = $("#game-desc").val();
	var container = $("#game-desc").parent();
	if (isValidDescr(descr)) {
		container.addClass("has-success").removeClass("has-error");
		$("#game-desc-icon").addClass("glyphicon-ok").removeClass("glyphicon-remove");
		$("#save").removeClass("disabled");
		$("#start").removeClass("disabled");
	}
	else {
		container.removeClass("has-success").addClass("has-error");
		$("#game-desc-icon").removeClass("glyphicon-ok").addClass("glyphicon-remove");
		$("#save").addClass("disabled");
		$("#start").addClass("disabled");
	}
}

function getAutoRoles() {
	var auto_roles = $(".auto-role:checked");
	var res = [];
	auto_roles.each(function(i,e) {
		var input = $(e);
		res.push(input.data("role"));
	});
	return res;
}
function getManualRoles() {
	var manual_roles = $(".manual-role");
	var res = {};
	manual_roles.each(function(i,e) {
		var input = $(e);
		res[input.data("role")] = input.val();
	});		
	return res;
}

function saveGame(start) {
	var game_descr = $("#game-desc").val();
	var auto_roles = getAutoRoles();
	var manual_roles = getManualRoles();
	console.log(auto_roles);
	
	var gen_info = {
		gen_mode: $("input[name=gen_mode]:checked").val(),
		auto: {
			num_players: $("#auto-num-players").val(),
			roles: auto_roles
		},
		manual: {
			roles: manual_roles
		}
	};
	$.ajax({
		url: APIdir + "/game/"+room_name+"/"+game_name+"/setup",
		type: 'GET',
		dataType: 'json',
		data: {
			descr: game_descr,
			gen_info: gen_info
		},
		success: function(data) {
			console.log(data);
			if (start) 
				ajaxStart();
		},
		error: function(error) {
			console.error(error);
			showError(getErrorMessage(error));
		}
	});
}
function startGame() {
	saveGame(true);
}
function ajaxStart() {
	$.ajax({
		url: APIdir + "/game/"+room_name+"/"+game_name+"/start",
		type: 'GET',
		dataType: 'json',
		success: function() {
			location.reload(true);
		},
		error: function(error) {
			console.error(error);
			showError(getErrorMessage(error));
		}
	});
}

function sortACLTable() {
	var $table = $('#acl-table'),
		$rows = $('tbody > tr', $table);

	$rows.sort(function (a, b) {
		var keyA = $('td', a).text().trim();
		var keyB = $('td', b).text().trim();

		return (keyA > keyB) ? 1 : 0;
	});

	$rows.each(function (index, row) {
		$table.append(row);
	});
}

function addACL() {
	var username = $("#add-acl-text").val();

	$.ajax({
		url: APIdir + "/room/" + room_name + "/add_acl",
		type: 'POST',
		dataType: 'json',
		data: { username: username },
		success: function(data) {
			console.log(data);
			var tr = $("<tr>");

			var td1 = $("<td>").text(data.user.username + " ");
			td1.append(
				$('<span>').addClass('label label-'+data.user['level-name'].toLowerCase())
					.text(data.user['level-name'])
			);

			var td2 = $("<td>").append(
				$("<button>").addClass("btn btn-xs btn-danger btn-remove-from-acl")
					.attr("data-id-user", data.user.id_user)
					.html("&times;")
					.click(removeACL)
			);

			tr.append(td1);
			tr.append(td2);
			$("#acl-table").append(tr);
			$("#add-acl-text").val('');

			sortACLTable();
		},
		error: function (error) {
			console.log(error);
			showError(getErrorMessage(error));
		}
	});

	return false;
}

function removeACL() {
	var $this = $(this);
	var id_user = $this.attr("data-id-user");

	console.log(id_user);
	$.ajax({
		url: APIdir + "/room/" + room_name + "/remove_acl",
		type: 'POST',
		dataType: 'json',
		data: { id_user: id_user },
		success: function(data) {
			$this.closest('tr').remove();
		},
		error: function(error) {
			console.log(error);
			showError(getErrorMessage(error));
		}
	});
}

function setupACLAutocompletion() {
	$("#add-acl-text").autocomplete({
		source: function (request, response) {
			$.ajax({
				url: APIdir + '/room/' + room_name + '/autocomplete',
				type: 'GET',
				data: { q: request.term },
				dataType: 'json',
				success: function(data) {
					var res = [];
					for (var i in data)
						res.push(data[i].username);
					response(res);
				},
				error: function(error) {
					console.log(error);
				}
			})
		}
	});
}

$(function(){
	var changeFunc = function() {
		if ($("input[name=gen_mode]:checked").val() == "auto") {
			$("#gen-auto-form").show();
			$("#gen-manual-form").hide();
		} else {
			$("#gen-auto-form").hide();
			$("#gen-manual-form").show();
		}			
	};
	
	$("#gen-auto").change(changeFunc);
	$("#gen-manual").change(changeFunc);

	$('.btn-remove-from-acl').click(removeACL);
	$('#add-acl').submit(addACL);

	changeFunc();
	setupACLAutocompletion();
});

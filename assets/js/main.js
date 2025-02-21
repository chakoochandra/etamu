var $ = $.noConflict();

$.busyLoadSetup({
	animation: "fade", //fade, false, slide
	animationDuration: "slow", //4000, etc...
	background: "rgba(44, 59, 65, 0.5)",
	color: "#EEFFDD", //white, etc...
	textColor: "#EEFFDD", //white, etc...
	text: "SEDANG MEMPROSES...",
	textPosition: "bottom",
	textMargin: "2rem",
	fontSize: "2rem",
	maxSize: "150px",
	minSize: "150px",
	spinner: "cube-grid", //accordion, circles, circle-line, cube, cubes, cube-grid, pulsar
	//fontawesome: 'fa fa-spinner fa-pulse fa-5x fa-fw',
	//containerClass: 'my-own-container-class',
	//containerItemClass: 'my-own-container-item-class',
	//spinnerClass: 'my-own-spinner-class',
	//textClass: 'my-own-text-class',
});

$(document).ajaxStart(function () {
	if (typeof hideLoader === "undefined" || !hideLoader) {
		$.busyLoadFull("show");
	}
});

$(document).ajaxSuccess(function (event, jqxhr, settings, response) {
	if (response.not_logged_in && response.not_logged_in === true) {
		//redirected if session expired
		document.location.href = window.location.href;
	}

	if (response.csrf_token_name) {
		csrf_token_name = response.csrf_token_name;
	}

	if (response.csrf_hash) {
		csrf_hash = response.csrf_hash;
	}
});

$(document).ajaxComplete(function (event, jqxhr, settings) {
	$.busyLoadFull("hide");
});

$(document).ajaxError(function (event, jqxhr, settings, thrownError) {
	toastr.error("Terjadi Kesalahan");
});

setInterval(function () {
	$(".realtime-clock").html(
		new Date()
			.toLocaleDateString("id-ID", {
				weekday: "long",
				day: "2-digit",
				month: "long",
				year: "numeric",
				hour: "2-digit",
				minute: "2-digit",
				second: "2-digit",
			})
			.replace(/\./g, ":")
	);
}, 1000);

//handle back button ajax
$(function () {
	var type;

	$.history
		.on("load change", function (event, url, type) {
			if (
				event.type === "change" ||
				(event.type === "load" && type === "hash")
			) {
				callAjax(url);
			}
		})
		.listen();

	type = $.history.type();
	if (type === "hash" && location.pathname.length > 1) {
		// /pathname -> /#/pathname
		location.href = "/#" + location.pathname;
	} else if (type === "pathname" && location.hash.substr(1, 1) === "/") {
		// /#/pathname -> /pathname
		location.href = location.hash.substr(1);
	}
});

function prepareModalContent(showLoadingBar = true) {
	$("#modal-input .modal-title").html(
		'<span class="modal-title"><i class="fa fa-spinner fa-pulse fa-fw margin-bottom" aria-hidden="true"></i>Sedang memproses</span>'
	);
	if (showLoadingBar) {
		$("#modal-input .modal-body").html(
			'<div class="progress mt-3"><div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%"></div></div>'
		);
	}
	$("#modal-input").modal("show", {});
}

function setModalContent(data) {
	let showTitle = data.showTitle != undefined ? data.showTitle : true;
	if (showTitle) {
		$("#modal-input .modal-title").html(data.title ? data.title : "Info");
	} else {
		$("#modal-input .modal-title").html("");
	}

	if (data.redirect && data.message) {
		toastr.error(data.message);
		$("#modal-input .modal-body").html(
			'<div class="alert alert-danger alert-dismissible"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button><h5><i class="icon fa fa-warning"></i> Perhatian!</h5>' +
				data.message +
				"</div>"
		);
	} else {
		$("#modal-input .modal-body").html("");
		$("#modal-input .modal-body").html(data.content ? data.content : "");

		$("#modal-input .modal-body").find(".table-sticky thead th").css("top", 0);
	}

	$("#modal-input-dialog").removeClass();
	$("#modal-input-dialog").addClass(
		"modal-dialog " + (data.size ? data.size : "modal-md")
	);

	$("#modal-input").modal("show", {});

	$(".btn-back").addClass("btn-modal");
}

function showAlert(
	message,
	title = "",
	target = ".container-main .card-body:first",
	alertClass = "danger",
	dismissible = true
) {
	removeAlert(target);
	$(target).prepend(
		'<div class="mb-1 my-alert alert alert-' +
			alertClass +
			" " +
			(dismissible ? "alert-dismissible " : "") +
			'"><h5>' +
			title +
			"</h5>" +
			message +
			"</div>"
	);
}

function removeAlert(target = ".container-main .card-body:first") {
	$(target + " .my-alert").remove();
}

function openModal(url, data = {}) {
	prepareModalContent();

	var title = data.title ? data.title : "";

	$.ajax({
		url: url,
		data: data,
		success: function (data) {
			if (title) {
				data.title = title;
			}

			setModalContent(data);
		},
	});
}

function postAjax(url, data = {}, processData = true, showLoadingBar = true) {
	var data_csrf = {};
	data_csrf[csrf_token_name] = csrf_hash;

	$.extend(data, data_csrf);

	let options = {
		url: url,
		type: "post",
		data: data,
		// dataType: 'text',
		// contentType: 'application/x-www-form-urlencoded',
		beforeSend: function () {
			if ($("#modal-input").is(":visible")) {
				prepareModalContent(showLoadingBar);
			} else if (showLoadingBar) {
				$(".container-main").html(
					'<div class="progress mt-3 w-100"><div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%"></div></div>'
				);
			}
		},
		success: function (data, textStatus, jQxhr) {		
			if (data.status) {
				if (data.urlSummary) {
					count_summary(data.urlSummary);
				}

				if (data.redirect) {
					callAjax(data.redirect, true, showLoadingBar);
				} else if (data.content) {
					if (data.showOnModal) {
						setModalContent(data);
					} else {
						if (data.breadcrumb) {
							$(".my-breadcrumb").html(data.breadcrumb);
						}

						$("#modal-input").modal("hide");
						$(".container-main").html(data.content);
					}
				}

				if (data.message) {
					toastr.success(data.message);
				}
			} else {
				if (data.content) {
					if ($("#modal-input.show").length > 0) {
						setModalContent(data);
					} else {
						if (data.breadcrumb) {
							$(".my-breadcrumb").html(data.breadcrumb);
						}

						$(".container-main").html(data.content);
						$(".btn-back").addClass("btn-ajax");
					}
				}

				if (data.message) {
					toastr.error(data.message);
				}
			}
		},
	};

	if (processData === false) {
		options.processData = false;
		options.contentType = false;
	}

	$.ajax(options);
}

function updateActiveMenu(url) {
	$("ul.my-menu li").removeClass("menu-open");
	$("ul.my-menu li a").removeClass("active");
	$("ul.my-menu li").each(function (idx, li) {
		if ($("a", $(li)).attr("href") == url) {
			$("a", $(li)).addClass("active");

			if ($(li).parent().hasClass("nav-treeview")) {
				$(li).parent().prev().addClass("active");
				$(li).parent().closest("li").addClass("menu-open");
			}
		}
	});

	$(".navbar-nav .nav-item-antrian a").removeClass("active");
	$(".navbar-nav .nav-item-antrian").each(function (idx, li) {
		if ($("a", $(li)).attr("href") == url) {
			$("a", $(li)).addClass("active");
		}
	});
}

function callAjax(url, showBreadcrumb = true, showLoadingBar = true) {
	$.ajax({
		url: url,
		beforeSend: function () {
			if ($("#modal-input").is(":visible")) {
				prepareModalContent();
			} else if (showLoadingBar) {
				$(".container-main").html(
					'<div class="progress mt-3 w-100"><div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%"></div></div>'
				);
			}
		},
		success: function (data) {
			if (data.redirect) {
				callAjax(data.redirect);
			} else if (data.content) {
				if (data.showOnModal) {
					setModalContent(data);
				} else {
					if (data.breadcrumb) {
						if (
							showBreadcrumb &&
							url != new RegExp(/^.*\//).exec(window.location.href)[0]
						) {
							$(".my-breadcrumb").html(data.breadcrumb);
						} else {
							$(".my-breadcrumb").html("");
						}
					}

					$("#modal-input").modal("hide");
					$(".container-main").html(data.content);

					if (data.title) {
						$(".container-main-title").html(data.title);
					}
				}

				if (data.message) {
					if (data.status) {
						toastr.success(data.message);
					} else {
						toastr.error(data.message);
					}
				}

				updateActiveMenu(url);

				$.history.push(url);

				if (document.title != data.title) {
					document.title = data.title;
				}
			}
		},
	});
}

function loadPartial(url, target, data = {}) {
	// $(target).closest('.card').append('<div class="overlay leaves"><i class="fa fa-spinner fa-pulse fa-3x fa-fw margin-bottom" aria-hidden="true"></i></div>');

	$.ajax({
		url: url,
		data: data,
		success: function (data) {
			$(target).html(data.content ? data.content : "");
			$(".overlay").remove();
		},
		error: function (jqXhr, textStatus, errorThrown) {
			$(".overlay").remove();
		},
	});
}

function paginationPartial(target) {
	$("body").on("click", target + " .pagination-partial li a", function (e) {
		e.preventDefault();
		loadPartial($(this).attr("href"), target);
	});
}

var enableRefresh = false;
var intervalRefresh = 1000;
var worker;

//handle form ajax
// $("body").on("submit", ".form-ajax", function (e) {
// 	e.preventDefault();

// 	// postAjax($('.form-ajax').attr('action'), $(this).serialize());
// 	postAjax($(".form-ajax").attr("action"), new FormData(this), false, false);
// });

$("body").on("click", ".btn-modal", function (e) {
	e.preventDefault();
	openModal($(this).attr("href"), {});
});

//klik pada left menu
$("body").on(
	"click",
	"aside.main-sidebar a:not(.btn-non-ajax):not(.btn-modal)",
	function (e) {
		e.preventDefault();

		if ($(this).attr("href") != "#") {
			callAjax($(this).attr("href"), true, false);
		}
	}
);

//klik pada nav item antrian
$("body").on("click", ".navbar-nav .nav-item-antrian a", function (e) {
	e.preventDefault();
	callAjax($(this).attr("href"), true, false);
});

//klik pada breadcrumb
$("body").on("click", ".breadcrumb li a", function (e) {
	e.preventDefault();
	callAjax($(this).attr("href"), !$(this).parent().is(":first-child"), false);
});

//klik pada card tab panel
$("body").on("click", '[role="tab"]', function (e) {
	let url = $(this).data("url");
	let control = $(this).attr("aria-controls");
	let html = $(this)
		.closest(".card")
		.find("#" + control)
		.html();

	if (url !== undefined && html.length == 0) {
		$(this)
			.closest(".card")
			.append(
				'<div class="overlay leaves"><i class="fa fa-spinner fa-pulse fa-3x fa-fw margin-bottom" aria-hidden="true"></i></div>'
			);

		$.ajax({
			url: url,
			success: function (data) {
				$("#" + control).html(data.content ? data.content : "");
				$(".overlay").remove();
			},
			error: function (jqXhr, textStatus, errorThrown) {
				$(".overlay").remove();
			},
		});
	}
});

//klik pada pagination
$("body").on("click", ".pagination-page li a", function (e) {
	e.preventDefault();
	callAjax($(this).attr("href"));
});

$("body").on("click", ".btn-ajax", function (e) {
	e.preventDefault();
	callAjax($(this).attr("href"));
});

$("body").on("click", ".btn-confirm", function (e) {
	e.preventDefault();
	if (confirm($(this).data("confirm-message"))) {
		postAjax(
			$(this).attr("href"),
			$(this).data("json") ? $(this).data("json") : {},
			true,
			false
		);
	}
});

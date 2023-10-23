$(document).ready(function() {
	//$(".header").sticky({topSpacing:0});
	$('.popup').colorbox({
		iframe: true,
		width: "895px",
		height: "600px"
	});
	$('.expand_box').click(function() {
		$('.expand_box').removeClass('expanded');
		$(this).toggleClass('expanded');
		$(".show_expand_box").addClass("expanded");
		resizeFrame();
	});
	$(document).on('click', '.close_panel', function() {
		$('.expand_box').removeClass('expanded');
		$(".show_expand_box").addClass("expanded");
		resizeFrame();
	});
	$(".scr_top").click(function() {
		$("html, body").animate({
			scrollTop: 0
		}, "slow");
		return false;
	});
	$(window).scroll(function() {
		var scroll = $(this).scrollTop();
		if (scroll >= 100) {
			$(".scr_top").addClass("on");
		} else {
			$(".scr_top").removeClass("on");
		}
	});
	//$(document).on("scroll", onScroll);	
	$(window).scroll(function() {
		$("section.index_scroll").each(function() {
			if ($(this).visible(true)) {
				$id_tmp = $(this).attr("id");
				console.log($id_tmp);
				if ($id_tmp != '') {
					$id = $id_tmp;
					// console.log($id);
					$href = $("a[href='#" + $id + "']");
					if ($href.length > 0) {
						// console.log($href);
						$href.addClass('active').parent().siblings().children().removeClass('active');
					} else {
						$("#menu-ul li a.active").removeClass("active");
					}
				} else {

				}
			}
		});
	});
});



/* menu sticky js */
$(document).on("click", ".menu ul li.scroll a", function(e) {
	e.preventDefault();
	$('html, body').animate({
		scrollTop: $($(this).attr("href")).offset().top - (parseInt($(".header").height())) - 50
	}, 1000);
	// $('.menu ul li.scroll a').addClass('active');
});
$(function() {
	$('.menu ul li.scroll a').on('click', function() {
		$(this).addClass('active').parent().siblings().children().removeClass('active');
	});
});

/* scroll menu add class active */
function onScroll(event) {
	var scrollPos = $(document).scrollTop() + 0;
	$('#menu-ul li.scroll a').each(function() {
		var currLink = $(this);
		var refElement = $("" + currLink.attr("href"));
		if (refElement.position().top <= scrollPos && refElement.position().top > scrollPos) {
			$('#menu-ul li.scroll a').removeClass("active");
			currLink.addClass("active");
		} else {
			currLink.removeClass("active");
		}
	});
}

$(document).off("click", ".mobile-icon");
$(document).on("click", ".mobile-icon", function() {
	$("#menu-ul").toggleClass('show');
	$(".mobile-icon").toggleClass('active');
	$(".event_notification").removeClass('show');
});
$(document).off("click", "#menu-ul > li.scroll > a");
$(document).on("click", "#menu-ul > li.scroll > a", function() {
	$("#menu-ul").removeClass('show');
	$(".mobile-icon").removeClass('active');
});

/*$(document).ready(function($) {
	$(document).on("scroll", onScroll);
});*/
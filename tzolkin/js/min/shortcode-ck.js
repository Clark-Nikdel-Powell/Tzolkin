jQuery(function(t){function i(){var i=0;for(i=0;i<t(".tzolkin-grid .tzolkin-dates .tzolkin-row").length;i++){var e=0;t(".row-"+i+" .date-top").each(function(){t(this).outerHeight()>e&&(e=t(this).outerHeight())}),t(".row-"+i).css("height",e)}}i(),t("body").on("click",".tzolkin-grid .tzolkin-row",function(){if(t(this).hasClass("open")){var i=0;t(this).find(".date-top").each(function(){t(this).outerHeight()>i&&(i=t(this).outerHeight())}),t(this).css("height",i).removeClass("open")}else if(0!==t(this).find(".circles").length){var e=0;t(this).find(".cell").each(function(){var i=t(this).find(".date-top").outerHeight(!0)+t(this).find(".details").outerHeight(!0);i>e&&(e=i)}),t(this).css("height",e).addClass("open")}}),t(".format .toggle").on("click",function(){t(this).hasClass("list")?(t(".format label").removeClass("active"),t(this).parent("label").addClass("active"),t(".tzolkin-grid").animate({opacity:0},"fast",function(){t(".tzolkin-row").removeAttr("style").removeClass("open"),t(".tzolkin-grid").removeClass("tzolkin-grid").addClass("tzolkin-list")}).animate({opacity:1},"fast")):(t(".format label").removeClass("active"),t(this).parent("label").addClass("active"),t(".tzolkin-list").animate({opacity:0},"fast",function(){t(".tzolkin-list").removeClass("tzolkin-list").addClass("tzolkin-grid"),i()}).animate({opacity:1},"fast"))})});
function dropdown_fix () {
	$('div.navbar-pivot ul li.dropdown li.dropdown-submenu a').click(function(e) {
		e.stopPropagation();
		$('div.navbar-pivot ul li.dropdown li.dropdown-submenu a .dropdown-menu').toggle();
	});
}